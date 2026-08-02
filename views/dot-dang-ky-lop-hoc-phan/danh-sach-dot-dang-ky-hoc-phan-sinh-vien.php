<?php
$section = $section ?? [];
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-dot-dang-ky-hoc-phan-sinh-vien.css">
<div class="session-container">
    <div class="session-page-header">
        <h1 class="session-page-title">
            <?= htmlspecialchars($section['section_code'] . ' - ' . $section['section_name']) ?>
        </h1>
        <p class="session-page-subtitle">
            Danh sách các đợt đăng ký nhóm & đề tài thuộc lớp học phần
        </p>
    </div>

    <div class="session-list">
        <?php if (!empty($registrationSessions)): ?>
            <?php foreach ($registrationSessions as $index => $session): ?>
                <div class="session-item">
                    <div class="session-header">
                        <div class="session-title-group">
                            <a href="/Project_cnw/danh-sach-nhom?section_id=<?= $section['id'] ?>&session_id=<?= $session['session_id'] ?>" class="session-title">
                                Đợt <?= $index + 1 ?>: <?= htmlspecialchars($session['registration_session_name']) ?>
                            </a>
                        </div>

                        <div class="session-meta-right">
                            <ul class="session-deadlines">
                                <li>
                                    <strong>Bắt đầu:</strong> 
                                    <?= !empty($session['start_time']) ? date('H:i d/m/Y', strtotime($session['start_time'])) : 'Chưa cập nhật' ?>
                                </li>
                                <li>
                                    <strong>Kết thúc:</strong> 
                                    <?= !empty($session['end_time']) ? date('H:i d/m/Y', strtotime($session['end_time'])) : 'Chưa cập nhật' ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <?php if (!empty($session['description'])): ?>
                        <div class="session-body">
                            <div class="session-description">
                                <?= nl2br(htmlspecialchars($session['description'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="session-empty">
                <p>Hiện chưa có đợt đăng ký nào được mở cho lớp học phần này.</p>
            </div>
        <?php endif; ?>
    </div>
</div>