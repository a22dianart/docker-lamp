<?php

require_once('../login/sesiones.php');    
require_once('../modelo/mysqli.php');
require_once('../modelo/pdo.php');

$response = 'error';
$messages = array();

$location = 'tareas.php';

if (!empty($_GET))
{
    $id = $_GET['id'];
    if (!empty($id))
    {
        if (checkAdmin() || esPropietarioTarea(buscaUsuario($_SESSION['usuario']['id']), buscaTarea($id)))
        {
            $resultado = borraTarea(buscaTarea($id));
            if ($resultado[0])
            {
                $response = 'success';
                array_push($messages, 'Tarea borrada correctamente.');
            }
            else
            {
                array_push($messages, 'No se pudo borrar la tarea. ' . $resultado[1]);
            }
        }
        else
        {
            array_push($messages, 'No tienes permisos sobre esta tarea.');
        }
    }
    else
    {
        array_push($messages, 'No se pudo recuperar la información de la tarea.');
    }
}
else
{
    array_push($messages, 'Debes acceder a través del listado de tareas.');
}

$_SESSION['status'] = $response;
$_SESSION['messages'] = $messages;

header("Location: $location");


