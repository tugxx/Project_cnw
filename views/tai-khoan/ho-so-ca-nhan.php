<?php
$avatarSrc = !empty($user['avatar'])
    ? '/Project_cnw/storage/uploads/avatars/' . htmlspecialchars($user['avatar'])
    : '/Project_cnw/assets/media/default-avatar.jpg';

$dobFormatted = !empty($user['dob']) ? date('d/m/Y', strtotime($user['dob'])) : '---';
$roleName = $user['role'] ?? 'Sinh viên';
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/ho-so-ca-nhan.css">

<div class="profile-page">
    <div class="profile-banner"></div>

    <div class="profile-header">
        <div class="profile-avatar-wrapper">
            <img src="<?= $avatarSrc ?>" alt="Avatar" class="profile-avatar">
        </div>

        <div class="profile-header-main">
            <div class="profile-user-detail">
                <h1 class="profile-name">
                    <?= htmlspecialchars($user['full_name'] ?? 'Người dùng') ?>
                </h1>
                <div class="profile-badge">
                    <?php if (function_exists('renderBadge')) renderBadge($roleName, 'blue'); ?>
                </div>
            </div>

            <div class="profile-actions">
                <a href="chinh-sua-ho-so" class="btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa hồ sơ
                </a>
                <a href="doi-mat-khau" class="btn-secondary">
                    <i class="fa-solid fa-key"></i> Đổi mật khẩu
                </a>
            </div>
        </div>
    </div>

    <div class="profile-body">
        <div class="profile-section-header">
            <h3>Thông tin tài khoản</h3>
            <p>Thông tin cá nhân và định danh của bạn trên hệ thống</p>
        </div>

        <div class="profile-info-grid">
            <?php 
            if (function_exists('renderProfileItem')) {
                renderProfileItem('Địa chỉ Email', $user['email'] ?? '', 'fa-regular fa-envelope');
                renderProfileItem('Ngày sinh', $dobFormatted, 'fa-regular fa-calendar');
                renderProfileItem('Lớp', $user['class'] ?? '', 'fa-solid fa-graduation-cap');
            }
            ?>
        </div>
    </div>
</div>