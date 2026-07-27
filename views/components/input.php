<?php

function renderInput($name, $label, $type = 'text', $value = '', $error = '', $placeholder = '', $required = false, $showToggle = false) {
    $borderColor = !empty($error) ? '#ef4444' : '#d1d5db';
    $reqBadge = $required ? '<span style="color:#ef4444;">*</span>' : '';
    
    // $paddingRight = ($showToggle && $type === 'password') ? '40px' : '14px';

    echo "
    <div style='margin-bottom: 18px;'>
        <label for='{$name}' style='display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;'>
            {$label} {$reqBadge}
        </label>
        <div style='position: relative;'>
            <input type='{$type}' 
                   id='{$name}' 
                   name='{$name}' 
                   value='" . htmlspecialchars($value) . "' 
                   placeholder='{$placeholder}'
                   style='width: 100%; padding: 10px 14px; border: 1px solid {$borderColor}; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box; transition: all 0.2s;'
                   onfocus='this.style.borderColor=\"#2563eb\"; this.style.boxShadow=\"0 0 0 3px rgba(37,99,235,0.15)\"'
                   onblur='this.style.borderColor=\"{$borderColor}\"; this.style.boxShadow=\"none\"'>";
    
    if ($showToggle && $type === 'password') {
        echo "
        <button type='button' 
                onclick='
                    const input = document.getElementById(\"{$name}\");
                    const icon = this.querySelector(\"i\");
                    if (input.type === \"password\") {
                        input.type = \"text\";
                        icon.className = \"fa-solid fa-eye-slash\";
                    } else {
                        input.type = \"password\";
                        icon.className = \"fa-solid fa-eye\";
                    }
                '
                style='position: absolute; right: 12px; background: none; border: none; padding: 0; margin: 0; color: #9ca3af; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px;'
                onmouseover='this.style.color=\"#4b5563\"'
                onmouseout='this.style.color=\"#9ca3af\"'>
            <i class='fa-solid fa-eye'></i>
        </button>";
    }

    echo "</div>";

    if (!empty($error)) {
        echo "
        <p style='margin-top: 6px; font-size: 12px; color: #dc2626; display: flex; align-items: center; gap: 4px;'>
            <i class='fa-solid fa-triangle-exclamation'></i> " . htmlspecialchars($error) . "
        </p>";
    }
    
    echo "</div>";
}

?>