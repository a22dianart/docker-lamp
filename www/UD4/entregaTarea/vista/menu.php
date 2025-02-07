
<?php

if (!isset($_SESSION["user"])) {
    header("Location: /UD4/entregaTarea/login.php"); 
    exit();
}

$rol = $_SESSION['rol']; 
?>

<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
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
                <select id="theme-selector" class="form-select w-50 ms-3">
                <option value="dark">Claro</option>
                    <option value="dark">Oscuro</option>
                    <option value="auto">Automático</option>
                </select>
            </li>
            <button class="btn btn-primary w-50 ms-3 mt-2 mb-2">Aplicar</button>
            
        </ul>
    </div>
</nav>