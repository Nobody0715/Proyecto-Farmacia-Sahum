<?php
namespace App\Controllers;

use App\Medicamento;

class MedicamentoController {
    private $medModel;

    public function __construct($pdo) {
        $this->medModel = new Medicamento($pdo);
    }

    public function procesarAgregar($datos) {
        $this->medModel->nombre = $datos['nombre'];
        $this->medModel->presentacion = $datos['presentacion'];
        $this->medModel->concentracion = $datos['concentracion'];
        $this->medModel->categoria_id = (int)$datos['categoria_id'];
        $this->medModel->stock = (int)$datos['stock'];
        $this->medModel->stock_minimo = (int)$datos['stock_minimo'];

        return $this->medModel->crear();
    }
}