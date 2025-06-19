<?php
function conectarBD() {
    $host = 'bdd1.ing.puc.cl'; // Cambiar al servidor bdd1.ing.puc.cl si se quiere usar el servidor remoto
    $dbname = 'lvaro.panozo.e3'; // Nombre de usuario
    $usuario = 'lvaro.panozo.e3'; // Nombre de usuario
    $clave = '24664057'; // Número de alumno

    try {
        $db = new PDO("pgsql:host=$host;dbname=$dbname", $usuario, $clave);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }
}


function limpiar_string($text) {
    $text = trim($text);
    return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
}
?>