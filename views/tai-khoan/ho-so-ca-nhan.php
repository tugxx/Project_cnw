<?php
$avatarSrc = !empty($user['avatar'])
    ? htmlspecialchars($user['avatar'])
    : '/Project_cnw/assets/images/default-avatar.jpg';

$dobFormatted = !empty($user['dob']) ? date('d/m/Y', strtotime($user['dob'])) : '---';
$roleName = $user['role'] ?? 'Sinh viên';
?>

<div style="max-width: 680px; margin: 30px auto; padding: 0 16px; font-family: system-ui, -apple-system, sans-serif;">
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden;">
        <div style="background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); height: 80px; position: relative;"></div>

        <div style="padding: 0 24px 24px 24px; position: relative; margin-top: -40px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: flex-end; gap: 16px;">
                    <img src="<?= $avatarSrc ?>" 
                         alt="Avatar" 
                         style="width: 84px; height: 84px; border-radius: 50%; border: 4px solid #ffffff; object-fit: cover; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <div style="margin-bottom: 4px; margin-top: 40px;">
                        <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                            <?= htmlspecialchars($user['name'] ?? 'Người dùng') ?>
                        </h2>
                        <?php renderBadge($roleName, 'blue'); ?>
                    </div>
                </div>

                <div style="display: flex; gap: 8px;">
                    <a href="doi-mat-khau" style="display: inline-flex; align-items: center; gap: 6px; background: #ffffff; color: #374151; border: 1px solid #d1d5db; font-weight: 500; padding: 8px 14px; border-radius: 8px; font-size: 13px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#ffffff'">
                        <i class="fa-solid fa-key" style="font-size: 12px; color: #6b7280;"></i> Đổi mật khẩu
                    </a>
                </div>
            </div>

            <hr style="border: none; border-top: 1px solid #f3f4f6; margin: 20px 0;">

            <h3 style="font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.05em;">
                Thông tin tài khoản
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px;">
                <?php 
                    renderProfileItem('Địa chỉ Email', $user['email'] ?? '', 'fa-regular fa-envelope');
                    renderProfileItem('Ngày sinh', $dobFormatted, 'fa-regular fa-calendar');
                    renderProfileItem('Lớp học / Đơn vị', $user['class'] ?? '', 'fa-solid fa-graduation-cap');
                ?>
            </div>

        </div>

    </div>
</div>