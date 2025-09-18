<?php

class Contenido {
    public static $contenido = [        
        "mascota" => "controladores/mascotacontroller.php",
        "tipomascota" => "controladores/tipomascotacontroller.php",
        "adoptante" => "controladores/adoptantecontroller.php"
    ];    

    public static function obtenerContenido($clave) {
        $vista=self::$contenido[$clave] ?? null;
        return $vista ?: "vistas/404.php";                
    }

}

?>