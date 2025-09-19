<?php
require_once 'modelos/tipomascotamodel.php';

class TipomascotaController
{
    private $model;
    public function __construct()
    {
        $this->model = new TipoMascotaModel();
    }

    public function index()
    {
        $filters = [];
        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        $tipos = $this->model->search($filters);
        include 'vistas/tipomascota/index.php';
    }
    public function create()
    {
        include 'vistas/tipomascota/create.php';
    }
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = new TipoMascota(null, $_POST['nombre'], $_POST['descripcion']);
            $this->model->insert($tipo);
            $this->index();
        }
    }
    public function edit($id)
    {
        $tipo = $this->model->getById($id);
        include 'vistas/tipomascota/edit.php';
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = new TipoMascota($id, $_POST['nombre'], $_POST['descripcion']);
            $this->model->update($tipo);
            $this->index();
        }
    }
    public function delete($id)
    {
        $tipo = $this->model->getById($id);
        if ($tipo) {
            $this->model->delete($tipo);
        }
        $this->index();
    }
}
?>