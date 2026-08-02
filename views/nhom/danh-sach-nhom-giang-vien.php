<?php
$groups = $groups ?? [];
$courseName = $sectionSession['course_name'] ?? '';
$sectionName = $sectionSession['section_name'] ?? '';
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/danh-sach-lop-hoc-phan-giang-vien.css">

<div class="lms-container">

    <div class="lms-page-header">

        <div class="lms-header-title">

            <h2>Danh sách nhóm</h2>

            <p>

                <strong><?= htmlspecialchars($courseName) ?></strong>

                -

                <?= htmlspecialchars($sectionName) ?>

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
                placeholder="Tìm nhóm..."
                onkeyup="filterGroupCards()"
            >

        </div>

        <div>

            <?php renderBadge(count($groups).' nhóm','blue'); ?>

        </div>

    </div>



    <div
        class="lms-section-grid"
        id="groupGrid"
    >

    <?php if(!empty($groups)): ?>

        <?php foreach($groups as $group): ?>

        <div
            class="section-card"
            data-name="<?= strtolower(htmlspecialchars($group['group_name'])) ?>"
        >

            <div class="section-card-body">

                <span class="section-card-code">

                    <?= htmlspecialchars($group['group_name']) ?>

                </span>

                <h3 class="section-card-title">

                    <?= htmlspecialchars($group['topic_name'] ?? 'Chưa đăng ký đề tài') ?>

                </h3>

                <p class="section-card-desc">

                    <strong>Trạng thái:</strong>

                    <?= $group['status']=='open'
                        ? 'Đang mở'
                        : 'Đã đóng'
                    ?>

                    <br><br>

                    <strong>Số thành viên:</strong>

                    <?= $group['total_members'] ?>

                </p>

                <div style="margin-top:10px">

                    <strong>Thành viên</strong>

                    <hr>

                    <?php foreach($group['members'] as $member): ?>

                        <div>

                            <?= htmlspecialchars($member['full_name']) ?>

                            <?php if($member['role']=='leader'): ?>

                                <b>(Trưởng nhóm)</b>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                </div>

                <div class="section-card-footer">

                    <span class="section-card-meta">

                        Ngày tạo

                        <?= date(
                            'd/m/Y',
                            strtotime($group['created_at'])
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

            Chưa có nhóm nào

            </p>

        </div>

    <?php endif; ?>

    </div>

</div>



<script>

function filterGroupCards(){

    const filter=document
        .getElementById("searchInput")
        .value
        .toLowerCase()
        .trim();

    const cards=document.querySelectorAll(
        "#groupGrid .section-card"
    );

    cards.forEach(card=>{

        const name=
            card.getAttribute("data-name");

        if(name.includes(filter)){

            card.style.display="flex";

        }

        else{

            card.style.display="none";

        }

    });

}

</script>