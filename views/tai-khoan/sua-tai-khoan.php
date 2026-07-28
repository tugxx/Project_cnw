<?php $targetUser = $targetUser ?? [];?>
<link rel="stylesheet" href="/Project_cnw/assets/css/sua-tai-khoan.css">
<div class="edit-modal-overlay">
    <div class="edit-modal-card">
        
        <div class="edit-modal-header">
            <h3 class="edit-modal-title">Cập nhật thông tin tài khoản</h3>
            <a href="/Project_cnw/danh-sach-tai-khoan" class="edit-modal-close">&times;</a>
        </div>

        <form method="POST" action="/Project_cnw/sua-tai-khoan&id=<?= htmlspecialchars($targetUser['id']) ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($targetUser['id']) ?>">
            <div class="form-grid-2">
                <div>
                    <?php renderInput('username', 'Tên đăng nhập', 'text', $_POST['username'] ?? $targetUser['username'], '', 'Nhập tên đăng nhập', true); ?>
                </div>

                <div>
                    <?php renderInput('email', 'Email', 'email', $_POST['email'] ?? $targetUser['email'], '', 'abc@example.com', true); ?>
                </div>

                <div>
                    <?php renderInput('password', 'Mật khẩu mới', 'password', '', '', 'Bỏ trống nếu giữ nguyên', false, true); ?>
                </div>

                <div>
                    <?php renderInput('full_name', 'Họ và tên', 'text', $_POST['full_name'] ?? $targetUser['full_name'], '', 'Nhập họ và tên', true); ?>
                </div>

                <div>
                    <?php renderInput('dob', 'Ngày sinh', 'date', $_POST['dob'] ?? $targetUser['dob'] ?? '', '', '', false); ?>
                </div>

                <div>
                    <?php renderInput('class', 'Lớp', 'text', $_POST['class'] ?? $targetUser['class'] ?? '', '', 'K72E4 CNTT'); ?>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Vai trò <span style="color:red">*</span></label>
                    <select name="role" class="form-control-custom" required>
                        <?php $selectedRole = $_POST['role'] ?? $targetUser['role']; ?>
                        <option value="student" <?= $selectedRole === 'student' ? 'selected' : '' ?>>Sinh viên</option>
                        <option value="lecturer" <?= $selectedRole === 'lecturer' ? 'selected' : '' ?>>Giảng viên</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Trạng thái tài khoản</label>
                    <select name="is_active" class="form-control-custom">
                        <?php $selectedActive = (string)($_POST['is_active'] ?? $targetUser['is_active']); ?>
                        <option value="1" <?= $selectedActive === '1' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= $selectedActive === '0' ? 'selected' : '' ?>>Khóa tài khoản</option>
                    </select>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="form-error-summary">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-actions-custom">
                <a href="/Project_cnw/danh-sach-tai-khoan" class="btn-cancel-custom">Hủy bỏ</a>
                <?php renderButton('Lưu thay đổi', 'submit', 'background-color: #2563eb; color: #fff; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer;'); ?>
            </div>

        </form>
    </div>
</div>