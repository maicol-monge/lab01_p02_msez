<?php
// Clase Mascota alineada con la tabla Mascotas (refugio) manteniendo los nombres
// usados en controladores/vistas antiguos para minimizar cambios.
// Mapeo:
//  idmascota -> id_mascota
//  idraza (legacy) -> id_tipo
//  nom_mascota -> nombre
//  foto -> foto
//  raza -> objeto TipoMascota (legacy nombre)
class Mascota
{
    private $idmascota;      // id_mascota
    private $id_tipo;        // nuevo (antes idraza)
    private $nom_mascota;    // nombre
    private $foto;           // foto
    private $tipo;           // TipoMascota (antes $raza)
    private $estado = 'Activo';       // Estado del registro (Activo/Inactivo)
    private $estado_adopcion = 'Disponible'; // Estado de adopción (Disponible/Adoptado)

    public function __construct($idmascota = null, $id_tipo = null, $nom_mascota = '', $foto = '', $tipo = null)
    {
        $this->idmascota = $idmascota;
        $this->id_tipo = $id_tipo;
        $this->nom_mascota = $nom_mascota;
        $this->foto = $foto;
        $this->tipo = $tipo;
    }

    public function getIdmascota()
    {
        return $this->idmascota;
    }
    public function setIdmascota($v)
    {
        $this->idmascota = $v;
    }
    public function getIdTipo()
    {
        return $this->id_tipo;
    }
    public function setIdTipo($v)
    {
        $this->id_tipo = $v;
    }

    public function getNomMascota()
    {
        return $this->nom_mascota;
    }
    public function setNomMascota($v)
    {
        $this->nom_mascota = $v;
    }
    public function getFoto()
    {
        return $this->foto;
    }
    public function setFoto($v)
    {
        $this->foto = $v;
    }
    // Acceso al objeto TipoMascota
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setTipo($v)
    {
        $this->tipo = $v;
    }

    public function getEstado()
    {
        return $this->estado;
    }
    public function setEstado($v)
    {
        $this->estado = $v;
    }

    public function getEstadoAdopcion()
    {
        return $this->estado_adopcion;
    }
    public function setEstadoAdopcion($v)
    {
        $this->estado_adopcion = $v;
    }
}
?>