<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project CNW</title>
    <link rel="stylesheet" href="/Project_cnw/assets/css/header.css">
</head>
<body>
<div id="appLayout" class="app-layout">
    <header class="site-header">
        <div class="site-header__inner">
            <div class="site-header__left">
                <button class="menu-toggle" id="menuToggleBtn" aria-label="Toggle Navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <a href="/Project_cnw" class="site-header__brand">Project CNW</a> 
            </div>

            <div class="site-header__right">
                <div class="user-menu">
                    <button class="user-menu__trigger">
                        <img 
                            src="<?= htmlspecialchars($_SESSION['user']['avatar'] ?? "") ?>" 
                            alt="User Avatar" 
                            class="user-menu__avatar"
                        />
                        <span class="user-menu__name"> 
                            <?= htmlspecialchars(($_SESSION['user']['user_code'] ?? '') . ' ' . ($_SESSION['user']['full_name'] ?? '')) ?>
                        </span>
                        <svg class="user-menu__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <ul class="user-menu__dropdown">
                        <li><a href="/Project_cnw/ho-so-ca-nhan">Hồ sơ cá nhân</a></li>
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

    <script>
    if (localStorage.getItem('sidebarState') === 'open') {
        document.documentElement.classList.add('sidebar-open');
    }
    </script>

    <nav class="sidebar-nav" id="sidebarNav">
        <ul class="sidebar-nav__list">
            <li><a href="/Project_cnw">Trang chủ</a></li>
            <li><a href="/Project_cnw/danh-sach-hoc-phan">Học phần</a></li>
            <!-- <li><a href="/Project_cnw/danh-sach-nhom?section_id=2&session_id=1">Danh sách nhóm</a></li> -->
        </ul>
    </nav>

    <main class="site-main">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userMenu = document.querySelector('.user-menu');
    const userTrigger = document.querySelector('.user-menu__trigger');

    if (userTrigger) {
        userTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            userMenu.classList.toggle('active');
        });

        document.addEventListener('click', function (e) {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const menuToggleBtn = document.getElementById('menuToggleBtn');
    const sidebarCloseBtn = document.querySelector('.sidebar-close');
    const sidebarNav    = document.getElementById('sidebarNav');
    const appLayout     = document.getElementById('appLayout');

    const isSidebarOpen = localStorage.getItem('sidebarState') === 'open';
    if (isSidebarOpen) {
        sidebarNav.classList.add('active');
        if (appLayout) appLayout.classList.add('sidebar-open');
    } 

    function openSidebar(saveState = true) {
        sidebarNav.classList.add('active');
        document.documentElement.classList.add('sidebar-open');
        if (appLayout) appLayout.classList.add('sidebar-open');
        if (saveState) localStorage.setItem('sidebarState', 'open');
    }

    function closeSidebar(saveState = true) {
        sidebarNav.classList.remove('active');
        document.documentElement.classList.remove('sidebar-open');
        if (appLayout) appLayout.classList.remove('sidebar-open'); 
        if (saveState) localStorage.setItem('sidebarState', 'closed');
    }

    function toggleSidebar() {
        if (sidebarNav.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    if (menuToggleBtn) menuToggleBtn.addEventListener('click', toggleSidebar);
    if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
});
</script>

