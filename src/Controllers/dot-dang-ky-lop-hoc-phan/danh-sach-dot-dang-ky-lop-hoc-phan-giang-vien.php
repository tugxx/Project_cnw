<?php
$userId = $_SESSION['user']['id'] ?? "";
if (empty($userId)) {
    destroyUserSession();
    showPopUp('Vui lòng đăng nhập để thực hiện chức năng này.', 'dang-nhap', 'error');
    exit;
}

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
    showPopUp('Tài khoản của bạn không có quyền thực hiện chức năng này.', 'trang-chu', 'error');
    exit;
}


$sectionId = $_GET['section_id'] ?? "";
$sql = "SELECT `id`, `section_code`, `section_name`, `description`, `course_id`
        FROM `sections`
        WHERE `id` = ?";
$section = DB::fetchOne($sql, [$sectionId]);
if (!$section) {
    showPopUp('Lớp học phần không tồn tại trong hệ thống.', 'danh-sach-lop-hoc-phan', 'error');
    exit;
}

$sql = "SELECT `id` 
        FROM `courses_lecturers` 
        WHERE `course_id` = ? AND `lecturer_id` = ?";
$isTeaching = DB::fetchOne($sql, [$section['course_id'], $userId]);
if (!$isTeaching) {
    showPopUp('Bạn không có quyền quản lý lớp học phần này.', 'danh-sach-lop-hoc-phan', 'error');
    exit;
}


$sql = "SELECT ss.id AS section_session_id,
            ss.group_deadline, ss.topic_deadline, ss.max_group,
            rs.id AS session_id,
            rs.registration_session_name,  rs.description,  rs.start_time,  rs.end_time
        FROM `sections_sessions` ss
        JOIN `registration_sessions` rs 
            ON ss.session_id = rs.id
        WHERE ss.section_id = ?
        ORDER BY rs.created_at DESC";
$registrationSessions = DB::fetchAll($sql, [$sectionId]);

require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
require_once __DIR__ . '/../../../views/dot-dang-ky-lop-hoc-phan/danh-sach-dot-dang-ky-lop-hoc-phan-giang-vien.php';
?>