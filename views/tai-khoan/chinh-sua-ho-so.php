<?php
$avatarSrc = !empty($user['avatar'])
    ? '/Project_cnw/storage/uploads/avatars/' . htmlspecialchars($user['avatar'])
    : '/Project_cnw/assets/media/default-avatar.jpg';

$roleName = $user['role'] ?? 'Sinh viên';
$fullNameVal = $_POST['full_name'] ?? $user['full_name'] ?? '';
$dobVal = $_POST['dob'] ?? $user['dob'] ?? '';
?>

<link rel="stylesheet" href="/Project_cnw/assets/css/chinh-sua-ho-so.css">

<div class="edit-profile-page">
    <div class="profile-banner"></div>

    <form action="" method="POST" enctype="multipart/form-data" class="edit-profile-form">
        <div class="profile-header-edit">
            <div class="profile-user-info">
                <div class="avatar-upload-group">
                    <img id="avatarPreview" src="<?= $avatarSrc ?>" 
                         alt="Avatar" 
                         class="profile-avatar"
                         onclick="document.getElementById('avatarInput').click();"
                         title="Bấm để đổi ảnh đại diện">
                    
                    <label for="avatarInput" title="Đổi ảnh" class="avatar-edit-badge">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                </div>

                <div class="user-title-group">
                    <h2><?= htmlspecialchars($user['full_name'] ?? 'Chỉnh sửa hồ sơ') ?></h2>
                    <div class="user-badge">
                        <?php if (function_exists('renderBadge')) renderBadge($roleName, 'blue'); ?>
                    </div>
                </div>
            </div>

            <div class="avatar-action-buttons">
                <div class="button-group">
                    <button type="button" class="btn-upload" onclick="document.getElementById('avatarInput').click();">
                        <i class="fa-solid fa-upload"></i> Tải ảnh mới
                    </button>

                    <button type="button" id="resetAvatarBtn" class="btn-reset-avatar" onclick="resetAvatarPreview()">
                        <i class="fa-solid fa-xmark"></i> Hủy chọn
                    </button>
                </div>
                <span class="upload-note">Hỗ trợ PNG, JPG, WEBP (Tối đa 2MB)</span>
            </div>

            <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden-input" onchange="previewImage(this)">
        </div>

        <hr class="section-divider">

        <div class="form-section-header">
            <h3>Thông tin cá nhân</h3>
            <p>Cập nhật thông tin chi tiết và dữ liệu tài khoản của bạn</p>
        </div>

        <div class="form-grid">
            <div>
                <?php 
                if (function_exists('renderInput')) {
                    renderInput('full_name', 'Họ và tên', 'text', $fullNameVal, $errors['full_name'] ?? '', '');
                }
                ?>
            </div>

            <div>
                <?php 
                if (function_exists('renderInput')) {
                    renderInput('dob', 'Ngày sinh', 'date', $dobVal, $errors['dob'] ?? '');
                }
                ?>
            </div>

            <div class="custom-input-group readonly-group">
                <label>Địa chỉ Email <span class="label-note">(Cố định)</span></label>
                <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled class="custom-input disabled">
            </div>

            <div class="custom-input-group readonly-group">
                <label>Lớp học<span class="label-note">(Cố định)</span></label>
                <input type="text" value="<?= htmlspecialchars($user['class'] ?? '') ?>" disabled class="custom-input disabled">
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="form-alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="ho-so-ca-nhan" class="btn-cancel">
                Hủy bỏ
            </a>

            <?php 
            if (function_exists('renderButton')) {
                renderButton('<i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi', 'submit', 'background-color: #2563eb; color: #ffffff;');
            }
            ?>
        </div>
    </form>
</div>

<script>
    const originalAvatarSrc = "<?= $avatarSrc ?>";
</script>
<script src="/Project_cnw/assets/js/chinh-sua-ho-so.js"></script>