<link rel="stylesheet" href="/Project_cnw/assets/css/doi-mat-khau.css">

<div class="password-change-container">
    <div class="form-header">
        <h2>Thay đổi mật khẩu</h2>
    </div>

    <?php 
    if (!empty($success) && function_exists('renderAlert')) {
        renderAlert($success, 'success');
    }
    ?>

    <form action="" method="POST" class="password-form">
        <div class="form-fields">
            <?php 
            if (function_exists('renderInput')) {
                renderInput('old_password', 'Mật khẩu hiện tại', 'password', '', '', 'Nhập mật khẩu hiện tại', true, true);
                renderInput('new_password', 'Mật khẩu mới', 'password', '', '', 'Nhập mật khẩu mới', true, true);
                renderInput('confirm_password', 'Xác nhận mật khẩu mới', 'password', '', '', 'Nhập lại mật khẩu mới', true, true);
            }
            ?>
        </div>
        <?php
            if (!empty($errors) && function_exists('renderAlert')) {
            if (is_array($errors)) {
                $errorMsg = implode('<br>', array_map('htmlspecialchars', $errors));
                echo $errorMsg;
            } else {
                echo $errors;
            }
        }
        ?>

        <div class="form-actions">
            <?php 
            if (function_exists('renderButton')) {
                renderButton('Lưu thay đổi', 'submit', 'background-color: #3182ce; color: #ffffff;');
            } 
            ?>
            
            <a href="/Project_cnw/ho-so-ca-nhan" class="btn-cancel">
                Hủy bỏ
            </a>
        </div>
    </form>
</div>