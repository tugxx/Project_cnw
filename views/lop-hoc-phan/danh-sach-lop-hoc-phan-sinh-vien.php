<?php
$sections = isset($sections) ? $sections : (!empty($sectionInfo) ? [$sectionInfo] : []);
$defaultCover = '/Project_cnw/assets/media/default-image-section.jpg';
?>
<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-lop-hoc-phan-sinh-vien.css">
<div class="lms-container">
    <div class="lms-page-header">
        <div class="lms-header-title">
            <h2>Danh sách lớp học phần</h2>
        </div>
    </div>

    <div class="lms-section-grid" id="sectionGrid">
        <?php if (!empty($sections)): 
            foreach ($sections as $section): 
                $coverImage = !empty($section['cover_image']) ? $section['cover_image'] : $defaultCover;
                $detailUrl = "/Project_cnw/danh-sach-dot-dang-ky-hoc-phan?section_id=" . urlencode($section['section_id']);?>
                
                <div class="section-card">
                    <a href="<?= $detailUrl ?>" class="section-card-cover-link">
                        <div class="section-card-cover">
                            <img 
                                src="<?= htmlspecialchars($coverImage) ?>" 
                                alt="<?= htmlspecialchars($section['section_name']) ?>" 
                                onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultCover) ?>';">
                        </div>
                    </a>

                    <div class="section-card-body">
                        <span class="section-card-code"><?= htmlspecialchars($section['section_code']) ?></span>
                        <h3 class="section-card-title">
                            <a href="<?= $detailUrl ?>" class="section-title-link">
                                <?= htmlspecialchars($section['section_name']) ?>
                            </a>
                        </h3>
                        
                        <p class="section-card-desc">
                            <?= htmlspecialchars($section['description'] ?: '') ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="lms-empty-state" style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 8px;">
                <p style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">Bạn chưa tham gia lớp học phần nào</p>
                <p style="font-size: 14px; color: #9ca3af; margin: 0;">Vui lòng liên hệ quản trị viên hoặc giảng viên nếu bạn chưa được thêm vào lớp.</p>
            </div>
        <?php endif; ?>
    </div>
</div>