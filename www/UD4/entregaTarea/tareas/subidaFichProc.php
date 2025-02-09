<?php
require_once('../modelo/pdo.php');
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit();
}


$id_tarea = $_POST['id_tarea'] ?? null;
$nombre = $_POST['name'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$file = $_FILES['file'] ?? null;


if (!$id_tarea || !$nombre || !$descripcion || !$file) {
    die("Error: Todos los campos son obligatorios.");
}

$allowed_extensions = ['jpg', 'png', 'pdf'];
$max_size = 20 * 1024 * 1024; 
$upload_dir = '../files/';

if ($file['error'] === UPLOAD_ERR_OK) {
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_extensions)) {
        die("Error: Formato de archivo no permitido.");
    }

    if ($file['size'] > $max_size) {
        die("Error: El archivo excede los 20MB.");
    }


    $new_file_name = uniqid('file_', true) . '.' . $file_ext;
    $file_path = $upload_dir . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $stmt = $pdo->prepare("INSERT INTO ficheros (nombre, file, descripcion, id_tarea) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $file_path, $descripcion, $id_tarea]);

        header("Location: tarea.php?id=$id_tarea&success=1");
        exit();
    } else {
        die("Error al subir el archivo.");
    }
} else {
    die("Error en la subida del archivo.");
}
?>
