<?php

class Contenido
{
    public static $contenido = [
        "mascota" => "controladores/mascotacontroller.php",
        "tipomascota" => "controladores/tipomascotacontroller.php",
        "adopcion" => "controladores/adopcioncontroller.php", // <-- Nuevo controlador
        "login" => "controladores/logincontroller.php"
        // Elimina "a" => "controladores/adoptantecontroller.php",
        "estadisticas" => "controladores/estadisticascontroller.php"
    ];

    public static function obtenerContenido($clave) {
        $vista=self::$contenido[$clave] ?? null;
        return $vista ?: "vistas/404.php";                
    }
}

?>