<?php
require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/Tarea.php';
require_once __DIR__ . '/Fichero.php';

function conectaPDO()
{
    $servername = $_ENV['DATABASE_HOST'];
    $username   = $_ENV['DATABASE_USER'];
    $password   = $_ENV['DATABASE_PASSWORD'];
    $dbname     = $_ENV['DATABASE_NAME'];

    $conPDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conPDO;
}

function listaUsuarios()
{
    try {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT id, username, nombre, apellidos, rol, contrasena FROM usuarios');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $resultados = $stmt->fetchAll();

        $usuarios = [];
        foreach ($resultados as $row) {
            $usuarios[] = new Usuario(
                (int)$row['id'],
                $row['username'],
                $row['nombre'],
                $row['apellidos'],
                $row['contrasena'],
                $row['rol']
            );
        }
        return [true, $usuarios];
    }
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
}

function nuevoUsuario(Usuario $usuario) //insert de Usuario
{
    try {
        $con = conectaPDO();
        $stmt = $con->prepare("INSERT INTO usuarios (nombre, apellidos, username, rol, contrasena) 
                               VALUES (:nombre, :apellidos, :username, :rol, :contrasena)");

        $hasheado = password_hash($usuario->getContrasena(), PASSWORD_DEFAULT);

        $stmt->execute([
            ':nombre' => $usuario->getNombre(),
            ':apellidos' => $usuario->getApellidos(),
            ':username' => $usuario->getUsername(),
            ':rol' => $usuario->getRol(),
            ':contrasena' => $hasheado
        ]);

        return [true, null];
    } 
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    } 
    finally {
        $con = null;
    }
}


function actualizaUsuario($id, $nombre, $apellidos, $username, $contrasena, $rol)
{
    try {
        $con = conectaPDO();
        $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, username = :username, rol = :rol";
        if (isset($contrasena)) {
            $sql .= ', contrasena = :contrasena';
        }
        $sql .= ' WHERE id = :id';

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':rol', $rol);
        if (isset($contrasena)) {
            $hasheado = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt->bindParam(':contrasena', $hasheado);
        }
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->closeCursor();

        return [true, null];
    }
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
}

function borraUsuario(Usuario $usuario) //delete de Usuario
{
    try {
        $con = conectaPDO();
        $con->beginTransaction();

        $stmt = $con->prepare('DELETE FROM tareas WHERE id_usuario = :id');
        $stmt->execute([':id' => $usuario->getId()]);

        $stmt = $con->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $usuario->getId()]);

        return [$con->commit(), ''];
    }
    catch (PDOException $e) {
        $con->rollBack(); 
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
}


function buscaUsuario($id) //select de Usuario
{
    try {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT id, username, nombre, apellidos, rol, contrasena FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch();
            return new Usuario(
                (int)$row['id'],
                $row['username'],
                $row['nombre'],
                $row['apellidos'],
                $row['contrasena'],
                $row['rol']
            );
        } else {
            return null;
        }
    }
    catch (PDOException $e) {
        return null;
    }
    finally {
        $con = null;
    }
}

function buscaUsername($username)
{
    try {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT id, rol, contrasena FROM usuarios WHERE username = :username');
        $stmt->execute([':username' => $username]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() == 1) {
            return $stmt->fetch();
        } else {
            return null;
        }
    }
    catch (PDOException $e) {
        return null;
    }
    finally {
        $con = null;
    }
}
function listaTareasPDO($id_usuario, $estado)
{
    try {
        $con = conectaPDO();
        $sql = 'SELECT * FROM tareas WHERE id_usuario = :id_usuario';
        if (isset($estado)) {
            $sql .= " AND estado = :estado";
        }
        $stmt = $con->prepare($sql);
        $params = [':id_usuario' => $id_usuario];
        if (isset($estado)) {
            $params[':estado'] = $estado;
        }
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $tareas = [];
        while ($row = $stmt->fetch()) {
            $usuario = buscaUsuario($row['id_usuario']);
            
            if (!$usuario) {
                $usuario = new Usuario(0, "Usuario no encontrado", "", "", "", "");
            }

            $tareas[] = new Tarea(
                (int)$row['id'],
                $row['titulo'],
                $row['descripcion'],
                $row['estado'],
                $usuario
            );
        }
        return [true, $tareas];
    }
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
}


?>
