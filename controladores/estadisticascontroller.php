<?php
require_once 'modelos/estadisticasmodel.php';

class EstadisticasController
{
    private $model;

    public function __construct()
    {
        $this->model = new EstadisticasModel();
    }

    public function index()
    {
        $stats = $this->model->obtenerEstadisticas();
        include 'vistas/estadisticas/index.php';
    }
}
?>