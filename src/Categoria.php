<?php
namespace App;

class Categoria extends BaseModel {
    public function listar() {
        return $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function crear($nombre) {
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }
}