<?php
session_start();
require_once 'utils.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Verificar que se hayan enviado datos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: crear_viaje.php');
    exit();
}

$db = ConectarBD();

// Obtener datos del formulario
$nombre_viaje = trim($_POST['nombre_viaje']);
$transportes = isset($_POST['transportes']) ? $_POST['transportes'] : [];
$panoramas = isset($_POST['panoramas']) ? $_POST['panoramas'] : [];
$hospedajes = isset($_POST['hospedajes']) ? $_POST['hospedajes'] : [];

// Validar que se haya ingresado un nombre de viaje
if (empty($nombre_viaje)) {
    header('Location: crear_viaje.php?error=nombre_vacio');
    exit();
}

// Validar que se haya seleccionado al menos una reserva
$total_selecciones = count($transportes) + count($panoramas) + count($hospedajes);
if ($total_selecciones == 0) {
    header('Location: crear_viaje.php?error=sin_selecciones');
    exit();
}

try {
    // Iniciar transacción
    $db->beginTransaction();
    
    // 1. Insertar en la tabla agenda
    $correo_usuario = $_SESSION['usuario'];
    $query_agenda = "INSERT INTO agenda (correo_usuario, etiqueta) VALUES (?, ?) RETURNING id";
    $stmt_agenda = $db->prepare($query_agenda);
    $stmt_agenda->execute([$correo_usuario, $nombre_viaje]);
    
    $agenda_row = $stmt_agenda->fetch(PDO::FETCH_ASSOC);
    if (!$agenda_row) {
        throw new Exception("Error al crear la agenda");
    }
    
    $agenda_id = $agenda_row['id'];
    
    // 2. Actualizar reservas seleccionadas
    $reservas_actualizadas = 0;
    
    // Procesar transportes
    foreach ($transportes as $transporte_id) {
        $transporte_id = intval($transporte_id);
        $query_update = "UPDATE reserva SET estado_disponibilidad = 'No Disponible', agenda_id = ? WHERE id = ? AND estado_disponibilidad = 'Disponible'";
        $stmt_update = $db->prepare($query_update);
        $stmt_update->execute([$agenda_id, $transporte_id]);
        
        $reservas_actualizadas += $stmt_update->rowCount();
    }
    
    // Procesar panoramas
    foreach ($panoramas as $panorama_id) {
        $panorama_id = intval($panorama_id);
        $query_update = "UPDATE reserva SET estado_disponibilidad = 'No Disponible', agenda_id = ? WHERE id = ? AND estado_disponibilidad = 'Disponible'";
        $stmt_update = $db->prepare($query_update);
        $stmt_update->execute([$agenda_id, $panorama_id]);
        
        $reservas_actualizadas += $stmt_update->rowCount();
    }
    
    // Procesar hospedajes
    foreach ($hospedajes as $hospedaje_id) {
        $hospedaje_id = intval($hospedaje_id);
        $query_update = "UPDATE reserva SET estado_disponibilidad = 'No Disponible', agenda_id = ? WHERE id = ? AND estado_disponibilidad = 'Disponible'";
        $stmt_update = $db->prepare($query_update);
        $stmt_update->execute([$agenda_id, $hospedaje_id]);
        
        $reservas_actualizadas += $stmt_update->rowCount();
    }
    
    // Verificar que se hayan actualizado reservas
    if ($reservas_actualizadas == 0) {
        throw new Exception("No se pudieron reservar los elementos seleccionados. Es posible que ya no estén disponibles.");
    }
    
    // Confirmar transacción
    $db->commit();
    
    // Preparar mensaje de éxito
    $mensaje = "¡Viaje creado exitosamente!";
    $detalle = sprintf(
        "Se ha creado el viaje '%s' con %d reserva%s.",
        htmlspecialchars($nombre_viaje),
        $reservas_actualizadas,
        $reservas_actualizadas == 1 ? '' : 's'
    );
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $db->rollBack();
    
    $mensaje = "Error al crear el viaje";
    $detalle = $e->getMessage();
    $error = true;
}

$db = null;
?>