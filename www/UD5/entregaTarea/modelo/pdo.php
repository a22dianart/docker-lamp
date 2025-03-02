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

function nuevoUsuario($nombre, $apellidos, $username, $contrasena, $rol = 0)
{
    try {
        $con = conectaPDO();
        $stmt = $con->prepare("INSERT INTO usuarios (nombre, apellidos, username, rol, contrasena) VALUES (:nombre, :apellidos, :username, :rol, :contrasena)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':rol', $rol);
        $hasheado = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt->bindParam(':contrasena', $hasheado);
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

function borraUsuario($id)
{
    try {
        $con = conectaPDO();
        $con->beginTransaction();
        $stmt = $con->prepare('DELETE FROM tareas WHERE id_usuario = :id');
        $stmt->execute([':id' => $id]);
        $stmt = $con->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return [$con->commit(), ''];
    }
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
}

function buscaUsuario($id)
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
            
            // Si el usuario no existe, creamos uno con datos vacíos
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


function listaFicheros($id_tarea)
{
    try {
        $con = conectaPDO();
        $sql = 'SELECT * FROM ficheros WHERE id_tarea = :id_tarea';
        $stmt = $con->prepare($sql);
        $stmt->execute([':id_tarea' => $id_tarea]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $ficheros = [];
        while ($row = $stmt->fetch()) {
            $tarea = buscaTarea($row['id_tarea']);
            $ficheros[] = new Fichero(
                (int)$row['id'],
                $row['nombre'],
                $row['file'],
                $row['descripcion'],
                $tarea
            );
        }
        return [true, $ficheros];
    }
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
}


function nuevoFichero($file, $nombre, $descripcion, $idTarea)
{
    try
    {
        $con = conectaPDO();
        $stmt = $con->prepare("INSERT INTO ficheros (nombre, file, descripcion, id_tarea) VALUES (:nombre, :file, :descripcion, :idTarea)");
        $stmt->bindParam(':file', $file);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':idTarea', $idTarea);
        $stmt->execute();
        
        $stmt->closeCursor();

        return [true, null];
    }
    catch (PDOExcetion $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        $con = null;
    }
}


function buscaFichero($id)
{
    try {
        $con = conectaPDO();
        $stmt = $con->prepare("SELECT * FROM ficheros WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch();
            $tarea = buscaTarea($row['id_tarea']);
            return new Fichero(
                (int)$row['id'],
                $row['nombre'],
                $row['file'],
                $row['descripcion'],
                $tarea
            );
        }
        return null;
    }
    catch (PDOException $e) {
        return null;
    }
    finally {
        $con = null;
    }
}

function borraFichero($id)
{
    try {
        $con = conectaPDO();
        $stmt = $con->prepare("DELETE FROM ficheros WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $stmt->closeCursor();
        return true;
    }
    catch (PDOException $e) {
        return false;
    }
    finally {
        $con = null;
    }
}
?>
