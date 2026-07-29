<?php
$page_num = $page_num ?? 1;
$totalPages = $totalPages ?? 1;
$searchIn = $searchIn ?? [];
$searchableFields = $searchableFields ?? [];
?>

<link rel="stylesheet" href="assets/css/danh-sach-tai-khoan.css">

<div class="account-list-container">
    <header class="page-header">
        <div class="page-title">
            <h1>Quản lý tài khoản</h1>
            <p>Danh sách tài khoản người dùng trong hệ thống</p>
        </div>
        <div class="header-actions">
            <a href="import-tai-khoan" style="text-decoration: none;">
                <?php renderButton('Import tài khoản', 'button', 'color: #334155; border: 1px solid #cbd5e1; padding: 0 14px; height: 36px; font-weight: 500; font-size: 13px; border-radius: 6px;', false, '#f1f5f9', '#ffffff') ?>
            </a>
            <a href="them-tai-khoan" style="text-decoration: none;">
                <?php renderButton('Thêm tài khoản', 'button', 'background-color: #2563eb; color: #ffffff; padding: 0 14px; height: 36px; font-weight: 500; font-size: 13px; border-radius: 6px;') ?>
            </a>
        </div>
    </header>

    <section class="filter-bar">
        <form method="GET" action="" class="filter-form">
            <div class="filter-group">
                <?php renderInput(
                    name: 'search', 
                    label: 'Tìm kiếm', 
                    type: 'text', 
                    value: $_GET['search'] ?? '', 
                    error: $errors['search'] ?? '', 
                    placeholder: 'Nhập mã, họ và tên hoặc email...',
                    wrapperStyle: 'margin-bottom: 0;'
                ) ?>
            </div>

            <div class="filter-group filter-select-wrapper">
                <label for="filter-search-in">Tìm theo</label>
                <div class="multiselect" data-multiselect>
                    <button type="button" class="multiselect-toggle filter-select" data-multiselect-toggle>
                        <span data-multiselect-label>
                            <?= getMultiselectLabel($searchIn, $searchableFields) ?>
                        </span>
                        <svg class="multiselect-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="multiselect-panel" data-multiselect-panel hidden>
                        <?php foreach ($searchableFields as $field => $label): ?>
                            <label class="multiselect-item">
                                <input 
                                    type="checkbox" 
                                    name="search_in[]" 
                                    value="<?= $field ?>"
                                    <?= in_array($field, $searchIn, true) ? 'checked' : '' ?>
                                    data-multiselect-checkbox
                                    data-label="<?= htmlspecialchars($label) ?>"
                                    hidden
                                >
                                <span class="multiselect-check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                </span>
                                <span class="multiselect-text"><?= $label ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="filter-group filter-select-wrapper">
                <label for="filter-role">Vai trò</label>
                <select name="role" id="filter-role" class="filter-select">
                    <option value="">-- Vai trò --</option>
                    <option value="admin" <?= (($_GET['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="lecturer" <?= (($_GET['role'] ?? '') === 'lecturer') ? 'selected' : '' ?>>Giảng viên</option>
                    <option value="student" <?= (($_GET['role'] ?? '') === 'student') ? 'selected' : '' ?>>Sinh viên</option>
                </select>
            </div>

            <div class="filter-group filter-select-wrapper">
                <label for="filter-status">Trạng thái</label>
                <select name="status" id="filter-status" class="filter-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" <?= (($_GET['status'] ?? '') === '1') ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="0" <?= (($_GET['status'] ?? '') === '0') ? 'selected' : '' ?>>Đã khóa</option>
                </select>
            </div>

            <div class="filter-buttons">
                <?php renderButton('Lọc dữ liệu', 'submit', 'background-color: #0f172a; color: #ffffff; padding: 0 16px; height: 42px; border-radius: 6px;'); ?>
            </div>
        </form>
    </section>

    <main class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">STT</th>

                        <?php $sortCode = getSortUrl('user_code'); ?>
                        <th>
                            <a href="<?= $sortCode['url'] ?>" class="sort-link <?= $sortCode['active'] ? 'active' : '' ?>">
                                <span>Mã</span>
                                <?= renderSortIcon($sortCode['direction']) ?>
                            </a>
                        </th>
                        
                        <?php $sortName = getSortUrl('full_name'); ?>
                        <th>
                            <a href="<?= $sortName['url'] ?>" class="sort-link <?= $sortName['active'] ? 'active' : '' ?>">
                                <span>Họ và tên</span>
                                <?= renderSortIcon($sortName['direction']) ?>
                            </a>
                        </th>

                        <?php $sortUser = getSortUrl('username'); ?>
                        <th>
                            <a href="<?= $sortUser['url'] ?>" class="sort-link <?= $sortUser['active'] ? 'active' : '' ?>">
                                <span>Tên đăng nhập</span>
                                <?= renderSortIcon($sortUser['direction']) ?>
                            </a>
                        </th>

                        <?php $sortEmail = getSortUrl('email'); ?>
                        <th>
                            <a href="<?= $sortEmail['url'] ?>" class="sort-link <?= $sortEmail['active'] ? 'active' : '' ?>">
                                <span>Email</span>
                                <?= renderSortIcon($sortEmail['direction']) ?>
                            </a>
                        </th>

                        <?php $sortRole = getSortUrl('role'); ?>
                        <th>
                            <a href="<?= $sortRole['url'] ?>" class="sort-link <?= $sortRole['active'] ? 'active' : '' ?>">
                                <span>Vai trò</span>
                                <?= renderSortIcon($sortRole['direction']) ?>
                            </a>
                        </th>

                        <?php $sortClass = getSortUrl('class'); ?>
                        <th>
                            <a href="<?= $sortClass['url'] ?>" class="sort-link <?= $sortClass['active'] ? 'active' : '' ?>">
                                <span>Lớp</span>
                                <?= renderSortIcon($sortClass['direction']) ?>
                            </a>
                        </th>

                        <?php $sortDob = getSortUrl('dob'); ?>
                        <th>
                            <a href="<?= $sortDob['url'] ?>" class="sort-link <?= $sortDob['active'] ? 'active' : '' ?>">
                                <span>Ngày sinh</span>
                                <?= renderSortIcon($sortDob['direction']) ?>
                            </a>
                        </th>

                        <?php $sortIsActive = getSortUrl('is_active'); ?>
                        <th>
                            <a href="<?= $sortIsActive['url'] ?>" class="sort-link <?= $sortIsActive['active'] ? 'active' : '' ?>">
                                <span>Trạng thái</span>
                                <?= renderSortIcon($sortIsActive['direction']) ?>
                            </a>
                        </th>

                        <?php $sortCreated = getSortUrl('created_at'); ?>
                        <th>
                            <a href="<?= $sortCreated['url'] ?>" class="sort-link <?= $sortCreated['active'] ? 'active' : '' ?>">
                                <span>Ngày tạo</span>
                                <?= renderSortIcon($sortCreated['direction']) ?>
                            </a>
                        </th>

                        <th style="width: 80px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($item['user_code'] ?? '—') ?></td>
                                <td>
                                    <div class="user-info">
                                        <span><?= htmlspecialchars($item['full_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($item['username']) ?></td>
                                <td><?= htmlspecialchars($item['email']) ?></td>
                                <td>
                                    <?php 
                                    if ($item['role'] === 'admin') {
                                        renderBadge('Admin', 'blue');
                                    } elseif ($item['role'] === 'lecturer') {
                                        renderBadge('Giảng viên', 'gray');
                                    } else {
                                        renderBadge('Sinh viên', 'gray');
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($item['class'] ?? '—') ?></td>
                                <td><?= !empty($item['dob']) ? date('d/m/Y', strtotime($item['dob'])) : '—' ?></td>
                                <td>
                                    <?php 
                                    if ($item['is_active']) {
                                        renderBadge('Hoạt động', 'green');
                                    } else {
                                        renderBadge('Khóa', 'red');
                                    }
                                    ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                <td style="text-align: center;">
                                    <?php if ($item['role'] !== 'admin'): ?>
                                    <a href="sua-tai-khoan?id=<?= $item['id'] ?>" class="action-icon action-edit" title="Sửa tài khoản">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                            <path d="m15 5 4 4"/>
                                        </svg>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="empty-state">
                                Không tìm thấy tài khoản nào trong hệ thống.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer class="table-footer">
            <nav class="pagination-wrapper">
                <ul class="pagination">
                    <?php if ($page_num > 1): ?>
                        <li class="page-item">
                            <a href="danh-sach-tai-khoan?<?= !empty($queryString) ? $queryString . '&' : '' ?>page_num=<?= $page_num - 1 ?>" class="page-link">&laquo; Trước</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i === $page_num) ? 'active' : '' ?>">
                            <a href="danh-sach-tai-khoan?<?= !empty($queryString) ? $queryString . '&' : '' ?>page_num=<?= $i ?>" class="page-link"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page_num < $totalPages): ?>
                        <li class="page-item">
                            <a href="danh-sach-tai-khoan?<?= !empty($queryString) ? $queryString . '&' : '' ?>page_num=<?= $page_num + 1 ?>" class="page-link">Sau &raquo;</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </footer>
    </main>
</div>

<script>
document.querySelectorAll('[data-multiselect]').forEach(function (root) {
    const toggle = root.querySelector('[data-multiselect-toggle]');
    const panel = root.querySelector('[data-multiselect-panel]');
    const labelEl = root.querySelector('[data-multiselect-label]');
    const checkboxes = root.querySelectorAll('[data-multiselect-checkbox]');

    function updateLabel() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked);
        const total = checkboxes.length;

        if (checked.length === 0) {
            labelEl.textContent = '-- Chọn trường --';
        } else if (checked.length === total) {
            labelEl.textContent = 'Tất cả';
        } else if (checked.length === 1) {
            labelEl.textContent = checked[0].dataset.label;
        } else {
            labelEl.textContent = `Đã chọn ${checked.length} trường`;
        }
    }

    function openPanel() {
        panel.hidden = false;
        root.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
    }

    function closePanel() {
        panel.hidden = true;
        root.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        panel.hidden ? openPanel() : closePanel();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateLabel));

    document.addEventListener('click', function (e) {
        if (!root.contains(e.target)) closePanel();
    });
});
</script>