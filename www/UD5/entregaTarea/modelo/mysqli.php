<?php
require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/Tarea.php';
require_once __DIR__ . '/Fichero.php';


function conecta($host, $user, $pass, $db)
{
    $conexion = new mysqli($host, $user, $pass, $db);
    return $conexion;
}

function conectaTareas()
{
    $host = $_ENV['DATABASE_HOST'];
    $user = $_ENV['DATABASE_USER'];
    $pass = $_ENV['DATABASE_PASSWORD'];
    $name = $_ENV['DATABASE_NAME'];
    return conecta($host, $user, $pass, $name);
}

function cerrarConexion($conexion)
{
    if (isset($conexion) && $conexion->connect_errno === 0) {
        $conexion->close();
    }
}

function creaDB()
{
    try {
        $host = $_ENV['DATABASE_HOST'];
        $user = $_ENV['DATABASE_USER'];
        $pass = $_ENV['DATABASE_PASSWORD'];
        $conexion = conecta($host, $user, $pass, null);
        
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {

            $sqlCheck = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'tareas'";
            $resultado = $conexion->query($sqlCheck);
            if ($resultado && $resultado->num_rows > 0) {
                return [false, 'La base de datos "tareas" ya existía.'];
            }
            $sql = 'CREATE DATABASE IF NOT EXISTS tareas';
            if ($conexion->query($sql)) {
                return [true, 'Base de datos "tareas" creada correctamente'];
            } else {
                return [false, 'No se pudo crear la base de datos "tareas".'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}

function createTablaUsuarios()
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $sqlCheck = "SHOW TABLES LIKE 'usuarios'";
            $resultado = $conexion->query($sqlCheck);
            if ($resultado && $resultado->num_rows > 0) {
                return [false, 'La tabla "usuarios" ya existía.'];
            }
            $sql = 'CREATE TABLE `usuarios` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `username` VARCHAR(50) NOT NULL,
                `rol` INT DEFAULT 0,
                `nombre` VARCHAR(50) NOT NULL,
                `apellidos` VARCHAR(100) NOT NULL,
                `contrasena` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id`)
            )';
            if ($conexion->query($sql)) {
                return [true, 'Tabla "usuarios" creada correctamente'];
            } else {
                return [false, 'No se pudo crear la tabla "usuarios".'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}

function createTablaTareas()
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $sqlCheck = "SHOW TABLES LIKE 'tareas'";
            $resultado = $conexion->query($sqlCheck);
            if ($resultado && $resultado->num_rows > 0) {
                return [false, 'La tabla "tareas" ya existía.'];
            }
            $sql = 'CREATE TABLE `tareas` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `titulo` VARCHAR(50) NOT NULL,
                `descripcion` VARCHAR(250) NOT NULL,
                `estado` VARCHAR(50) NOT NULL,
                `id_usuario` INT NOT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`)
            )';
            if ($conexion->query($sql)) {
                return [true, 'Tabla "tareas" creada correctamente'];
            } else {
                return [false, 'No se pudo crear la tabla "tareas".'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}

function createTablaFicheros()
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $sqlCheck = "SHOW TABLES LIKE 'ficheros'";
            $resultado = $conexion->query($sqlCheck);
            if ($resultado && $resultado->num_rows > 0) {
                return [false, 'La tabla "ficheros" ya existía.'];
            }
            $sql = 'CREATE TABLE `ficheros` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `nombre` VARCHAR(100) NOT NULL,
                `file` VARCHAR(250) NOT NULL,
                `descripcion` VARCHAR(250) NOT NULL,
                `id_tarea` INT NOT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`id_tarea`) REFERENCES `tareas`(`id`)
            )';
            if ($conexion->query($sql)) {
                return [true, 'Tabla "ficheros" creada correctamente'];
            } else {
                return [false, 'No se pudo crear la tabla "ficheros".'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}


function listaTareas()
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $sql = "SELECT * FROM tareas";
            $resultados = $conexion->query($sql);
            $tareas = array();
            while ($row = $resultados->fetch_assoc()) {
                $usuario = buscaUsuarioMysqli($row['id_usuario']);
                $tarea = new Tarea(
                    (int)$row['id'],
                    $row['titulo'],
                    $row['descripcion'],
                    $row['estado'],
                    $usuario
                );
                array_push($tareas, $tarea);
            }
            return [true, $tareas];
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}

function nuevaTarea(Tarea $tarea)
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $stmt = $conexion->prepare("INSERT INTO tareas (titulo, descripcion, estado, id_usuario) VALUES (?,?,?,?)");
            $titulo      = $tarea->getTitulo();
            $descripcion = $tarea->getDescripcion();
            $estado      = $tarea->getEstado();
            $id_usuario  = $tarea->getUsuario()->getId();

            $stmt->bind_param("sssi", $titulo, $descripcion, $estado, $id_usuario);
            $stmt->execute();
            $tarea->setId($conexion->insert_id);
            return [true, $tarea];
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}

