<?php

//convertir fecha al idioma español
function x_fechaEspañol($fecha){
    setlocale(LC_TIME, "spanish");
    return strftime("%d de %B", strtotime($fecha));
}

?>