<?php 
$sessionDetail = $sessionDetail ?? [];
$appliedSections = $appliedSections ?? [];
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/chi-tiet-dot-dang-ky-lop-hoc-phan.css">
<div class="registration-detail-container">
    <div class="page-header">
        <div>
            <h1 class="page-title"><?= htmlspecialchars($sessionDetail['registration_session_name']) ?></h1>
            <span style="color: #64748b; font-size: 0.875rem;">
                Học phần: <strong><?= htmlspecialchars($sessionDetail['course_code']) ?> - <?= htmlspecialchars($sessionDetail['course_name']) ?></strong>
            </span>
        </div>
        <div>
            <?php renderButton(
                'Quay lại danh sách',
                'button',
                '',
                false,
                '#475569',
                '#64748b',
                'onclick="window.location.href=\'/Project_cnw/danh-sach-dot-dang-ky-hoc-phan?course_id=' . $sessionDetail['course_id'] . '\'"'
            ) ?>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-header-title">
                Thông Tin Chung Đợt Đăng Ký
            </h2>
            
            <div class="card-header-actions">
            <?php 
            $startTime = !empty($sessionDetail['start_time']) ? strtotime($sessionDetail['start_time']) : 0;
            $endTime = !empty($sessionDetail['end_time']) ? strtotime($sessionDetail['end_time']) : 0;
            $now = time();

            $badgeStatus = 'gray';
            $statusText = 'Chưa xác định';
            if ($endTime > 0 && $endTime < $now) {
                $badgeStatus = 'red';
                $statusText = 'Đã kết thúc';
            } elseif ($startTime > 0 && $startTime > $now) {
                $badgeStatus = 'yellow';
                $statusText = 'Sắp diễn ra';
            } elseif ($startTime > 0 && ($endTime == 0 || $endTime >= $now)) {
                $badgeStatus = 'green';
                $statusText = 'Đang mở đăng ký';
            }
            ?>

            <span class="status-badge <?= $badgeStatus ?>">
                <span class="status-dot"></span>
                <?= $statusText ?>
            </span>

            <?php renderButton(
                'Sửa Thông Tin Hoặc Gắn Lớp Học Phần',
                'button',
                'padding: 6px 12px; font-size: 0.875rem;',
                false,
                '#b45309',
                '#d97706',
                'onclick="window.location.href=\'/Project_cnw/sua-dot-dang-ky?session_id=' . $sessionDetail['session_id'] . '\'"'
            ) ?>
        </div>
        </div>

        <div class="card-body-custom">
            <div class="info-grid">
                <?php renderProfileItem(
                    'Tên đợt đăng ký', 
                    htmlspecialchars($sessionDetail['registration_session_name']), 
                ) ?>
                
                <?php renderProfileItem(
                    'Thời gian bắt đầu', 
                    !empty($sessionDetail['start_time']) ? date('d/m/Y H:i', strtotime($sessionDetail['start_time'])) : '---',
                ) ?>
                
                <?php renderProfileItem(
                    'Thời gian kết thúc', 
                    !empty($sessionDetail['end_time']) ? date('d/m/Y H:i', strtotime($sessionDetail['end_time'])) : '---',
                ) ?>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-header-title">
                Danh Sách Lớp Học Phần Áp Dụng
            </h2>
            <?php renderBadge(count($appliedSections) . ' Lớp học phần', 'blue') ?>
        </div>
        
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Mã Lớp</th>
                        <th>Tên Lớp</th>
                        <th>Hạn Chọn Nhóm</th>
                        <th>Hạn Chọn Đề Tài</th>
                        <th class="text-center">Số nhóm tối đa</th>
                        <th class="text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($appliedSections)): ?>
                        <?php foreach ($appliedSections as $section): ?>
                            <tr>
                                <td>
                                    <span class="font-medium"><?= htmlspecialchars($section['section_code']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($section['section_name']) ?></td>
                                <td>
                                    <?php if (!empty($section['group_deadline'])): ?>
                                        <?= date('d/m/Y H:i', strtotime($section['group_deadline'])) ?>
                                    <?php else: ?>
                                        <span class="text-subdued">Chưa thiết lập</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($section['topic_deadline'])): ?>
                                        <?= date('d/m/Y H:i', strtotime($section['topic_deadline'])) ?>
                                    <?php else: ?>
                                        <span class="text-subdued">Chưa thiết lập</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php renderBadge($section['max_group'] ?? "Chưa thiết lập", 'gray') ?>
                                </td>
                                <td class="text-center">
                                    <?php renderButton(
                                        'Sửa',
                                        'button',
                                        'padding: 4px 10px; font-size: 0.8rem; height: 30px;',
                                        false,
                                        '#1d4ed8',
                                        '#2563eb',
                                        'onclick="window.location.href=\'/Project_cnw/sua-dot-dang-ky-lop-hoc-phan?section_id=' . $section['section_id'] . '&session_id=' . $section['session_id'] . '\'"'
                                    );?>

                                    <form method="POST" action="/Project_cnw/xoa-dot-dang-ky-lop-hoc-phan" style="margin: 0; display: inline-block;" onsubmit="return confirm('Xóa đợt đăng ký khỏi lớp học phần này?');">
                                        <input type="hidden" name="section_id" value="<?= htmlspecialchars($section['section_id'] ?? '') ?>">
                                        <input type="hidden" name="session_id" value="<?= htmlspecialchars($section['session_id'] ?? '') ?>">
                                        <?php renderButton(
                                            'Xóa',
                                            'submit',
                                            'padding: 4px 10px; font-size: 0.8rem; height: 30px;',
                                            false,
                                            '#b91c1c',
                                            '#dc2626'
                                        ) ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-subdued" style="padding: 2rem;">
                                Chưa có lớp học phần nào được áp dụng cho đợt đăng ký này.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>