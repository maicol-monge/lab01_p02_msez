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

    // Página de confirmación de adopción cuando proviene de un QR con ID
    public function confirmar($id)
    {
        $mascota = $this->mascotaModel->getById($id);
        require 'vistas/cliente/confirmar.php';
    }

    // Permite abrir por token QR: /cliente/qr?code=...
    public function qr()
    {
        $code = $_GET['code'] ?? '';
        if ($code === '') {
            header('Location: ' . RUTA . 'cliente');
            exit;
        }
        // Normalizar: decodificar y quitar base si viene como URL completa
        $decoded = urldecode($code);
        if (strpos($decoded, RUTA) === 0) {
            $decoded = substr($decoded, strlen(RUTA));
        }

        // Si el parámetro es un ID numérico
        if (ctype_digit($decoded)) {
            // ID de mascota directo
            header('Location: ' . RUTA . 'index.php?url=cliente/confirmar/' . $decoded);
            exit;
        }

        // Si viene la ruta/nombre del PNG del QR: assets/qrs/mascota_4.png
        if (preg_match('/mascota_(\d+)\.png$/i', $decoded, $m)) {
            header('Location: ' . RUTA . 'index.php?url=cliente/confirmar/' . $m[1]);
            exit;
        }

        // Intentar resolver por token/valor de qr_code almacenado
        $mascota = $this->mascotaModel->getByQrCode($decoded);
        if ($mascota && method_exists($mascota, 'getIdmascota')) {
            header('Location: ' . RUTA . 'index.php?url=cliente/confirmar/' . $mascota->getIdmascota());
            exit;
        }

        // Si no se encontró, llevar al listado del cliente con aviso
        header('Location: ' . RUTA . 'cliente?msg=qr_no_valido');
        exit;
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