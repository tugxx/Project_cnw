<h2>Danh sách học phần</h2>

<div style="
display:grid;
grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
gap:20px;
">

<?php
    if (!isset($courses) || !is_array($courses)) {
        $courses = [];
    }

    foreach ($courses as $course) {
        renderCourseCard($course);
    }

?>

</div>

<?php
require_once __DIR__.'/../layouts/footer.php';
?>