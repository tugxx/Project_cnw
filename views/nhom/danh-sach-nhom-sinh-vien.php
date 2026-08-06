<?php
$groupDeadline = $sectionSession['group_deadline'] ?? null;
$isExpired = !empty($groupDeadline) && date('Y-m-d H:i:s') > $groupDeadline;
$maxGroup = $sectionSession['max_group'] ?? null;
$groups = $groups ?? [];
$sectionId = $sectionId ?? "";
$sessionId = $sessionId ?? "";
$myGroupId = $myGroupId ?? "";
$userId = $userId ?? "";

$canCreateGroup = empty($myGroupId) && !$isExpired;
if ($maxGroup !== null && count($groups) >= $maxGroup) {
    $canCreateGroup = false;
}
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-nhom-sinh-vien.css">
<div class="group-page-wrapper">
    <div class="page-header-bar">
        <div class="header-info">
            <h1 class="page-title">Danh Sách Nhóm Đợt Đăng Ký</h1>
            <p class="header-subtitle">Quản lý và đăng ký nhóm làm việc cho đợt học phần</p>

            <div class="header-meta">
                <?php if (!empty($maxGroup)): ?>
                    <span class="meta-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Tối đa: <strong><?= $maxGroup ?> nhóm</strong>
                    </span>
                <?php endif; ?>

                <span class="meta-item <?= $isExpired ? 'text-danger' : '' ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Hạn đăng ký: <strong><?= !empty($groupDeadline) ? date('H:i d/m/Y', strtotime($groupDeadline)) : 'Không giới hạn' ?></strong>
                </span>
            </div>
        </div>
        
        <div class="header-actions">
            <a href="<?= "/Project_cnw/danh-sach-de-tai-sinh-vien?section_id=" . urlencode($sectionId) . "&session_id=" . urlencode($sessionId) . "&group_id=" . urldecode($myGroupId)?>" class="btn-create-link">
                <?php renderButton('Xem đề tài', 'button', 'background-color: #2563eb; color: #fff; cursor: pointer;'); ?>
            </a>

            <?php if ($canCreateGroup): ?>
                <a href="<?= "/Project_cnw/tao-nhom?section_id=" . urlencode($sectionId) . "&session_id=" . urlencode($sessionId) ?>" class="btn-create-link">
                    <?php renderButton('Tạo nhóm mới', 'button', 'background-color: #2563eb; color: #fff; cursor: pointer;'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="form-error-box">
            <div class="error-box-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>Không thể thực hiện thao tác:</span>
            </div>
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="groups-section">
        <div class="groups-grid">
            <?php foreach ($groups as $group): ?>
                <?php 
                    $isMyGroup = ($myGroupId == $group['id']);
                    $canJoin = empty($myGroupId) && !$isExpired && $group['status'] === 'open';
                ?>

                <div class="group-card <?= $isMyGroup ? 'is-my-group' : '' ?>">
                    <?php if ($isMyGroup): ?>
                        <div class="my-group-ribbon">
                            Nhóm của bạn
                        </div>
                    <?php endif; ?>
                
                    <div class="card-header">
                        <div class="header-main">
                            <h3 class="group-title">
                                Nhóm <?= $group['stt'] ?>
                            </h3>

                            <?php if (!empty($group['group_name'])): ?>
                                <span class="group-subtitle" title="<?= htmlspecialchars($group['group_name']) ?>">
                                    <?= htmlspecialchars($group['group_name']) ?>
                                </span>
                            <?php else: ?>
                                <span class="group-subtitle empty"></span>
                            <?php endif; ?>
                        </div>
                        <div class="header-badge">
                            <?php 
                            if ($group['status'] === 'closed') {
                                    renderBadge('Đã khóa', 'red');
                                } else {
                                    renderBadge('Đang mở', 'green');
                                }
                            ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="members-progress">
                            <div class="progress-info">
                                <span>Thành viên</span>
                                <strong><?= $group['total_members'] ?></strong>
                            </div>
                        </div>

                        <ul class="member-list">
                            <?php foreach ($group['members'] as $member): ?>
                                <li class="member-item">
                                    <div class="avatar-placeholder">
                                        <?= mb_substr($member['full_name'], 0, 1) ?>
                                    </div>
                                    <div class="member-details">
                                        <span class="member-name"><?= htmlspecialchars($member['full_name']) ?></span>
                                        <span class="member-class"><?= htmlspecialchars($member['class'] ?? '-') ?></span>
                                    </div>
                                    <div class="member-role">
                                        <?php if ($member['role'] === 'leader'): ?>
                                            <?php renderBadge('Trưởng nhóm', 'blue'); ?>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="card-footer">
                        <?php if ($isMyGroup): ?>
                            <a href="<?= "/Project_cnw/chi-tiet-nhom-sinh-vien?group_id=" . urlencode($group['id']) ?>" style="width: 100%; text-decoration: none; display: flex; justify-content: flex-end;">
                                <?php 
                                    renderButton(
                                        'Chi tiết nhóm', 
                                        'button', 
                                        'background-color: #2563eb;', 
                                        false, 
                                        '#1d4ed8', 
                                        '#2563eb'
                                    );
                                ?>
                            </a>
                        <?php elseif ($canJoin): ?>
                            <form action="" method="POST" class="join-form" style="width: 100%; display: flex; justify-content: flex-end;">
                                <?php 
                                    renderButton(
                                        'Gia nhập nhóm', 
                                        'submit', 
                                        '', 
                                        false, 
                                        '#1d4ed8', 
                                        '#2563eb', 
                                        'name="join_group" value="' . $group['id'] . '"'
                                    ); 
                                ?>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>