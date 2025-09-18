<?php
class Adoptante
{
    private $id_adoptante;
    private $nombre;
    private $telefono;
    private $id_mascota; // puede ser null
    private $estado = 'Activo';

    public function __construct($id_adoptante = null, $nombre = '', $telefono = '', $id_mascota = null)
    {
        $this->id_adoptante = $id_adoptante;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->id_mascota = $id_mascota;
    }
    public function getIdAdoptante()
    {
        return $this->id_adoptante;
    }
    public function setIdAdoptante($v)
    {
        $this->id_adoptante = $v;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($v)
    {
        $this->nombre = $v;
    }
    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setTelefono($v)
    {
        $this->telefono = $v;
    }
    public function getIdMascota()
    {
        return $this->id_mascota;
    }
    public function setIdMascota($v)
    {
        $this->id_mascota = $v;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($v)
    {
        $this->estado = $v;
    }
}
?>