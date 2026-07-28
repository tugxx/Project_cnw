<link rel="stylesheet" href="assets/css/import-tai-khoan.css">

<div class="import-container">
    <h2 class="import-title">Import Tài Khoản Từ File Excel</h2>
    <div class="guide-section">
        <div class="guide-header">
            <h3 class="guide-title">Cấu trúc dữ liệu file mẫu (.xlsx)</h3>
            <a href="assets/templates/mau_import_tai_khoan.xlsx" class="btn-download-sample" download>
                Tải file Excel mẫu
            </a>
        </div>

        <div class="table-wrapper">
            <table class="sample-table">
                <thead>
                    <tr>
                        <th>Cột A</th>
                        <th>Cột B</th>
                        <th>Cột C</th>
                        <th>Cột D</th>
                        <th>Cột E</th>
                        <th>Cột F</th>
                        <th>Cột G</th>
                    </tr>
                    <tr>
                        <th>Email (*)</th>
                        <th>Username (*)</th>
                        <th>Mật khẩu (*)</th>
                        <th>Họ và tên (*)</th>
                        <th>Ngày sinh</th>
                        <th>Lớp</th>
                        <th>Vai trò</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>nguyenvana@gmail.com</td>
                        <td>nguyenvana</td>
                        <td>123456</td>
                        <td>Nguyễn Văn A</td>
                        <td>2003-05-20</td>
                        <td>CNTT1</td>
                        <td><?php renderBadge('student', 'blue') ?></td>
                    </tr>
                    <tr>
                        <td>tranvanb@gmail.com</td>
                        <td>tranvanb</td>
                        <td>123456</td>
                        <td>Trần Văn B</td>
                        <td>1998-11-12</td>
                        <td>CNTT2</td>
                        <td><?php renderBadge('lecturer', 'green') ?></td>
                    </tr>
                    <tr>
                        <td>lethic@gmail.com</td>
                        <td>lethic</td>
                        <td>123456</td>
                        <td>Lê Thị C</td>
                        <td>2004-02-15</td>
                        <td>KTPM1</td>
                        <td><?php renderBadge('student', 'blue') ?></td>
                    </tr>
                    <tr>
                        <td>phamvand@gmail.com</td>
                        <td>phamvand</td>
                        <td>123456</td>
                        <td>Phạm Văn D</td>
                        <td>1995-08-30</td>
                        <td></td>
                        <td><?php renderBadge('lecturer', 'green') ?></td>
                    </tr>
                    <tr>
                        <td>hoangthie@gmail.com</td>
                        <td>hoangthie</td>
                        <td>123456</td>
                        <td>Hoàng Thị E</td>
                        <td></td>
                        <td>HTTT1</td>
                        <td><?php renderBadge('student', 'blue') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="form-error-msg">
            <?php foreach ($errors as $error): ?>
                <div>• <?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="upload-box">
            <label for="excel_file" class="file-input-label">Chọn file dữ liệu Excel (.xlsx):</label>
            
            <div class="upload-inputs">
                <?php renderInput('excel_file', '', 'file', '', '', '') ?>
                <?php renderButton('Thực hiện Import', 'submit', 'padding: 8px 20px; font-weight: 600; margin-bottom: 5px;') ?>
            </div>
        </div>
    </form>
</div>