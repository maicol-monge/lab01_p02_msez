<?php
require_once 'config/cn.php';

class AdopcionModel
{
    private $cn;
    public function __construct()
    {
        $this->cn = new CNpdo();
    }

    // Buscar adopciones con filtros (por usuario, mascota, estado, etc.)
    public function search($filters = array())
    {
        $sql = "SELECT a.*, u.nombre AS usuario_nombre, m.nombre AS mascota_nombre, m.foto, t.nombre AS tipo_nombre
                FROM Adopciones a
                JOIN Usuarios u ON u.id_usuario = a.id_usuario
                JOIN Mascotas m ON m.id_mascota = a.id_mascota
                JOIN TiposMascota t ON t.id_tipo = m.id_tipo
                WHERE 1";
        $params = [];

        if (!empty($filters['estado'])) {
            $sql .= " AND a.estado = ?";
            $params[] = $filters['estado'];
        }
        if (!empty($filters['usuario'])) {
            $sql .= " AND u.nombre LIKE ?";
            $params[] = "%" . $filters['usuario'] . "%";
        }
        if (!empty($filters['usuarioId'])) {
            $sql .= " AND u.id_usuario = ?";
            $params[] = $filters['usuarioId'];
        }
        if (!empty($filters['mascota'])) {
            $sql .= " AND m.nombre LIKE ?";
            $params[] = "%" . $filters['mascota'] . "%";
        }

        $sql .= " ORDER BY a.fecha_adopcion DESC";
        return $this->cn->consulta($sql, $params);
    }

    // Obtener todas las adopciones
    public function getAll()
    {
        return $this->search();
    }

    // Obtener adopción por ID
    public function getById($id)
    {
        $sql = "SELECT * FROM Adopciones WHERE id_adopcion = ?";
        $rows = $this->cn->consulta($sql, [$id]);
        return $rows ? $rows[0] : null;
    }

    // Obtener detalle completo para ticket/consulta
    public function getDetalleById($id)
    {
        $sql = "SELECT a.*, 
                       u.nombre AS usuario_nombre, u.correo AS usuario_correo,
                       m.nombre AS mascota_nombre, m.foto AS mascota_foto,
                       t.nombre AS tipo_nombre
                FROM Adopciones a
                JOIN Usuarios u ON u.id_usuario = a.id_usuario
                JOIN Mascotas m ON m.id_mascota = a.id_mascota
                JOIN TiposMascota t ON t.id_tipo = m.id_tipo
                WHERE a.id_adopcion = ?";
        $rows = $this->cn->consulta($sql, [$id]);
        return $rows ? $rows[0] : null;
    }

    // Insertar nueva adopción
    public function insert($usuarioId, $mascotaId)
    {
        $sql = "INSERT INTO Adopciones (id_usuario, id_mascota) VALUES (?, ?)";
        return $this->cn->ejecutar($sql, [$usuarioId, $mascotaId]);
    }

    // Verificar si un usuario ya tiene solicitud para una mascota
    public function existeSolicitudUsuarioMascota($usuarioId, $mascotaId): bool
    {
        $sql = "SELECT 1 FROM Adopciones WHERE id_usuario = ? AND id_mascota = ? LIMIT 1";
        $rows = $this->cn->consulta($sql, [$usuarioId, $mascotaId]);
        return !empty($rows);
    }

    // Listar solicitudes por usuario
    public function listarPorUsuario($usuarioId)
    {
        return $this->search(['usuarioId' => $usuarioId]);
    }

    // Actualizar estado de adopción
    public function updateEstado($idAdopcion, $nuevoEstado)
    {
        $sql = "UPDATE Adopciones SET estado = ? WHERE id_adopcion = ?";
        return $this->cn->ejecutar($sql, [$nuevoEstado, $idAdopcion]);
    }

    // Eliminar adopción (opcional, según reglas de negocio)
    public function delete($idAdopcion)
    {
        $sql = "DELETE FROM Adopciones WHERE id_adopcion = ?";
        return $this->cn->ejecutar($sql, [$idAdopcion]);
    }
}
?>