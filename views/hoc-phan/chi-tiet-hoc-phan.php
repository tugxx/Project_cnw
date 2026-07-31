<?php 
require_once __DIR__.'/../layouts/header.php'; 
?>


<h2>
    <?= htmlspecialchars($course['course_name'] ?? '') ?>
</h2>

<p>
    <b>Mã học phần:</b>
    <?= htmlspecialchars($course['course_code'] ?? '') ?>
</p>

<p>
    <?= nl2br(htmlspecialchars($course['description'] ?? '')) ?>
</p>

<hr>

<h3>Quản lý học phần</h3>

<div style="display:flex;gap:15px;flex-wrap:wrap;">

    <a
        href="/Project_cnw/tao-lop-hoc-phan?courseId=<?= htmlspecialchars($course['id'] ?? '') ?>"
        class="btn">
        Tạo lớp học phần
    </a>

    <a
        href="/Project_cnw/tao-de-tai?courseId=<?= htmlspecialchars($course['id'] ?? '') ?>"
        class="btn">
        Tạo đề tài
    </a>

    <a
        href="/Project_cnw/tao-dot-dang-ky?courseId=<?= htmlspecialchars($course['id'] ?? '') ?>"
        class="btn">
        Tạo đợt đăng ký
    </a>

</div>

<h2><?= htmlspecialchars($course['course_name'] ?? '') ?></h2>

<hr>

<h2>Danh sách đợt đăng ký</h2>

<?php if(empty($registrationSessions)): ?>

    <p>Học phần này chưa có đợt đăng ký nào.</p>

<?php else: ?>

<table border="1" cellpadding="8" cellspacing="0" width="100%">

    <tr>
        <th>Tên đợt</th>
        <th>Áp dụng cho</th>
        <th>Bắt đầu</th>
        <th>Kết thúc</th>
        <th>Số nhóm</th>
        <th>Thao tác</th>
    </tr>

<?php foreach($registrationSessions as $session): ?>

<tr>

    <td><?= htmlspecialchars($session['session_name']) ?></td>
    
    <td><?= $session['sections'] ?></td>
    
    <td><?= $session['start_time'] ?></td>

    <td><?= $session['end_time'] ?></td>

    <td><?= $session['max_groups'] ?></td>

    <td>

        <a href="index.php?page=chi-tiet-dot-dang-ky&id=<?= $session['id'] ?>">
            Xem
        </a>

        |

        <a href="index.php?page=sua-dot-dang-ky&id=<?= $session['id'] ?>">
            Sửa
        </a>

        |

        <a
            href="index.php?page=xoa-dot-dang-ky&id=<?= $session['id'] ?>"
            onclick="return confirm('Bạn có chắc muốn xóa?')"
        >
            Xóa
        </a>

    </td>

</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>