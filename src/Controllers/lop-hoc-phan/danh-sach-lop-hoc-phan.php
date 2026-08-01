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
        require_once __DIR__ . '/danh-sach-lop-hoc-phan-giang-vien.php';
        break;

    case 'student':
        require_once __DIR__ . '/danh-sach-lop-hoc-phan-sinh-vien.php';
        break;

    default:
        destroyUserSession();
        showPopUp('Bạn không có quyền truy cập trang này.', 'dang-nhap', 'error');
        exit;
}