<?php

?>

<h2>Tạo đề tài</h2>

<?php if(!empty($errors)): ?>

<div style="color:red">

<?php foreach($errors as $error): ?>

<div><?= htmlspecialchars($error) ?></div>

<?php endforeach; ?>

</div>

<?php endif; ?>

<form method="POST">

<input
type="hidden"
name="courseId"
value="<?= htmlspecialchars($courseId ?? '') ?>"
>

<div>

<label>Tên đề tài</label>

<br>

<input
type="text"
name="topic_name"
value="<?= htmlspecialchars($topicName ?? '') ?>"
style="width:400px"
>

</div>

<br>

<div>

<label>Mô tả</label>

<br>

<textarea
name="description"
rows="6"
cols="60"
><?= htmlspecialchars($description ?? '') ?></textarea>

</div>

<br>

<div>

<label>Trạng thái</label>

<br>

<select name="status">

<option value="public"
<?= ($status ?? '')=='public'?'selected':'' ?>>

Công khai

</option>

<option value="private"
<?= ($status ?? '')=='private'?'selected':'' ?>>

Riêng tư

</option>

</select>

</div>

<br>

<button type="submit">

Lưu đề tài

</button>

<a
href="index.php?page=chi-tiet-hoc-phan&id=<?= htmlspecialchars($courseId ?? '') ?>"
>

Quay lại

</a>

</form>

<?php
require_once __DIR__.'/../layouts/footer.php';
?>