<?php
$sections = $sections ?? [];
$courseId = $courseId ?? 0;
$selectedSections = $selectedSections ?? [];

require_once __DIR__.'/../layouts/header.php';
?>

<h2>Sửa đợt đăng ký</h2>

<?php if (!empty($errors)): ?>

    <div style="color:red;margin-bottom:20px;">

        <?php foreach($errors as $error): ?>

            <div><?= htmlspecialchars($error) ?></div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<form method="POST">

    <input
        type="hidden"
        name="courseId"
        value="<?= $courseId ?>"
    >

    <div>

        <label>Tên đợt đăng ký</label><br>

        <input
            type="text"
            name="session_name"
            value="<?= htmlspecialchars($sessionName) ?>"
        >

    </div>

    <br>

    <div>

        <label>Mô tả</label><br>

        <textarea
            name="description"
        ><?= htmlspecialchars($description) ?></textarea>

    </div>

    <br>

    <div>

        <label>Thời gian bắt đầu</label><br>

        <input
            type="datetime-local"
            name="start_time"
            value="<?= $startTime ?>"
        >

    </div>

    <br>

    <div>

        <label>Thời gian kết thúc</label><br>

        <input
            type="datetime-local"
            name="end_time"
            value="<?= $endTime ?>"
        >

    </div>

    <br>

    <div>

        <label>Hạn lập nhóm</label><br>

        <input
            type="datetime-local"
            name="group_deadline"
            value="<?= $groupDeadline ?>"
        >

    </div>

    <br>

    <div>

        <label>Hạn chọn đề tài</label><br>

        <input
            type="datetime-local"
            name="topic_deadline"
            value="<?= $topicDeadline ?>"
        >

    </div>

    <br>

    <div>

        <label>Số nhóm tối đa</label><br>

        <input
            type="number"
            min="1"
            name="max_groups"
            value="<?= $maxGroups ?>"
        >

    </div>

    <br>

    <h3>Chọn lớp học phần</h3>

    <?php foreach($sections as $section): ?>

        <?php

            $checked = in_array(
                $section['id'],
                $selectedSections
            );

            /*
             Nếu lớp đã thuộc đợt khác
             nhưng đang thuộc chính đợt này
             thì vẫn cho phép chọn.
            */

            $disabled = $section['is_used'] && !$checked;

        ?>

        <label>

            <input
                type="checkbox"
                name="sections[]"
                value="<?= $section['id'] ?>"
                <?= $checked ? 'checked' : '' ?>
                <?= $disabled ? 'disabled' : '' ?>
            >

            <?= htmlspecialchars($section['section_code']) ?>

            -

            <?= htmlspecialchars($section['section_name']) ?>

            <?php if($disabled): ?>

                <span style="color:red">
                    (Đã thuộc đợt đăng ký khác)
                </span>

            <?php endif; ?>

        </label>

        <br>

    <?php endforeach; ?>

    <hr>

    <button type="submit">
        Cập nhật đợt đăng ký
    </button>

</form>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>