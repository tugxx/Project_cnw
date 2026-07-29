<?php

if (!defined('ALLOW_ACCESS')) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap');
    exit;
}

require_once __DIR__ . '/../../views/trang-chu.php';
require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/trang-chu.php';
require_once __DIR__ . '/../../views/layouts/footer.php';
?>