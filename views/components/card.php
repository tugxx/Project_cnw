<?php

function renderProfileItem($label, $value, $icon = '') {
    $iconHtml = !empty($icon) ? "<i class='{$icon}' style='color: #9ca3af; width: 18px;'></i> " : '';
    $displayValue = (!empty($value) && $value !== '---') ? htmlspecialchars($value) : '<span style="color: #9ca3af; font-style: italic;">Chưa cập nhật</span>';

    echo "
    <div style='padding: 12px 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #f3f4f6;'>
        <div style='font-size: 12px; font-weight: 500; color: #6b7280; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;'>
            {$iconHtml} {$label}
        </div>
        <div style='font-size: 14px; font-weight: 600; color: #111827;'>
            {$displayValue}
        </div>
    </div>";
}

?>