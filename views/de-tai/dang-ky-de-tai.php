<?php
require_once __DIR__.'/../layouts/header-student.php';

$topics = $topics ?? [];
$groupId = $groupId ?? 0;

$sectionId = $sectionId ?? 0;
$sessionId = $sessionId ?? 0;

$myTopicId = $myTopicId ?? 0;
$canSubmit = $canSubmit ?? false;
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

            <th>Chọn</th>

            <th>Tên đề tài</th>

            <th>Mô tả</th>

            <th>Thành viên</th>

            <th>Số nhóm tối đa</th>

            <th>Các nhóm</th>
        </tr>

        <?php foreach($topics as $topic): ?>
            <?php 
                $isMyGroupTopic = ($myTopicId > 0 && $myTopicId == $topic['id']);
                $isFull = ($topic['max_group'] != null && $topic['current_groups'] >= $topic['max_group']);
                
                $rowBg = $isMyGroupTopic ? 'background-color: #e2f0d9;' : '';
            ?>
        <tr style="<?= $rowBg ?>">

            <td>

                <input
                    type="radio"
                    name="section_session_topic_id"
                    value="<?= $topic['id'] ?>"
                    <?= $isMyGroupTopic ? 'checked' : '' ?>
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

                <?=$topic['current_groups'] .  "/" . $topic['max_group'] ?? 'Không giới hạn' ?>

            </td>

            <td>
                <?php if (!empty($topic["registered_group_names"])) {
                    echo htmlspecialchars($topic['registered_group_names']);
                } else {
                    echo "Chưa có nhóm đăng ký";
                }?>
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

<a href="/Project_cnw/danh-sach-nhom?section_id=<?= $sectionId ?>&session_id=<?= $sessionId ?>">

Quay lại nhóm

</a>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>