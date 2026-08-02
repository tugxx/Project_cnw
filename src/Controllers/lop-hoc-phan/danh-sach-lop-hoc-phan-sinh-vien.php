<?php
$userId = $_SESSION['user']['id'];
$sql = "SELECT `id`, `is_active`, `role` 
        FROM `users` 
        WHERE `id` = ?";
$userData = DB::fetchOne($sql, [$userId]);

if (!$userData || !$userData['is_active']) {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không tồn tại hoặc đã bị khoá.', 'dang-nhap', 'error');
    exit;
}

if ($userData['role'] !== 'student') {
    destroyUserSession();
    showPopUp('Tài khoản của bạn không có quyền thực hiện chức năng này.', 'dang-nhap', 'error');
    exit;
}

$sql = "SELECT s.id AS section_id, 
            s.section_code, 
            s.section_name, 
            s.description
        FROM `sections_students` ss
        JOIN `sections` s 
            ON s.id = ss.section_id
        WHERE ss.student_id = ?
        ORDER BY s.id DESC";
$sections = DB::fetchAll($sql, [$userId]);

require_once __DIR__ . '/../../../views/layouts/header-student.php';
require_once __DIR__ . '/../../../views/lop-hoc-phan/danh-sach-lop-hoc-phan-sinh-vien.php';
?>