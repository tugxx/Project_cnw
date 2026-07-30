<?php
$userId = $_SESSION['user']['id'];
$sql = "SELECT `id`, `is_active`, role 
        FROM ``users`` 
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
$sql = "SELECT `id`, `course_code`, `course_name` 
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

$sql = "SELECT s.id AS section_id, s.section_code, s.section_name, s.description, s.created_at, s.cover_image, 
                    COUNT(ss.student_id) AS total_students
                FROM `sections` s
                LEFT JOIN `sections_students` ss 
                    ON s.id = ss.section_id
                WHERE s.course_id = ?
                GROUP BY s.id, s.section_code, s.section_name, s.description, s.created_at, s.cover_image
                ORDER BY s.id DESC";
$sections = DB::fetchAll($sql, [$courseId]);

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/lop-hoc-phan/danh-sach-lop-hoc-phan-giang-vien.php';
?>