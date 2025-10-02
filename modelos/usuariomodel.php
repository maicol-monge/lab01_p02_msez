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
        $sql = "SELECT * FROM Usuarios WHERE correo = ? AND estado = 'Activo' LIMIT 1";
        $rows = $this->cn->consulta($sql, [$correo]);
        if ($rows) {
            $usuario = $rows[0];
            // Verifica la contraseña (usa password_verify si usas password_hash)
            if (password_verify($password, $usuario['password'])) {
                return $usuario;
            }
        }
        return null;
    }
}
?>