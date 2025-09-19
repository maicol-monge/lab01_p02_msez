<?php
require_once 'modelos/adoptantemodel.php';

class AdoptanteController
{
    private $model;
    public function __construct()
    {
        $this->model = new AdoptanteModel();
    }

    public function index()
    {
        $filters = array();
        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        $adoptantes = $this->model->search($filters);
        include 'vistas/adoptante/index.php';
    }
    public function create()
    {
        $mascotas = $this->model->mascotasDisponibles();
        $mascota_preseleccionada = isset($_GET['id_mascota']) ? $_GET['id_mascota'] : '';
        include 'vistas/adoptante/create.php';
    }
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ad = new Adoptante(null, $_POST['nombre'], $_POST['telefono'], $_POST['id_mascota'] !== '' ? $_POST['id_mascota'] : null);
            $this->model->insert($ad);
            $this->index();
        }
    }
    public function edit($id)
    {
        $adoptante = $this->model->getById($id);
        $mascotas = $this->model->mascotasDisponibles();
        // incluir también su mascota actual si existe (para editar sin perderla)
        if ($adoptante && $adoptante->getIdMascota()) {
            $ya = array_filter($mascotas, fn($m) => $m['id_mascota'] == $adoptante->getIdMascota());
            if (empty($ya)) {
                // Recuperar su mascota específica
                // reutilizamos consulta directa mínima
                $sql = "SELECT * FROM Mascotas WHERE id_mascota = ?";
                $cn = new CNpdo();
                $fila = $cn->consulta($sql, [$adoptante->getIdMascota()]);
                if ($fila) {
                    $mascotas[] = $fila[0];
                }
            }
        }
        include 'vistas/adoptante/edit.php';
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ad = new Adoptante($id, $_POST['nombre'], $_POST['telefono'], $_POST['id_mascota'] !== '' ? $_POST['id_mascota'] : null);
            $this->model->update($ad);
            $this->index();
        }
    }
    public function delete($id)
    {
        $ad = $this->model->getById($id);
        if ($ad) {
            $this->model->delete($ad);
        }
        $this->index();
    }
}
?>