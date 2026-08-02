<?php require_once __DIR__.'/../layouts/header.php'; ?>
<?php
$topics = $topics ?? [];
$group = $group ?? [];
$errors = $errors ?? [];
?>

<h2>Đổi đề tài</h2>

<?php if(!empty($errors)): ?>

<div style="color:red">

<?php foreach($errors as $error): ?>

<div><?= htmlspecialchars($error) ?></div>

<?php endforeach; ?>

</div>

<?php endif; ?>

<form method="POST">

<input type="hidden" name="groupId" value="<?= htmlspecialchars($group['id'] ?? '') ?>">

<table border="1" cellpadding="8" cellspacing="0" width="100%">

<tr>

<th></th>

<th>Tên đề tài</th>

<th>Số nhóm</th>

</tr>

<?php foreach($topics as $topic): ?>

<tr>

<td>

<input
type="radio"
name="section_session_topic_id"
value="<?= $topic['id'] ?>"
<?= $topic['id']==$group['section_session_topic_id']
? 'checked'
: '' ?>
>

</td>

<td>

<?= htmlspecialchars($topic['topic_name']) ?>

</td>

<td>

<?= $topic['total_group'] ?>

/

<?= $topic['max_group'] ?>

</td>

</tr>

<?php endforeach; ?>

</table>

<br>

<button type="submit">

Lưu thay đổi

</button>

</form>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>