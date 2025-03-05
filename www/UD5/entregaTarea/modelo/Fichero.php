<?php
require_once 'Tarea.php'; 

class Fichero {

    //Propiedades: id, nombre, file, descripcion y tarea (1.0)
    private int $id;
    private string $nombre;
    private string $file;
    private string $descripcion;
    private Tarea $tarea;
    
    //Constantes FORMATOS, MAX_SIZE (2.0)
    public const FORMATOS = ['pdf', 'doc', 'docx', 'jpg', 'png'];
    public const MAX_SIZE = 2097152;

    
    // Constructor (1.0)
    public function __construct(int $id, string $nombre, string $file, string $descripcion, Tarea $tarea) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->file = $file;
        $this->descripcion = $descripcion;
        $this->tarea = $tarea;
    }


    public function getId(): int {
        return $this->id;
    }
    public function getNombre(): string {
        return $this->nombre;
    }
    public function getFile(): string {
        return $this->file;
    }
    public function getDescripcion(): string {
        return $this->descripcion;
    }
    public function getTarea(): Tarea {
        return $this->tarea;
    }


    public function setId(int $id): void {
        $this->id = $id;
    }
    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }
    public function setFile(string $file): void {
        $this->file = $file;
    }
    public function setDescripcion(string $descripcion): void {
        $this->descripcion = $descripcion;
    }
    public function setTarea(Tarea $tarea): void {
        $this->tarea = $tarea;
    }
    

    // Método estático de validación (devuelve array asociativo) (4.0)
    public static function validateFields(array $data): array {
        $errors = [];


        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es obligatorio.';
        } elseif (strlen($data['nombre']) > 100) {
            $errors['nombre'] = 'El nombre no puede tener más de 100 caracteres.';
        }


        if (empty($data['file'])) {
            $errors['file'] = 'El archivo es obligatorio.';
        } else {
   
            $ext = strtolower(pathinfo($data['file'], PATHINFO_EXTENSION));
            if (!in_array($ext, self::FORMATOS)) {
                $errors['file'] = 'El formato del archivo no es válido. Formatos permitidos: ' . implode(', ', self::FORMATOS);
            }

            if (isset($data['size']) && $data['size'] > self::MAX_SIZE) {
                $errors['file'] = 'El tamaño del archivo excede el máximo permitido (2MB).';
            }
        }

 
        if (!empty($data['descripcion']) && strlen($data['descripcion']) > 250) {
            $errors['descripcion'] = 'La descripción no puede tener más de 250 caracteres.';
        }

  
        if (empty($data['tarea']) || !($data['tarea'] instanceof Tarea)) {
            $errors['tarea'] = 'La tarea asociada no es válida.';
        }

        return $errors;
    }
}
?>
