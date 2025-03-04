<?php
require_once 'FicherosDBInt.php';
require_once 'DatabaseException.php';
require_once 'Fichero.php';
require_once 'Tarea.php';
require_once '../modelo/mysqli.php';
require_once '../modelo/pdo.php';

class FicherosDBImp implements FicherosDBInt {

    public function listaFicheros($id_tarea): array {
        try {
            $con = conectaPDO();
            $stmt = $con->prepare("SELECT * FROM ficheros WHERE id_tarea = :id_tarea");
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
            return $ficheros;

        } catch (PDOException $e) {
            throw new DatabaseException("Error al listar ficheros", __METHOD__, "SELECT * FROM ficheros WHERE id_tarea = :id_tarea", 0, $e);
        } finally {
            $con = null;
        }
    }

    public function buscaFichero($id): ?Fichero {
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

        } catch (PDOException $e) {
            throw new DatabaseException("Error al buscar fichero", __METHOD__, "SELECT * FROM ficheros WHERE id = :id", 0, $e);
        } finally {
            $con = null;
        }
    }

    public function borraFichero($id): bool {
        try {
            $con = conectaPDO();
            $stmt = $con->prepare("DELETE FROM ficheros WHERE id = :id");
            return $stmt->execute([':id' => $id]);

        } catch (PDOException $e) {
            throw new DatabaseException("Error al borrar fichero", __METHOD__, "DELETE FROM ficheros WHERE id = :id", 0, $e);
        } finally {
            $con = null;
        }
    }

    public function nuevoFichero(Fichero $fichero): bool {
        try {
            $con = conectaPDO();
            $stmt = $con->prepare("INSERT INTO ficheros (nombre, file, descripcion, id_tarea) VALUES (:nombre, :file, :descripcion, :id_tarea)");
            return $stmt->execute([
                ':nombre' => $fichero->getNombre(),
                ':file' => $fichero->getFile(),
                ':descripcion' => $fichero->getDescripcion(),
                ':id_tarea' => $fichero->getTarea()->getId()
            ]);

        } catch (PDOException $e) {
            throw new DatabaseException("Error al insertar fichero", __METHOD__, "INSERT INTO ficheros (nombre, file, descripcion, id_tarea) VALUES (:nombre, :file, :descripcion, :id_tarea)", 0, $e);
        } finally {
            $con = null;
        }
    }
}
?>
