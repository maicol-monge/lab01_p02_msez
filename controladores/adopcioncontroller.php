<?php
require_once "modelos/adopcionmodel.php";
class adopcioncontroller
{
    public function index()
    {
        require_once "vistas/adopcion/index.php";
    }

    public function adoptar()
    {
        if (
            isset($_SESSION['usuario']) &&
            $_SESSION['usuario']['rol'] === 'Cliente' &&
            isset($_POST['id_mascota'])
        ) {
            $adopcionModel = new AdopcionModel();
            $uid = $_SESSION['usuario']['id_usuario'];
            $mid = (int) $_POST['id_mascota'];
            if ($adopcionModel->existeSolicitudUsuarioMascota($uid, $mid)) {
                header("Location: index.php?url=cliente/mascota/$mid&msg=ya_solicitada");
                exit;
            }
            $adopcionModel->insert($uid, $mid);
            header("Location: index.php?url=adopcion&msg=solicitud_enviada");
            exit;
        } else {
            header("Location: index.php?url=mascota");
            exit;
        }
    }

    public function aprobar()
    {
        if (
            isset($_SESSION['usuario']) &&
            $_SESSION['usuario']['rol'] === 'Administrador' &&
            isset($_POST['id_adopcion'])
        ) {
            $adopcionModel = new AdopcionModel();
            $adopcionModel->updateEstado($_POST['id_adopcion'], 'Aprobada');
            // Marcar mascota como Adoptado
            $adop = $adopcionModel->getById($_POST['id_adopcion']);
            if ($adop && isset($adop['id_mascota'])) {
                require_once 'modelos/mascotamodel.php';
                $mm = new MascotaModel();
                $mm->updateEstadoAdopcion($adop['id_mascota'], 'Adoptado');
            }
            header("Location: index.php?url=adopcion&msg=aprobada");
            exit;
        }
    }

    public function rechazar()
    {
        if (
            isset($_SESSION['usuario']) &&
            $_SESSION['usuario']['rol'] === 'Administrador' &&
            isset($_POST['id_adopcion'])
        ) {
            $adopcionModel = new AdopcionModel();
            $adopcionModel->updateEstado($_POST['id_adopcion'], 'Rechazada');
            header("Location: index.php?url=adopcion&msg=rechazada");
            exit;
        }
    }

    // Ver ticket por id (admin y cliente dueño)
    public function ticket($id)
    {
        $model = new AdopcionModel();
        $detalle = $model->getDetalleById($id);
        if (!$detalle) {
            require 'vistas/404.php';
            return;
        }
        // permisos: admin o dueño de la adopción
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . RUTA . 'login');
            exit;
        }
        if ($_SESSION['usuario']['rol'] !== 'Administrador' && $_SESSION['usuario']['id_usuario'] != $detalle['id_usuario']) {
            require 'vistas/404.php';
            return;
        }
        require 'vistas/adopcion/ticket.php';
    }

    // Abrir ticket desde QR (admin)
    public function scan()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
            header('Location: ' . RUTA . 'login');
            exit;
        }
        $id = $_GET['id'] ?? '';
        if (ctype_digit($id)) {
            header('Location: ' . RUTA . 'adopcion/ticket/' . $id);
            exit;
        }
        require 'vistas/adopcion/scan.php';
    }
}
?>