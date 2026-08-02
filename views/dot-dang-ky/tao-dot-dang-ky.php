<?php 
require_once __DIR__.'/../layouts/header-lecturer.php'; 
$courseId = $courseId ?? "";
$sections = $sections ?? [];
$selectedSections = $selectedSections ?? [];
$topics = $topics ?? [];
$selectedTopics = $selectedTopics ?? [];
?>

<h2>Tạo đợt đăng ký</h2>
<?php if (!empty($errors)): ?>

    <div style="color:red; margin-bottom:20px">

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
            value="<?= htmlspecialchars($sessionName ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label>Mô tả</label><br>
        <textarea
            name="description"
        ><?= htmlspecialchars($description ?? '') ?></textarea>
    </div>

    <br>

    <div>
        <label>Thời gian bắt đầu</label><br>
        <input
            type="datetime-local"
            name="start_time"
            value="<?= htmlspecialchars($startTime ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label>Thời gian kết thúc</label><br>
        <input
            type="datetime-local"
            name="end_time"
            value="<?= htmlspecialchars($endTime ?? '') ?>"
        >
    </div>
    <div>
        <label>Hạn lập nhóm</label><br>

        <input
            type="datetime-local"
            name="group_deadline"
            value="<?= htmlspecialchars($groupDeadline ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label>Hạn chọn đề tài</label><br>

        <input
            type="datetime-local"
            name="topic_deadline"
            value="<?= htmlspecialchars($topicDeadline ?? '') ?>"
        >
    </div>

    <br>

    <br>

    <div>
        <label>Số nhóm tối đa</label><br>
        <input
            type="number"
            name="max_group"
            min="1"
            value="<?= htmlspecialchars($maxGroup ?? '') ?>"
        >
    </div>

    <br>

    <fieldset style="padding: 10px; border: 1px solid #ccc;">
        <legend><strong>Cấu hình đề tài & thành viên nhóm</strong></legend>
        <div>
            <label>Số thành viên tối thiểu/nhóm (min_member):</label>
            <input type="number" name="min_member" min="1" value="<?= htmlspecialchars($minMember) ?>" required>
        </div>
        <br>
        <div>
            <label>Số thành viên tối đa/nhóm (max_member):</label>
            <input type="number" name="max_member" min="1" value="<?= htmlspecialchars($maxMember) ?>" required>
        </div>
        <br>
        <div>
            <label>Số nhóm tối đa được chọn cùng 1 đề tài (max_group):</label>
            <input type="number" name="max_group_per_topic" min="1" value="<?= htmlspecialchars($maxGroupPerTopic) ?>" required>
        </div>
    </fieldset>

    <h3>Chọn lớp học phần</h3>
    

    <?php
     foreach($sections as $section): ?>

        <label>

            <input
                type="checkbox"
                name="sections[]"
                value="<?= $section['id'] ?>"
                <?= $section['is_used'] ? 'disabled' : '' ?>
            <?= in_array($section['id'], $selectedSections) ? 'checked' : '' ?>
            >

            <?= htmlspecialchars($section['section_code']) ?> - <?= htmlspecialchars($section['section_name']) ?>

            <?php if($section['is_used']) : ?>

                <span style="color:red">
                    (Đã thuộc đợt đăng ký khác)
                </span>

            <?php endif; ?>
        </label>

        <br>

    <?php endforeach; ?>

    <hr>
        <hr>

    <h3>Chọn đề tài áp dụng</h3>

    <?php foreach($topics as $topic): ?>

        <label>

            <input
                type="checkbox"
                name="topics[]"
                value="<?= $topic['id'] ?>"
                <?= in_array($topic['id'], $selectedTopics) ? 'checked' : '' ?>
            >

            <?= htmlspecialchars($topic['topic_name'] ?? "") ?>

        </label>

        <br>

    <?php endforeach; ?>

    <br>

    <button type="submit">
        Tạo đợt đăng ký
    </button>

</form>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>