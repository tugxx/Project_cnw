<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: dang-nhap');
    exit;
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT * 
        FROM `users` 
        WHERE `id` = ?";
$user = DB::fetchOne($sql, [$userId]);
if (!$user || !$user['is_active']) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
}

if ($user['role'] !== 'student') {
    showPopUp('Chức năng này chỉ dành cho sinh viên.', 'trang-chu', 'error');
}

$sectionId = trim($_GET['section_id'] ?? '');
$sessionId = trim($_GET['session_id'] ?? '');
if (empty($sectionId) || empty($sessionId)) {
    showPopUp('Thiếu thông tin lớp học phần hoặc đợt đăng ký.', 'danh-sach-lop-hoc-phan', 'error');
}

$sql = "SELECT ss.*,
            s.section_code, s.section_name,
            rs.registration_session_name
        FROM `sections_sessions` ss
        JOIN `sections` s 
            ON ss.section_id = s.id
        JOIN `registration_sessions` rs 
            ON ss.session_id = rs.id
        WHERE ss.section_id = ? AND ss.session_id = ?";
$sectionSession = DB::fetchOne($sql, [$sectionId, $sessionId]);
if (!$sectionSession) {
    showPopUp('Lớp học phần - Đợt đăng ký không tồn tại.', 'danh-sach-lop-hoc-phan', 'error');
}

$sql = "SELECT `id` 
        FROM `sections_students` 
        WHERE `section_id` = ? AND `student_id` = ?";
$isEnrolled = DB::fetchOne($sql, [$sectionId, $userId]);
if (!$isEnrolled) {
    showPopUp('Bạn không phải là sinh viên thuộc lớp học phần này.', 'danh-sach-lop-hoc-phan', 'error');
}


$sectionSessionId = $sectionSession['id'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupName = trim($_POST['group_name'] ?? null);

    if (empty($errors)) {
        $now = date('Y-m-d H:i:s');
        if (!empty($sectionSession['group_deadline']) && $now > $sectionSession['group_deadline']) {
            $errors[] = 'Đã quá thời hạn lập nhóm cho đợt đăng ký này.';
        }

        if (empty($errors)) {
            try {
                DB::beginTransaction();
                $sql = "SELECT `id`, `max_group`, `group_deadline` 
                        FROM `sections_sessions` 
                        WHERE `id` = ? 
                        FOR UPDATE";
                $sectionSessionLocked = DB::fetchOne($sql, [$sectionSessionId]);

                if (!empty($sectionSessionLocked['max_group'])) {
                    $sql = "SELECT COUNT(*) as total 
                            FROM groups 
                            WHERE section_session_id = ?";
                    $groupCount = DB::fetchOne($sql, [$sectionSessionId]);
                    if ($groupCount && $groupCount['total'] >= $sectionSessionLocked['max_group']) {
                        throw new Exception('MAX_GROUP_REACHED');
                    }
                }

                $sql = "SELECT g.id 
                        FROM `groups` g
                        JOIN `group_members` gm 
                            ON g.id = gm.group_id
                        WHERE g.section_session_id = ? AND gm.student_id = ?";
                $existingGroup = DB::fetchOne($sql, [$sectionSessionId, $userId]);
                if ($existingGroup) {
                    throw new Exception('ALREADY_IN_GROUP');
                }

                $createdAt = date('Y-m-d H:i:s');
                $sql = "INSERT INTO `groups` (`section_session_id`, `group_name`, `created_at`, `status`) 
                        VALUES (?, ?, ?, 'open')";
                $paramsGroup = [$sectionSessionId, $groupName, $createdAt];
                $groupId = DB::insert($sql, $paramsGroup);

                $sql = "INSERT INTO `group_members` (`group_id`, `student_id`, `role`, `joined_at`) 
                        VALUES (?, ?, 'leader', ?)";
                DB::execute($sql, [$groupId, $userId, $createdAt]);
                DB::commit();
                header("Location: /Project_cnw/danh-sach-nhom?section_id=" . $sectionId . "&session_id=" . $sessionId);
                exit;
            } catch (Exception $e) {
                DB::rollBack();
                if ($e->getMessage()=="MAX_GROUP_REACHED") {
                    $errors[] = 'Số lượng nhóm trong lớp học phần này đã đạt đến giới hạn cho phép.';
                } elseif ($e->getMessage()=="ALREADY_IN_GROUP") {
                    $errors[] = 'Bạn đã tham gia một nhóm khác trong lớp học phần - đợt đăng ký này.';
                } else {
                    $errors[] = 'Đã xảy ra lỗi trong quá trình khởi tạo nhóm. Vui lòng thử lại.';
                    error_log('Create Group Error: ' . $e->getMessage());
                }
            }
        }
    }
}

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/nhom/tao-nhom.php';