<link rel="stylesheet" href="/Project_cnw/assets/css/them-tai-khoan.css">

<div class="form-container">
    <div class="form-header">
        <h2>Thêm tài khoản người dùng</h2>
        <p>Tạo tài khoản mới và phân quyền truy cập hệ thống</p>
    </div>

    <form method="POST" action="">
        <div class="form-grid">
            <div>
                <?php if (function_exists('renderInput')) renderInput('user_code', 'Mã', 'text', $_POST['user_code'] ?? '', '', '725105187', true); ?>
            </div>

            <div>
                <?php if (function_exists('renderInput')) renderInput('full_name', 'Họ và tên', 'text', $_POST['full_name'] ?? '', '', 'Nguyễn Văn A', true); ?>
            </div>
            
            <div>
                <?php if (function_exists('renderInput')) renderInput('username', 'Tên đăng nhập', 'text', $_POST['username'] ?? '', '', 'username', true); ?>
            </div>
            
            <div>
                <?php if (function_exists('renderInput')) renderInput('email', 'Địa chỉ Email', 'email', $_POST['email'] ?? '', '', 'example@domain.com', true); ?>
            </div>
            
            <div>
                <?php if (function_exists('renderInput')) renderInput('password', 'Mật khẩu', 'password', '', '', 'Nhập mật khẩu', true, true); ?>
            </div>
            
            <div>
                <?php if (function_exists('renderInput')) renderInput('dob', 'Ngày sinh', 'date', $_POST['dob'] ?? ''); ?>
            </div>
            
            <div>
                <?php if (function_exists('renderInput')) renderInput('class', 'Lớp học', 'text', $_POST['class'] ?? '', '', 'CNTT K72E4'); ?>
            </div>

            <div class="form-grid custom-select-group">
                <label for="role">Vai trò hệ thống</label>
                <select id="role" name="role" class="custom-select">
                    <option value="student" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>
                        Sinh viên
                    </option>
                    <option value="lecturer" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>
                        Giảng viên
                    </option>
                </select>
            </div>
        </div><br>

        <?php if (!empty($errors) && is_array($errors)): ?>
            <div class="error-msg">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="/Project_cnw/danh-sach-tai-khoan" class="btn-cancel">
                Hủy bỏ
            </a>
            <?php if (function_exists('renderButton')) renderButton('Tạo tài khoản mới', 'submit', 'background-color: #3182ce; color: #ffffff;'); ?>
        </div>
    </form>
</div>