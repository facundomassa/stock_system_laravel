<?php
namespace App\Helpers;

class Helpers {

    //convertir fecha al idioma español
    public static function x_fechaEspañol($fecha){
        setlocale(LC_TIME, "spanish");
        return strftime("%d de %B", strtotime($fecha));
    }

}
