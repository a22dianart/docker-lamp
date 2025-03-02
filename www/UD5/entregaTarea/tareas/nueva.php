<?php
require_once('../login/sesiones.php');
require_once('../utils.php');
require_once('../modelo/mysqli.php');
require_once('../modelo/pdo.php');

$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$estado = $_POST['estado'];
$id_usuario = $_POST['id_usuario'];

$response = 'error';
$messages = array();
$location = 'nuevaForm.php';

$error = false;

if (!checkAdmin()) $id_usuario = $_SESSION['usuario']['id'];

//verificar titulo
if (!validarCampoTexto($titulo))
{
    $error = true;
    array_push($messages, 'El campo titulo es obligatorio y debe contener al menos 3 caracteres.');
}
//verificar descripcion
if (!validarCampoTexto($descripcion))
{
    $error = true;
    array_push($messages, 'El campo descripcion es obligatorio y debe contener al menos 3 caracteres.');
}
//verificar estado
if (!validarCampoTexto($estado))
{
    $error = true;
    array_push($messages, 'El campo estado es obligatorio.');
}
//verificar id_usuario
if (!esNumeroValido($id_usuario))
{
    $error = true;
    array_push($messages, 'El campo usuario es obligatorio.');
}

if (!$error)
{

    $tarea = new Tarea(1, filtraCampo($titulo), filtraCampo($descripcion), filtraCampo($estado), buscaUsuario($id_usuario));


    $resultado = nuevaTarea($tarea);
    if ($resultado[0])
    {
        $response = 'success';
        array_push($messages, 'Tarea guardada correctamente.');
    }
    else
    {
        $response = 'error';
        array_push($messages, 'Ocurrió un error guardando la tarea: ' . $resultado[1] . '.');
    }
}

$_SESSION['status'] = $response;
$_SESSION['messages'] = $messages;

header("Location: $location");