<?php 
$courseId = $courseId ?? "";
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-dot-dang-ky-hoc-phan.css">
<div class="lms-container">
    <div class="lms-page-header">
        <div class="lms-header-tabs">
            <a href="/Project_cnw/danh-sach-lop-hoc-phan?course_id=<?= urlencode($courseId) ?>" class="lms-tab-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                Lớp học phần
            </a>
            <a href="/Project_cnw/danh-sach-dot-dang-ky-hoc-phan?course_id=<?= urlencode($courseId) ?>" class="lms-tab-item active">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Đợt đăng ký
            </a>
        </div>

        <div class="lms-header-top">
            <div class="lms-header-title">
                <h2><?= htmlspecialchars($course['course_name'] ?? 'Danh sách đợt đăng ký') ?></h2>
                <p>Mã học phần: <strong><?= htmlspecialchars($course['course_code'] ?? '') ?></strong></p>
            </div>
            <div class="lms-header-actions">
                <?php 
                $addSessionUrl = "/Project_cnw/tao-dot-dang-ky?course_id=" . urlencode($courseId);
                echo '<a href="' . $addSessionUrl . '" style="text-decoration:none;">';
                renderButton(
                    'Tạo đợt đăng ký',
                    'button',
                    'padding: 10px 18px; border-radius: 8px; font-weight: 500;',
                    false,
                    '#047857',
                    '#059669'
                );
                echo '</a>';
                ?>
            </div>
        </div>
    </div>

    <div class="lms-card">
        <div class="lms-table-responsive">
            <table class="lms-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Tên Đợt Đăng Ký</th>
                        <th>Thời Gian Mở</th>
                        <th>Thời Gian Kết Thúc</th>
                        <th>Các lớp Áp Dụng</th>
                        <th>Thời Gian Tạo</th>
                        <th class="text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($registrationSessions)): ?>
                        <?php $stt = 1; ?>
                        <?php foreach ($registrationSessions as $session): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $stt++ ?></td>
                                <td class="font-semibold text-dark">
                                    <?= htmlspecialchars($session['registration_session_name']) ?>
                                </td>
                                <td>
                                    <div class="lms-datetime">
                                        <span>
                                            <?= !empty($session['start_time']) ? date('d/m/Y - H:i', strtotime($session['start_time'])) : '--' ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="lms-datetime">
                                        <span>
                                            <?= !empty($session['end_time']) ? date('d/m/Y - H:i', strtotime($session['end_time'])) : '--' ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($session['section_list'])): ?>
                                        <?php 
                                        $sectionsList = explode('||', $session['section_list']); 
                                        ?>
                                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                            <?php foreach ($sectionsList as $sectionName): ?>
                                                <?php renderBadge(htmlspecialchars($sectionName), 'blue'); ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <?php renderBadge('Chưa gán lớp', 'gray'); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="lms-datetime text-muted">
                                        <span><?= !empty($session['created_at']) ? date('d/m/Y - H:i', strtotime($session['created_at'])) : '--' ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $detailUrl = "/Project_cnw/chi-tiet-dot-dang-ky-lop-hoc-phan?session_id=" . urlencode($session['session_id']);
                                    echo '<a href="' . $detailUrl . '" style="text-decoration:none;">';
                                    renderButton(
                                        'Xem chi tiết',
                                        'button',
                                        'padding: 6px 12px; font-size: 13px; border-radius: 6px;',
                                        false,
                                        '#1d4ed8',
                                        '#2563eb'
                                    );
                                    echo '</a>';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-empty">
                                Chưa có đợt đăng ký nào được tạo cho học phần này.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>