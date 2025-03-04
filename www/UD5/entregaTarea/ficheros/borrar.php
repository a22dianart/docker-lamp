<?php
require_once('../login/sesiones.php');
require_once('../modelo/mysqli.php');
require_once('../modelo/pdo.php');
require_once('../modelo/FicherosDBImp.php');


$status = 'error';
$messages = array();
$id_tarea = 0;

if (!empty($_GET))
{
    $id = $_GET['id'];
    $ficherosDB = new FicherosDBImp();
    $archivo =$ficherosDB->buscaFichero($id);
    if (!empty($id) && $archivo)
    {
        $id_tarea = $archivo->getTarea()->getId();
        if (checkAdmin() || esPropietarioTarea($_SESSION['usuario']['id'], $archivo->getTarea()->getId()))
        {
            $ruta = '../' . $archivo->getFile();
            $borrado = borrarArchivo($ruta);
            if ($borrado) $borrado = $ficherosDB->borraFichero($archivo->getId());

            if ($borrado)
            {
                $status = 'success';
                array_push($messages, 'Archivo borrado correctamente.');
            }
            else
            {
                array_push($messages, 'No se pudo borrar el archivo.');
            }
        }
        else
        {
            array_push($messages, 'No tienes permisos sobre este archivo.');
        }
    }
    else
    {
        array_push($messages, 'No se pudo recuperar la información del archivo.');
    }
}
else
{
    array_push($messages, 'Debes acceder a través del listado de tareas.');
}


$_SESSION['status'] = $status;
$_SESSION['messages'] = $messages;
header("Location: ../tareas/verTarea.php?id=" . $id_tarea);

function borrarArchivo($archivo)
{
    return (file_exists($archivo) && unlink($archivo));
}

