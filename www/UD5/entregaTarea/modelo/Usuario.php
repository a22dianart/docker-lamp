<?php
class Usuario {

    //Propiedades: id, username, nombre, apellidos, contrasena, rol (1.0)
    private int $id;
    private string $username;
    private string $nombre;
    private string $apellidos;
    private string $contrasena;
    private string $rol;

    // Constructor (1.0)
    public function __construct(int $id, string $username, string $nombre, string $apellidos, string $contrasena, string $rol) {
        $this->id = $id;
        $this->username = $username;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
    }

    
    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getNombre(): string { return $this->nombre; }
    public function getApellidos(): string { return $this->apellidos; }
    public function getContrasena(): string { return $this->contrasena; }
    public function getRol(): string { return $this->rol; }

 
    public function setUsername(string $username): void { $this->username = $username; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setApellidos(string $apellidos): void { $this->apellidos = $apellidos; }
    public function setContrasena(string $contrasena): void { $this->contrasena = $contrasena; }
    public function setRol(string $rol): void { $this->rol = $rol; }
}
?>
