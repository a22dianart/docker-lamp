<?php

/*

 Interface FicherosDBInt define los métodos:
 listaFicheros($id_tarea): array (1.0)
 buscaFichero($id): Fichero (1.0)
 borraFichero($id): boolean (1.0)
 nuevoFichero($fichero): boolean (1.0)
*/
interface FicherosDBInt {
    public function listaFicheros($id_tarea): array;
    public function buscaFichero($id): ?Fichero;
    public function borraFichero($id): bool; //bool: tipo correcto en PHP
    public function nuevoFichero(Fichero $fichero): bool; //bool: tipo correcto en PHP
}

?>
