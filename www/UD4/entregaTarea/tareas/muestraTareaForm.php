<?php

require_once('../modelo/pdo.php');
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit();
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if (strpos($referer, 'tareas.php') === false) {
    header("Location: tareas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UD3. Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include_once('../vista/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include_once('../vista/menu.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="container justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Detalles de la tarea</h2>
                </div>

                <div class="container justify-content-between">
                    <div class="mb-5 w-50">
                        <?php
                        require_once('../modelo/mysqli.php');
                        if (!empty($_GET)) {
                            $id = $_GET['id'];
                            $tarea = buscaTarea($id);
                            if (!empty($id) && $tarea) {
                                $titulo = $tarea['titulo'];
                                $descripcion = $tarea['descripcion'];
                                $estado = $tarea['estado'];
                                $id_usuario = $tarea['id_usuario'];

                                // Llamamos a la función buscaUsuario para obtener los detalles del usuario
                                $usuario = buscaUsuario($id_usuario);
                                if ($usuario) {
                                    $nombre_usuario = $usuario['username'];
                                } else {
                                    $nombre_usuario = 'Usuario no encontrado';
                                }
                        ?>
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" value="<?php echo $titulo ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" rows="3" readonly><?php echo $descripcion ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <input type="text" class="form-control" id="estado" value="<?php echo $estado ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="usuario" value="<?php echo $nombre_usuario; ?>" readonly>
                            </div>

                            <a href="tareas.php" class="btn btn-primary">Volver al listado de tareas</a>
                        <?php
                            } else {
                                echo '<div class="alert alert-danger" role="alert">No se pudo recuperar la información de la tarea.</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Debes acceder a través del listado de tareas.</div>';
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include_once('../vista/footer.php'); ?>
    
</body>
</html>
