<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"])) {
    header("Location: /UD4/entregaTarea/login.php"); 
    exit();
}

$rol = $_SESSION['rol']; 

$tema = 'light';
if (isset($_COOKIE['tema'])) {
    $tema = $_COOKIE['tema'];
}

$navClass = "col-md-3 col-lg-2 d-md-block sidebar";
if ($tema === 'light') {
    $navClass .= " bg-light";
}
?>
<nav class="<?= $navClass ?>">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="/UD4/entregaTarea/index.php">
                    Home
                </a>
            </li>
            <?php if ($rol == 1): ?>
            <li class="nav-item">
                <a class="nav-link" href="/UD4/entregaTarea/init.php">
                    Inicializar 
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/UD4/entregaTarea/usuarios/usuarios.php">
                    Lista de usuarios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/UD4/entregaTarea/usuarios/nuevoUsuarioForm.php">
                    Nuevo usuario 
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/UD4/entregaTarea/tareas/buscaTareas.php">
                   Buscador de tareas
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="/UD4/entregaTarea/tareas/nuevaForm.php">
                    Nueva tarea
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/UD4/entregaTarea/tareas/tareas.php">
                    Lista de tareas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/UD4/entregaTarea/logout.php">
                    Salir
                </a>
            </li>
            <li class="nav-item mt-3">
                <form method="POST" action="/UD4/entregaTarea/tema.php" class="w-50 ms-3">
                    <select id="tema" name="tema" class="form-select mb-2" aria-label="Selector de tema">
                        <option value="light" <?= $tema === 'light' ? 'selected' : '' ?>>Claro</option>
                        <option value="dark" <?= $tema === 'dark' ? 'selected' : '' ?>>Oscuro</option>
                        <option value="auto" <?= $tema === 'auto' ? 'selected' : '' ?>>Automático</option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Aplicar</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
