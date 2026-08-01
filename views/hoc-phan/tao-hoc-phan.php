<?php
$sql = "
    SELECT
        id,
        username,
        full_name
    FROM users
    WHERE role = 'lecturer'
      AND is_active = 1
    ORDER BY full_name ASC
";

$lecturers = DB::fetchAll($sql);
?>



<div style="max-width:900px;margin:40px auto;">

    <div style="
        background:#ffffff;
        border-radius:12px;
        padding:30px;
        box-shadow:0 4px 20px rgba(0,0,0,.08);
    ">

        <h2 style="
            margin-bottom:8px;
            color:#1f2937;
            font-size:28px;
            font-weight:bold;
        ">
            Khởi tạo học phần
        </h2>

        <p style="
            color:#6b7280;
            margin-bottom:30px;
        ">
            Tạo học phần mới và phân công giảng viên phụ trách.
        </p>

        <?php
        if (!empty($errors['system'])) {
            renderAlert($errors['system']);
        }

        if (!empty($success)) {
            renderAlert($success, 'success');
        }
        ?>

        <form method="POST">

            <?php
            renderInput(
                "course_name",
                "Tên học phần",
                "text",
                $course_name ?? "",
                $errors['course_name'] ?? "",
                null,
                true
            );
            ?>

            <div style="margin-bottom:20px;">

                <label style="
                    display:block;
                    font-size:14px;
                    font-weight:500;
                    margin-bottom:6px;
                    color:#374151;
                ">
                    Thông tin học phần
                </label>

                <textarea
                    name="description"
                    rows="5"
                    placeholder="Nhập mô tả học phần..."
                    style="
                        width:100%;
                        padding:12px;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                        font-size:14px;
                        resize:vertical;
                        box-sizing:border-box;
                    "
                ><?= htmlspecialchars($description ?? '') ?></textarea>

                <?php
                if (!empty($errors['description'])) {
                    echo "<p style='color:#dc2626;font-size:12px;margin-top:6px;'>"
                        . htmlspecialchars($errors['description']) .
                        "</p>";
                }
                ?>

            </div>

            <div style="margin-bottom:30px;">

                <label style="
                    display:block;
                    font-size:14px;
                    font-weight:500;
                    margin-bottom:10px;
                    color:#374151;
                ">
                    Giảng viên phụ trách
                </label>

                <div style="
                    border:1px solid #d1d5db;
                    border-radius:8px;
                    padding:15px;
                    max-height:250px;
                    overflow-y:auto;
                ">

                    <?php if (!empty($lecturers)) : ?>

                        <?php foreach ($lecturers as $lecturer) : ?>

                            <label style="
                                display:flex;
                                align-items:center;
                                gap:10px;
                                margin-bottom:12px;
                                cursor:pointer;
                            ">

                                <input
                                    type="checkbox"
                                    name="lecturers[]"
                                    value="<?= $lecturer['id'] ?>"

                                    <?= (
                                        !empty($_POST['lecturers']) &&
                                        in_array($lecturer['id'], $_POST['lecturers'])
                                    ) ? "checked" : "" ?>

                                >

                                <span>

                                    <strong>
                                        <?= htmlspecialchars($lecturer['full_name']) ?>
                                    </strong>

                                    <span style="color:#6b7280;">

                                        (<?= htmlspecialchars($lecturer['username']) ?>)

                                    </span>

                                </span>

                            </label>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <p style="color:#6b7280;">
                            Chưa có giảng viên nào.
                        </p>

                    <?php endif; ?>

                </div>

                <?php
                if (!empty($errors['lecturers'])) {
                    echo "<p style='color:#dc2626;font-size:12px;margin-top:6px;'>"
                        . htmlspecialchars($errors['lecturers']) .
                        "</p>";
                }
                ?>

            </div>

            <?php renderButton("Khởi tạo học phần"); ?>

        </form>

    </div>

</div>

<?php

require_once __DIR__ . '/../layouts/footer.php';
?>
