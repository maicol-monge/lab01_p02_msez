<?php
require_once 'modelos/mascotamodel.php';
require_once 'modelos/tipomascotamodel.php';
require_once "libs/phpqrcode/phpqrcode.php";

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
        $id_tipo = $_POST['id_tipo'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $foto = $_POST['foto'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        $mascota = new Mascota(null, $id_tipo, $nombre, $foto, $descripcion);
        $id_mascota = $this->mascotaModel->insert($mascota);

        if ($id_mascota) {
            // Ruta absoluta para guardar la imagen QR
            $projectRoot = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR;
            $qrDir = $projectRoot . 'assets' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR;

            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0755, true);
            }

            $qrFilename = 'mascota_' . $id_mascota . '.png';
            $qrFullPath = $qrDir . $qrFilename;              // ruta física
            $qrWebPath  = 'assets/qrs/' . $qrFilename;      // ruta para guardar en DB

            $url = RUTA . 'mascota/ver/' . $id_mascota;

            // Generar QR: suprimir warnings con @ y validar que el archivo fue creado
            try {
                // Asegúrate GD esté habilitado en php.ini; usamos @ para que no imprima warnings
                @QRcode::png($url, $qrFullPath, QR_ECLEVEL_L, 4);
            } catch (Exception $e) {
                // opcional: registrar error en log
            }

            // Verifica que el archivo se creó antes de actualizar la BD
            if (file_exists($qrFullPath)) {
                $this->mascotaModel->updateQR($id_mascota, $qrWebPath);
            }

            // redirección segura: evita "headers already sent"
            if (!headers_sent($file, $line)) {
                header('Location: ' . RUTA . 'mascota');
                exit;
            } else {
                error_log("No se pudo redirigir a " . RUTA . "mascota — salida ya enviada en $file en la línea $line");
                // Fallback: muestra la lista sin usar header (no imprimir nada adicional)
                $this->index();
                return;
            }
        } else {
            include 'vistas/mascotas/create.php';
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
}
?>