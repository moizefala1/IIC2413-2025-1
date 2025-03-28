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

//personasOK (escritura)
$personasOK_ruta = __DIR__ . '/../CSV_limpios/personasOK.csv';
$personasOK = fopen($personasOK_ruta, "w");

//temporales (escritura)
$temporales_ruta = __DIR__ . '/../CSV_limpios/temporales.csv';
$temporales = fopen($temporales_ruta, "w");



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
    $tupla = implode(",", $tupla);
    fwrite($temporales, $tupla);

}
fclose($usuarios_rescatados);
fclose($temporales);
echo "Contenido del archivo temporal:\n";
echo "--------------------------------\n";
$temp_content = file_get_contents($temporales_ruta);
echo $temp_content;
echo "--------------------------------\n";
unlink($temporales_ruta);

?>
