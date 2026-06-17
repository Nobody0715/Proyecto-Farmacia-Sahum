<?php
namespace App;

class Categoria extends BaseModel {
    public function listar() {
        return $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function editar($id, $nombre) {
        $stmt = $this->db->prepare("UPDATE categorias SET nombre = ? WHERE id = ?");
        return $stmt->execute([$nombre, $id]);
    }

    public function crear($nombre) {
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    // AÑADIMOS EL MÉTODO ELIMINAR
    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM categorias WHERE id = ?");
        return $stmt->execute([$id]);
    }
}