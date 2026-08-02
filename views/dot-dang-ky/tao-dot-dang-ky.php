<?php 
$sections = $sections ?? [];
$courseId = $courseId ?? 0;
require_once __DIR__.'/../layouts/header-lecturer.php'; 
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
            value="<?= htmlspecialchars($_POST['session_name'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label>Mô tả</label><br>
        <textarea
            name="description"
        ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <br>

    <div>
        <label>Thời gian bắt đầu</label><br>
        <input
            type="datetime-local"
            name="start_time"
            value="<?= $_POST['start_time'] ?? '' ?>"
        >
    </div>

    <br>

    <div>
        <label>Thời gian kết thúc</label><br>
        <input
            type="datetime-local"
            name="end_time"
            value="<?= $_POST['end_time'] ?? '' ?>"
        >
    </div>
    <div>
        <label>Hạn lập nhóm</label><br>

        <input
            type="datetime-local"
            name="group_deadline"
            value="<?= $_POST['group_deadline'] ?? '' ?>"
        >
    </div>

    <br>

    <div>
        <label>Hạn chọn đề tài</label><br>

        <input
            type="datetime-local"
            name="topic_deadline"
            value="<?= $_POST['topic_deadline'] ?? '' ?>"
        >
    </div>

    <br>

    <br>

    <div>
        <label>Số nhóm tối đa</label><br>
        <input
            type="number"
            name="max_groups"
            min="1"
            value="<?= $_POST['max_groups'] ?? '' ?>"
        >
    </div>

    <br>

    <h3>Chọn lớp học phần</h3>
    

    <?php
     foreach($sections as $section): ?>

        <label>

            <input
                type="checkbox"
                name="sections[]"
                value="<?= $section['id'] ?>"
                <?= $section['is_used'] ? 'disabled' : '' ?>
            >

            <?= htmlspecialchars($section['section_code']) ?>

                <?php if($section['is_used']) : ?>

                    <span style="color:red">
                        (Đã thuộc đợt đăng ký khác)
                    </span>

                <?php endif; ?>

            -

            <?= htmlspecialchars($section['section_name']) ?>

        </label>

        <br>

    <?php endforeach; ?>

    <hr>

    <button type="submit">
        Tạo đợt đăng ký
    </button>

</form>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>