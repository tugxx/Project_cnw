<?php
$errors = $errors ?? [];
$userId = $userId ?? '';
$sectionId = $sectionId ?? '';
$sessionId = $sessionId ?? '';

$hasErrors = !empty($errors);
$inputError = $errors[0] ?? ''; 
?>
<link rel="stylesheet" href="/Project_cnw/assets/css/tao-nhom.css">

<div class="create-group-wrapper">
    <div class="create-group-container">
        <header class="page-header-block">
            <h1 class="page-title">Khởi tạo nhóm học tập</h1>
        </header>

        <?php if ($hasErrors): ?>
            <div class="system-alert-banner">
                <?php foreach ($errors as $err): ?>
                    <div class="system-alert-item">
                        <svg class="system-alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?= htmlspecialchars($err) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <main class="group-form-card">
            <section class="info-summary-panel">
                <div class="summary-grid">
                    <div>
                        <div class="info-item-label">Lớp Học Phần</div>
                        <div class="info-item-value"><?= htmlspecialchars(($sectionSession['section_code'] ?? '') . ' ' . ($sectionSession['section_name'] ?? '')) ?></div>
                    </div>
                    <div>
                        <div class="info-item-label">Đợt Đăng Ký</div>
                        <div class="info-item-value"><?= htmlspecialchars($sectionSession['registration_session_name'] ?? '') ?></div>
                    </div>
                    <div>
                        <div class="info-item-label">Người Tạo (Trưởng nhóm)</div>
                        <div class="info-item-value">
                            <?= htmlspecialchars($user['user_code'] ?? "") . " - " ?>
                            <?= htmlspecialchars($user['full_name'] ?? '') ?>
                        </div>
                    </div>
                    <div>
                        <div class="info-item-label">Hạn Lập Nhóm</div>
                        <div class="info-item-value">
                            <?php if (!empty($sectionSession['group_deadline'])): ?>
                                <?= date('H:i - d/m/Y', strtotime($sectionSession['group_deadline'])) ?>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-weight: normal;">Không giới hạn</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <form method="POST" action="" class="form-body" novalidate>
                <div class="form-group-field">
                    <label for="group_name" class="field-label">
                        Tên nhóm học tập <span class="required-star">*</span>
                    </label>
                    
                    <div class="input-container">
                        <input 
                            type="text" 
                            id="group_name" 
                            name="group_name" 
                            class="custom-input <?= $hasErrors ? 'input-has-error' : '' ?>"
                            placeholder="Nhóm 01 - Đồ án CNW, Nhóm Power..."
                            value="<?= htmlspecialchars($_POST['group_name'] ?? '') ?>"
                            autofocus>
                    </div>

                    <?php if ($hasErrors && !empty($inputError)): ?>
                        <div class="field-error-message">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span><?= htmlspecialchars($inputError) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="field-hint">
                            Tên nhóm không bắt buộc
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-action-bar">
                    <a href="/Project_cnw/danh-sach-nhom?section_id=<?= htmlspecialchars($sectionId) ?>&session_id=<?= htmlspecialchars($sessionId) ?>" class="btn-secondary">
                        Hủy bỏ
                    </a>
                    <button type="submit" class="btn-primary">
                        Tạo nhóm mới
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>