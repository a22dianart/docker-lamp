<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tema = $_POST['tema'] ?? 'light';
    
    if (!in_array($tema, ['light', 'dark', 'auto'])) {
        $tema = 'light';
    }
    
    
    setcookie("tema", $tema, time() + (86400 * 30), "/"); //30dias
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/UD4/entregaTarea/index.php'));
exit;
?>
