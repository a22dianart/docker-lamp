<?php
require_once('../login/sesiones.php');
if (!checkAdmin()) redirectIndex();

require_once('../utils.php');
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$username = $_POST['username'];
$contrasena = $_POST['contrasena'];
$rol = $_POST['rol'];
$error = false;

$message = 'Error creando el usuario.';

//verificar nombre
if (!validarCampoTexto($nombre))
{
    $error = true;
    $message = 'El campo nombre es obligatorio y debe contener al menos 3 caracteres.';
}
//verificar apellidos
if (!$error && !validarCampoTexto($apellidos))
{
    $error = true;
    $message = 'El campo apellidos es obligatorio y debe contener al menos 3 caracteres.';
}
//verificar username
if (!$error && !validarCampoTexto($username))
{
    $error = true;
    $message = 'El campo username es obligatorio y debe contener al menos 3 caracteres.';
}
//verificar contrasena
if (!$error && !empty($contrasena) && !validaContrasena($contrasena))
{
    $error = true;
    $message = 'La contraseña debe ser compleja.';
}
if (!$error)
{
    require_once('../modelo/pdo.php');
    if (empty($contrasena)) $contrasena = null;
    $resultado = actualizaUsuario($id, filtraCampo($nombre), filtraCampo($apellidos), filtraCampo($username), $contrasena, $rol);
    if ($resultado[0])
    {
        $message = 'Usuario actualizado correctamente.';
    }
    else
    {
        $message = 'Ocurrió un error actualizando el usuario: ' . $resultado[1];
        $error = true;
    }
}

$status = $error ? 'error' : 'success';
header("Location: editaUsuarioForm.php?id=$id&status=$status&message=$message");
