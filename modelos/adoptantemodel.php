<?php
require_once 'config/cn.php';
require_once 'clases/adoptante.php';

class AdoptanteModel
{
    private $cn;
    public function __construct()
    {
        $this->cn = new CNpdo();
    }

    public function search($filters = array())
    {
        $sql = "SELECT a.*, m.nombre AS nom_mascota, m.foto, t.nombre AS tipo_nombre
                FROM Adoptantes a
                LEFT JOIN Mascotas m ON m.id_mascota = a.id_mascota
                LEFT JOIN TiposMascota t ON t.id_tipo = m.id_tipo
                WHERE a.estado = 'Activo'";

        $params = array();

        if (!empty($filters['search'])) {
            $sql .= " AND (a.nombre LIKE ? OR CAST(a.id_adoptante AS CHAR) LIKE ? OR a.telefono LIKE ? OR m.nombre LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY a.nombre ASC";
        $rows = $this->cn->consulta($sql, $params);

        $out = array();
        foreach ($rows as $r) {
            $a = new Adoptante($r['id_adoptante'], $r['nombre'], $r['telefono'], $r['id_mascota']);
            $a->setEstado($r['estado']);
            $a->nom_mascota = $r['nom_mascota'] ?? null;
            $a->foto_mascota = $r['foto'] ?? null;
            $a->tipo_mascota = $r['tipo_nombre'] ?? null;
            $out[] = $a;
        }
        return $out;
    }

    public function getAll()
    {
        return $this->search();
        $out = [];
        foreach ($rows as $r) {
            $a = new Adoptante($r['id_adoptante'], $r['nombre'], $r['telefono'], $r['id_mascota']);
            $a->setEstado($r['estado']);
            $a->nom_mascota = $r['nom_mascota'] ?? null;
            $a->foto_mascota = $r['foto'] ?? null;
            $a->tipo_mascota = $r['tipo_nombre'] ?? null;
            $out[] = $a;
        }
        return $out;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM Adoptantes WHERE id_adoptante = ? AND estado = 'Activo'";
        $rows = $this->cn->consulta($sql, [$id]);
        if ($rows) {
            $r = $rows[0];
            $adoptante = new Adoptante($r['id_adoptante'], $r['nombre'], $r['telefono'], $r['id_mascota']);
            $adoptante->setEstado($r['estado']);
            return $adoptante;
        }
        return null;
    }

    public function insert($obj)
    {
        $sql = "INSERT INTO Adoptantes (nombre, telefono, id_mascota, estado) VALUES (?,?,?,'Activo')";
        $result = $this->cn->ejecutar($sql, [$obj->getNombre(), $obj->getTelefono(), $obj->getIdMascota()]);

        // Si se asigna una mascota, actualizar su estado de adopción
        if ($result && $obj->getIdMascota()) {
            $sql = "UPDATE Mascotas SET estado_adopcion = 'Adoptado' WHERE id_mascota = ?";
            $this->cn->ejecutar($sql, [$obj->getIdMascota()]);
        }

        return $result;
    }

    public function update($obj)
    {
        // Primero, obtenemos el adoptante actual para ver si cambió la mascota
        $actual = $this->getById($obj->getIdAdoptante());

        $sql = "UPDATE Adoptantes SET nombre = ?, telefono = ?, id_mascota = ? 
                WHERE id_adoptante = ? AND estado = 'Activo'";
        $result = $this->cn->ejecutar($sql, [
            $obj->getNombre(),
            $obj->getTelefono(),
            $obj->getIdMascota(),
            $obj->getIdAdoptante()
        ]);

        if ($result) {
            // Si la mascota cambió, actualizar estados
            if ($actual && $actual->getIdMascota() != $obj->getIdMascota()) {
                // Liberar la mascota anterior
                if ($actual->getIdMascota()) {
                    $sql = "UPDATE Mascotas SET estado_adopcion = 'Disponible' 
                           WHERE id_mascota = ?";
                    $this->cn->ejecutar($sql, [$actual->getIdMascota()]);
                }

                // Marcar la nueva mascota como adoptada
                if ($obj->getIdMascota()) {
                    $sql = "UPDATE Mascotas SET estado_adopcion = 'Adoptado' 
                           WHERE id_mascota = ?";
                    $this->cn->ejecutar($sql, [$obj->getIdMascota()]);
                }
            }
        }

        return $result;
    }

    public function delete($obj)
    {
        // Primero, liberamos la mascota si tiene una asignada
        $adoptante = $this->getById($obj->getIdAdoptante());
        if ($adoptante && $adoptante->getIdMascota()) {
            $sql = "UPDATE Mascotas SET estado_adopcion = 'Disponible' WHERE id_mascota = ?";
            $this->cn->ejecutar($sql, [$adoptante->getIdMascota()]);
        }

        // Luego, marcamos el adoptante como inactivo
        $sql = "UPDATE Adoptantes SET estado = 'Inactivo' WHERE id_adoptante = ?";
        return $this->cn->ejecutar($sql, [$obj->getIdAdoptante()]);
    }

    public function mascotasDisponibles()
    {
        // Mascotas activas y sin adoptante
        $sql = "SELECT m.id_mascota, m.nombre, m.foto, t.nombre AS tipo_nombre
                FROM Mascotas m
                JOIN TiposMascota t ON t.id_tipo = m.id_tipo
                WHERE m.estado = 'Activo' 
                AND m.estado_adopcion = 'Disponible'";
        return $this->cn->consulta($sql);
    }
}
?>