<?php
class TipoMascota
{
    private $id_tipo;
    private $nombre;
    private $descripcion;
    private $estado = 'Activo';

    public function __construct($id_tipo = null, $nombre = '', $descripcion = '')
    {
        $this->id_tipo = $id_tipo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }
    public function getIdTipo()
    {
        return $this->id_tipo;
    }
    public function setIdTipo($id_tipo)
    {
        $this->id_tipo = $id_tipo;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
}
?>