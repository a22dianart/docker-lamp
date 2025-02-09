<?php

require_once('../modelo/pdo.php');
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit();
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if (strpos($referer, 'tarea.php') === false) {
    header("Location: tarea.php");
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
                        <form action="subidaFichProc.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Descripcion</label>
                                <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Seleccionar archivo</label>
                                <input class="form-control" type="file" name="file" required>
                            </div>
                            <input type="hidden" name="id_tarea" value="<?php echo $_GET['id_tarea'] ?? ''; ?>">
                            <button type="submit" class="btn btn-primary">Subir archivo</button>
                        </form>
    
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include_once('../vista/footer.php'); ?>
    
</body>
</html>
