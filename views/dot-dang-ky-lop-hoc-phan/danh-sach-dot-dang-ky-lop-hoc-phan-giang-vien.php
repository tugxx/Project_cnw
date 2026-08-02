<?php
$section = $section ?? [];
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-dot-dang-ky-lop-hoc-phan-giang-vien.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="session-container">
    <div class="session-actions-top">
        <div class="session-page-header" style="margin-bottom: 0;">
            <h1 class="session-page-title">
                <?= htmlspecialchars($section['section_code'] . ' - ' . $section['section_name']) ?>
            </h1>
            <p class="session-page-subtitle">
                Quản lý danh sách các đợt đăng ký nhóm & đề tài của lớp học phần
            </p>
        </div>
        <div>
            <a href="/Project_cnw/tao-dot-dang-ky?section_id=<?= $section['id'] ?>" class="btn-primary-custom">
                Thêm đợt đăng ký
            </a>
        </div>
    </div>

    <div class="session-list" style="margin-top: 20px;">
        <?php if (!empty($registrationSessions)): ?>
            <?php foreach ($registrationSessions as $index => $session): ?>
                <div class="session-item">
                    <div class="session-header">
                        <div class="session-title-group">
                            <a href="/Project_cnw/quan-ly-nhom?section_id=<?= $section['id'] ?>&session_id=<?= $session['session_id'] ?>" class="session-title">
                                Đợt <?= $index + 1 ?>: <?= htmlspecialchars($session['registration_session_name']) ?>
                            </a>
                            
                            <div class="session-info-pills">
                                <?php if (!empty($session['max_group'])): ?>
                                    <span class="pill-item"><i class="fa-solid fa-users me-1"></i> Tối đa: <strong><?= $session['max_group'] ?> nhóm</strong></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="action-icons">
                            <a href="/Project_cnw/quan-ly-de-tai?section_id=<?= $section['id'] ?>&session_id=<?= $session['session_id'] ?>?>" 
                               class="action-btn" title="Quản lý danh sách đề tài">
                                <i class="fa-solid fa-book-open"></i>
                            </a>
                            
                            <a href="/Project_cnw/sua-dot-dang-ky-lop-hoc-phan?section_id=<?= $section['id'] ?>&session_id=<?= $session['session_id'] ?>" 
                               class="action-btn" title="Cấu hình đợt đăng ký">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <a href="/Project_cnw/xoa-dot-dang-ky-lop-hoc-phan?section_id=<?= $section['id'] ?>&session_id=<?= $session['session_id'] ?>" 
                               class="action-btn delete" 
                               title="Gỡ đợt đăng ký khỏi lớp"
                               onclick="return confirm('Bạn có chắc chắn muốn gỡ đợt đăng ký này khỏi lớp học phần?');">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>
                    </div>

                    <div class="session-body" style="margin-top: 12px;">
                        <div class="session-meta-right" style="margin-bottom: 10px;">
                            <ul class="session-deadlines" style="display: flex; gap: 20px; list-style: none; padding: 0; margin: 0;">
                                <li>
                                    <strong><i class="fa-regular fa-clock"></i> Bắt đầu:</strong> 
                                    <?= !empty($session['start_time']) ? date('H:i d/m/Y', strtotime($session['start_time'])) : 'Chưa cập nhật' ?>
                                </li>
                                <li>
                                    <strong><i class="fa-regular fa-clock"></i> Kết thúc:</strong> 
                                    <?= !empty($session['end_time']) ? date('H:i d/m/Y', strtotime($session['end_time'])) : 'Chưa cập nhật' ?>
                                </li>
                                <?php if (!empty($session['group_deadline'])): ?>
                                    <li>
                                        <strong><i class="fa-solid fa-hourglass-half"></i> Hạn chót chọn nhóm:</strong> 
                                        <?= date('H:i d/m/Y', strtotime($session['group_deadline'])) ?>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($session['topic_deadline'])): ?>
                                    <li>
                                        <strong><i class="fa-solid fa-hourglass-end"></i> Hạn chót chọn đề tài:</strong> 
                                        <?= date('H:i d/m/Y', strtotime($session['topic_deadline'])) ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <?php if (!empty($session['description'])): ?>
                            <div class="session-description">
                                <?= nl2br(htmlspecialchars($session['description'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="session-empty">
                <p>Chưa có đợt đăng ký nào được mở cho lớp học phần này.</p>
            </div>
        <?php endif; ?>
    </div>
</div>