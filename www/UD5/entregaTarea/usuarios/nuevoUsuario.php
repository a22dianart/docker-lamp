<?php
require_once('../login/sesiones.php');
require_once('../modelo/Usuario.php');
require_once('../modelo/pdo.php');

if (!checkAdmin()) redirectIndex();
    
require_once('../utils.php');
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$username = $_POST['username'];
$contrasena = $_POST['contrasena'];
$rol = $_POST['rol'];
$error = false;

$message = 'Error creando el usuario.';

if (!validarCampoTexto($nombre))
{
    $error = true;
    $message = 'El campo nombre es obligatorio y debe contener al menos 3 caracteres.';
}
if (!$error && !validarCampoTexto($apellidos))
{
    $error = true;
    $message = 'El campo apellidos es obligatorio y debe contener al menos 3 caracteres.';
}
if (!$error && !validarCampoTexto($username))
{
    $error = true;
    $message = 'El campo username es obligatorio y debe contener al menos 3 caracteres.';
}
if (!$error && !validaContrasena($contrasena))
{
    $error = true;
    $message = 'El campo contraseña es obligatorio y debe ser compleja.';
}
if (!$error)
{
  
    $resultado = nuevoUsuario(new Usuario(1, filtraCampo($username), filtraCampo($nombre),  filtraCampo($apellidos) , $contrasena, $rol ));
    if ($resultado[0])
    {
        $message = 'Usuario guardado correctamente.';
    }
    else
    {
        $message = 'Ocurrió un error guardando el usuario: ' . $resultado[1];
        $error = true;
    }
}

$status = $error ? 'error' : 'success';
header("Location: nuevoUsuarioForm.php?status=$status&message=$message");

