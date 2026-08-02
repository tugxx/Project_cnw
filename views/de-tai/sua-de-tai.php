<?php
require_once __DIR__.'/../layouts/header.php';
?>

<h2>Sửa đề tài</h2>

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
name="id"
value="<?= htmlspecialchars($topicId ?? '') ?>"
>

<div>

<label>Tên đề tài</label><br>

<input
type="text"
name="topic_name"
value="<?= htmlspecialchars($topicName ?? '') ?>"
style="width:500px"
>

</div>

<br>

<div>

<label>Mô tả</label><br>

<textarea
name="description"
rows="6"
style="width:500px"
><?= htmlspecialchars($description ?? '') ?></textarea>

</div>

<br>

<div>

<label>Trạng thái</label><br>

<select name="status">

<option
value="public"
<?= ($status ?? '')=='public'?'selected':'' ?>
>

Public

</option>

<option
value="private"
<?= ($status ?? '')=='private'?'selected':'' ?>
>

Private

</option>

</select>

</div>

<br>

<button type="submit">

Lưu thay đổi

</button>

<a href="index.php?page=danh-sach-de-tai&courseId=<?= htmlspecialchars($courseId ?? '') ?>">

Hủy

</a>

</form>

<?php
require_once __DIR__.'/../layouts/footer.php';
?>