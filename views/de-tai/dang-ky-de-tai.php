<?php
require_once __DIR__.'/../layouts/header.php';

$topics = $topics ?? [];
$groupId = $groupId ?? 0;
?>

<h2>Đăng ký đề tài</h2>

<?php if (!empty($errors)): ?>

<div style="color:red;margin-bottom:20px">

    <?php foreach($errors as $error): ?>

        <div><?= htmlspecialchars($error) ?></div>

    <?php endforeach; ?>

</div>

<?php endif; ?>

<?php if (empty($topics)): ?>

<p>Hiện chưa có đề tài nào được áp dụng cho lớp học phần này.</p>

<?php else: ?>

<form method="POST">

    <input
        type="hidden"
        name="groupId"
        value="<?= $groupId ?>"
    >

    <table
        border="1"
        cellpadding="8"
        cellspacing="0"
        width="100%"
    >

        <tr>

            <th></th>

            <th>Tên đề tài</th>

            <th>Mô tả</th>

            <th>Thành viên</th>

            <th>Số nhóm tối đa</th>

        </tr>

        <?php foreach($topics as $topic): ?>

        <tr>

            <td>

                <input
                    type="radio"
                    name="section_session_topic_id"
                    value="<?= $topic['id'] ?>"
                    required
                >

            </td>

            <td>

                <?= htmlspecialchars($topic['topic_name']) ?>

            </td>

            <td>

                <?= nl2br(htmlspecialchars($topic['description'])) ?>

            </td>

            <td>

                <?= $topic['min_member'] ?? '-' ?>

                -

                <?= $topic['max_member'] ?? '-' ?>

            </td>

            <td>

                <?= $topic['max_group'] ?? 'Không giới hạn' ?>

            </td>

        </tr>

        <?php endforeach; ?>

    </table>

    <br>

    <button type="submit">

        Đăng ký đề tài

    </button>

</form>

<?php endif; ?>

<br>

<a href="index.php?page=chi-tiet-nhom&id=<?= $groupId ?>">

Quay lại nhóm

</a>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>