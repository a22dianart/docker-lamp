<?php
require_once('../login/sesiones.php');
require_once('../utils.php');

$id = $_POST['id'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$estado = $_POST['estado'];
$id_usuario = $_POST['id_usuario'];

$response = 'error';
$messages = array();

$error = false;

$location = 'editaTareaForm.php?id=' . $id;

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
    require_once('../modelo/mysqli.php');
    $resultado = actualizaTarea($id, filtraCampo($titulo), filtraCampo($descripcion), filtraCampo($estado), $id_usuario);
    if ($resultado[0])
    {
        $response = 'success';
        array_push($messages, 'Tarea actualizada correctamente.');
        $location = 'tareas.php';
    }
    else
    {
        $response = 'error';
        array_push($messages, 'Ocurrió un error actualizando la tarea: ' . $resultado[1] . '.');
    }
}

$_SESSION['status'] = $response;
$_SESSION['messages'] = $messages;

header("Location: $location");
                   