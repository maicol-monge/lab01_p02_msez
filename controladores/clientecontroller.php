<?php
require_once 'modelos/mascotamodel.php';
require_once 'modelos/tipomascotamodel.php';
require_once 'modelos/adopcionmodel.php';

class clientecontroller
{
    private $mascotaModel;
    private $adopcionModel;

    public function __construct()
    {
        $this->mascotaModel = new MascotaModel();
        $this->adopcionModel = new AdopcionModel();
    }

    // Landing del cliente (móvil)
    public function index()
    {
        // Mostrar lista resumida + buscador
        $tipoModel = new TipoMascotaModel();
        $tipos = $tipoModel->getAll();
        $filters = [];
        if (!empty($_GET['nombre']))
            $filters['nombre'] = $_GET['nombre'];
        if (!empty($_GET['tipo']))
            $filters['tipo'] = $_GET['tipo'];
        $filters['estado'] = 'disponible';
        $mascotas = $this->mascotaModel->search($filters);
        require 'vistas/cliente/home.php';
    }

    public function mascota($id)
    {
        $mascota = $this->mascotaModel->getById($id);
        require 'vistas/cliente/mascota.php';
    }

    // Permite abrir por token QR: /cliente/qr?code=...
    public function qr()
    {
        $code = $_GET['code'] ?? '';
        if ($code === '') {
            header('Location: ' . RUTA . 'cliente');
            exit;
        }
        if (ctype_digit($code)) {
            $mascota = $this->mascotaModel->getById((int) $code);
        } else {
            $mascota = $this->mascotaModel->getByQrCode($code);
        }
        require 'vistas/cliente/mascota.php';
    }

    public function misSolicitudes()
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . RUTA . 'login');
            exit;
        }
        $todas = $this->adopcionModel->search();
        $usuarioId = $_SESSION['usuario']['id_usuario'];
        $solicitudes = array_filter($todas, fn($s) => $s['id_usuario'] == $usuarioId);
        require 'vistas/cliente/mis_solicitudes.php';
    }

    public function scan()
    {
        require 'vistas/cliente/scan.php';
    }

    public function adoptar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . RUTA . 'cliente');
            exit;
        }
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Cliente') {
            header('Location: ' . RUTA . 'login');
            exit;
        }
        $idMascota = (int) ($_POST['id_mascota'] ?? 0);
        if ($idMascota <= 0) {
            header('Location: ' . RUTA . 'cliente');
            exit;
        }

        // Regla: solo 1 solicitud por mascota por usuario
        $usuarioId = $_SESSION['usuario']['id_usuario'];
        if ($this->adopcionModel->existeSolicitudUsuarioMascota($usuarioId, $idMascota)) {
            header('Location: ' . RUTA . 'cliente/mascota/' . $idMascota . '?msg=ya_solicitada');
            exit;
        }
        $ok = $this->adopcionModel->insert($usuarioId, $idMascota);
        if ($ok) {
            header('Location: ' . RUTA . 'cliente/misSolicitudes?msg=sol_enviada');
        } else {
            header('Location: ' . RUTA . 'cliente/mascota/' . $idMascota . '?msg=error');
        }
        exit;
    }
}
?>