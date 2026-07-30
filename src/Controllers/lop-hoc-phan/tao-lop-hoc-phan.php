<?php
use Shuchkin\SimpleXLSX;

if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT `id`, `is_active`, `role` 
        FROM `users` 
        WHERE `id` = ?";
$user = DB::fetchOne($sql, [$userId]);

if (!$user || !$user['is_active']) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
    exit;
}

if ($user['role'] !== 'lecturer') {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không có quyền thực hiện chức năng này.', 'dang-nhap', 'error');
    exit;
}

$courseId = (int)($_GET['courseId'] ?? $_POST['courseId'] ?? 0);
$sql = "SELECT `id` 
        FROM `courses` 
        WHERE `id` = ?";
$course = DB::fetchOne($sql, [$courseId]);
if (!$course) {
    showPopUp('Học phần không tồn tại trong hệ thống.', 'dang-nhap', 'error');
    exit;
}

$sql = "SELECT `id` 
        FROM `courses_lecturers` 
        WHERE `course_id` = ? AND `lecturer_id` = ?";
$isAssigned = DB::fetchOne($sql, [$courseId, $userId]);
if (!$isAssigned) {
    showPopUp('Giảng viên không phụ trách học phần này.', 'dang-nhap', 'error');
    exit;
}

if (isset($_POST['checked_ids']) && is_array($_POST['checked_ids'])) {
    foreach ($_POST['checked_ids'] as $studentId => $value)  {
        if (!ctype_digit((string)$studentId)) {
            continue;
        }
        $_SESSION['checked_student_ids_' . $courseId][(int)$studentId] = ($value === '1');
    }
}

$errors = [];
if (isset($_POST["preview_excel"])) {
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
        $extension = strtolower(pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION));

        if ($extension === 'xlsx') {
            $xlsx = SimpleXLSX::parse($_FILES['excel_file']['tmp_name']);
            if ($xlsx) {
                $rows = $xlsx->rows();
                $sql = "SELECT `id` 
                        FROM `users` 
                        WHERE `role` = 'student'";
                $existingStudents = DB::fetchAll($sql);
                $existingStudentIdsMap = [];
                foreach ($existingStudents as $student) {
                    $existingStudentIdsMap[$student['id']] = true;
                }
                
                $lineNum = 0;
                foreach ($rows as $index => $row) {
                    if ($index === 0) continue; 
                    $lineNum = $index + 1;
                    $studentId = trim((string)($row[0] ?? ''));  
                    if ($studentId === '') {
                        continue; 
                    }
                    if (!ctype_digit($studentId)) {
                        $errors[] = "Dòng {$lineNum}: Mã SV '{$studentId}' không hợp lệ (Phải là số).";
                        continue;
                    }

                    $studentId = (int)$studentId;
                    if (!isset($existingStudentIdsMap[$studentId])) {
                        $errors[] = "Dòng {$lineNum}: Sinh viên ID {$studentId} không tồn tại trên hệ thống.";
                        continue;
                    }

                    $_SESSION['checked_student_ids_' . $courseId][$studentId] = true;
                }
            } else {
                $errors[] = "Lỗi đọc file Excel.";
                error_log(SimpleXLSX::parseError());
            }
        } else {
            $errors[] = "Chỉ chấp nhận file Excel (.xlsx).";
        }
    } else {
        $errors[] = "Vui lòng chọn file Excel hợp lệ.";
    }
}

$checkedStudentIds = [];
foreach (($_SESSION['checked_student_ids_' . $courseId] ?? []) as $studentId => $isChecked) {
    if ($isChecked) {
        $checkedStudentIds[] = $studentId;
    }
}

$viewMode = $_POST['view_mode'] ?? 'all';  
$keyword  = trim($_POST['search_keyword'] ?? '');
$page     = max(1, (int)($_POST['page'] ?? 1));
$perPage  = 10;
$offset   = ($page - 1) * $perPage;

if ($viewMode=="checked_only") {
    if (!empty($checkedStudentIds)) {
        $placeholders = implode(',', array_fill(0, count($checkedStudentIds), '?'));
        $sql = "SELECT `id`, `full_name`, `email`, `user_code`, `is_active`
                FROM `users` 
                WHERE `id` IN ($placeholders)";
        $studentsList = DB::fetchAll($sql, $checkedStudentIds);
    } else {
        $studentsList = [];
    }
} elseif ($viewMode=="search") {
    $sql = "SELECT `id`, `full_name`, `email`, `user_code`, `is_active`
            FROM `users` 
            WHERE `role` = 'student' AND `user_code` LIKE ?
            LIMIT ? OFFSET ?";
    $studentsList = DB::fetchAll($sql, ["%{$keyword}%", $perPage, $offset]);
} else {
    $sql = "SELECT `id`, `full_name`, `email`, `user_code`, `is_active`
            FROM `users` 
            WHERE `role` = 'student' AND `is_active` = 1
            LIMIT ? OFFSET ?";
    $studentsList = DB::fetchAll($sql, [$perPage, $offset]);
}

