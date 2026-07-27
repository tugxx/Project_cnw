<?php
function renderButton($text, $type = 'submit', $customStyle = '', $fullWidth = false) {
    $widthStyle = $fullWidth ? 'width: 100%;' : 'width: auto; white-space: nowrap;';
    
    echo "
    <button type='{$type}' 
            style='{$widthStyle} display: inline-flex; align-items: center; justify-content: center; gap: 6px; background-color: #2563eb; color: #ffffff; font-weight: 500; padding: 9px 20px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; transition: background-color 0.2s; {$customStyle}'
            onmouseover='this.style.backgroundColor=\"#1d4ed8\"'
            onmouseout='this.style.backgroundColor=\"#2563eb\"'>
        {$text}
    </button>";
}

?>