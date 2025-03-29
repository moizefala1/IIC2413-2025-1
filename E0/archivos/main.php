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

//temporales (escritura)
$temporales_usuario_ruta = __DIR__ . '/../CSV_limpios/temporales.csv';
$temporales_usuarios = fopen($temporales_usuario_ruta, "w");


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
    fputcsv($temporales_usuarios, $tupla, ',', '"', '\\');
    if ($tupla[3] != $correo_anterior){
        $tupla_usuarios = [$tupla[0], $tupla[1], $tupla[2], $tupla[3], $tupla[4], $tupla[5],
        $tupla[6], $tupla[7]];

        $tupla_personas =  [$tupla[0], $tupla[1], $tupla[2], $tupla[3], $tupla[4], $tupla[5],
        $tupla[6]];

        fwrite($personasOK, implode(",", $tupla_personas)."\n");
        fwrite($usuariosOK, implode(",", $tupla_usuarios)."\n");
    }
    $correo_anterior = $tupla[3];   
};


fclose($usuarios_rescatados);

$correo_anterior = null;
while (feof($empleados_rescatados)!= true){
    $linea = fgets($empleados_rescatados);
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
        fwrite($empleados_descartados, $tupla);
        continue;
    }
    else{
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
    $tupla = check_monto_empleado ($tupla);
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
    if ($tupla[10] == 537589){
        $comodidades = htmlspecialchars_decode($tupla[26]); // Convierte &quot; de vuelta a "
        echo $comodidades;
    }
    



    fwrite($empleadosOK, implode(",", $tupla));
};


unlink($temporales_usuario_ruta);

?>
