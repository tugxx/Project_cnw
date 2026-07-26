<div style="max-width: 600px; margin: 40px auto; padding: 0 16px; font-family: system-ui, -apple-system, sans-serif;">
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 32px; box-sizing: border-box;">
        <div style="border-bottom: 1px solid #f3f4f6; padding-bottom: 16px; margin-bottom: 24px;">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">Thay đổi mật khẩu</h2>
            <p style="font-size: 13px; color: #6b7280; margin: 0;">Khuyên dùng mật khẩu mạnh bao gồm chữ cái, chữ số và ký tự đặc biệt</p>
        </div>

        <?php 
        if (!empty($success)) {
            renderAlert($success, 'success');
        }
        
        if (!empty($errors)) {
            foreach ($errors as $err) {
                renderAlert($err, 'error');
            }
        }
        ?>

        <form action="" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
            <div style="max-width: 480px;">
                <?php 
                    renderInput('old_password', 'Mật khẩu hiện tại', 'password', '', '', '', true);
                    renderInput('new_password', 'Mật khẩu mới', 'password', '', '', '', true);
                    renderInput('confirm_password', 'Xác nhận mật khẩu mới', 'password', '', '', '', true);
                ?>
            </div>

            <div style="padding-top: 16px; margin-top: 8px; border-top: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                <div style="width: auto; min-width: 140px;">
                    <?php renderButton('Lưu thay đổi', 'submit'); ?>
                </div>
                <a href="index.php" style="display: inline-block; background: #ffffff; color: #374151; border: 1px solid #d1d5db; font-weight: 500; padding: 9px 18px; border-radius: 8px; font-size: 14px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#ffffff'">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>