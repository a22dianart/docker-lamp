<?php

declare(strict_types=1);
require_once 'flight/Flight.php';

$db = new PDO('mysql:host=db;dbname=agenda;charset=utf8mb4', 'agenda', 'agenda');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//middleware
Flight::before('start', function () use ($db) {
    $route = Flight::request()->url;
    if (in_array($route, ['/register', '/login', '/'])) return;
    $token = Flight::request()->getHeader('X-Token');
    if (!$token) {
        Flight::halt(401, 'Token no proporcionado');
    }
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        Flight::halt(401, 'Token inválido');
    }

    Flight::set('user', $user);
});

Flight::route('GET /', function () {
    echo 'API de Agenda funcionando';
});

//rexistrarse
Flight::route('POST /register', function () use ($db) {
    $data = Flight::request()->data;
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([
            $data['nombre'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        Flight::json(['message' => 'Usuario registrado']);
    } catch (PDOException $e) {
        Flight::halt(400, 'Error al registrar usuario');
    }
});

//loggearse
Flight::route('POST /login', function () use ($db) {
    $data = Flight::request()->data;

    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data['password'], $user['password'])) {
        Flight::halt(401, 'Credenciales inválidas');
    }

    $token = bin2hex(random_bytes(32));
    $stmt = $db->prepare("UPDATE usuarios SET token = ? WHERE id = ?");
    $stmt->execute([$token, $user['id']]);

    Flight::json(['token' => $token]);
});

//listar contactos ou buscar por id contacto
Flight::route('GET /contactos(/@id)', function ($id = null) use ($db) {
    $user = Flight::get('user');

    if ($id) {

        $stmt = $db->prepare("SELECT * FROM contactos WHERE id = ?");
        $stmt->execute([$id]);
        $contacto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contacto) {
            Flight::halt(404, 'Contacto no encontrado');
        }

        if ((int)$contacto['usuario_id'] !== (int)$user['id']) {
            Flight::halt(403, 'No tienes permiso para ver este contacto');
        }

        Flight::json($contacto);
    } else {

        $stmt = $db->prepare("SELECT * FROM contactos WHERE usuario_id = ?");
        $stmt->execute([$user['id']]);
        Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
});


//engadir novo contacto
Flight::route('POST /contactos', function () use ($db) {

    $user = Flight::get('user');

    $data = Flight::request()->data;

    $stmt = $db->prepare("INSERT INTO contactos (nombre, telefono, email, usuario_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['nombre'],
        $data['telefono'],
        $data['email'],
        $user['id']
    ]);

    Flight::json(['message' => 'Contacto agregado']);
});

//modificar contacto
Flight::route('PUT /contactos/@id', function ($id) use ($db) {
    $user = Flight::get('user');
    $data = Flight::request()->data;


    $stmt = $db->prepare("SELECT * FROM contactos WHERE id = ?");
    $stmt->execute([$id]);
    $contacto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contacto) {
        Flight::halt(404, 'Contacto no encontrado');
    }

    if ($contacto['usuario_id'] != $user['id']) {
        Flight::halt(403, 'No tienes permiso para editar este contacto');
    }


    $stmt = $db->prepare("UPDATE contactos SET nombre = ?, telefono = ?, email = ? WHERE id = ?");
    $stmt->execute([
        $data['nombre'],
        $data['telefono'],
        $data['email'],
        $id
    ]);

    Flight::json(['message' => 'Contacto actualizado']);
});


//borrar contacto
Flight::route('DELETE /contactos/@id', function ($id) use ($db) {
    $user = Flight::get('user');

    $stmt = $db->prepare("SELECT * FROM contactos WHERE id = ?");
    $stmt->execute([$id]);
    $contacto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contacto) {
        Flight::halt(404, 'Contacto no encontrado');
    }



    if ((int)$contacto['usuario_id'] !== (int)$user['id']) {
        Flight::halt(403, 'No tienes permiso para borrar este contacto');
    }


    $stmt = $db->prepare("DELETE FROM contactos WHERE id = ?");
    $stmt->execute([$id]);

    Flight::json(['message' => 'Contacto eliminado']);
});


Flight::start();