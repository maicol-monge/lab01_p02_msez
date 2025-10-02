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
}
?>