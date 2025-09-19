<?php
require_once 'config/cn.php';
require_once 'clases/tipomascota.php';

class TipoMascotaModel
{
    private $cn;

    public function __construct()
    {
        $this->cn = new CNpdo();
    }

    public function search($filters = array())
    {
        $sql = "SELECT * FROM TiposMascota WHERE estado = 'Activo'";
        $params = array();

        if (!empty($filters['search'])) {
            $sql .= " AND (nombre LIKE ? OR CAST(id_tipo AS CHAR) LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY nombre ASC";
        $rows = $this->cn->consulta($sql, $params);

        $out = array();
        foreach ($rows as $r) {
            $tipo = new TipoMascota($r['id_tipo'], $r['nombre'], $r['descripcion']);
            $tipo->setEstado($r['estado']);
            $out[] = $tipo;
        }
        return $out;
    }

    public function getAll()
    {
        return $this->search();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM TiposMascota WHERE id_tipo = ? AND estado = 'Activo'";
        $rows = $this->cn->consulta($sql, [$id]);
        if ($rows) {
            $r = $rows[0];
            $tipo = new TipoMascota($r['id_tipo'], $r['nombre'], $r['descripcion']);
            $tipo->setEstado($r['estado']);
            return $tipo;
        }
        return null;
    }

    public function insert($obj)
    {
        $sql = "INSERT INTO TiposMascota (nombre, descripcion, estado) VALUES (?, ?, 'Activo')";
        return $this->cn->ejecutar($sql, [$obj->getNombre(), $obj->getDescripcion()]);
    }

    public function update($obj)
    {
        $sql = "UPDATE TiposMascota SET nombre = ?, descripcion = ? WHERE id_tipo = ? AND estado = 'Activo'";
        return $this->cn->ejecutar($sql, [$obj->getNombre(), $obj->getDescripcion(), $obj->getIdTipo()]);
    }

    public function delete($obj)
    {
        $sql = "UPDATE TiposMascota SET estado = 'Inactivo' WHERE id_tipo = ?";
        return $this->cn->ejecutar($sql, [$obj->getIdTipo()]);
    }
}
?>