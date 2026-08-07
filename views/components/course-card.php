<?php
/**
 * @param array $course
 */
function renderCourseCard($course, $role)
{
?>
<div style="
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:20px;
    box-shadow:0 2px 10px rgba(0,0,0,.05);
">

    <h3 style="margin:0;color:#2563eb;">
        <?= htmlspecialchars($course['course_name']) ?>
    </h3>

    <p style="margin-top:8px;color:#6b7280;">
        <strong>Mã học phần:</strong>
        <?= htmlspecialchars($course['course_code']) ?>
    </p>

    <p style="margin-top:8px;">
        <?= nl2br(htmlspecialchars($course['description'])) ?>
    </p>

    <div style="margin-top:15px;">
        <?php 
        if ($role == "lecturer"):?>
        <a href="/Project_cnw/danh-sach-lop-hoc-phan?course_id=<?= $course['id'] ?>"
            style="
                padding:8px 14px;
                background:#2563eb;
                color:white;
                text-decoration:none;
                border-radius:6px;">
            Chi tiết
        </a>
        <?php elseif ($role == "admin"): ?>
        <a href="/Project_cnw/chi-tiet-hoc-phan?course_id=<?= $course['id'] ?>"
        style="
            padding:8px 14px;
            background:#2563eb;
            color:white;
            text-decoration:none;
            border-radius:6px;">
        Chi tiết
        </a>
        <?php endif; ?>
    </div>

</div>
<?php
}