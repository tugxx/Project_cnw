<?php
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

$sectionId = $_GET['section_id'] ?? "";
$sql = "SELECT `id`, `section_code`, `section_name`, `description`, `course_id`, `cover_image` 
        FROM `sections` 
        WHERE `id` = ?";
$section = DB::fetchOne($sql, [$sectionId]);
if (!$section) {
    showPopUp('Lớp học phần không tồn tại trên hệ thống.', 'trang-chu', 'error');
    exit;
}

$courseId = $section['course_id'];
$sql = "SELECT `id` 
        FROM `courses_lecturers` 
        WHERE `course_id` = ? AND `lecturer_id` = ?";
$isAssigned = DB::fetchOne($sql, [$courseId, $userId]);
if (!$isAssigned) {
    showPopUp('Bạn không có quyền quản lý học phần này.', 'danh-sach-hoc-phan', 'error');
    exit;
}

$errors = [];
$successMsg = '';
if (isset($_POST['add_student'])) {
    $studentCode = trim($_POST['student_code'] ?? '');
    if (empty($studentCode)) {
        $errors[] = 'Vui lòng nhập Mã sinh viên.';
    } 

    if (empty($errors)) {
        $sql = "SELECT `id`, `is_active`, `role`, `full_name` 
                FROM `users` 
                WHERE `user_code` = ? AND `role` = 'student'";
        $student = DB::fetchOne($sql, [$studentCode]);

        if (!$student) {
            $errors[] = "Không tìm thấy sinh viên có mã '{$studentCode}' trong hệ thống.";
        } elseif (!$student['is_active']) {
            $errors[] = "Tài khoản của sinh viên '{$student['full_name']}' đang bị khóa.";
        } else {
            $sql = "SELECT `id` 
                    FROM `sections_students` 
                    WHERE `section_id` = ? AND `student_id` = ?";
            $existsInSection = DB::fetchOne($sql, [$sectionId, $student['id']]);

            if ($existsInSection) {
                $errors[] = "Sinh viên '{$student['full_name']}' ({$studentCode}) đã có trong lớp học phần này.";
            }
        }
    }
        
    if (empty($errors)) {
        try {
            $sqlInsert = "INSERT INTO `sections_students` (`section_id`, `student_id`) VALUES (?, ?)";
            DB::execute($sqlInsert, [$sectionId, $student['id']]);
            $successMsg = "Đã thêm sinh viên '{$student['full_name']}' vào lớp học phần thành công.";
        } catch (Exception $e) {
            $errors[] = "Lỗi khi thêm sinh viên vào lớp. Vui lòng thử lại.";
            error_log($e->getMessage());
        }
    }
}


if (isset($_POST['remove_student_id'])) {
    $studentId = trim($_POST['remove_student_id'] ?? '');
    if (empty($studentId)) {
        $errors[] = "ID sinh viên không hợp lệ.";
    }

    if (empty($errors)) {
        $sql = "SELECT `id` 
                FROM `sections_students` 
                WHERE `section_id` = ? AND `student_id` = ?";
        $exists = DB::fetchOne($sql, [$sectionId, $studentId]);

        if (!$exists) {
            $errors[] = "Sinh viên này không tồn tại trong lớp học phần.";
        }
    }

    if (empty($errors)) {
        try {
            DB::beginTransaction();
            $sql = "SELECT gm.`group_id`, gm.`role`, 
                        g.`section_session_id`, g.`section_session_topic_id`
                    FROM `group_members` gm
                    JOIN `groups` g 
                        ON gm.`group_id` = g.`id`
                    JOIN `sections_sessions` ss 
                        ON g.`section_session_id` = ss.`id`
                    WHERE ss.`section_id` = ? AND gm.`student_id` = ?";
            $userGroup = DB::fetchOne($sql, [$sectionId, $studentId]);
            if ($userGroup) {
                $groupId = $userGroup['group_id'];
                $isLeader = ($userGroup['role'] === 'leader');
                $topicId = $userGroup['section_session_topic_id'];

                $sql = "DELETE FROM `group_members` 
                        WHERE `group_id` = ? AND `student_id` = ?";
                DB::execute($sql, [$groupId, $studentId]);

                $sql = "SELECT `student_id`, `joined_at` 
                        FROM `group_members` 
                        WHERE `group_id` = ? 
                        ORDER BY `joined_at` ASC";
                $remainingMembers = DB::fetchAll($sql, [$groupId]);
                $remainingCount = count($remainingMembers);

                if ($remainingCount === 0) {
                    $sql = "DELETE FROM `groups` 
                            WHERE `id` = ?";
                    DB::execute($sql, [$groupId]);
                } else {
                    if ($isLeader) {
                        $newLeaderId = $remainingMembers[0]['student_id'];
                        $sql = "UPDATE `group_members` 
                                SET `role` = 'leader' 
                                WHERE `group_id` = ? AND `student_id` = ?";
                        DB::execute($sql, [$groupId, $newLeaderId]);
                    }

                    if (!empty($topicId)) {
                        $sql = "SELECT `min_member` 
                                FROM `sections_sessions_topics` 
                                WHERE `id` = ?";
                        $topicConstraint = DB::fetchOne($sql, [$topicId]);

                        if ($topicConstraint) {
                            $minMember = (int)$topicConstraint['min_member'];
                            if ($remainingCount < $minMember) {
                                $sql = "UPDATE `groups` 
                                        SET `section_session_topic_id` = NULL
                                        WHERE `id` = ?";
                                DB::execute($sql, [$groupId]);
                            }
                        }
                    }
                }
            }

            $sql = "DELETE FROM `sections_students` 
                    WHERE `section_id` = ? AND `student_id` = ?";
            DB::execute($sql, [$sectionId, $studentId]);
            DB::commit();
            $successMsg = "Đã xóa sinh viên khỏi lớp học phần.";
        } catch (Throwable $e) {
            DB::rollBack();
            $errors[] = "Lỗi khi xóa sinh viên khỏi lớp. Vui lòng thử lại.";
            error_log("Lỗi xóa sinh viên ID {$studentId} khỏi section ID {$sectionId}: " . $e->getMessage());
        }
    }
}


