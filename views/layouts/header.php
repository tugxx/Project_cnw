<?php 
if (!defined('ALLOW_ACCESS')) { 
    header("HTTP/1.1 404 Not Found"); exit(); 
} 
?>
<style>
    .logout-btn {
        color: #e53e3e !important;
        font-weight: 600;
    }
    .logout-btn:hover {
        color: #c53030 !important;
    }
</style>
<header>
    <nav>
        <ul>
            <li><a href="index.php">Trang chủ</a></li>
            <?php if (isset($_SESSION['user'])): ?>
                <li>
                    <a href="/Project_cnw/index.php?page=dang-xuat" onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                        Đăng xuất
                    </a>
                </li>
            <?php else: ?>
                <li><a href="/Project_cnw/index.php?page=dang-nhap">Đăng nhập</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>