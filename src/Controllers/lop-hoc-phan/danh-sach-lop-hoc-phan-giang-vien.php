<?php
$userId = $_SESSION['user']['id'];
$courseId = (int)($_GET['courseId'] ?? $_POST['courseId'] ?? 0);

$sqlCheck = "SELECT u.id AS user_id, u.is_active, u.role,
                c.id AS course_id, c.course_code, c.course_name,
                cl.id AS is_assigned
            FROM `users` u
            LEFT JOIN `courses` c 
                ON c.id = ?
            LEFT JOIN `courses_lecturers` cl 
                ON cl.course_id = c.id AND cl.lecturer_id = u.id
            WHERE u.id = ?";

$checkData = DB::fetchOne($sqlCheck, [$courseId, $userId]);
if (!$checkData || !$checkData['is_active']) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
    exit;
}

if ($checkData['role'] !== 'lecturer') {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không có quyền thực hiện chức năng này.', 'dang-nhap', 'error');
    exit;
}

if (!$checkData['course_id']) {
    showPopUp('Học phần không tồn tại trong hệ thống.', 'dang-nhap', 'error');
    exit;
}

if (!$checkData['is_assigned']) {
    showPopUp('Giảng viên không phụ trách học phần này.', 'dang-nhap', 'error');
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

require_once __DIR__ . '/../../../views/layouts/header.php';
require_once __DIR__ . '/../../../views/lop-hoc-phan/danh-sach-lop-hoc-phan-giang-vien.php';
?>