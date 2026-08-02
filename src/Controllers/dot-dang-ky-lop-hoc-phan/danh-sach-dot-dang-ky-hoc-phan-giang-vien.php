<?php
$userId = $_SESSION['user']['id'] ?? "";
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

$courseId = $_GET['course_id'] ?? "";
$sql = "SELECT `id`, `course_code`, `course_name`, `description` 
        FROM `courses`
        WHERE `id` = ?";
$course = DB::fetchOne($sql, [$courseId]);
if (!$course) {
    showPopUp('Học phần không tồn tại trong hệ thống.', 'danh-sach-hoc-phan', 'error');
    exit;
}

$sql = "SELECT `id` 
        FROM `courses_lecturers` 
        WHERE `course_id` = ? AND `lecturer_id` = ?";
$isAssigned = DB::fetchOne($sql, [$courseId, $userId]);
if (!$isAssigned) {
    showPopUp('Giảng viên không phụ trách học phần này.', 'danh-sach-hoc-phan', 'error');
    exit;
}

$sql = "SELECT rs.id AS session_id,
            rs.registration_session_name, rs.start_time, rs.end_time, rs.created_at,
            GROUP_CONCAT(CONCAT(s.section_code, ' - ', s.section_name) SEPARATOR '||') AS section_list
        FROM `registration_sessions` rs
        LEFT JOIN `sections_sessions` ss 
            ON rs.id = ss.session_id
        LEFT JOIN `sections` s 
            ON ss.section_id = s.id
        WHERE rs.course_id = ?
        GROUP BY rs.id, rs.registration_session_name, rs.start_time, rs.end_time, rs.created_at
        ORDER BY rs.created_at DESC";
$registrationSessions = DB::fetchAll($sql, [$courseId]);

require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
require_once __DIR__ . '/../../../views/dot-dang-ky-lop-hoc-phan/danh-sach-dot-dang-ky-hoc-phan-giang-vien.php';
?>