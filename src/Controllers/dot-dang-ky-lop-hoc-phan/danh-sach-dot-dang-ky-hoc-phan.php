<?php
if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

$role = $_SESSION['user']['role'] ?? '';
switch ($role) {
    case 'lecturer':
        require_once __DIR__ . '/danh-sach-dot-dang-ky-hoc-phan-giang-vien.php';
        break;

    case 'student':
        require_once __DIR__ . '/danh-sach-dot-dang-ky-hoc-phan-sinh-vien.php';
        break;

    default:
        showPopUp('Bạn không có quyền truy cập trang này.', 'trang-chu', 'error');
        exit;
}