<?php
require_once 'modelos/mascotamodel.php';
require_once 'modelos/tipomascotamodel.php';

class MascotaController
{
    private $mascotaModel;

    public function __construct()
    {
        $this->mascotaModel = new MascotaModel();
    }

    public function index()
    {
        $tipoModel = new TipoMascotaModel();
        $tipos = $tipoModel->getAll();

        // Procesar filtros de búsqueda
        $filters = [];
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!empty($_GET['nombre'])) {
                $filters['nombre'] = $_GET['nombre'];
            }
            if (!empty($_GET['tipo'])) {
                $filters['tipo'] = $_GET['tipo'];
            }
            if (!empty($_GET['estado'])) {
                $filters['estado'] = $_GET['estado'];
            }
        }

        $mascotas = $this->mascotaModel->search($filters);
        include 'vistas/mascotas/index.php';
    }

    public function create()
    {
        // Tipos de mascota (antes razas)
        $tipoModel = new TipoMascotaModel();
        $tipos = $tipoModel->getAll();
        include 'vistas/mascotas/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Campos esperados: id_tipo (alias idraza legacy), nombre (alias nom_mascota), foto
            $idTipo = $_POST['id_tipo'] ?? ($_POST['idraza'] ?? null);
            $nombre = $_POST['nombre'] ?? ($_POST['nom_mascota'] ?? '');
            $foto = $_POST['foto'] ?? '';
            $mascota = new Mascota(null, $idTipo, $nombre, $foto, null);
            $this->mascotaModel->insert($mascota);
            $this->index();
        }
    }

    public function edit($id)
    {
        $tipoModel = new TipoMascotaModel();
        $tipos = $tipoModel->getAll();
        $mascota = $this->mascotaModel->getById($id);
        include 'vistas/mascotas/edit.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idTipo = $_POST['id_tipo'] ?? ($_POST['idraza'] ?? null);
            $nombre = $_POST['nombre'] ?? ($_POST['nom_mascota'] ?? '');
            $foto = $_POST['foto'] ?? '';
            $mascota = new Mascota($id, $idTipo, $nombre, $foto, null);
            $this->mascotaModel->update($mascota);
            $this->index();
        }
    }

    public function delete($id)
    {
        $mascota = $this->mascotaModel->getById($id);
        if ($mascota) {
            $this->mascotaModel->delete($mascota);
        }
        $this->index();
    }

    public function ver($id)
    {
        $mascota = $this->mascotaModel->getById($id);
        require "vistas/mascotas/mascota_ver.php";
    }

    // Página para imprimir QR (admin)
    public function qr($id)
    {
        // seguridad mínima: solo admin
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
            header('Location: ' . RUTA . 'login');
            exit;
        }
        $mascota = $this->mascotaModel->getById($id);
        $qrValue = $this->mascotaModel->getQrCodeValue($id);
        // Construir URL a abrir desde la cámara nativa: si existe token QR usar /cliente/qr?code=TOKEN, si no usar /cliente/mascota/{id}
        $url = RUTA . ($qrValue ? ('cliente/qr?code=' . urlencode($qrValue)) : ('cliente/mascota/' . $id));
        require 'vistas/mascotas/qr.php';
    }
}
?>