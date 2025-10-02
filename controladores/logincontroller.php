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

    // Mostrar formulario de registro
    public function registrar()
    {
        require_once "vistas/registro.php";
    }

    // Procesar registro
    public function guardarRegistro()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        $errores = [];
        if ($nombre === '')
            $errores[] = 'El nombre es obligatorio';
        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL))
            $errores[] = 'Correo inválido';
        if (strlen($password) < 6)
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        if ($password !== $password2)
            $errores[] = 'Las contraseñas no coinciden';

        $usuarioModel = new UsuarioModel();
        if ($usuarioModel->existeCorreo($correo)) {
            $errores[] = 'El correo ya está registrado';
        }

        if (!empty($errores)) {
            $error = implode('<br>', $errores);
            // Mantener valores ingresados (excepto contraseñas)
            $old = ['nombre' => $nombre, 'correo' => $correo];
            require_once "vistas/registro.php";
            return;
        }

        $id = $usuarioModel->crearUsuario($nombre, $correo, $password);
        if ($id) {
            // Autologin tras registro
            $_SESSION['usuario'] = [
                'id_usuario' => $id,
                'nombre' => $nombre,
                'rol' => 'Cliente'
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = 'Ocurrió un error al registrar el usuario';
            $old = ['nombre' => $nombre, 'correo' => $correo];
            require_once "vistas/registro.php";
        }
    }
}
?>