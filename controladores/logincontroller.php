<?php
require_once 'modelos/usuariomodel.php';

class logincontroller
{
    public function index()
    {
        require_once "vistas/login.php";
    }

    public function autenticar()
    {
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->login($correo, $password);

        if ($usuario) {
            $_SESSION['usuario'] = [
                'id_usuario' => $usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'rol' => $usuario['rol']
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = "Correo o contraseña incorrectos";
            require_once "vistas/login.php";
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
?>