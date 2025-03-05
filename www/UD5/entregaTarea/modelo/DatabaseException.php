<?php

class DatabaseException extends Exception {

   // Atributos extra: method y sql (4.0)
    private string $method;
    private string $sql;

    public function __construct(string $message, string $method, string $sql, int $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->method = $method;
        $this->sql = $sql;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getSql(): string {
        return $this->sql;
    }
}
?>