if (isset($_POST['update_section_info'])) {
    $sectionCode = trim($_POST['section_code'] ?? '');
    $sectionName = trim($_POST['section_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $imageCoverPath = $section['cover_image']; 

    if (empty($sectionCode)) {
        $errors[] = 'Mã lớp học phần không được để trống.';
    }

    if (empty($sectionName)) {
        $errors[] = 'Tên lớp học phần không được để trống.';
    }

    if (empty($errors)) {
        $sql = "SELECT `id` 
                FROM `sections` 
                WHERE `course_id` = ? AND (`section_code` = ?) AND `id` != ?";
        $existing = DB::fetchOne($sql, [$courseId, $sectionCode, $sectionId]);
        if ($existing) {
            $errors[] = 'Mã lớp học phần bị trùng với một lớp khác trong cùng học phần.';
        }
    }

    if (empty($errors) && isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cover_image'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = 'Ảnh bìa chỉ chấp nhận định dạng: JPG, JPEG, PNG, WEBP.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Dung lượng ảnh tối đa là 2MB.';
        } else {
            $fileName = 'section_' . $sectionCode . '_' . time() . '.' . $fileExtension;
            $uploadDir = __DIR__ . '/../../../storage/uploads/sections/';

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                if (!empty($section['cover_image'])) {
                    $oldFilePath = $uploadDir . $section['cover_image'];
                    if (file_exists($oldFilePath)) {
                        try {
                            unlink($oldFilePath);
                        } catch (Throwable $e) {
                            $errors[] = 'Lỗi khi xoá ảnh cũ.';
                            error_log("Lỗi xóa file ảnh cũ ({$oldFilePath}): " . $e->getMessage());
                        }
                    }
                }
                $imageCoverPath = $fileName;
            } else {
                $errors[] = 'Lỗi khi lưu tệp ảnh bìa mới.';
            }
        }
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE `sections` 
                        SET `section_code` = ?, 
                            `section_name` = ?, 
                            `description` = ?, 
                            `cover_image` = ? 
                        WHERE `id` = ?";
            $updated = DB::execute($sql, [$sectionCode, $sectionName, !empty($description) ? $description : null, $imageCoverPath, $sectionId]);

            $successMsg = 'Cập nhật thông tin lớp học phần thành công!';
            $sql = "SELECT `id`, `section_code`, `section_name`, `description`, `course_id`, `cover_image` 
                    FROM `sections` 
                    WHERE `id` = ?";
            $section = DB::fetchOne($sql, [$sectionId]);
        } catch (Exception $e) {
            $errors[] = 'Không thể cập nhật thông tin. Vui lòng thử lại.';
            error_log($e->getMessage());
        }
    }
}


$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$sqlCount = "SELECT COUNT(*) as total 
             FROM `sections_students` 
             WHERE `section_id` = ?";
$totalRow = DB::fetchOne($sqlCount, [$sectionId]);
$totalStudents = (int)($totalRow['total'] ?? 0);
$totalPages = ceil($totalStudents / $perPage);

$sql = "SELECT u.`id`, u.`user_code`, u.`full_name`, u.`email`, u.`class`, u.`is_active`
        FROM `sections_students` ss
        JOIN `users` u ON ss.`student_id` = u.`id`
        WHERE ss.`section_id` = ?
        ORDER BY u.`user_code` ASC
        LIMIT ? OFFSET ?";
$studentsList = DB::fetchAll($sql, [$sectionId, $perPage, $offset]);


require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
require_once __DIR__ . '/../../../views/lop-hoc-phan/sua-lop-hoc-phan.php';