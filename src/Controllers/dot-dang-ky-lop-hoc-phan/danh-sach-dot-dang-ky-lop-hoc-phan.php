<?php


$role = $_SESSION['user']['role'] ?? '';
switch ($role) {
    case 'lecturer':
        require_once __DIR__ . '/danh-sach-dot-dang-ky-lop-hoc-phan-giang-vien.php';
        break;

    case 'student':
        require_once __DIR__ . '/danh-sach-dot-dang-ky-lop-hoc-phan-sinh-vien.php';
        break;

    default:
        showPopUp('Bạn không có quyền truy cập trang này.', 'trang-chu', 'error');
        exit;
}