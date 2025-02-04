<?php
session_start(); 
require 'modelo/pdo.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Todos os campos son obrigatorios");
        exit();
    }

    try {
        $con = conectaPDO(); 
        $stmt = $con->prepare("SELECT id, username, nombre, apellidos, contrasena, rol FROM usuarios WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario["contrasena"])) {
            $_SESSION["user"] = $usuario["username"];
            $_SESSION["user_id"] = $usuario["id"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["apellidos"] = $usuario["apellidos"];
            $_SESSION["rol"] = $usuario["rol"];

            header("Location: index.php"); //redirixir
            exit();
        } else {
            header("Location: login.php?error=Usuario ou contrasinal incorrecto");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: login.php?error=Erro na conexión á base de datos");
        exit();
    }
}
?>
