<?php
namespace App;

class Medicamento extends BaseModel {
    private $logger;

    public function __construct(\PDO $pdo) {
        parent::__construct($pdo);
        $this->logger = new Logger($pdo);
    }

    // MÉTODO QUE TE FALTABA
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM medicamentos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function buscarExistente($nombre) {
        $stmt = $this->db->prepare("SELECT id, stock FROM medicamentos WHERE LOWER(nombre) = LOWER(?) AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$nombre]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function buscar($search = '', $bajo_stock = false) {
        $sql = "SELECT m.*, c.nombre as categoria_nombre FROM medicamentos m LEFT JOIN categorias c ON m.categoria_id = c.id WHERE (m.deleted_at IS NULL OR m.deleted_at = '')";
        $params = [];
        if ($search) { $sql .= " AND (m.nombre LIKE ? OR c.nombre LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($bajo_stock) { $sql .= " AND m.stock <= m.stock_minimo"; }
        $sql .= " ORDER BY m.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $presentacion, $concentracion, $categoria_id, $stock, $stock_min) {
        $stmt = $this->db->prepare("INSERT INTO medicamentos (nombre, presentacion, concentracion, categoria_id, stock, stock_minimo, deleted_at) VALUES (?, ?, ?, ?, ?, ?, NULL)");
        $result = $stmt->execute([$nombre, $presentacion, $concentracion, $categoria_id, $stock, $stock_min]);
        if($result) $this->logger->registrar($this->db->lastInsertId(), 'entrada', $stock, $_SESSION['user_id'], 'Stock inicial - Nuevo');
        return $result;
    }

    public function actualizar($id, $nombre, $presentacion, $concentracion, $categoria_id, $stock, $stock_min) {
        $stmt = $this->db->prepare("UPDATE medicamentos SET nombre = ?, presentacion = ?, concentracion = ?, categoria_id = ?, stock = ?, stock_minimo = ? WHERE id = ?");
        return $stmt->execute([$nombre, $presentacion, $concentracion, $categoria_id, $stock, $stock_min, $id]);
    }

    public function eliminarLogico($id, $usuario_id, $observacion, $stock_actual) {
        $this->logger->registrar($id, 'salida', $stock_actual, $usuario_id, "Eliminación: " . $observacion);
        $stmt = $this->db->prepare("UPDATE medicamentos SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}