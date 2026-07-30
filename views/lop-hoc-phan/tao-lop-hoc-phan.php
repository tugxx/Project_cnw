<?php 
$viewMode = $viewMode ?? "";
$page = $page ?? 1;
$checkedStudentIds = $checkedStudentIds ?? [];
$keyword = $keyword ?? "";
$studentsList = $studentsList ?? [];
$courseId = $courseId ?? "";
?>


<link rel="stylesheet" href="/Project_cnw/assets/css/tao-lop-hoc-phan.css">

<div class="create-section-container">
    <h1 class="page-title">Tạo Lớp Học Phần Mới</h1>
    <?php if (!empty($errors)): ?>
        <div class="global-error-box">
            <strong>Đã xảy ra lỗi:</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="section-grid">
        <div class="left-column">
            <div class="content-box">
                <h2 class="box-title">Thông tin lớp học phần</h2>
                
                <form id="form-create-section" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="courseId" value="<?= htmlspecialchars($courseId) ?>">
                    
                    <div class="form-group">
                        <label for="section_code">Mã lớp học phần <span style="color: red;">*</span></label>
                        <input 
                            type="text" 
                            id="section_code" 
                            name="section_code" 
                            class="form-control" 
                            value="<?= htmlspecialchars($_POST['section_code'] ?? '') ?>" 
                            placeholder="VD: INT1001_01"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="section_name">Tên lớp học phần <span style="color: red;">*</span></label>
                        <input 
                            type="text" 
                            id="section_name" 
                            name="section_name" 
                            class="form-control" 
                            value="<?= htmlspecialchars($_POST['section_name'] ?? '') ?>" 
                            placeholder="VD: Lập trình web - Nhóm 1"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả thêm</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-control" 
                            rows="3" 
                            placeholder="Ghi chú hoặc thông tin bổ sung..."
                        ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image_cover">Ảnh bìa lớp học phần</label>
                        <input 
                            type="file" 
                            id="image_cover" 
                            name="image_cover" 
                            class="form-control" 
                            accept=".jpg,.jpeg,.png,.webp"
                        >
                    </div>

                    <div class="selected-count-badge">
                        Sinh viên đã chọn: <?= count($checkedStudentIds) ?>
                    </div>

                    <button type="submit" name="create_section" class="btn-primary btn-block">
                        Thực hiện Tạo Lớp
                    </button>
                </form>
            </div>

            <div class="content-box">
                <h2 class="box-title">Import từ File Excel</h2>
                <form id="form-upload-excel" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="courseId" value="<?= htmlspecialchars($courseId) ?>">
                    
                    <div class="form-group">
                        <label for="excel_file">Chọn file (.xlsx)</label>
                        <input type="file" id="excel_file" name="excel_file" accept=".xlsx" class="form-control" required>
                    </div>

                    <button type="submit" name="preview_excel" class="btn-secondary btn-block">
                        Đọc dữ liệu Excel
                    </button>
                </form>
            </div>
        </div>

        <div class="right-column">
            <div class="content-box">
                <h2 class="box-title">Chọn sinh viên vào lớp</h2>
                <form id="form-students-list" action="" method="POST">
                    <input type="hidden" name="courseId" value="<?= htmlspecialchars($courseId) ?>">
                    <input type="hidden" name="view_mode" value="<?= htmlspecialchars($viewMode) ?>">
                    <input type="hidden" name="page" value="1">
                
                    <div class="toolbar-actions">
                        <div class="filter-group">
                            <button type="submit" form="form-students-list" name="view_mode" value="all" class="btn-secondary">
                                Tất cả SV
                            </button>
                            <button type="submit" form="form-students-list" name="view_mode" value="checked_only" class="btn-secondary">
                                Đã chọn (<span id="checked-count-filter"><?= count($checkedStudentIds) ?></span>)
                            </button>
                        </div>

                        <div class="search-form-inline">
                            <input 
                                type="text" 
                                name="search_keyword" 
                                class="form-control" 
                                placeholder="Mã sinh viên..." 
                                value="<?= htmlspecialchars($keyword) ?>">
                            <button type="submit" name="view_mode" value="search" class="btn-secondary">Tìm</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">
                                        <input type="checkbox" id="check-all-students">
                                    </th>
                                    <th>Mã SV</th>
                                    <th>Họ và tên</th>
                                    <th>Email</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($studentsList)): ?>
                                    <?php foreach ($studentsList as $student): ?>
                                        <?php $isChecked = in_array($student['id'], $checkedStudentIds); ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="hidden" name="checked_ids[<?= $student['id'] ?>]" value="0">
                                                <input 
                                                    type="checkbox" 
                                                    class="student-checkbox"
                                                    name="checked_ids[<?= $student['id'] ?>]" 
                                                    value="1" 
                                                    <?= $isChecked ? 'checked' : '' ?>>
                                            </td>
                                            <td><strong><?= htmlspecialchars($student['user_code']) ?></strong></td>
                                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                                            <td><?= htmlspecialchars($student['email']) ?></td>
                                            <td>
                                                <?php if ($student['is_active']): ?>
                                                    <?php renderBadge('Hoạt động', 'green') ?>
                                                <?php else: ?>
                                                    <?php renderBadge('Khóa', 'red') ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center" style="padding: 20px; color: #64748b;">
                                            Không tìm thấy sinh viên nào.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($viewMode !== 'checked_only'): ?>
                        <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                            <button 
                                type="submit" 
                                name="page" 
                                value="<?= max(1, $page - 1) ?>" 
                                class="btn-secondary" 
                                <?= $page <= 1 ? 'disabled' : '' ?>>
                                &laquo; Trang trước
                            </button>
                            <span>Trang <?= $page ?></span>
                            <button 
                                type="submit" 
                                name="page" 
                                value="<?= $page + 1 ?>" 
                                class="btn-secondary" 
                                <?= count($studentsList) < $perPage ? 'disabled' : '' ?>>
                                Trang sau &raquo;
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const checkAllBtn = document.getElementById('check-all-students');
    const countBadge = document.getElementById('checked-count-filter');
    const formCreateSection = document.getElementById('form-create-section');
    const formStudentsList = document.getElementById('form-students-list');

    function updateSelectedCount(change) {
        if (!countBadge) return;
        let currentCount = parseInt(countBadge.textContent) || 0;
        currentCount = Math.max(0, currentCount + change);
        countBadge.textContent = currentCount;

        const leftBadge = document.querySelector('.selected-count-badge');
        if (leftBadge) {
            leftBadge.textContent = 'Sinh viên đã chọn: ' + currentCount;
        }
    }

    studentCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            updateSelectedCount(this.checked ? 1 : -1);
        });
    });

    if (checkAllBtn) {
        checkAllBtn.addEventListener('change', function () {
            let changedCount = 0;
            studentCheckboxes.forEach(cb => {
                if (cb.checked !== this.checked) {
                    cb.checked = this.checked;
                    changedCount += this.checked ? 1 : -1;
                }
            });
            updateSelectedCount(changedCount);
        });
    }

    if (formCreateSection && formStudentsList) {
        formCreateSection.addEventListener('submit', function (e) {
            const oldClones = formCreateSection.querySelectorAll('.cloned-checkbox');
            oldClones.forEach(el => el.remove());

            const studentInputs = formStudentsList.querySelectorAll('input[name^="checked_ids"]');
            studentInputs.forEach(input => {
                if (input.type === 'hidden' || (input.type === 'checkbox' && input.checked)) {
                    const clone = input.cloneNode(true);
                    clone.classList.add('cloned-checkbox');
                    clone.type = 'hidden'; 
                    formCreateSection.appendChild(clone);
                }
            });
        });
    }
});
</script>