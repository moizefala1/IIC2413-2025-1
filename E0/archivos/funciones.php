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
    if (empty($tupla[1])){
        $tupla[1] = 0;
        return $tupla;
    }
    $tupla[1] = str_replace(".", "", $tupla[1]);
    return $tupla;
};
function check_dv($tupla){
    if(empty($tupla[2])){
        $tupla[2] = 10;
        return $tupla;
    }
    elseif ($tupla[2] == 'K' || $tupla[2] == 'k'){
        return $tupla;
    }
    else {
        $tupla[2] = (int)$tupla[2];
        if ($tupla[2] >= 0 && $tupla[2] <= 9 ) {
            return $tupla;
        } 
        else {
            $tupla[2] = 10;
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
    $tupla[7] = $puntos;
    return $tupla;

};
function check_codigo_agenda($tupla){
    $codigo_agenda = $tupla[8];
    if (empty($codigo_agenda)){
        $tupla[8] = -1;
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

function check_fecha($tupla){
    $fecha = $tupla[11];
    if (empty($fecha)){
        $tupla[11] = null;
        return $tupla;
    }
    elseif (gettype($fecha) != "string"){
        $tupla[11] = (string)$fecha;
        return $tupla;
    }
    $fecha = str_replace("/", "-", $fecha); 
    $tupla[11] = $fecha;
    return $tupla;
};

function check_monto ($tupla){
    $monto = $tupla[12];
    if (empty($monto)){
        $tupla[12] = -1.0;
    }
    elseif (!is_numeric($monto)){
        $monto = floatval($monto);
    }
    return $tupla;

};

function check_personas($tupla){
    if (empty($tupla[13])){
        $tupla[13] = null;
    }
    elseif (!is_numeric($tupla[13])){
        $tupla[13] = (int)$tupla[13];
    }
    return $tupla;

};

function check_jornada ($tupla){
    if (empty($tupla[7])){
        $tupla[7] = null;
    }
    return $tupla;
}

function check_isapre ($tupla){
    if (empty($tupla[8])){
        $tupla[8] = null;
    }
    return $tupla;
}

function check_contrato ($tupla){
    if (empty($tupla[9])){
        $tupla[9] = null;
    }
    return $tupla;
}

function check_codigo_reserva_empleado($tupla){
    if (!is_numeric($tupla[10])){
        $tupla[10] = (int)$tupla[10];
    }
    if (empty($tupla[10])){
        $tupla[10] = -1;
    }
    return $tupla;
}

function check_codigo_agenda_empleado($tupla){
    if (!is_numeric($tupla[11])){
        $tupla[11] = (int)$tupla[11];
    }
    if (empty($tupla[11])){
        $tupla[11] = null;
    }
    return $tupla;
}

function check_fecha_empleado($tupla){
    if (empty($tupla[12])){
        $tupla[12] = null;
        return $tupla;
    }
    $tupla[12] = str_replace("/", "-", $tupla[12]);
    return $tupla;
}

function check_monto_empleado($tupla){
    if (empty($tupla[13])){
        $tupla[13] = -1.0;
    }
    elseif (!is_numeric($tupla[13])){
        $tupla[13] = floatval($tupla[13]);
    }
    return $tupla;
}

function check_personas_empleado($tupla){
    if (empty($tupla[14])){
        $tupla[14] = null;
    }
    elseif (!is_numeric($tupla[14])){
        $tupla[14] = (int)$tupla[14];
    }
    return $tupla;
}

function check_disponibilidad($tupla){
    if ($tupla[15] != 'Disponible' || $tupla[15] != 'No Disponible'){
        $tupla[15] = null;
    }
    return $tupla;
}

function check_num_viaje($tupla){
    if (empty($tupla[16])){
        $tupla[16] = -1;
    }
    elseif (!is_numeric($tupla[16])){
        $tupla[16] = (int)$tupla[16];
    }
    return $tupla;
}

function check_origen($tupla){
    if (empty($tupla[17])){
        $tupla[17] = null;
        return $tupla;
    }
    $tupla[17] = str_replace("#", "", $tupla[17]);
    $tupla[17] = str_replace("-", " ", $tupla[17]);
    return $tupla;
}

function check_destino($tupla){
    if (empty($tupla[18])){
        $tupla[18] = null;
        return $tupla;
    }
    $tupla[18] = str_replace("!", "", $tupla[18]);
    $tupla[18] = str_replace("-", " ", $tupla[18]);
    return $tupla;
}

function check_fecha_salida($tupla){
    if (empty($tupla[19])){
        $tupla[19] = null;
        return $tupla;
    }
    $tupla[19] = str_replace("/", "-", $tupla[19]);
    return $tupla;
}

function check_fecha_llegada($tupla){
    if (empty($tupla[20])){
        $tupla[20] = "0000-00-00";
        return $tupla;
    }
    $tupla[20] = str_replace("/", "-", $tupla[20]);
    return $tupla;
}

function check_capacidad($tupla){
    if (empty($tupla[21])){
        $tupla[21] = null;
        return $tupla;
    }
    elseif (!is_numeric($tupla[21])){
        $tupla[21] = (int)$tupla[21];
    }
    return $tupla;
}

function check_tiempo_estimado($tupla){
    if (empty($tupla[22])){
        $tupla[22] = null;
        return $tupla;
    }
    elseif (!is_numeric($tupla[22])){
        $tupla[22] = (int)$tupla[22];
    }
    return $tupla;
}
function check_precio_asiento($tupla){
    if (empty($tupla[23])){
        $tupla[23] = -1;
        return $tupla;
    }
    elseif (!is_numeric($tupla[23])){
        $tupla[23] = (int)$tupla[23];
    }
    return $tupla;
}

function check_empresa($tupla){
    if (empty($tupla[24])){
        $tupla[24] = null;
        return $tupla;
    }
    return $tupla;
}

function check_bus($tupla){
    if (empty($tupla[25])){
        $tupla[25] = null;
        return $tupla;
    }
    return $tupla;
}

function check_comodidades($tupla){
    if (empty($tupla[26])){
        $tupla[26] = null;
        return $tupla;
    }

    return $tupla;
}
?> 