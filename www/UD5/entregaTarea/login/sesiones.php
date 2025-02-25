<?php
session_start();

$raiz = $_ENV['RAIZ_UD4'];

if (!checkSession()) {	
    redirectLogin();
}

function checkSession()
{
    return isset($_SESSION['usuario']);
}

function redirectLogin()
{
    global $raiz;
    header("Location: $raiz/login/login.php?redirect=true");
    exit();
}

function checkAdmin()
{
    global $raiz;
    return (checkSession() && $_SESSION['usuario']['rol'] == 1);
}

function redirectIndex()
{
    global $raiz;
    header("Location: $raiz/index.php?redirect=true");
    exit();
}