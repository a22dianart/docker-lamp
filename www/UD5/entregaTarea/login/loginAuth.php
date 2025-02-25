<?php
session_start();

require_once('../modelo/pdo.php');


function comprobarUsuario($nombre, $pass, $conPDO)
{
    $usuarioBD = buscaUsername($nombre);

    //Comprobamos que haya usuario
    if ($usuarioBD)
    {
        $passBD=$usuarioBD['contrasena'];
        //Comprobamos la contraseña introducida
        if (password_verify($pass, $passBD))
        {
            $usuario['username']=$nombre;
            $usuario['rol']=$usuarioBD['rol'];
            $usuario['id']=$usuarioBD['id'];
            return $usuario;
        }
        else
        {
            return null;
        }
    }
    else
    {
        return null;
    }
}

//Comprobar si se reciben los datos
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $usuario = $_POST["username"];
    $pass = $_POST["pass"];

    // >>>> IMPORTANTE: estas líneas están solo para permitir el primer acceso, cuando no está creada la base de datos. Solo se deben descomentar para la primera conexión.
    if ($usuario == 'admintest' && $pass == 'test123')
    {
        $user['username']='admintest';
        $user['rol']=1;
        $user['id']=0;
        $_SESSION['usuario'] = $user;
        //Redirigimos a index.php
        header('Location: ../index.php');
        exit();
    }

    if (empty($usuario) || empty($pass))
    {
        header('Location: ./login.php?error=true&message=Los campos del formulario son obligatorios.');
    }

    $conPDO = conectaPDO();
    if (is_string($conPDO))
    {
        header('Location: ./login.php?error=true&message=' . $conPDO);
    }
    $user = comprobarUsuario($usuario, $pass, $conPDO);
    if(!$user)
    {
        header('Location: ./login.php?error=true');
    }
    elseif (is_string($user))
    {
        header('Location: ./login.php?error=true&message=' . $user);
    }
    else
    {
        $_SESSION['usuario'] = $user;
        //Redirigimos a index.php
        header('Location: ../index.php');
    }
}