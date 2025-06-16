<?php
session_start();
require_once 'utils.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$db = ConectarBD();
$db->beginTransaction();

$query_transportes = "SELECT t.*, r.estado_disponibilidad 
                      FROM transporte t 
                      JOIN reserva r ON t.id = r.id 
                      WHERE r.estado_disponibilidad = 'Disponible'
                      ORDER BY t.empresa, t.lugar_origen";
$stmt_transportes = $db->prepare($query_transportes);
$stmt_transportes->execute();
$transportes = $stmt_transportes->fetchAll(PDO::FETCH_ASSOC);

$query_panoramas = "SELECT p.*, r.estado_disponibilidad 
                    FROM panorama p 
                    JOIN reserva r ON p.id = r.id 
                    WHERE r.estado_disponibilidad = 'Disponible'
                    ORDER BY p.empresa, p.nombre";
$stmt_panoramas = $db->prepare($query_panoramas);
$stmt_panoramas->execute();
$panoramas = $stmt_panoramas->fetchAll(PDO::FETCH_ASSOC);

$query_hospedajes = "SELECT h.*, r.estado_disponibilidad 
                     FROM hospedaje h 
                     JOIN reserva r ON h.id = r.id 
                     WHERE r.estado_disponibilidad = 'Disponible'
                     ORDER BY h.nombre_hospedaje, h.ubicacion";
