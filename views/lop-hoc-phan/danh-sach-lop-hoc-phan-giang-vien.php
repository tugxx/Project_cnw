<?php
$sections = $sections ?? [];
$defaultCover = '/Project_cnw/assets/media/default-image-section.jpg';
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-lop-hoc-phan-giang-vien.css">

<div class="lms-container">

    <div class="lms-page-header">

        <div class="lms-header-title">

            <h2>Danh sách lớp học phần</h2>

            <p>
                Giảng viên phụ trách
            </p>

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
                onkeyup="filterSectionCards()"
            >

        </div>

        <div>
            <span class="badge" style="background-color: blue; color: white; padding: 5px 10px; border-radius: 12px; font-size: 14px;">
                <?= count($sections) ?> lớp học phần
            </span>
        </div>

    </div>

    <div
        class="lms-section-grid"
        id="sectionGrid"
    >

    <?php if(!empty($sections)): ?>

        <?php foreach($sections as $section): ?>

            <?php

            $coverImage = !empty($section['cover_image'])
                ? $section['cover_image']
                : $defaultCover;

            $detailUrl = "index.php?page=danh-sach-nhom&section_id=" . 
                htmlspecialchars($section['section_id'] ?? 0) . "&session_id=" . 
                htmlspecialchars($section['session_id'] ?? 0);

            ?>

            <div
                class="section-card"
                data-code="<?= strtolower($section['section_code']) ?>"
                data-name="<?= strtolower($section['section_name']) ?>"
            >

                <a
                    href="<?= $detailUrl ?>"
                    class="section-card-cover-link"
                >

                    <div class="section-card-cover">

                        <img
                            src="<?= htmlspecialchars($coverImage) ?>"
                            alt="<?= htmlspecialchars($section['section_name']) ?>"
                            onerror="this.src='<?= $defaultCover ?>'"
                        >

                    </div>

                </a>

                <div class="section-card-body">

                    <span class="section-card-code">

                        <?= htmlspecialchars($section['section_code']) ?>

                    </span>

                    <h3 class="section-card-title">

                        <a
                            href="<?= $detailUrl ?>"
                            class="section-title-link"
                        >

                            <?= htmlspecialchars($section['section_name']) ?>

                        </a>

                    </h3>

                    <p class="section-card-desc">

                        <?= htmlspecialchars(
                            $section['description']
                            ?: 'Chưa có mô tả.'
                        ) ?>

                    </p>

                    <div class="section-card-footer">

                        <span class="section-card-meta">

                            Ngày tạo

                            <?= date(
                                'd/m/Y',
                                strtotime($section['created_at'])
                            ) ?>

                        </span>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="lms-empty-state">

            <p
                style="
                font-size:18px;
                font-weight:600;
                "
            >

                Chưa có lớp học phần nào

            </p>

            <p
                style="
                color:#6b7280;
                "
            >

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

        if(
            code.includes(filter)
            ||
            name.includes(filter)
        ){

            card.style.display="flex";

        }

        else{

            card.style.display="none";

        }

    });

}

</script>