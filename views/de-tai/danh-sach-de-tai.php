<?php 
$courseId = $_GET['courseId'] ?? '';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">

    <h2>Danh sách đề tài - <?= htmlspecialchars($course['course_name'] ?? '') ?></h2>

        <a href="index.php?page=tao-de-tai&courseId=<?= htmlspecialchars($courseId) ?>">
            Tạo đề tài
        </a>

</div>

<?php if(empty($topics)): ?>

<p>Chưa có đề tài.</p>

<?php else: ?>

<table
border="1"
cellpadding="8"
cellspacing="0"
width="100%"
>

<tr>

<th>Học phần</th>

<th>Tên đề tài</th>

<th>Người tạo</th>

<th>Trạng thái</th>

<th>Ngày tạo</th>

<th>Thao tác</th>

</tr>

<?php foreach($topics as $topic): ?>

<tr>

<td><?= htmlspecialchars($topic['course_name']) ?></td>

<td><?= htmlspecialchars($topic['topic_name']) ?></td>

<td><?= htmlspecialchars($topic['lecturer_name']) ?></td>

<td>
<?= $topic['status']=='public'
? 'Công khai'
: 'Riêng tư' ?>
</td>

<td><?= $topic['created_at'] ?></td>

<td>

<?php
$canEdit = false;

if($_SESSION['user']['role'] == 'admin'){
    $canEdit = true;
}

if(
    $_SESSION['user']['role'] == 'lecturer'
    && $topic['created_by'] == $_SESSION['user']['id']
){
    $canEdit = true;
}

if($canEdit):
?>

<a href="index.php?page=sua-de-tai&id=<?= $topic['id'] ?>">
Sửa
</a>

|

<a
href="index.php?page=xoa-de-tai&id=<?= $topic['id'] ?>"
onclick="return confirm('Bạn có chắc muốn xóa?')"
>
Xóa
</a>

<?php else: ?>

-

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>