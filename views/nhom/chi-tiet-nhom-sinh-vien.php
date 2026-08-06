<?php 
    $sectionId = $sectionId ?? "";
    $sessionId = $sessionId ?? "";
    $isLeader = $isLeader ?? false;
    $members = $members ?? [];
    $userId = $userId ?? "";
?>

<div style="max-width: 900px; margin: 20px auto; font-family: Arial, sans-serif; line-height: 1.5;">
    <p>
        <a href="danh-sach-nhom?section_id=<?= urlencode($sectionId) ?>&session_id=<?= urlencode($sessionId) ?>" 
           style="text-decoration: none; color: #0066cc;">
            &larr; Quay lại danh sách nhóm
        </a>
    </p>

    <?php if (!empty($errors)): ?>
        <div style="background-color: #f8d7da; color: #842029; padding: 12px 15px; border-radius: 4px; border: 1px solid #f5c2c7; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 1. THÔNG TIN CHUNG VỀ NHÓM -->
    <fieldset style="border: 1px solid #ccc; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <legend style="font-weight: bold; font-size: 1.2em; padding: 0 8px;">Thông tin nhóm</legend>
        
        <p><strong>Tên nhóm:</strong> <?= htmlspecialchars($group['group_name'] ?? '') ?></p>
        <p>
            <strong>Trạng thái:</strong> 
            <?php if (($group['status'] ?? '') === 'open'): ?>
                <span style="color: green; font-weight: bold;">Đang mở (Open)</span>
            <?php else: ?>
                <span style="color: red; font-weight: bold;">Đã khóa (Closed)</span>
            <?php endif; ?>
        </p>
        <p><strong>Hạn chót sửa nhóm:</strong> <?= !empty($group['group_deadline']) ? date('d/m/Y H:i', strtotime($group['group_deadline'])) : 'Không giới hạn' ?></p>
        <p><strong>Hạn chót chọn đề tài:</strong> <?= !empty($group['topic_deadline']) ? date('d/m/Y H:i', strtotime($group['topic_deadline'])) : 'Không giới hạn' ?></p>

        <!-- Thao tác quản lý nhóm -->
        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #ddd;">
            <?php if ($isLeader): ?>
                <!-- Đổi trạng thái nhóm (Leader) -->
                <form method="POST" style="display: inline-block; margin-right: 10px;">
                    <input type="hidden" name="action" value="toggle_status">
                    <button type="submit" style="padding: 6px 12px; cursor: pointer; background: #ffc107; border: 1px solid #e0a800; border-radius: 3px;">
                        <?= ($group['status'] ?? '') === 'closed' ? 'Mở đăng ký nhóm' : 'Đóng đăng ký nhóm' ?>
                    </button>
                </form>

                <!-- Giải tán nhóm (Leader) -->
                <form method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn giải tán nhóm này không?');">
                    <input type="hidden" name="action" value="disband_group">
                    <button type="submit" style="padding: 6px 12px; cursor: pointer; background: #dc3545; color: white; border: 1px solid #dc3545; border-radius: 3px;">
                        Giải tán nhóm
                    </button>
                </form>
            <?php else: ?>
                <!-- Rời nhóm (Member) -->
                <form method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn rời nhóm không?');">
                    <input type="hidden" name="action" value="leave_group">
                    <button type="submit" style="padding: 6px 12px; cursor: pointer; background: #dc3545; color: white; border: 1px solid #dc3545; border-radius: 3px;">
                        Rời khỏi nhóm
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </fieldset>

    <fieldset style="border: 1px solid #ccc; padding: 15px; border-radius: 6px;">
        <legend style="font-weight: bold; font-size: 1.2em; padding: 0 8px;">Danh sách thành viên (<?= count($members) ?>)</legend>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;" border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Mã SV</th>
                    <th>Họ và tên</th>
                    <th>Lớp</th>
                    <th>Vai trò</th>
                    <th>Ngày tham gia</th>
                    <?php if ($isLeader): ?>
                        <th style="text-align: center;">Thao tác</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($members)): ?>
                    <tr>
                        <td colspan="<?= $isLeader ? 7 : 6 ?>" style="text-align: center; color: #777;">Chưa có thành viên nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($members as $index => $member): ?>
                        <tr>
                            <td style="text-align: center;"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($member['user_code'] ?? '') ?></td>
                            <td><?= htmlspecialchars($member['full_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($member['class'] ?? '') ?></td>
                            <td>
                                <?php if (($member['role'] ?? '') === 'leader'): ?>
                                    <strong style="color: #0d6efd;">Trưởng nhóm</strong>
                                <?php else: ?>
                                    Thành viên
                                <?php endif; ?>
                            </td>
                            <td><?= !empty($member['joined_at']) ? date('d/m/Y H:i', strtotime($member['joined_at'])) : '' ?></td>
                            
                            <?php if ($isLeader): ?>
                                <td style="text-align: center;">
                                    <?php if ($member['student_id'] !== $userId): ?>
                                        <form method="POST" style="display: inline-block; margin-right: 5px;" onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này khỏi nhóm?');">
                                            <input type="hidden" name="action" value="kick_member">
                                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($member['student_id']) ?>">
                                            <button type="submit" style="padding: 3px 8px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.85em;">
                                                Xóa
                                            </button>
                                        </form>

                                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc muốn nhượng quyền Trưởng nhóm cho người này?');">
                                            <input type="hidden" name="action" value="transfer_leader">
                                            <input type="hidden" name="new_leader_id" value="<?= htmlspecialchars($member['student_id']) ?>">
                                            <button type="submit" style="padding: 3px 8px; background: #17a2b8; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.85em;">
                                                Đổi làm trưởng nhóm
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #888;">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </fieldset>
</div>