if (isset($_POST["create_section"])) {
    $sectionCode = trim($_POST['section_code'] ?? ''); 
    $sectionName = trim($_POST['section_name'] ?? ''); 
    $description = trim($_POST['description'] ?? ''); 
    $studentIds  = $checkedStudentIds;
    $imageCoverPath = null;

    if (empty($sectionCode)) {
        $errors[] = 'Mã lớp học phần không được để trống.';
    }

    if (empty($sectionName)) {
        $errors[] = 'Tên lớp học phần không được để trống.';
    }

    if (empty($errors)) {
        $sql = "SELECT id 
                FROM sections 
                WHERE course_id = ? AND section_code = ? ";
        $existingSection = DB::fetchOne($sql, [$courseId, $sectionCode]);
        if ($existingSection) {
            $errors[] = 'Mã lớp học phần đã tồn tại trong học phần này.';
        }
    }

    $validStudentIds = [];
    if (empty($errors) && !empty($studentIds) && is_array($studentIds)) {
        $invalidFormatIds = [];
        $duplicateIds = [];
        $validIds = [];
        $seenIds = [];    

        foreach ($studentIds as $rawId) {
            $trimmedId = trim($rawId);
            if ($trimmedId === '' || !ctype_digit($trimmedId)) {
                $invalidFormatIds[] = $rawId;
                continue;
            }

            $id = (int)$trimmedId;
            if (isset($seenIds[$id])) {
                $duplicateIds[] = $id;
                continue;
            }

            $seenIds[$id] = true;
            $validIds[] = $id;
        }

        if (!empty($invalidFormatIds)) {
            $errors[] = "Các giá trị sau không phải là ID hợp lệ: " . implode(', ', $invalidFormatIds);
        }

        if (!empty($duplicateIds)) {
            $errors[] = "Các ID sinh viên sau bị trùng lặp trong danh sách: " . implode(', ', $duplicateIds);
        }

        if (empty($errors) && !empty($validIds)) {
            $notFoundIds   = [];
            $notStudentIds = [];
            $inactiveIds   = [];

            $placeholders = implode(',', array_fill(0, count($validIds), '?'));
            $sql = "SELECT `id`, `role`, `is_active` 
                    FROM `users` 
                    WHERE `id` IN ($placeholders)";
            $foundUsers = DB::fetchAll($sql, $validIds);

            $foundUsersById = [];
            foreach ($foundUsers as $foundUser) {
                $foundUsersById[(int) $foundUser['id']] = $foundUser;
            }

            foreach ($validIds as $id) {
                if (!isset($foundUsersById[$id])) {
                    $notFoundIds[] = $id;
                    continue;
                }

                $foundUser = $foundUsersById[$id];
                if ($foundUser['role'] !== 'student') {
                    $notStudentIds[] = $id;
                    continue;
                }

                if (!$foundUser['is_active']) {
                    $inactiveIds[] = $id;
                    continue;
                }

                $validStudentIds[] = $id;
            }

            if (!empty($notFoundIds)) {
                $errors[] = "Các ID sau không tồn tại trong hệ thống: " . implode(', ', $notFoundIds);
            }

            if (!empty($notStudentIds)) {
                $errors[] = "Các ID sau không phải là sinh viên: " . implode(', ', $notStudentIds);
            }

            if (!empty($inactiveIds)) {
                $errors[] = "Các ID sinh viên sau đang bị khóa tài khoản: " . implode(', ', $inactiveIds);
            }
        }
    }

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cover_image'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = 'Chỉ chấp nhận các định dạng ảnh: JPG, JPEG, PNG, WEBP.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Dung lượng ảnh tối đa là 2MB.';
        } else {
            $fileName = 'section_' . $sectionCode . '_' . time() . '.' . $fileExtension;
            $uploadDir = __DIR__ . '/../../../storage/uploads/sections/';

            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $imageCoverPath = $fileName;
            } else {
                $errors[] = 'Lỗi trong quá trình lưu tệp ảnh.';
            }
        }
    } elseif (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Lỗi khi tải ảnh lên.';
    }

    if (empty($errors)) {
        try {
            DB::beginTransaction();
            $sql = "INSERT INTO `sections` (`section_code`, `section_name`, `description`, `course_id`, `cover_image`) 
                    VALUES (?, ?, ?, ?, ?)";
            $sectionId = DB::insert($sql, [$sectionCode, $sectionName, !empty($description) ? $description : null, $courseId, $imageCoverPath]);
            if (!$sectionId) {
                throw new Exception('Không thể tạo lớp học phần. Vui lòng thử lại.');
            }

            foreach ($validStudentIds as $studentId) {
                $sql = "INSERT INTO `sections_students` (`section_id`, `student_id`) VALUES (?, ?)";
                $inserted = DB::execute($sql, [$sectionId, $studentId]);
                if (!$inserted) {
                    throw new Exception("Không thể thêm sinh viên ID {$studentId} vào lớp học phần.");
                }
            }
            DB::commit();
            unset($_SESSION['checked_student_ids_' . $courseId]);
        } catch (Exception $e) {
            DB::rollBack();
            $errors[] = 'Đã xảy ra lỗi trong quá trình lưu dữ liệu. Vui lòng thử lại.';
            error_log($e->getMessage());
        }
    }
}

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/lop-hoc-phan/tao-lop-hoc-phan.php';