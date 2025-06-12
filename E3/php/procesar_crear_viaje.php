<?php
session_start();
require_once 'utils.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php?error=Debes iniciar sesión');
    exit();
}

$nombre = $_POST['nombre'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;
$ciudad = $_POST['ciudad'] ?? null;
$organizador = $_POST['organizador'] ?? null;

if (empty($nombre) || empty($fecha_inicio)) {
    header('Location: crear_viaje.php?error=Faltan campos obligatorios');
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Insertar en agenda (asumo estructura)
    $sqlAgenda = "INSERT INTO agenda (nombre, descripcion, fecha_inicio, fecha_fin, ciudad, organizador) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $stmtAgenda = $pdo->prepare($sqlAgenda);
    $stmtAgenda->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin, $ciudad, $organizador]);
    $agendaId = $stmtAgenda->fetchColumn();

    // 2. Insertar reservas (ejemplo simplificado)
    $sqlReserva = "INSERT INTO reserva (fecha, monto, cantidad_personas, estado_disponibilidad, puntos, agenda_id) 
                   VALUES (?, ?, ?, 'disponible', ?, ?)";
    $stmtReserva = $pdo->prepare($sqlReserva);
    $montoEjemplo = 50000; // Valor de ejemplo
    $puntos = $montoEjemplo / 1000; // Cálculo de puntos
    $stmtReserva->execute([$fecha_inicio, $montoEjemplo, 1, $puntos, $agendaId]);

    // 3. Llamar al SP para actualizar puntos del usuario (asumo SP existe)
    $sqlSP = "CALL sp_actualizar_puntos_usuario(?)";
    $stmtSP = $pdo->prepare($sqlSP);
    $stmtSP->execute([$organizador]);

    $pdo->commit();
    header('Location: crear_viaje.php?mensaje=Agenda creada correctamente');
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: crear_viaje.php?error=No se pudo crear el viaje: ' . $e->getMessage());
}
?>
