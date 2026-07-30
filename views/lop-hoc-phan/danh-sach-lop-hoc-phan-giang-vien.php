<?php
$sections = $sections ?? [];
$coverImage = !empty($section['cover_image']) ? $section['cover_image'] : '/Project_cnw/assets/media/default-image-section.jpg';
$defaultCover = '/Project_cnw/assets/media/default-image-section.jpg';
$studentCount = (int)($section['total_students'] ?? 0);
?>
<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-lop-hoc-phan-giang-vien.css">

<div class="lms-container">
    <div class="lms-page-header">
        <div class="lms-header-title">
            <h2>Học phần: <?= htmlspecialchars($checkData['course_name'] ?? 'Danh sách lớp học phần') ?></h2>
            <p>Mã học phần: <strong><?= htmlspecialchars($checkData['course_code'] ?? 'N/A') ?></strong></p>
        </div>
        <div class="lms-header-actions">
            <?php 
            $addUrl = "index.php?page=tao-lop-hoc-phan&courseId=" . urlencode($courseId);
            echo '<a href="' . $addUrl . '" style="text-decoration:none;">';
            renderButton('Tạo lớp học phần', 'button', 'background-color: #2563eb; color: #fff; padding: 10px 18px; border-radius: 8px; font-weight: 500;');
            echo '</a>';
            ?>
        </div>
    </div>

    <div class="lms-control-bar">
        <div class="lms-search-box">
            <span class="lms-search-icon">🔍</span>
            <input type="text" id="searchInput" class="lms-search-input" placeholder="Tìm theo tên hoặc mã lớp..." onkeyup="filterSectionCards()">
        </div>
        <div>
            Tổng số: <?php renderBadge(count($sections) . ' lớp học phần', 'blue'); ?>
        </div>
    </div>

    <div class="lms-section-grid" id="sectionGrid">
        <?php if (!empty($sections)): 
            foreach ($sections as $section): 
                $detailUrl = "/Project_cnw/danh-sach-dot-dang-ky&sectionId=" . urlencode($section['section_id']);?>
                <div class="section-card" data-code="<?= strtolower(htmlspecialchars($section['section_code'])) ?>" data-name="<?= strtolower(htmlspecialchars($section['section_name'])) ?>">
                    <a href="<?= $detailUrl ?>" class="section-card-cover-link">
                        <div class="section-card-cover">
                            <img 
                                src="<?= htmlspecialchars($coverImage) ?>" 
                                alt="<?= htmlspecialchars($section['section_name']) ?>" 
                                onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultCover) ?>';">
                            <div class="section-card-badge">
                                <?php renderBadge($studentCount . ' Sinh viên', $studentCount > 0 ? 'green' : 'gray'); ?>
                            </div>
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
                            <?= htmlspecialchars($section['description'] ?: 'Chưa có mô tả cho lớp học phần này.') ?>
                        </p>

                        <div class="section-card-footer">
                            <span class="section-card-meta">
                                Ngày tạo: <?= date('d/m/Y', strtotime($section['created_at'])) ?>
                            </span>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="lms-empty-state">
                <p style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">Chưa có lớp học phần nào</p>
                <p style="font-size: 14px; color: #9ca3af; margin: 0;">Bấm vào nút "Tạo lớp học phần" để tạo lớp đầu tiên cho học phần này.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
function filterSectionCards() {
    const filter = document.getElementById('searchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('#sectionGrid .section-card');

    cards.forEach(card => {
        const code = card.getAttribute('data-code') || '';
        const name = card.getAttribute('data-name') || '';

        if (code.includes(filter) || name.includes(filter)) {
            card.style.display = "flex";
        } else {
            card.style.display = "none";
        }
    });
}
</script>