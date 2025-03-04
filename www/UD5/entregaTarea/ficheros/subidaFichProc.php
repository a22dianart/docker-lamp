<?php

require_once('../login/sesiones.php');
require_once('../modelo/FicherosDBImp.php');
require_once('../modelo/mysqli.php');

$directorioDestino = "files/"; 
$maxFileSize = 20 * 1024 * 1024; 
$tipoPermitido = ['image/jpeg', 'image/png', 'application/pdf']; 

$location = '../tareas.php';
$response = 'error';
$messages = array();

$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombreArchivo = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $archivo = $_FILES['archivo'] ?? null;
    $id_tarea = $_POST['id_tarea'] ?? '';
    $location = 'subidaFichForm.php?id=' . $id_tarea;


    if (empty($nombreArchivo) || empty($descripcion) || !$archivo || empty($id_tarea)) {
        array_push($messages, "Todos los campos son obligatorios.");
        $error = true;
    }

 
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        array_push($messages, "Error al subir el archivo.");
        $error = true;
    }

 
    if ($archivo['size'] > $maxFileSize) {
        array_push($messages, "Error: El archivo excede el tamaño máximo permitido de 20 MB.");
        $error = true;
    }


    if (!in_array($archivo['type'], $tipoPermitido)) {
        array_push($messages, "Tipo de archivo no permitido.");
        $error = true;
    }

  
    $codigoAleatorio = bin2hex(random_bytes(8)); 
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreFinal = $codigoAleatorio . '.' . $extension;
    $rutaDestino = $directorioDestino . $nombreFinal;


    if (!is_writable('../' . $directorioDestino)) {
        array_push($messages, "No hay permisos de escritura en la carpeta destino.");
        $error = true;
    }


    if (!$error) {
        if (move_uploaded_file($archivo['tmp_name'], '../' . $rutaDestino)) {
            try {
                require_once('../modelo/pdo.php');

                $ficherosDB = new FicherosDBImp();
                $fichero = new Fichero($id_tarea, $nombreArchivo, $rutaDestino, $descripcion, buscaTarea($id_tarea));
                $resultado = $ficherosDB->nuevoFichero($fichero);

                if ($resultado) {
                    $response = 'success';
                    array_push($messages, "Archivo subido correctamente.");
                    $location = '../tareas/verTarea.php?id=' . $id_tarea;
                } else {
                    array_push($messages, "Error al guardar el archivo en la base de datos.");
                }
            } catch (Exception $e) {
                array_push($messages, "Error: " . $e->getMessage());
            }
        } else {
            array_push($messages, "Error al mover el archivo al directorio de destino.");
        }
    }
}

$_SESSION['status'] = $response;
$_SESSION['messages'] = $messages;

header("Location: " . $location);
exit();
?>
