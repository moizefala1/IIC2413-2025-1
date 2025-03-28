<?php

function check_nombre($tupla){
    if (empty($tupla[0])){
        $tupla[0] = null;
        return $tupla;
    } 
    elseif (gettype($tupla[0]) != "string"){
        $tupla[0] = (string)$tupla[0];
        return $tupla;
    }
    
    return $tupla;

};


function check_rut($tupla){ 
    $rut = $tupla[1];
    $rut = str_replace(".", "", $rut);
    if ($rut == ""){
        $rut = 0;
    }
    $tupla[1] = $rut;
    return $tupla;
};
function check_dv($tupla){
    $dv = $tupla[2];
    if(empty($dv)){
        $dv = 10;
        $tupla[2] = $dv;
        return $tupla;
    }
    elseif (!is_numeric($dv) && $dv == 'K' || $dv == 'k'){
        return $tupla;
    }
    else {
        $dv = (int)$dv;
        if ($dv >= 0 && $dv <= 9 ) {
            return $tupla;
        } 
        else {
            $dv = 10;
            $tupla[2] = $dv;
            return $tupla;
        }
}
};

function check_correo($tupla){
    $correo = $tupla[3];
    if (empty($correo)){
        return [0, $tupla];
    }
    $dominios_permitidos = ['viajes.cl', 'tourket.com',
    'wass.com', 'marmol.com', 'outluc.com', 'edubus.cal', 'viajesanma.com'];
    $correo_array = explode("@", $correo);

    if (count($correo_array) > 2){
        $correo = $correo_array[0] . '@' . implode('', array_slice($correo_array, 1));
    };
    $correo = explode("@", $correo);
    if (count($correo) !=2){
        return [0, $tupla];
    }
    $dominio = $correo[1];
    $correo = $correo[0];

    if (strlen($correo) < 1){
        return [0, $tupla];
    }

    if ($dominio === null || empty($dominio)) {
        return [0, $tupla];
    }

    $dominio = explode(".", $dominio);
    if (count($dominio) < 2){
        return [0, $tupla];
    }
    if ($dominio[1] == 'ceele'){
        if ($dominio[0] == 'viajes'){
            $dominio[1] = 'cl';
        }
        elseif ($dominio[0] == 'edubus'){
            $dominio[1] = 'cal';
        }
        else {
            $dominio[1] = 'com';
        }
    }
    $dominio = implode(".", $dominio);

    if (in_array($dominio, $dominios_permitidos)) {
        $correo = $correo . '@' . $dominio;
        $tupla[3] = $correo;
        return [1, $tupla];
    } else {
        return [0, $tupla];
    }
};

function check_usuario($tupla){
    $usuario = $tupla[4];
    if (empty($usuario)){
        $tupla[4] = null;
        return $tupla;
    }
    elseif (gettype($usuario) != "string"){
        $tupla[4] = (string)$usuario;
        return $tupla;
    }
    return $tupla;
};

function check_contrasena($tupla){
    $contrasena = $tupla[5];
    if (empty($contrasena)){
        $tupla[5] = null;
        return $tupla;
    }
    elseif (gettype($contrasena) != "string"){
        $tupla[5] = (string)$contrasena;
        return $tupla;
    }
    return $tupla;
};

function check_telefono ($tupla){
    $telefono = $tupla[6];
    if (empty($telefono)){
        $tupla[6] = '0 0000 0000';
        return $tupla;
    }
    elseif (gettype($telefono) != "string"){
        $telefono = (string)$telefono;
    }

    $telefono = str_replace("-", " ", $telefono);
    $tupla[6] = $telefono;
    return $tupla;
    };

function check_puntos($tupla){
    $puntos = $tupla[7];
    if (empty($puntos)){
        $tupla[7] = null;
        return $tupla;
    }
    elseif (!is_numeric($puntos)){
        $puntos = (int)$puntos;
    }
    elseif ($puntos < 0){
        $puntos = 0;
    }
    return $tupla;
};

function check_codigo_agenda($tupla){
    $codigo_agenda = $tupla[8];
    if (empty($codigo_agenda)){
        $tupla[8] = (-1);
        return $tupla;
    }
    elseif (!is_numeric($codigo_agenda)){
        $codigo_agenda = (int)$codigo_agenda;
    }
    $tupla[8] = $codigo_agenda;
    return $tupla;
};

function check_etiqueta($tupla){
    $etiqueta = $tupla[9];
    if (empty($etiqueta)){
        $tupla[9] = null;
        return $tupla;
    }
    elseif (gettype($etiqueta) != "string"){
        $tupla[9] = (string)$etiqueta;
        return $tupla;
    }
    return $tupla;
};

function check_codigo_reserva($tupla){
    $codigo_reserva = $tupla[10];
    if (empty($codigo_reserva)){
        $tupla[10] = -1;
        return $tupla;
    }
    elseif (!is_numeric($codigo_reserva)){
        $tupla[10] = (int)$codigo_reserva;
        return $tupla;
    }
    return $tupla;
};

?> 