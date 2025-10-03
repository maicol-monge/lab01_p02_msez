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

        // Total de adoptantes (usuarios con rol Cliente)
        $sql = "SELECT COUNT(*) as total FROM Usuarios WHERE estado = 'Activo' AND rol = 'Cliente'";
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

        // Total de adopciones activas (Pendiente o Aprobada)
        $sql = "SELECT COUNT(*) as recientes 
        FROM Adopciones 
        WHERE estado IN ('Pendiente','Aprobada')";
        $result = $this->cn->consulta($sql);
        $stats['adopciones_recientes'] = $result[0]['recientes'];

        return $stats;
    }

    // Listado de usuarios activos (para filtros)
    public function obtenerUsuariosActivos()
    {
        $sql = "SELECT id_usuario, nombre, correo FROM Usuarios WHERE estado = 'Activo' ORDER BY nombre";
        return $this->cn->consulta($sql);
    }

    // Mascotas de un usuario (adoptadas por ese usuario)
    public function obtenerMascotasPorUsuario($idUsuario)
    {
        $sql = "SELECT m.id_mascota, m.nombre AS mascota, t.nombre AS tipo, m.estado_adopcion
                FROM Adopciones a
                INNER JOIN Mascotas m ON a.id_mascota = m.id_mascota
                INNER JOIN TiposMascota t ON m.id_tipo = t.id_tipo
                WHERE a.id_usuario = :id AND a.estado IN ('Aprobada','Finalizada')
                ORDER BY t.nombre, m.nombre";
        return $this->cn->consulta($sql, [':id' => $idUsuario]);
    }

    // Adopciones detalle en rango de fechas
    public function obtenerAdopcionesDetalle($desde = null, $hasta = null, $estados = ['Pendiente','Aprobada','Rechazada','Finalizada'])
    {
        $params = [];
        $wheres = [];
        if ($desde) { $wheres[] = 'a.fecha_adopcion >= :desde'; $params[':desde'] = $desde . ' 00:00:00'; }
        if ($hasta) { $wheres[] = 'a.fecha_adopcion <= :hasta'; $params[':hasta'] = $hasta . ' 23:59:59'; }
        if ($estados && count($estados)>0) {
            $in = [];
            foreach ($estados as $i=>$st){ $in[] = ':st'.$i; $params[':st'.$i] = $st; }
            $wheres[] = 'a.estado IN ('.implode(',', $in).')';
        }
        $whereSql = count($wheres) ? ('WHERE '.implode(' AND ',$wheres)) : '';

        $sql = "SELECT a.id_adopcion, a.fecha_adopcion, a.estado,
                        u.nombre AS usuario, u.correo,
                        m.nombre AS mascota, t.nombre AS tipo
                FROM Adopciones a
                INNER JOIN Usuarios u ON a.id_usuario = u.id_usuario
                INNER JOIN Mascotas m ON a.id_mascota = m.id_mascota
                INNER JOIN TiposMascota t ON m.id_tipo = t.id_tipo
                $whereSql
                ORDER BY a.fecha_adopcion DESC";
        return $this->cn->consulta($sql, $params);
    }

    // Adopciones por tipo en rango (agrupado)
    public function obtenerAdopcionesPorTipo($desde = null, $hasta = null, $estados = ['Pendiente','Aprobada','Rechazada','Finalizada'])
    {
        $params = [];
        $wheres = [];
        if ($desde) { $wheres[] = 'a.fecha_adopcion >= :desde'; $params[':desde'] = $desde . ' 00:00:00'; }
        if ($hasta) { $wheres[] = 'a.fecha_adopcion <= :hasta'; $params[':hasta'] = $hasta . ' 23:59:59'; }
        if ($estados && count($estados)>0) {
            $in = [];
            foreach ($estados as $i=>$st){ $in[] = ':st'.$i; $params[':st'.$i] = $st; }
            $wheres[] = 'a.estado IN ('.implode(',', $in).')';
        }
        $whereSql = count($wheres) ? ('WHERE '.implode(' AND ',$wheres)) : '';

        $sql = "SELECT t.nombre AS tipo, COUNT(*) AS cantidad
                FROM Adopciones a
                INNER JOIN Mascotas m ON a.id_mascota = m.id_mascota
                INNER JOIN TiposMascota t ON m.id_tipo = t.id_tipo
                $whereSql
                GROUP BY t.id_tipo, t.nombre
                ORDER BY t.nombre";
        return $this->cn->consulta($sql, $params);
    }

    // Conteo de adopciones por estado (para embudo) con filtros opcionales de año y mes
    public function obtenerConteoAdopcionesPorEstado(?int $anio = null, ?int $mes = null): array
    {
        $params = [];
        $wheres = [];
        if (!empty($anio) && $anio > 0) { $wheres[] = 'YEAR(fecha_adopcion) = :anio'; $params[':anio'] = $anio; }
        if (!empty($mes) && $mes > 0 && $mes <= 12) { $wheres[] = 'MONTH(fecha_adopcion) = :mes'; $params[':mes'] = $mes; }
        $whereSql = count($wheres) ? ('WHERE '.implode(' AND ', $wheres)) : '';

        $sql = "SELECT estado, COUNT(*) AS cantidad
                FROM Adopciones
                $whereSql
                GROUP BY estado
                ORDER BY FIELD(estado,'Pendiente','Aprobada','Rechazada','Finalizada')";
        $rows = $this->cn->consulta($sql, $params);
        $out = ['Pendiente'=>0,'Aprobada'=>0,'Rechazada'=>0,'Finalizada'=>0];
        foreach ($rows as $r) { $out[$r['estado']] = (int)$r['cantidad']; }
        return $out;
    }

    // Aprobadas vs Rechazadas por mes (año actual por defecto)
    public function obtenerAprobadasRechazadasPorMes(?int $anio = null): array
    {
        $anio = $anio ?: (int)date('Y');
        $params = [':anio' => $anio];

        $sql = "SELECT MONTH(fecha_adopcion) AS mes,
                       SUM(CASE WHEN estado='Aprobada'  THEN 1 ELSE 0 END) AS aprobadas,
                       SUM(CASE WHEN estado='Rechazada' THEN 1 ELSE 0 END) AS rechazadas
                FROM Adopciones
                WHERE YEAR(fecha_adopcion) = :anio
                GROUP BY MONTH(fecha_adopcion)
                ORDER BY mes";
        $rows = $this->cn->consulta($sql, $params);

        // Normalizar a 12 meses
        $aprob = array_fill(1, 12, 0);
        $rech  = array_fill(1, 12, 0);
        foreach ($rows as $r) {
            $m = (int)$r['mes'];
            $aprob[$m] = (int)$r['aprobadas'];
            $rech[$m]  = (int)$r['rechazadas'];
        }
        return ['anio'=>$anio,'aprobadas'=>$aprob,'rechazadas'=>$rech];
    }
}
?>