function actualizaTarea(Tarea $tarea)
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, estado = ?, id_usuario = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $titulo      = $tarea->getTitulo();
            $descripcion = $tarea->getDescripcion();
            $estado      = $tarea->getEstado();
            $id_usuario  = $tarea->getUsuario()->getId();
            $id          = $tarea->getId();


            $stmt->bind_param("sssii", $titulo, $descripcion, $estado, $id_usuario, $id);
            $stmt->execute();
            return [true, $tarea];
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}


function borraTarea($id)
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $stmt = $conexion->prepare("DELETE FROM tareas WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return [true, 'Tarea borrada correctamente.'];
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}


function buscaTarea($id)
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return null;
        } else {
            $stmt = $conexion->prepare("SELECT * FROM tareas WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $usuario = buscaUsuarioMysqli($row['id_usuario']);
                return new Tarea(
                    (int)$row['id'],
                    $row['titulo'],
                    $row['descripcion'],
                    $row['estado'],
                    $usuario
                );
            }
            return null;
        }
    } catch (mysqli_sql_exception $e) {
        return null;
    } finally {
        cerrarConexion($conexion);
    }
}


function esPropietarioTarea($idUsuario, $idTarea)
{
    $tarea = buscaTarea($idTarea);
    if ($tarea) {
        return $tarea->getUsuario()->getId() == $idUsuario;
    } else {
        return false;
    }
}


function buscaUsuarioMysqli($id)
{
    $conexion = conectaTareas();
    if ($conexion->connect_error) {
        return null;
    } else {
        $stmt = $conexion->prepare("SELECT id, username, nombre, apellidos, rol, contrasena FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows == 1) {
            $row = $result->fetch_assoc();
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
}

function nuevoFichero(Fichero $fichero)
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $stmt = $conexion->prepare("INSERT INTO ficheros (nombre, file, descripcion, id_tarea) VALUES (?,?,?,?)");
            $nombre      = $fichero->getNombre();
            $file        = $fichero->getFile();
            $descripcion = $fichero->getDescripcion();
            $id_tarea    = $fichero->getTarea()->getId();

            $stmt->bind_param("sssi", $nombre, $file, $descripcion, $id_tarea);
            $stmt->execute();
            $fichero->setId($conexion->insert_id);
            return [true, $fichero];
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}

function buscaFichero($id)
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return null;
        } else {
            $stmt = $conexion->prepare("SELECT * FROM ficheros WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
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
    } catch (mysqli_sql_exception $e) {
        return null;
    } finally {
        cerrarConexion($conexion);
    }
}

function borraFichero($id)
{
    try {
        $conexion = conectaTareas();
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $stmt = $conexion->prepare("DELETE FROM ficheros WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return [true, 'Fichero borrado correctamente.'];
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}
?>
