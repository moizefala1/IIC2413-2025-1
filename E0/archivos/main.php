<?php
require 'funciones.php';
//USUARIOS RESCATADOS (lectura)
$usuarios_rescatados_ruta =  __DIR__ . '/../CSV_sucios/usuarios_rescatados.csv';
$usuarios_rescatados = fopen($usuarios_rescatados_ruta, "r");

//EMPLEADOS RESCATADOS (lectura)
$empleados_rescatados_ruta = __DIR__ . '/../CSV_sucios/empleados_rescatados.csv';
$empleados_rescatados = fopen($empleados_rescatados_ruta, "r");

//USUARIOS DESCARTADOS (escritura)
$usuarios_descartados_ruta = __DIR__ . '/../CSV_limpios/datos_descartados_usuarios.csv';
$usuarios_descartados = fopen ($usuarios_descartados_ruta, "w");

//EMPLEADOS DESCARTADOS (escritura)
$empleados_descartados_ruta = __DIR__ . '/../CSV_limpios/datos_descartados_empleados.csv';
$empleados_descartados = fopen ($empleados_descartados_ruta, "w");


//personasOK (escritura)
$personasOK_ruta = __DIR__ . '/../CSV_limpios/personasOK.csv';
$personasOK = fopen($personasOK_ruta, "w");

//usuariosOK (escritura)
$usuariosOK_ruta = __DIR__ . '/../CSV_limpios/usuariosOK.csv';
$usuariosOK = fopen($usuariosOK_ruta, "w");

//empleadosOK (escritura)
$empleadosOK_ruta = __DIR__ . '/../CSV_limpios/empleadosOK.csv';
$empleadosOK = fopen($empleadosOK_ruta, "w");

//agendaOK (escritura)
$agendaOK_ruta = __DIR__ . '/../CSV_limpios/agendaOK.csv';
$agendaOK = fopen($agendaOK_ruta, "w");

//reservasOK (escritura)
$reservasOK_ruta = __DIR__ . '/../CSV_limpios/reservasOK.csv';
$reservasOK = fopen($reservasOK_ruta, "w");

//transportesOK (escritura) 
$transportesOK_ruta = __DIR__ . '/../CSV_limpios/transportesOK.csv';
$transportesOK = fopen($transportesOK_ruta, "w");

//busesOK (escritura)  
$busesOK_ruta = __DIR__ . '/../CSV_limpios/busesOK.csv';
$busesOK = fopen($busesOK_ruta, "w");

//trenesOK (escritura)
$trenesOK_ruta = __DIR__ . '/../CSV_limpios/trenesOK.csv';
$trenesOK = fopen($trenesOK_ruta, "w");

//avionesOK (escritura)
$avionesOK_ruta = __DIR__ . '/../CSV_limpios/avionesOK.csv';
$avionesOK = fopen($avionesOK_ruta, "w");


$correo_anterior = null;
while ((feof($usuarios_rescatados) !== true)){
    $linea = fgets($usuarios_rescatados);
    $tupla = explode(",", $linea);

    //Limpiamos datos
    $tupla = check_nombre($tupla);
    $tupla = check_rut($tupla);
    $tupla = check_dv($tupla);


    $correo_valido = check_correo($tupla);
    //Borramos datos en caso de no tener correo
    //(correo es el identificador)
    if ($correo_valido[0] == 0){
        $tupla = implode(",", $tupla);
        fwrite($usuarios_descartados, $tupla);
        continue;
    }
    else{
        $tupla = $correo_valido[1];
    }

    $tupla = check_usuario($tupla);
    $tupla = check_contrasena($tupla);
    $tupla = check_telefono($tupla);
    $tupla = check_puntos($tupla);
    $tupla = check_codigo_agenda($tupla);
    $tupla = check_etiqueta($tupla);
    $tupla = check_codigo_reserva($tupla);
    $tupla = check_fecha($tupla);
    $tupla = check_monto($tupla);
    $tupla = check_personas($tupla);
    //Escribimos los datos en los archivos correspondientes
    if ($tupla[3] != $correo_anterior){
        $tupla_usuarios = [$tupla[0], $tupla[1], $tupla[2], $tupla[3], $tupla[4], $tupla[5],
        $tupla[6], $tupla[7]];

        $tupla_personas =  [$tupla[0], $tupla[1], $tupla[2], $tupla[3], $tupla[4], $tupla[5],
        $tupla[6]];

        fwrite($personasOK, implode(",", $tupla_personas)."\n");
        fwrite($usuariosOK, implode(",", $tupla_usuarios)."\n");
    }
    
    $tupla_agenda = [$tupla[3], $tupla[8], $tupla[9]];
    if ($tupla_agenda[1] != -1 and $tupla_agenda[0] != $correo_anterior){
        fwrite($agendaOK, implode(",", $tupla_agenda)."\n");
    }

    $correo_anterior = $tupla[3];  


};


