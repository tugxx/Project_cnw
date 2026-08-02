<?php
$sections = $sections ?? [];
$defaultCover = '/Project_cnw/assets/media/default-image-section.jpg';
$courseId = $courseId ?? "";
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-lop-hoc-phan-giang-vien.css">
<div class="lms-container">
    <div class="lms-page-header">
        <div class="lms-header-tabs">
            <a href="/Project_cnw/danh-sach-lop-hoc-phan?course_id=<?= urlencode($courseId) ?>" class="lms-tab-item active">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                Lớp học phần
            </a>
            <a href="/Project_cnw/danh-sach-dot-dang-ky-hoc-phan?course_id=<?= urlencode($courseId) ?>" class="lms-tab-item">
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
                <h2><?= htmlspecialchars($course['course_name'] ?? 'Danh sách lớp học phần') ?></h2>
                <p>Mã học phần: <strong><?= htmlspecialchars($course['course_code'] ?? '') ?></strong></p>
            </div>
            <div class="lms-header-actions">
                <?php 
                $addSectionUrl = "/Project_cnw/tao-lop-hoc-phan?course_id=" . urlencode($courseId);
                echo '<a href="' . $addSectionUrl . '" style="text-decoration:none;">';
                renderButton('Tạo lớp học phần', 'button', 'background-color: #2563eb; color: #fff; padding: 10px 18px; border-radius: 8px; font-weight: 500;');
                echo '</a>';
                ?>
            </div>
        </div>
    </div>

    <div class="lms-control-bar">
        <div class="lms-search-box">
            <span class="lms-search-icon">🔍</span>
            <input
                type="text"
                id="searchInput"
                class="lms-search-input"
                placeholder="Tìm lớp học phần..."
                onkeyup="filterSectionCards()">
        </div>
        <div>
            <span class="badge" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 12px; font-size: 13px; font-weight: 500;">
                <?= count($sections) ?> lớp học phần
            </span>
        </div>
    </div>

    <div class="lms-section-grid" id="sectionGrid">
        <?php if (!empty($sections)): 
            foreach ($sections as $section): 
                $detailUrl = "/Project_cnw/danh-sach-dot-dang-ky-lop-hoc-phan?section_id=" . urlencode($section['section_id']);
                $editUrl = "/Project_cnw/sua-lop-hoc-phan?section_id=" . urlencode($section['section_id']);
                $coverImage = !empty($section['cover_image']) ? $section['cover_image'] : $defaultCover;
                $linkedSessions = $section['sessions'] ?? [];
                $sessionCount = count($linkedSessions);?>

                <div class="section-card" data-code="<?= strtolower(htmlspecialchars($section['section_code'])) ?>" data-name="<?= strtolower(htmlspecialchars($section['section_name'])) ?>">
                    <div class="section-card-cover-wrapper" style="position: relative;">
                        <a href="<?= $detailUrl ?>" class="section-card-cover-link">
                            <div class="section-card-cover">
                                <img 
                                    src="<?= htmlspecialchars($coverImage) ?>" 
                                    alt="<?= htmlspecialchars($section['section_name']) ?>" 
                                    onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultCover) ?>';">
                                <div class="section-card-badge">
                                    <?php renderBadge($section['total_students'] . ' Sinh viên', $section['total_students'] > 0 ? 'green' : 'gray'); ?>
                                </div>
                            </div>
                        </a>
                        <a href="<?= $editUrl ?>" class="btn-edit-section" title="Chỉnh sửa lớp học phần">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="section-card-body">
                        <span class="section-card-code"><?= htmlspecialchars($section['section_code']) ?></span>
                        <h3 class="section-card-title">
                            <a href="<?= $detailUrl ?>" class="section-title-link">
                                <?= htmlspecialchars($section['section_name']) ?>
                            </a>
                        </h3>
                        
                        <div class="section-card-session-box">
                            <?php if ($sessionCount === 0): ?>
                                <span class="session-badge empty">
                                    Chưa có đợt ĐK
                                </span>
                            <?php elseif ($sessionCount === 1): ?>
                                <?php 
                                    $section_session = $linkedSessions[0]; 
                                    $configUrl = "/Project_cnw/sua-dot-dang-ky-lop-hoc-phan?section_id=" . $section_session['section_id'] . "&session_id=" . $section_session['session_id'];
                                ?>
                                <a href="<?= $configUrl ?>" class="session-badge single" title="Bấm để xem/chỉnh sửa config lớp trong đợt này">
                                    <?= htmlspecialchars($section_session['session_name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="session-badge multiple" onclick="toggleSessionDropdown(event, 'dropdown-<?= $section['section_id'] ?>')">
                                    <span><?= $sessionCount ?> Đợt đăng ký</span>
                                    <svg class="dropdown-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                </span>
                                <div class="session-dropdown-menu" id="dropdown-<?= $section['section_id'] ?>">
                                    <?php foreach ($linkedSessions as $section_session): ?>
                                        <a href="/Project_cnw/sua-dot-dang-ky-lop-hoc-phan?section_id=<?= $section_session['section_id'] ?>&session_id=<?= $section_session['session_id'] ?>" class="session-dropdown-item">
                                            <?= htmlspecialchars($section_session['session_name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <p class="section-card-desc">
                            <?= htmlspecialchars($section['description'] ?: 'Chưa có mô tả cho lớp học phần này.') ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="lms-empty-state">
                <p style="font-size:18px; font-weight:600;">
                    Chưa có lớp học phần nào
                </p>
                <p style="color:#6b7280;">
                    Bạn chưa được phân công quản lý lớp học phần nào.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>


<script>
function filterSectionCards(){
    const filter=document
        .getElementById("searchInput")
        .value
        .toLowerCase()
        .trim();

    const cards=document.querySelectorAll(
        "#sectionGrid .section-card"
    );

    cards.forEach(card=>{
        const code=card.dataset.code;
        const name=card.dataset.name;
        if(code.includes(filter) || name.includes(filter)){
            card.style.display="flex";
        } else {
            card.style.display="none";
        }
    });
}


function toggleSessionDropdown(event, dropdownId) {
    event.stopPropagation();
    
    document.querySelectorAll('.session-dropdown-menu').forEach(menu => {
        if (menu.id !== dropdownId) {
            menu.classList.remove('show');
        }
    });

    const targetDropdown = document.getElementById(dropdownId);
    if (targetDropdown) {
        targetDropdown.classList.toggle('show');
    }
}

document.addEventListener('click', function () {
    document.querySelectorAll('.session-dropdown-menu').forEach(menu => {
        menu.classList.remove('show');
    });
});
</script>