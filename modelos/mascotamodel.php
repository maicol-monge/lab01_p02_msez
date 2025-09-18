<?php
require_once 'config/cn.php';
require_once 'clases/mascota.php';
require_once 'clases/tipomascota.php';


class MascotaModel
{
    private $cn;

    public function __construct()
    {
        $this->cn = new CNpdo();
    }

    public function search($filters = [])
    {
        $sql = "SELECT m.id_mascota, m.nombre, m.foto, m.id_tipo, m.estado_adopcion,
                       t.nombre AS tipo_nombre, t.descripcion
                FROM Mascotas m
                JOIN TiposMascota t ON t.id_tipo = m.id_tipo
                WHERE m.estado = 'Activo'";

        $params = [];

        if (!empty($filters['nombre'])) {
            $sql .= " AND m.nombre LIKE ?";
            $params[] = "%" . $filters['nombre'] . "%";
        }

        if (!empty($filters['tipo'])) {
            $sql .= " AND m.id_tipo = ?";
            $params[] = $filters['tipo'];
        }

        if (!empty($filters['estado'])) {
            $sql .= " AND m.estado_adopcion = ?";
            $params[] = ucfirst($filters['estado']); // Convertir 'disponible' a 'Disponible'
        }

        $sql .= " ORDER BY m.nombre ASC";

        $results = $this->cn->consulta($sql, $params);
        $mascotas = [];
        foreach ($results as $row) {
            $tipo = new TipoMascota($row['id_tipo'], $row['tipo_nombre'], $row['descripcion']);
            $mascota = new Mascota($row['id_mascota'], $row['id_tipo'], $row['nombre'], $row['foto'], $tipo);
            $mascota->setEstadoAdopcion($row['estado_adopcion']);
            $mascotas[] = $mascota;
        }
        return $mascotas;
    }

    public function getAll()
    {
        return $this->search();
    }

    public function getById($id)
    {
        $sql = "SELECT m.*, t.nombre AS tipo_nombre, t.descripcion 
                FROM Mascotas m 
                JOIN TiposMascota t ON t.id_tipo = m.id_tipo 
                WHERE m.id_mascota = ? AND m.estado = 'Activo'";
        $results = $this->cn->consulta($sql, [$id]);
        if (!empty($results)) {
            $row = $results[0];
            $tipo = new TipoMascota($row['id_tipo'], $row['tipo_nombre'], $row['descripcion']);
            $mascota = new Mascota($row['id_mascota'], $row['id_tipo'], $row['nombre'], $row['foto'], $tipo);
            $mascota->setEstadoAdopcion($row['estado_adopcion']);
            return $mascota;
        }
        return null;
    }

    public function insert($mascotaObj)
    {
        $sql = "INSERT INTO Mascotas (id_tipo, nombre, foto, estado, estado_adopcion) 
                VALUES (?, ?, ?, 'Activo', 'Disponible')";
        $foto = method_exists($mascotaObj, 'getFoto') ? $mascotaObj->getFoto() : '';
        return $this->cn->ejecutar($sql, [
            $mascotaObj->getIdTipo(),
            $mascotaObj->getNomMascota(),
            $foto
        ]);
    }

    public function update($mascotaObj)
    {
        $sql = "UPDATE Mascotas 
                SET id_tipo = ?, nombre = ?, foto = ?, estado_adopcion = ? 
                WHERE id_mascota = ? AND estado = 'Activo'";
        $foto = method_exists($mascotaObj, 'getFoto') ? $mascotaObj->getFoto() : '';
        return $this->cn->ejecutar($sql, [
            $mascotaObj->getIdTipo(),
            $mascotaObj->getNomMascota(),
            $foto,
            $mascotaObj->getEstadoAdopcion(),
            $mascotaObj->getIdmascota()
        ]);
    }

    public function delete($mascotaObj)
    {
        $sql = "UPDATE Mascotas SET estado = 'Inactivo' WHERE id_mascota = ?";
        return $this->cn->ejecutar($sql, [$mascotaObj->getIdmascota()]);
    }
}
?>