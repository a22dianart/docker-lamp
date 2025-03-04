<?php
require_once('../login/sesiones.php');

function getFileIcon($filename)
{
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $fileTypeIcons = [
        'pdf' => 'bi-file-earmark-pdf text-danger',
        'jpg' => 'bi-file-earmark-image text-primary',
        'jpeg' => 'bi-file-earmark-image text-primary',
        'png' => 'bi-file-earmark-image text-primary',
    ];

    return $fileTypeIcons[$extension] ?? 'bi-file-earmark text-secondary';
}
?>

<?php include_once('../vista/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <?php include_once('../vista/menu.php'); ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="container justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Tarea</h2>
                <?php include_once('../vista/erroresSession.php'); ?>
            </div>

            <div class="container justify-content-between">
                <?php
                require_once('../modelo/mysqli.php');
                if (!empty($_GET)) {
                    $id = $_GET['id'];
                    if (!empty($id)) {
                        if (checkAdmin() || esPropietarioTarea($_SESSION['usuario']['id'], $id)) {
                            $tarea = buscaTarea($id);
                            $usuario = buscaUsuarioMysqli($tarea->getUsuario()->getId());
                            if ($tarea) {
                                $titulo = $tarea->getTitulo();
                                $descripcion = $tarea->getDescripcion();
                                $estado = $tarea->getEstado();
                                $id_usuario = $tarea->getUsuario()->getId();

                                require_once('../modelo/pdo.php');
                                require_once('../modelo/FicherosDBImp.php');
                                $ficherosDB = new FicherosDBImp();
                                $ficheros = $ficherosDB->listaFicheros($id);
                ?>

                                <div class="container my-4">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">Detalles</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <strong>Titulo:</strong>
                                                </div>
                                                <div class="col-md-9">
                                                    <p class="mb-0"><?php echo $tarea->getTitulo(); ?></p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <strong>Descripción:</strong>
                                                </div>
                                                <div class="col-md-9">
                                                    <p class="mb-0"><?php echo $tarea->getDescripcion(); ?></p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <strong>Estado:</strong>
                                                </div>
                                                <div class="col-md-9">
                                                    <p class="mb-0"><?php echo $tarea->getEstado(); ?></p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <strong>Usuario:</strong>
                                                </div>
                                                <div class="col-md-9">
                                                    <p class="mb-0"><?php echo $usuario->getUsername(); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mt-4">
                                        <div class="card-header bg-secondary text-white">
                                            <h5 class="mb-0">Archivos Adjuntos</h5>
                                        </div>
                                        <div class="container my-4">
                                            <div class="row g-3">
                                                <?php
                                                foreach ($ficheros as $fichero) {
                                                    if (is_object($fichero) && $fichero->getFile() && $fichero->getNombre() && $fichero->getDescripcion()) {
                                                ?>
                                                        <div class="col-md-4">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h5 class="card-title">
                                                                        <i class="<?= getFileIcon($fichero->getFile()); ?> me-3 fs-4"></i>
                                                                        <?php echo htmlspecialchars($fichero->getNombre(), ENT_QUOTES, 'UTF-8'); ?>
                                                                    </h5>
                                                                    <p class="card-text text-muted text-truncate">
                                                                        <?php echo htmlspecialchars($fichero->getDescripcion(), ENT_QUOTES, 'UTF-8'); ?>
                                                                    </p>
                                                                    <div class="d-flex gap-2">
                                                                        <a href="../<?php echo htmlspecialchars($fichero->getFile(), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-primary" download>Descargar</a>
                                                                        <a href="../ficheros/borrar.php?id=<?php echo $fichero->getId(); ?>" class="btn btn-sm btn-outline-danger">Eliminar</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php
                                                    }
                                                }

                                            
                                                ?>

                                                <div class="col-md-4">
                                                    <a href="../ficheros/subidaFichForm.php?id=<?php echo $id; ?>" class="text-decoration-none">
                                                        <div class="card text-center border-dashed h-100">
                                                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                                                <h5 class="card-title text-primary">
                                                                    <i class="bi bi-plus-circle" style="font-size: 2rem;"></i>
                                                                </h5>
                                                                <p class="card-text text-muted">Añadir nuevo archivo</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            } else {
                                echo '<div class="alert alert-danger" role="alert">No se pudo recuperar la información de la tarea.</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger" role="alert">No tienes permisos sobre esta tarea.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">No se pudo recuperar la información de la tarea.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger" role="alert">Debes acceder a través del listado de tareas.</div>';
                }
                ?>

            </div>
        </main>
    </div>
</div>

<?php include_once('../vista/footer.php'); ?>

</body>
</html>
