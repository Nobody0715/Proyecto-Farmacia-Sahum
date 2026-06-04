<?php
namespace App;

class Usuario {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function registrar($username, $password, $nombre, $apellido, $cedula, $telefono, $email, $rol) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, nombre, apellido, cedula, telefono, email, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $hashedPassword, $nombre, $apellido, $cedula, $telefono, $email, $rol]);
    }
}