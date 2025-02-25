<?php

require_once('../login/sesiones.php');

$directorioDestino = "files/"; // Carpeta donde se guardarán los archivos --> revisar permisos si da error
$maxFileSize = 20 * 1024 * 1024; // Tamaño máximo del archivo en bytes (20 MB)
$tipoPermitido = ['image/jpeg', 'image/png', 'application/pdf']; //Tipos de ficheros permitidos

$location = '../tareas.php';
$response = 'error';
$messages = array();

$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{

    $nombreArchivo = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $archivo = $_FILES['archivo'] ?? null;
    $id_tarea = $_POST['id_tarea'] ?? '';
    $location = 'subidaFichForm.php?id=' . $id_tarea;

    // Validar campos no vacíos
    if (empty($nombreArchivo) || empty($descripcion) || !$archivo || empty($id_tarea))
    {
        array_push($messages, "Todos los campos son obligatorios.");
        $error = true;
    }

    // Validar archivo subido
    if ($archivo['error'] !== UPLOAD_ERR_OK)
    {
        array_push($messages, "Error al subir el archivo.");
        $error = true;
    }

    // Validar tamaño del archivo
    if ($archivo['size'] > $maxFileSize)
    {
        array_push($messages, "Error: El archivo excede el tamaño máximo permitido de 20 MB.");
        $error = true;
    }

    // Validar tipo de archivo (opcional, aquí se permiten imágenes y PDF)
    if (!in_array($archivo['type'], $tipoPermitido))
    {
        array_push($messages, "Todos los campos son obligatorios.");
        $error = true;
    }

    $codigoAleatorio = bin2hex(random_bytes(8)); // 16 caracteres alfanuméricos

    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);

    $nombreFinal = $codigoAleatorio . '.' . $extension;

    $rutaDestino = $directorioDestino . $nombreFinal;

    if (!is_writable('../' . $directorioDestino))
    {
        array_push($messages, "No hay permisos de escritura en la carpeta destino.");
        $error = true;
    }
    
    // Mover el archivo al directorio de destino
    if (!$error)
    {
        if (move_uploaded_file($archivo['tmp_name'], '../' . $rutaDestino))
        {
            require_once('../modelo/pdo.php');

            $resultado = nuevoFichero($rutaDestino, $nombreArchivo, $descripcion, $id_tarea);

            if ($resultado[0])
            {
                $response = 'success';
                array_push($messages, 'Archivo subido correctamente.');
                $location = '../tareas/tarea.php?id=' . $id_tarea;
            }
            else
            {
                array_push($messages, 'Ocurrió un error guardando la tarea: ' . $resultado[1] . '.');
            }
        }
        else
        {
            array_push($messages, 'Error al guardar el archivo.');
        }
    }

}
else
{
    array_push($messages, 'Método de solicitud no válido.');
}

$_SESSION['status'] = $response;
$_SESSION['messages'] = $messages;
header("Location: " . $location);