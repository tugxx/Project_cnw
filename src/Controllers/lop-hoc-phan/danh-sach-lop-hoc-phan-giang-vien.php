<?php
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

$courseId = $_GET['course_id'] ?? 0;
$sql = "SELECT `id`, `course_code`, `course_name` 
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

$sql = "SELECT s.id AS section_id, s.section_code, s.section_name, s.description, s.created_at, s.cover_image, 
            COUNT(ss.student_id) AS total_students
        FROM `sections` s
        LEFT JOIN `sections_students` ss 
            ON s.id = ss.section_id
        WHERE s.course_id = ?
        GROUP BY s.id
        ORDER BY s.id DESC";
$sections = DB::fetchAll($sql, [$courseId]);

if (!empty($sections)) {
    $sectionIds = array_column($sections, 'section_id');
    $placeholders = implode(',', array_fill(0, count($sectionIds), '?'));
    $sql = "SELECT ss.section_id, 
                ss.id AS sections_session_id, 
                rs.id AS session_id, 
                rs.registration_session_name AS session_name
            FROM `sections_sessions` ss
            JOIN `registration_sessions` rs ON ss.session_id = rs.id
            WHERE ss.section_id IN ($placeholders)";
    $allRegistrationSessions = DB::fetchAll($sql, $sectionIds);

    $sessionsBySection = [];
    foreach ($allRegistrationSessions as $session) {
        $sessionsBySection[$session['section_id']][] = $session;
    }

    foreach ($sections as &$section) {
        $sectionId = $section['section_id'];
        $section['sessions'] = $sessionsBySection[$sectionId] ?? [];
    }
    unset($section);
}

require_once __DIR__ . '/../../../views/layouts/header-lecturer.php';
require_once __DIR__ . '/../../../views/lop-hoc-phan/danh-sach-lop-hoc-phan-giang-vien.php';
?>