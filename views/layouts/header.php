<?php 
if (!defined('ALLOW_ACCESS')) { 
    header("HTTP/1.1 404 Not Found"); 
    exit(); 
} 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project CNW</title>
    <link rel="stylesheet" href="/Project_cnw/assets/css/header.css">
</head>
<body>

<header class="site-header">
    <div class="site-header__inner">
        <div class="site-header__left">
            <a href="/Project_cnw/index.php" class="site-header__brand">Project CNW</a>
            <nav class="site-header__nav">
                <ul>
                    <li><a href="/Project_cnw">Trang chủ</a></li>
                </ul>
            </nav>
        </div>

        <div class="site-header__right">
            <div class="user-menu">
                <button class="user-menu__trigger">
                    <span><?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?></span>
                    <small>▾</small>
                </button>

                <ul class="user-menu__dropdown">
                    <li><a href="/Project_cnw/ho-so-ca-nhan">Trang cá nhân</a></li>
                    <li class="divider"></li>
                    <li>
                        <a href="/Project_cnw/dang-xuat" 
                            class="logout-link"
                            onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                            Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<main class="site-main">