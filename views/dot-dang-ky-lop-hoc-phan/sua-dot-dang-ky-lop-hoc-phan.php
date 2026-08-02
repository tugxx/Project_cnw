<?php
$sessionId = $sessionId ?? "";
$sessionData = $sessionData ?? [];
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/sua-dot-dang-ky-lop-hoc-phan.css">
<div class="config-page">
    <div class="top-nav">
        <a href="/Project_cnw/danh-sach-lop-hoc-phan?course_id=<?= htmlspecialchars($sessionData['course_id'] ?? "") ?>" class="btn-back">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Quay lại</span>
        </a>
    </div>

    <div class="page-header">
        <h1 class="main-title">
            <span><?= htmlspecialchars($section['section_code'] ?? '') ?> <?= htmlspecialchars($section['section_name'] ?? '') ?> - <?= htmlspecialchars($sessionData['registration_session_name'] ?? '') ?></span>
        </h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert-box alert-error">
            <div class="alert-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="alert-content">
                <span class="alert-title">Vui lòng kiểm tra lại thông tin:</span>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="block-card block-info">
        <div class="block-header">
            <div class="block-title">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2>THÔNG TIN ĐỢT ĐĂNG KÝ</h2>
            </div>
        </div>

        <p class="block-subtitle">(Thông tin này áp dụng chung cho tất cả các lớp thuộc đợt)</p>

        <div class="info-body">
            <?php 
                renderProfileItem(
                    'Tên đợt', 
                    $sessionData['registration_session_name'] ?? 'N/A', 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h10" />'
                );

                $formattedStart = !empty($sessionData['start_time']) ? date('d/m/Y - H:i', strtotime($sessionData['start_time'])) : 'N/A';
                renderProfileItem(
                    'Thời gian mở đợt', 
                    $formattedStart, 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
                );

                $formattedEnd = !empty($sessionData['end_time']) ? date('d/m/Y - H:i', strtotime($sessionData['end_time'])) : 'N/A';
                renderProfileItem(
                    'Thời gian đóng đợt', 
                    $formattedEnd, 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'
                );
            ?>
        </div>

        <div class="block-footer">
            <a href="/Project_cnw/chi-tiet-dot-dang-ky-lop-hoc-phan?session_id=<?= htmlspecialchars($sessionId) ?>" class="link-manage">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Chi tiết đợt đăng ký
            </a>
        </div>
    </div>

    <div class="block-card block-config">
        <div class="block-header">
            <div class="block-title">
                <h2>CẤU HÌNH CHO LỚP HỌC PHẦN: <?= htmlspecialchars($section['section_code'] ?? '') ?> - <?= htmlspecialchars($section['section_name'] ?? '') ?></h2>
            </div>
        </div>

        <form method="POST" action="" class="config-form">
            <div class="form-grid">
                <div class="form-item">
                    <?php 
                        $groupValue = $_POST['group_deadline'] ?? ($sessionData['group_deadline'] ? date('Y-m-d\TH:i', strtotime($sessionData['group_deadline'])) : '');
                        renderInput(
                            'group_deadline', 
                            '1. Hạn chót chọn nhóm (group_deadline)', 
                            'datetime-local', 
                            $groupValue, 
                            '', 
                            '', 
                            true, 
                            false, 
                            'input-wrapper'
                        ); 
                    ?>
                </div>

                <div class="form-item">
                    <?php 
                        $topicValue = $_POST['topic_deadline'] ?? ($sessionData['topic_deadline'] ? date('Y-m-d\TH:i', strtotime($sessionData['topic_deadline'])) : '');
                        renderInput(
                            'topic_deadline', 
                            '2. Hạn chót chọn đề tài (topic_deadline)', 
                            'datetime-local', 
                            $topicValue, 
                            '', 
                            '', 
                            true, 
                            false, 
                            'input-wrapper'
                        ); 
                    ?>
                </div>

                <div class="form-item">
                    <?php 
                        $maxGroupValue = $_POST['max_group'] ?? ($sessionData['max_group'] ?? 10);
                        renderInput(
                            'max_group', 
                            '3. Số nhóm tối đa (max_group)', 
                            'number', 
                            $maxGroupValue, 
                            '', 
                            'Nhập số nhóm...', 
                            true, 
                            false, 
                            'input-wrapper'
                        ); 
                    ?>
                </div>
            </div>

            <div class="form-actions-bar">
                <?php 
                    renderButton(
                        'Lưu Cấu Hình Lớp', 
                        'submit', 
                        'padding: 10px 24px; font-weight: 600;', 
                        false, 
                        '#1d4ed8', 
                        '#2563eb'
                    ); 
                ?>
            </div>
        </form>
    </div>
</div>