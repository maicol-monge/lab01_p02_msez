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
}
?>