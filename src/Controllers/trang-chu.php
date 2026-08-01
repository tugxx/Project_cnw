<?php

if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

if ($_SESSION['user']['role'] == 'admin') {
    require_once __DIR__ . '/../../views/layouts/header-admin.php';
} elseif ($_SESSION['user']['role'] == 'lecturer') {
    require_once __DIR__ . '/../../views/layouts/header-lecturer.php';
} else {
    require_once __DIR__ . '/../../views/layouts/header-student.php';
}
require_once __DIR__ . '/../../views/trang-chu.php';
?>