<?php
function renderButton($text, $type = 'submit', $customStyle = '', $fullWidth = false, $hoverBg = '#1d4ed8', $normalBg = '#2563eb', $extraAttrs = '') {
    $widthStyle = $fullWidth ? 'width: 100%;' : 'width: auto; white-space: nowrap;';

    $isDisabled = strpos($extraAttrs, 'disabled') !== false;
    $hoverEvents = $isDisabled ? '' : "onmouseover='this.style.backgroundColor=\"{$hoverBg}\"' onmouseout='this.style.backgroundColor=\"{$normalBg}\"'";

    echo "
    <button type='{$type}' 
            style='{$widthStyle} display: inline-flex; align-items: center; justify-content: center; background-color: {$normalBg}; color: #ffffff; font-weight: 500; padding: 9px 20px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; transition: background-color 0.2s; box-sizing: border-box; {$customStyle}'
            {$hoverEvents}
            {$extraAttrs}>
        <span style='display: inline-flex; align-items: center; justify-content: center; gap: 6px; line-height: 1; text-align: center; width: 100%;'>
            {$text}
        </span>
    </button>";
}
?>