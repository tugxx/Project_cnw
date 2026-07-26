<?php
function renderButton($text, $type = 'submit', $customStyle = '') {
    echo "
    <button type='{$type}' 
            style='width: 100%; background-color: #2563eb; color: #ffffff; font-weight: 600; padding: 11px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; transition: background-color 0.2s; {$customStyle}'
            onmouseover='this.style.backgroundColor=\"#1d4ed8\"'
            onmouseout='this.style.backgroundColor=\"#2563eb\"'>
        {$text}
    </button>";
}

?>