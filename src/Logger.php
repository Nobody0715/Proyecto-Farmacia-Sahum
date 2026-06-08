<?php
namespace App;
class Logger {
    private $db;
    public function __construct(\PDO $pdo) { $this->db = $pdo; }
    public function registrar($med_id, $tipo, $cantidad, $usuario_id, $obs) {
        $stmt = $this->db->prepare("INSERT INTO movimientos (medicamento_id, tipo, cantidad, usuario_id, observacion) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$med_id, $tipo, $cantidad, $usuario_id, $obs]);
    }
}