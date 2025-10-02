<?php
require_once 'config/cn.php';
class UsuarioModel
{
    private $cn;
    public function __construct()
    {
        $this->cn = new CNpdo();
    }

    // Login: retorna usuario si el correo y contraseña son correctos
    public function login($correo, $password)
    {
        // Alias de `PASSWORD` a "password" para evitar problemas de mayúsculas en la clave del array
        $sql = "SELECT id_usuario, nombre, correo, `PASSWORD` AS password, rol, estado
                FROM Usuarios
                WHERE correo = ? AND estado = 'Activo' LIMIT 1";
        $rows = $this->cn->consulta($sql, [$correo]);
        if ($rows) {
            $usuario = $rows[0];
            // Verifica la contraseña (usa password_verify si usas password_hash)
            if (isset($usuario['password']) && is_string($usuario['password']) && password_verify($password, $usuario['password'])) {
                return $usuario;
            }
        }
        return null;
    }

    // Verifica si ya existe un usuario con el mismo correo
    public function existeCorreo(string $correo): bool
    {
        $sql = "SELECT 1 FROM Usuarios WHERE correo = ? LIMIT 1";
        $rows = $this->cn->consulta($sql, [$correo]);
        return !empty($rows);
    }

    // Crea un usuario con contraseña encriptada y retorna el ID insertado o false
    public function crearUsuario(string $nombre, string $correo, string $password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO Usuarios (nombre, correo, `PASSWORD`, rol, estado) VALUES (?, ?, ?, 'Cliente', 'Activo')";
        try {
            $ok = $this->cn->ejecutar($sql, [$nombre, $correo, $hash]);
            if ($ok) {
                // Obtener el último ID insertado usando la misma conexión PDO
                $pdo = $this->cn->getConexion();
                return $pdo->lastInsertId();
            }
        } catch (Exception $e) {
            // Loguear si fuera necesario
        }
        return false;
    }
}
?>