fclose($usuarios_rescatados);

// Procesar empleados_rescatados
$correo_anterior = null;
while (($tupla = fgetcsv($empleados_rescatados, 0, ',', '"', '\\')) !== false){
    // Limpiamos datos
    $tupla = check_nombre($tupla);
    $tupla = check_rut($tupla);
    $tupla = check_dv($tupla);

    $correo_valido = check_correo($tupla);
    // Borramos datos en caso de no tener correo (correo es el identificador)
    if ($correo_valido[0] == 0) {
        fputcsv($empleados_descartados, $tupla, ',', '"', '\\');
        continue;
    } else {
        $tupla = $correo_valido[1];
    }

    $tupla = check_usuario($tupla);
    $tupla = check_contrasena($tupla);
    $tupla = check_telefono($tupla);
    $tupla = check_jornada($tupla);
    $tupla = check_isapre($tupla);
    $tupla = check_contrato($tupla);
    $tupla = check_codigo_reserva_empleado($tupla);
    $tupla = check_codigo_agenda_empleado($tupla);
    $tupla = check_fecha_empleado($tupla);
    $tupla = check_monto_empleado($tupla);
    $tupla = check_personas_empleado($tupla);
    $tupla = check_disponibilidad($tupla);
    $tupla = check_num_viaje($tupla);
    $tupla = check_origen($tupla);
    $tupla = check_destino($tupla);
    $tupla = check_fecha_salida($tupla);
    $tupla = check_fecha_llegada($tupla);
    $tupla = check_capacidad($tupla);
    $tupla = check_tiempo_estimado($tupla);
    $tupla = check_precio_asiento($tupla);
    $tupla = check_empresa($tupla);
    $tupla = check_bus($tupla);
    $tupla = check_comodidades($tupla);
    $tupla = check_escalas($tupla);
    $tupla = check_clase($tupla);
    $tupla = check_paradas($tupla);

    if ($tupla[3] != $correo_anterior){
        $tupla_empleados = [$tupla[0], $tupla[1], $tupla[2], $tupla[3], $tupla[4], $tupla[5],
        $tupla[6], $tupla[7], $tupla[8], $tupla[9]];

        $tupla_personas =  [$tupla[0], $tupla[1], $tupla[2], $tupla[3], $tupla[4], $tupla[5],
        $tupla[6]];

        fwrite($personasOK, implode(",", $tupla_personas)."\n");
        fputcsv($empleadosOK, $tupla_empleados, ',', '"', '\\');
    }
    
    $tupla_reservas = [$tupla[11], $tupla[10], $tupla[12], $tupla[13], $tupla[14], $tupla[15]];
    if ($tupla_reservas[1] != -1 and $tupla_reservas[0] != ""){
        fwrite($agendaOK, implode(",", $tupla_reservas)."\n");
    }
    fwrite($reservasOK, implode(",", $tupla_reservas)."\n");
    






    $correo_anterior = $tupla[3];  
    
    


}
fclose($empleados_rescatados);
?>