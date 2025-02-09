<?php

$tema = 'light';
if (isset($_COOKIE['tema'])) {
    $tema = $_COOKIE['tema'];
}

$headerBgClass = 'bg-primary';
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= htmlspecialchars($tema) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <header class="<?= $headerBgClass ?> text-white text-center py-3">
        <h1>Gestión de tareas</h1>
        <p>Solución tarea unidad 4</p>
    </header>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
