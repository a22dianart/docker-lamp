<?php

interface FicherosDBInt {
    public function listaFicheros($id_tarea): array;
    public function buscaFichero($id): ?Fichero;
    public function borraFichero($id): bool;
    public function nuevoFichero(Fichero $fichero): bool;
}

?>
