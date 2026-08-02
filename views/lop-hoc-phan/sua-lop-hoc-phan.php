<?php
$section = $section ?? [];
$totalPages = $totalPages ?? 1;
$offset = $offset ?? 0;
$sectionId = $sectionId ?? '';
$page = $page ?? 1;
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/sua-lop-hoc-phan.css">

<div class="edit-section-wrapper">
    <div class="section-banner">
        <div class="section-banner-info">
            <?php if (!empty($section['cover_image'])): ?>
                <img src="/Project_cnw/storage/uploads/sections/<?= htmlspecialchars($section['cover_image']) ?>" 
                     alt="Cover" class="section-cover-preview">
            <?php else: ?>
                <img src="/Project_cnw/assets/media/default-image-section.jpg" 
                     alt="Cover" class="section-cover-preview">
            <?php endif; ?>

            <div class="section-title-group">
                <h1><?= htmlspecialchars($section['section_name']) ?></h1>
                <div class="section-meta-tags">
                    <?php renderBadge('Mã lớp: ' . htmlspecialchars($section['section_code']), 'blue'); ?>
                    <?php renderBadge('Tổng sĩ số: ' . $totalStudents . ' sinh viên', 'green'); ?>
                </div>
            </div>
        </div>

        <div>
            <a href="danh-sach-lop-hoc-phan?course_id=<?= $section['course_id'] ?>" class="btn-secondary">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <?php if (!empty($successMsg)): ?>
        <div class="alert-box alert-success">
            <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert-box alert-danger">
            <strong>Đã xảy ra lỗi:</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <div class="grid-column">
            <div class="panel">
                <h2 class="panel-title">Chỉnh sửa thông tin lớp</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <?php 
                    renderInput(
                        'section_code', 
                        'Mã lớp học phần', 
                        'text', 
                        $_POST['section_code'] ?? $section['section_code'], 
                        '', 
                        'Nhập mã lớp học phần...', 
                        true
                    ); 
                    ?>

                    <?php 
                    renderInput(
                        'section_name', 
                        'Tên lớp học phần', 
                        'text', 
                        $_POST['section_name'] ?? $section['section_name'], 
                        '', 
                        'Nhập tên lớp học phần...', 
                        true
                    ); 
                    ?>

                    <div class="form-group">
                        <label for="description">Mô tả chi tiết</label>
                        <textarea 
                            name="description" 
                            id="description" 
                            class="form-control-custom" 
                            placeholder="Mô tả tóm tắt nội dung lớp học..."
                        ><?= htmlspecialchars($_POST['description'] ?? $section['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="cover_image">Thay đổi ảnh bìa (Tối đa 2MB)</label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control-custom" accept="image/*">
                    </div>

                    <div style="margin-top: 20px;">
                        <?php renderButton('Lưu thay đổi', 'submit'); ?>
                    </div>
                    
                    <input type="hidden" name="update_section_info" value="1">
                </form>
            </div>
        </div>

        <div class="grid-column">
            <div class="panel">
                <h2 class="panel-title">Thêm sinh viên mới</h2>
                <form action="" method="POST">
                    <div class="inline-form">
                    <div class="form-group">
                            <?php 
                            renderInput(
                                'student_code', 
                                'Mã sinh viên', 
                                'text', 
                                $_POST['student_code'] ?? '', 
                                '', 
                                'Nhập mã SV (VD: SV12345)', 
                                true); 
                            ?>
                        </div>
                        <div>
                            <?php renderButton('Thêm vào lớp', 'submit'); ?>
                        </div>
                    </div>
                    <input type="hidden" name="add_student" value="1">
                </form>
            </div>

            <div class="panel">
                <h2 class="panel-title">Danh sách sinh viên trong lớp</h2>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ và tên</th>
                                <th>Lớp sinh hoạt</th>
                                <th>Trạng thái</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($studentsList)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">
                                        Chưa có sinh viên nào trong lớp học phần này.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($studentsList as $st): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($st['user_code']) ?></strong></td>
                                        <td><?= htmlspecialchars($st['full_name']) ?></td>
                                        <td><?= htmlspecialchars($st['class'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php if ($st['is_active']): ?>
                                                <?php renderBadge('Hoạt động', 'green'); ?>
                                            <?php else: ?>
                                                <?php renderBadge('Đã khóa', 'red'); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <form action="" method="POST" onsubmit="return confirm('Xóa sinh viên này khỏi lớp?');" style="display: inline;">
                                                <input type="hidden" name="remove_student_id" value="<?= $st['id'] ?>">
                                                <?php 
                                                renderButton(
                                                    'Xóa', 
                                                    'submit', 
                                                    'padding: 4px 10px; font-size: 12px; height: 28px;', 
                                                    false, 
                                                    '#dc2626', 
                                                    '#ef4444'); 
                                                ?>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination-container">
                        <div>
                            Hiển thị từ <strong><?= $offset + 1 ?></strong> đến <strong><?= min($offset + $perPage, $totalStudents) ?></strong> trên <strong><?= $totalStudents ?></strong> sinh viên
                        </div>
                        <div class="pagination-links">
                            <a href="?section_id=<?= urlencode($sectionId) ?>&page=<?= $page - 1 ?>" 
                               class="page-btn <?= ($page <= 1) ? 'disabled' : '' ?>">&laquo; Trước</a>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?section_id=<?= urlencode($sectionId) ?>&page=<?= $i ?>" 
                                   class="page-btn <?= ($i === $page) ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <a href="?section_id=<?= urlencode($sectionId) ?>&page=<?= $page + 1 ?>" 
                               class="page-btn <?= ($page >= $totalPages) ? 'disabled' : '' ?>">Sau &raquo;</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
