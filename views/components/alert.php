<?php
function renderAlert($message, $type = 'error') {
    if (empty($message)) return;
    
    if ($type === 'error') {
        $bgColor = '#fef2f2';
        $borderColor = '#ef4444';
        $textColor = '#b91c1c';
        $icon = '<i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>';
    } else {
        $bgColor = '#f0fdf4';
        $borderColor = '#22c55e';
        $textColor = '#15803d';
        $icon = '<i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>';
    }

    echo "
    <div style='background: {$bgColor}; border-left: 4px solid {$borderColor}; color: {$textColor}; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; display: flex; align-items: center;'>
        {$icon}
        <div>{$message}</div>
    </div>";
}