<?php

require_once('../login/sesiones.php');
require_once('../modelo/FicherosDBImp.php');
require_once('../modelo/mysqli.php');
require_once('../modelo/Fichero.php');
require_once('../modelo/pdo.php');

$directorioDestino = "files/"; 
$location = '../tareas.php';
$response = 'error';
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombreArchivo = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $archivo = $_FILES['archivo'] ?? null;
    $id_tarea = $_POST['id_tarea'] ?? '';
    $location = 'subidaFichForm.php?id=' . $id_tarea;

    $data = [
        'nombre' => $nombreArchivo,
        'file' => $archivo['name'] ?? '',
        'size' => $archivo['size'] ?? 0,
        'descripcion' => $descripcion,
        'tarea' => buscaTarea($id_tarea)
    ];

    
    $errors = Fichero::validateFields($data);

    if (!empty($errors)) {
        $messages = array_merge($messages, $errors);
    } elseif (!is_writable('../' . $directorioDestino)) {
        array_push($messages, "No hay permisos de escritura en la carpeta destino.");
    } elseif ($archivo['error'] !== UPLOAD_ERR_OK) {
        array_push($messages, "Error al subir el archivo.");
    } else {
    
        $codigoAleatorio = bin2hex(random_bytes(8));
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreFinal = $codigoAleatorio . '.' . $extension;
        $rutaDestino = $directorioDestino . $nombreFinal;

    
        if (move_uploaded_file($archivo['tmp_name'], '../' . $rutaDestino)) {
            try {
                $ficherosDB = new FicherosDBImp();
                $fichero = new Fichero(0, $nombreArchivo, $rutaDestino, $descripcion, $data['tarea']);
                $resultado = $ficherosDB->nuevoFichero($fichero); //Las vistas y controladores hacen uso de la interfaz y la clase (6.0)

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
