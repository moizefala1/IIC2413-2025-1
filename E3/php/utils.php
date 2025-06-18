<?php
function conectarBD() {
    $host = 'localhost'; // Cambiar al servidor bdd1.ing.puc.cl si se quiere usar el servidor remoto
    $dbname = 'e3'; // Nombre de usuario
    $usuario = 'booked'; // Nombre de usuario
    $clave = 'bdd2025-1'; // Número de alumno

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