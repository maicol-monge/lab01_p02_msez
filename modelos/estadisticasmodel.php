<?php
require_once 'config/cn.php';

class EstadisticasModel
{
    private $cn;

    public function __construct()
    {
        $this->cn = new CNpdo();
    }

    public function obtenerEstadisticas()
    {
        $stats = array();

        // Total de mascotas registradas (activas)
        $sql = "SELECT COUNT(*) as total FROM Mascotas WHERE estado = 'Activo'";
        $result = $this->cn->consulta($sql);
        $stats['total_mascotas'] = $result[0]['total'];

        // Mascotas disponibles para adopción
        $sql = "SELECT COUNT(*) as disponibles FROM Mascotas 
                WHERE estado = 'Activo' AND estado_adopcion = 'Disponible'";
        $result = $this->cn->consulta($sql);
        $stats['mascotas_disponibles'] = $result[0]['disponibles'];

        // Mascotas adoptadas
        $sql = "SELECT COUNT(*) as adoptadas FROM Mascotas 
                WHERE estado = 'Activo' AND estado_adopcion = 'Adoptado'";
        $result = $this->cn->consulta($sql);
        $stats['mascotas_adoptadas'] = $result[0]['adoptadas'];

        // Total de adoptantes
        $sql = "SELECT COUNT(*) as total FROM Adoptantes WHERE estado = 'Activo'";
        $result = $this->cn->consulta($sql);
        $stats['total_adoptantes'] = $result[0]['total'];

        // Estadísticas por tipo de mascota y estado de adopción
        $sql = "SELECT 
                    t.nombre as tipo_nombre,
                    m.estado_adopcion,
                    COUNT(m.id_mascota) as cantidad,
                    t.id_tipo
                FROM TiposMascota t
                LEFT JOIN Mascotas m ON t.id_tipo = m.id_tipo AND m.estado = 'Activo'
                WHERE t.estado = 'Activo'
                GROUP BY t.id_tipo, t.nombre, m.estado_adopcion
                ORDER BY t.nombre, m.estado_adopcion";
        $stats['distribucion'] = $this->cn->consulta($sql);

        // Total de adopciones activas como estadística reciente
        $sql = "SELECT COUNT(*) as recientes 
                FROM Adoptantes 
                WHERE estado = 'Activo' 
                AND id_mascota IS NOT NULL";
        $result = $this->cn->consulta($sql);
        $stats['adopciones_recientes'] = $result[0]['recientes'];

        return $stats;
    }
}
?>