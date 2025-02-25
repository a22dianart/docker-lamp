<?php
require_once 'Usuario.php';
class Tarea {
    private int $id;
    private string $titulo;
    private string $descripcion;
    private string $estado;
    private Usuario $usuario; 


    public function __construct(int $id, string $titulo, string $descripcion, string $estado, Usuario $usuario) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->estado = $estado;
        $this->usuario = $usuario; 
    }


    public function getId(): int {
        return $this->id;
    }
    public function getTitulo(): string {
        return $this->titulo;
    }
    public function getDescripcion(): string {
        return $this->descripcion;
    }
    public function getEstado(): string {
        return $this->estado;
    }
    public function getUsuario(): Usuario{
        return $this->usuario;
    }


    public function setId(int $id): void {
        $this->id = $id;
    }
    public function setTitulo(string $titulo): void {
        $this->titulo = $titulo;
    }
    public function setDescripcion(string $descripcion): void {
        $this->descripcion = $descripcion;
    }
    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }
    public function setUsuario(Usuario $usuario): void {
        $this->usuario = $usuario;
    }
}
?>