$stmt_hospedajes = $db->prepare($query_hospedajes);
$stmt_hospedajes->execute();
$hospedajes = $stmt_hospedajes->fetchAll(PDO::FETCH_ASSOC);
$db->commit();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Viaje</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #555;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .section {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            background-color: white;
        }
        
        /* Contenedor con scroll para cada sección */
        .section-content {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        /* Estilos para las barras de scroll de cada sección */
        .section-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .section-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .section-content::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 4px;
        }
        
        .section-content::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }
        
        /* Estilos específicos para cada tipo de sección */
        .transportes-content::-webkit-scrollbar-thumb {
            background: #28a745;
        }
        
        .transportes-content::-webkit-scrollbar-thumb:hover {
            background: #1e7e34;
        }
        
        .panoramas-content::-webkit-scrollbar-thumb {
            background: #fd7e14;
        }
        
        .panoramas-content::-webkit-scrollbar-thumb:hover {
            background: #e55b00;
        }
        
        .hospedajes-content::-webkit-scrollbar-thumb {
            background: #6f42c1;
        }
        
        .hospedajes-content::-webkit-scrollbar-thumb:hover {
            background: #5a32a3;
        }
        
        .item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        .item input[type="checkbox"] {
            margin-right: 15px;
            margin-top: 5px;
            transform: scale(1.2);
        }
        .item-info {
            flex: 1;
        }
        .item-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .item-details {
            color: #666;
            font-size: 14px;
            line-height: 1.4;
        }
        .btn-submit {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .no-items {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        
        /* Indicador de scroll */
        .scroll-indicator {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-bottom: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nuevo Viaje</h1>
        
        <form action="procesar_crear_viaje.php" method="POST">
            <div class="form-group">
                <label for="nombre_viaje">Nombre del viaje:</label>
                <input type="text" id="nombre_viaje" name="nombre_viaje" required 
                       placeholder="Ingresa el nombre de tu viaje">
            </div>

            <div class="scroll-indicator">
                📋 Selecciona los servicios para tu viaje
            </div>
            
            <!-- SECCIÓN TRANSPORTES -->
            <div class="section">
                <h2>🚌 Transportes Disponibles</h2>
                <div class="section-content transportes-content">
                    <?php if (count($transportes) > 0): ?>
                        <?php foreach ($transportes as $transporte): ?>
                            <div class="item">
                                <input type="checkbox" name="transportes[]" value="<?php echo $transporte['id']; ?>" 
                                       id="transporte_<?php echo $transporte['id']; ?>">
                                <div class="item-info">
                                    <div class="item-title">
                                        <?php echo htmlspecialchars($transporte['empresa']); ?> - 
                                        <?php echo htmlspecialchars($transporte['lugar_origen']); ?> → 
                                        <?php echo htmlspecialchars($transporte['lugar_llegada']); ?>
                                    </div>
                                    <div class="item-details">
                                        <strong>Precio:</strong> $<?php echo number_format($transporte['precio_asiento']); ?> por asiento<br>
                                        <strong>Fecha salida:</strong> <?php echo date('d/m/Y', strtotime($transporte['fecha_salida'])); ?><br>
                                        <?php if ($transporte['fecha_llegada']): ?>
                                            <strong>Fecha llegada:</strong> <?php echo date('d/m/Y', strtotime($transporte['fecha_llegada'])); ?><br>
                                        <?php endif; ?>
                                        <strong>Capacidad:</strong> <?php echo $transporte['capacidad']; ?> personas<br>
                                        <strong>Tiempo estimado:</strong> <?php echo $transporte['tiempo_estimado']; ?> horas
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-items">No hay transportes disponibles en este momento.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECCIÓN PANORAMAS -->
            <div class="section">
                <h2>🎯 Panoramas Disponibles</h2>
                <div class="section-content panoramas-content">
                    <?php if (count($panoramas) > 0): ?>
                        <?php foreach ($panoramas as $panorama): ?>
                            <div class="item">
                                <input type="checkbox" name="panoramas[]" value="<?php echo $panorama['id']; ?>" 
                                       id="panorama_<?php echo $panorama['id']; ?>">
                                <div class="item-info">
                                    <div class="item-title">
                                        <?php echo htmlspecialchars($panorama['nombre']); ?> - 
                                        <?php echo htmlspecialchars($panorama['empresa']); ?>
                                    </div>
                                    <div class="item-details">
                                        <strong>Descripción:</strong> <?php echo htmlspecialchars($panorama['descripcion']); ?><br>
                                        <strong>Ubicación:</strong> <?php echo htmlspecialchars($panorama['ubicacion']); ?><br>
                                        <?php if ($panorama['precio_persona']): ?>
                                            <strong>Precio:</strong> $<?php echo number_format($panorama['precio_persona']); ?> por persona<br>
                                        <?php endif; ?>
                                        <?php if ($panorama['duracion']): ?>
                                            <strong>Duración:</strong> <?php echo $panorama['duracion']; ?> horas<br>
                                        <?php endif; ?>
                                        <?php if ($panorama['capacidad']): ?>
                                            <strong>Capacidad:</strong> <?php echo $panorama['capacidad']; ?> personas<br>
                                        <?php endif; ?>
                                        <?php if ($panorama['fecha_panorama']): ?>
                                            <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($panorama['fecha_panorama'])); ?><br>
                                        <?php endif; ?>
                                        <?php if ($panorama['restricciones']): ?>
                                            <strong>Restricciones:</strong> <?php echo htmlspecialchars($panorama['restricciones']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-items">No hay panoramas disponibles en este momento.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECCIÓN HOSPEDAJES -->
            <div class="section">
                <h2>🏨 Hospedajes Disponibles</h2>
                <div class="section-content hospedajes-content">
                    <?php if (count($hospedajes) > 0): ?>
                        <?php foreach ($hospedajes as $hospedaje): ?>
                            <div class="item">
                                <input type="checkbox" name="hospedajes[]" value="<?php echo $hospedaje['id']; ?>" 
                                       id="hospedaje_<?php echo $hospedaje['id']; ?>">
                                <div class="item-info">
                                    <div class="item-title">
                                        <?php echo htmlspecialchars($hospedaje['nombre_hospedaje']); ?> - 
                                        <?php echo htmlspecialchars($hospedaje['ubicacion']); ?>
                                    </div>
                                    <div class="item-details">
                                        <strong>Precio:</strong> $<?php echo number_format($hospedaje['precio_noche']); ?> por noche<br>
                                        <?php if ($hospedaje['estrellas']): ?>
                                            <strong>Estrellas:</strong> <?php echo str_repeat('⭐', $hospedaje['estrellas']); ?><br>
                                        <?php endif; ?>
                                        <strong>Check-in:</strong> <?php echo date('d/m/Y', strtotime($hospedaje['fecha_checkin'])); ?><br>
                                        <?php if ($hospedaje['fecha_checkout']): ?>
                                            <strong>Check-out:</strong> <?php echo date('d/m/Y', strtotime($hospedaje['fecha_checkout'])); ?><br>
                                        <?php endif; ?>
                                        <?php if ($hospedaje['comodidades']): ?>
                                            <strong>Comodidades:</strong> <?php echo htmlspecialchars($hospedaje['comodidades']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-items">No hay hospedajes disponibles en este momento.</div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-submit">Crear Viaje</button>
        </form>
    </div>
</body>
</html>