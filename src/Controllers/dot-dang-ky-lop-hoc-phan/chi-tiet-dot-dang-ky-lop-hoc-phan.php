<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: dang-nhap');
    exit;
}

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

$sessionId = $_GET['session_id'] ?? "";
$sql = "SELECT rs.id AS session_id, 
            rs.course_id, rs.registration_session_name, rs.start_time, rs.end_time, rs.created_at,
            c.course_code, c.course_name
        FROM `registration_sessions` rs
        JOIN `courses` c 
            ON rs.course_id = c.id
        WHERE rs.id = ?";
$sessionDetail = DB::fetchOne($sql, [$sessionId]);
if (!$sessionDetail) {
    showPopUp('Đợt đăng ký không tồn tại trong hệ thống.', 'danh-sach-hoc-phan', 'error');
    exit;
}

$courseId = $sessionDetail['course_id'];
$sql = "SELECT `id` 
        FROM `courses_lecturers` 
        WHERE `course_id` = ? AND `lecturer_id` = ?";
$isAssigned = DB::fetchOne($sql, [$courseId, $userId]);
if (!$isAssigned) {
    showPopUp('Bạn không có quyền quản lý đợt đăng ký của học phần này.', 'danh-sach-hoc-phan', 'error');
    exit;
}


$sql = "SELECT ss.id AS section_session_id,
            ss.section_id, ss.session_id, ss.group_deadline, ss.topic_deadline, ss.max_group,
            s.section_code, s.section_name
        FROM `sections_sessions` ss
        JOIN `sections` s 
            ON ss.section_id = s.id
        WHERE ss.session_id = ?
        ORDER BY s.section_code ASC";
$appliedSections = DB::fetchAll($sql, [$sessionId]);


require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
require_once __DIR__ . '/../../../views/dot-dang-ky-lop-hoc-phan/chi-tiet-dot-dang-ky-lop-hoc-phan.php';
?>