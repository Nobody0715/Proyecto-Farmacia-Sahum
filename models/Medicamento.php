<?php
class Medicamento {
    private $db;
    
    // Propiedades del objeto
    public $id;
    public $nombre;
    public $presentacion;
    public $concentracion;
    public $categoria_id;
    public $stock;
    public $stock_minimo;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function obtenerCategorias() {
        return $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarExistente($nombre) {
        $stmt = $this->db->prepare("SELECT * FROM medicamentos WHERE LOWER(nombre) = LOWER(?) AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$nombre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar($search = '', $bajo_stock = false) {
        $sql = "SELECT m.*, c.nombre as categoria_nombre 
                FROM medicamentos m 
                LEFT JOIN categorias c ON m.categoria_id = c.id 
                WHERE (m.deleted_at IS NULL OR m.deleted_at = '')";
        
        $params = [];
        if ($search) {
            $sql .= " AND (m.nombre LIKE ? OR c.nombre LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($bajo_stock) {
            $sql .= " AND m.stock <= m.stock_minimo";
        }
        $sql .= " ORDER BY m.nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM medicamentos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear() {
        $stmt = $this->db->prepare("INSERT INTO medicamentos (nombre, presentacion, concentracion, categoria_id, stock, stock_minimo, deleted_at) VALUES (?, ?, ?, ?, ?, ?, NULL)");
        return $stmt->execute([$this->nombre, $this->presentacion, $this->concentracion, $this->categoria_id, $this->stock, $this->stock_minimo]);
    }

    public function actualizar($id) {
        $stmt = $this->db->prepare("UPDATE medicamentos SET nombre = ?, presentacion = ?, concentracion = ?, categoria_id = ?, stock = ?, stock_minimo = ? WHERE id = ?");
        return $stmt->execute([$this->nombre, $this->presentacion, $this->concentracion, $this->categoria_id, $this->stock, $this->stock_minimo, $id]);
    }

    public function registrarMovimiento($id, $tipo, $cantidad, $usuario_id, $observacion) {
        $stmt = $this->db->prepare("INSERT INTO movimientos (medicamento_id, tipo, cantidad, usuario_id, observacion) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$id, $tipo, $cantidad, $usuario_id, $observacion]);
    }

    // Nuevo método para eliminar lógicamente
    public function eliminarLogico($id, $usuario_id, $observacion, $stock_actual) {
        $pdo = $this->db;
        $pdo->beginTransaction();
        
        // Registrar movimiento de salida al eliminar
        $stmt = $pdo->prepare("INSERT INTO movimientos (medicamento_id, tipo, cantidad, usuario_id, observacion) VALUES (?, 'salida', ?, ?, ?)");
        $stmt->execute([$id, $stock_actual, $usuario_id, "Eliminación: " . $observacion]);
        
        // Soft delete (marcar eliminado)
        $stmt = $pdo->prepare("UPDATE medicamentos SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        
        $pdo->commit();
    }
}