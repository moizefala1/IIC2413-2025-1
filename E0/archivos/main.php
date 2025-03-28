<?php
require 'funciones.php';
//USUARIOS RESCATADOS
$usuarios_rescatados_ruta =  __DIR__ . '/../CSV_sucios/usuarios_rescatados.csv';
$usuarios_rescatados = fopen($usuarios_rescatados_ruta, "r");

$usuarios_descartados_ruta = __DIR__ . '/../CSV_limpios/datos_descartados_usuarios.csv';
$usuarios_descartados = fopen ($usuarios_descartados_ruta, "w");

$personasOK_ruta = __DIR__ . '/../CSV_limpios/personasOK.csv';
$personasOK = fopen($personasOK_ruta, "w");


while (feof($usuarios_rescatados) !== true){
    $linea = fgets($usuarios_rescatados);
    $tupla = explode(",", $linea);
    //Verifica el nombre
    $tupla = check_nombre($tupla);
    //Verifica si el rut es correcto y lo limpia
    $tupla = check_rut($tupla);
    //Verificamos el DV y limpiamos en caso de no valido
    $tupla = check_dv($tupla);
    //Verificamos el correo 
    $correo_valido = check_correo($tupla);
    if ($correo_valido[0] == 0){
        $tupla = implode(",", $tupla);
        fwrite($usuarios_descartados, $tupla);
        continue;
    }
    else{
        $tupla = $correo_valido[1];
    }
    //Verificamos nombre de usuario
    $tupla = check_usuario($tupla);
    //Verificamos contraseña
    $tupla = check_contrasena($tupla);
    //Verificamos el telefono
    $tupla = check_telefono($tupla);
    //Verificamos puntos
    $tupla = check_puntos($tupla);
    //Verificamos el codigo de agenda
    $tupla = check_codigo_agenda($tupla);
    //Etiqueta
    $tupla = check_etiqueta($tupla);
    //Codigo de reserva
    $tupla = check_codigo_reserva($tupla);

    fwrite($personasOK, implode(",", $tupla));

}

fclose($usuarios_rescatados);
?>
