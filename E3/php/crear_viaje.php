<?php
session_start();
require_once 'utils.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$db = ConectarBD();
$db->beginTransaction();

// TRANSPORTES
$where_transporte = "r.estado_disponibilidad = 'Disponible'";
$params_transporte = [];

if (!empty($_GET['empresa_transporte'])) {
    $where_transporte .= " AND unaccent(LOWER(t.empresa)) LIKE LOWER(?)";
    $params_transporte[] = '%' . limpiar_string($_GET['empresa_transporte']) . '%'; 
}

if (!empty($_GET['origen_transporte']) || !empty($_GET['destino_transporte'])) {
    $where_transporte .= " AND (";
    $conditions = [];
    if (!empty($_GET['origen_transporte'])) {
        $conditions[] = "unaccent(LOWER(t.lugar_origen)) LIKE LOWER(?)";
        $params_transporte[] = '%' . limpiar_string($_GET['origen_transporte']) . '%';
    }
    if (!empty($_GET['destino_transporte'])) {
        $conditions[] = "unaccent(LOWER(t.lugar_llegada)) LIKE LOWER(?)";
        $params_transporte[] = '%' . limpiar_string($_GET['destino_transporte']) . '%';
    }
    $where_transporte .= implode(" OR ", $conditions) . ")";
}

if (!empty($_GET['precio_min_transporte'])) {
    $where_transporte .= " AND t.precio_asiento >= ?";
    $params_transporte[] = (int)$_GET['precio_min_transporte'];
}

if (!empty($_GET['precio_max_transporte'])) {
    $where_transporte .= " AND t.precio_asiento <= ?";
    $params_transporte[] = (int)$_GET['precio_max_transporte'];
}

if (!empty($_GET['fecha_salida_desde'])) {
    $where_transporte .= " AND t.fecha_salida >= ?";
    $params_transporte[] = $_GET['fecha_salida_desde'];
}

if (!empty($_GET['fecha_salida_hasta'])) {
    $where_transporte .= " AND t.fecha_salida <= ?";
    $params_transporte[] = $_GET['fecha_salida_hasta'];
}

if (!empty($_GET['capacidad_min_transporte'])) {
    $where_transporte .= " AND t.capacidad >= ?";
    $params_transporte[] = (int)$_GET['capacidad_min_transporte'];
}

$query_transportes = "SELECT t.*, r.estado_disponibilidad 
                      FROM transporte t 
                      JOIN reserva r ON t.id = r.id 
                      WHERE $where_transporte
                      ORDER BY t.empresa, t.lugar_origen";
$stmt_transportes = $db->prepare($query_transportes);
$stmt_transportes->execute($params_transporte);
$transportes = $stmt_transportes->fetchAll(PDO::FETCH_ASSOC);

//PAANORAMAS
$where_panorama = "r.estado_disponibilidad = 'Disponible'";
$params_panorama = [];

if (!empty($_GET['empresa_panorama'])) {
    $where_panorama .= " AND unaccent(LOWER(p.empresa)) LIKE LOWER(?)";
    $params_panorama[] = '%' . limpiar_string($_GET['empresa_panorama']) . '%';
}

if (!empty($_GET['nombre_panorama'])) {
    $where_panorama .= " AND unaccent(LOWER(p.nombre)) LIKE LOWER(?)";
    $params_panorama[] = '%' . limpiar_string($_GET['nombre_panorama']) . '%';
}

if (!empty($_GET['ubicacion_panorama'])) {
    $where_panorama .= " AND unaccent(LOWER(p.ubicacion)) LIKE LOWER(?)";
    $params_panorama[] = '%' . limpiar_string($_GET['ubicacion_panorama']) . '%';
}

if (!empty($_GET['precio_min_panorama'])) {
    $where_panorama .= " AND p.precio_persona >= ?";
    $params_panorama[] = (int)$_GET['precio_min_panorama'];
}

if (!empty($_GET['precio_max_panorama'])) {
    $where_panorama .= " AND p.precio_persona <= ?";
    $params_panorama[] = (int)$_GET['precio_max_panorama'];
}

if (!empty($_GET['duracion_min_panorama'])) {
    $where_panorama .= " AND p.duracion >= ?";
    $params_panorama[] = (int)$_GET['duracion_min_panorama'];
}

if (!empty($_GET['duracion_max_panorama'])) {
    $where_panorama .= " AND p.duracion <= ?";
    $params_panorama[] = (int)$_GET['duracion_max_panorama'];
}

if (!empty($_GET['fecha_panorama_desde'])) {
    $where_panorama .= " AND p.fecha_panorama >= ?";
    $params_panorama[] = $_GET['fecha_panorama_desde'];
}

if (!empty($_GET['fecha_panorama_hasta'])) {
    $where_panorama .= " AND p.fecha_panorama <= ?";
    $params_panorama[] = $_GET['fecha_panorama_hasta'];
}

if (!empty($_GET['capacidad_min_panorama'])) {
    $where_panorama .= " AND p.capacidad >= ?";
    $params_panorama[] = (int)$_GET['capacidad_min_panorama'];
}

$query_panoramas = "SELECT p.*, r.estado_disponibilidad 
                    FROM panorama p 
                    JOIN reserva r ON p.id = r.id 
                    WHERE $where_panorama
                    ORDER BY p.empresa, p.nombre";
$stmt_panoramas = $db->prepare($query_panoramas);
$stmt_panoramas->execute($params_panorama);
$panoramas = $stmt_panoramas->fetchAll(PDO::FETCH_ASSOC);

//HOSPEDAJE
$where_hospedaje = "r.estado_disponibilidad = 'Disponible'";
$params_hospedaje = [];

if (!empty($_GET['nombre_hospedaje'])) {
    $where_hospedaje .= " AND unaccent(LOWER(h.nombre_hospedaje)) LIKE LOWER(?)";
    $params_hospedaje[] = '%' . limpiar_string($_GET['nombre_hospedaje']) . '%';
}

if (!empty($_GET['ubicacion_hospedaje'])) {
    $where_hospedaje .= " AND unaccent(LOWER(h.ubicacion)) LIKE LOWER(?)";
    $params_hospedaje[] = '%' . limpiar_string($_GET['ubicacion_hospedaje']) . '%';
}

if (!empty($_GET['precio_min_hospedaje'])) {
    $where_hospedaje .= " AND h.precio_noche >= ?";
    $params_hospedaje[] = (int)$_GET['precio_min_hospedaje'];
}

if (!empty($_GET['precio_max_hospedaje'])) {
    $where_hospedaje .= " AND h.precio_noche <= ?";
    $params_hospedaje[] = (int)$_GET['precio_max_hospedaje'];
}

if (!empty($_GET['estrellas_min'])) {
    $where_hospedaje .= " AND h.estrellas >= ?";
    $params_hospedaje[] = (int)$_GET['estrellas_min'];
}

if (!empty($_GET['checkin_desde'])) {
    $where_hospedaje .= " AND h.fecha_checkin >= ?";
    $params_hospedaje[] = $_GET['checkin_desde'];
}

if (!empty($_GET['checkin_hasta'])) {
    $where_hospedaje .= " AND h.fecha_checkin <= ?";
    $params_hospedaje[] = $_GET['checkin_hasta'];
}

if (!empty($_GET['checkout_desde'])) {
    $where_hospedaje .= " AND h.fecha_checkout >= ?";
    $params_hospedaje[] = $_GET['checkout_desde'];
}

if (!empty($_GET['checkout_hasta'])) {
    $where_hospedaje .= " AND h.fecha_checkout <= ?";
    $params_hospedaje[] = $_GET['checkout_hasta'];
}

if (!empty($_GET['tipo_hospedaje'])) {
    $tipo = $_GET['tipo_hospedaje'];
    if ($tipo == 1) {
        $tabla = 'airbnb';
    } elseif ($tipo == 2) {
        $tabla = 'hotel';
    }
    $where_hospedaje .= " AND h.id IN (SELECT id FROM " . $tabla . ")";

    //Se hace de esta manera ya que con algun proxy podria suceder que alguien intervenga la solicitud, cambie el valor
    //de la string, y logra enviar una inyeccion sql, entonces se valida mediante numeros en lugar de reemplazar
    //la string directamente en la query.
}

$query_hospedajes = "SELECT h.*, r.estado_disponibilidad 
                     FROM hospedaje h 
                     JOIN reserva r ON h.id = r.id 
                     WHERE $where_hospedaje
                     ORDER BY h.nombre_hospedaje, h.ubicacion";
$stmt_hospedajes = $db->prepare($query_hospedajes);
$stmt_hospedajes->execute($params_hospedaje);
$hospedajes = $stmt_hospedajes->fetchAll(PDO::FETCH_ASSOC);

$db->commit();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Viaje</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            text-align: center;
            width: 70vw;
            height: 80vh;
            display: grid;
            grid-template-columns: 1fr;
            grid-template-rows: auto;
            overflow-y: auto;
        }
        /*FILTROS*/
        .filtros {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
                
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        /*RESERVAS*/
        .section-content {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .reserva {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        .reserva-info {
            flex: 1;
        }
        
        .reserva-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .reserva-details {
            color: #666;
            font-size: 14px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class = "boton-volver">
        <button onclick="location.href='main.php'">Volver</button>
    </div>
    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
        <div class="error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <h1>Filtros de Viaje</h1>
            <div class="filtros">

                <h3>Filtrar Transportes</h3>
                <form method="GET" action="">
                    <div class="filtros-grid">
                        <div>
                            <label>Empresa</label>
                            <input type="text" name="empresa_transporte" 
                                    value="<?php echo($_GET['empresa_transporte'] ?? ''); ?>" 
                                    placeholder="Nombre empresa">
                        </div>
                        <div>
                            <label>Origen</label>
                            <input type="text" name="origen_transporte" 
                                    value="<?php echo($_GET['origen_transporte'] ?? ''); ?>" 
                                    placeholder="Ciudad origen">
                        </div>
                        <div>
                            <label>Destino</label>
                            <input type="text" name="destino_transporte" 
                                    value="<?php echo ($_GET['destino_transporte'] ?? ''); ?>" 
                                    placeholder="Ciudad destino">
                        </div>
                        <div>
                            <label>Precio mínimo</label>
                            <input type="number" name="precio_min_transporte" 
                                    value="<?php echo($_GET['precio_min_transporte'] ?? ''); ?>" 
                                    placeholder="0">
                        </div>
                        <div>
                            <label>Precio máximo</label>
                            <input type="number" name="precio_max_transporte" 
                                    value="<?php echo($_GET['precio_max_transporte'] ?? ''); ?>" 
                                    placeholder="999999">
                        </div>
                        <div>
                            <label>Salida desde</label>
                            <input type="date" name="fecha_salida_desde" 
                                    value="<?php echo($_GET['fecha_salida_desde'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Capacidad mínima</label>
                            <input type="number" name="capacidad_min_transporte" 
                                    value="<?php echo($_GET['capacidad_min_transporte'] ?? ''); ?>" 
                                    placeholder="1">
                        </div>
                    </div>
                    <?php foreach (['empresa_panorama', 'nombre_panorama', 'ubicacion_panorama', 
                    'precio_min_panorama', 'precio_max_panorama', 'duracion_min_panorama', 'duracion_max_panorama', 
                    'fecha_panorama_desde', 'fecha_panorama_hasta', 'capacidad_min_panorama', 'nombre_hospedaje', 
                    'ubicacion_hospedaje', 'precio_min_hospedaje', 'precio_max_hospedaje', 'estrellas_min', 
                    'checkin_desde', 'checkin_hasta', 'checkout_desde', 'checkout_hasta'] as $filtro): ?>
                        <?php if (!empty($_GET[$filtro])): ?>
                            <input type="hidden" name="<?php echo $filtro; ?>" value="<?php echo($_GET[$filtro]); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <div>
                        <button type="submit">Aplicar Filtros</button>
                    </div>
                </form>
            </div>

             <div class="filtros">
                <h3>Filtrar Panoramas</h3>
                <form method="GET" action="">
                    <div class="filtros-grid">
                        <div>
                            <label>Empresa</label>
                            <input type="text" name="empresa_panorama" 
                                    value="<?php echo($_GET['empresa_panorama'] ?? ''); ?>" 
                                    placeholder="Nombre empresa">
                        </div>
                        <div>
                            <label>Nombre</label>
                            <input type="text" name="nombre_panorama" 
                                    value="<?php echo($_GET['nombre_panorama'] ?? ''); ?>" 
                                    placeholder="Nombre panorama">
                        </div>
                        <div>
                            <label>Ubicación</label>
                            <input type="text" name="ubicacion_panorama" 
                                    value="<?php echo($_GET['ubicacion_panorama'] ?? ''); ?>" 
                                    placeholder="Ciudad/ubicación">
                        </div>
                        <div>
                            <label>Precio mínimo</label>
                            <input type="number" name="precio_min_panorama" 
                                    value="<?php echo($_GET['precio_min_panorama'] ?? ''); ?>" 
                                    placeholder="0">
                        </div>
                        <div>
                            <label>Precio máximo</label>
                            <input type="number" name="precio_max_panorama" 
                                    value="<?php echo($_GET['precio_max_panorama'] ?? ''); ?>" 
                                    placeholder="999999">
                        </div>
                        <div>
                            <label>Duración mínima (horas)</label>
                            <input type="number" name="duracion_min_panorama" 
                                    value="<?php echo($_GET['duracion_min_panorama'] ?? ''); ?>" 
                                    placeholder="1">
                        </div>
                        <div>
                            <label>Duración máxima (horas)</label>
                            <input type="number" name="duracion_max_panorama" 
                                    value="<?php echo($_GET['duracion_max_panorama'] ?? ''); ?>" 
                                    placeholder="24">
                        </div>
                        <div>
                            <label>Fecha desde</label>
                            <input type="date" name="fecha_panorama_desde" 
                                    value="<?php echo($_GET['fecha_panorama_desde'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Fecha hasta</label>
                            <input type="date" name="fecha_panorama_hasta" 
                                    value="<?php echo($_GET['fecha_panorama_hasta'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Capacidad mínima</label>
                            <input type="number" name="capacidad_min_panorama" 
                                    value="<?php echo($_GET['capacidad_min_panorama'] ?? ''); ?>" 
                                    placeholder="1">
                        </div>
                    </div>
                    <?php foreach (['empresa_transporte', 'origen_transporte', 'destino_transporte', 
                    'precio_min_transporte', 'precio_max_transporte', 'fecha_salida_desde', 'fecha_salida_hasta', 
                    'capacidad_min_transporte', 'nombre_hospedaje', 'ubicacion_hospedaje', 'precio_min_hospedaje', 
                    'precio_max_hospedaje', 'estrellas_min', 'checkin_desde', 'checkin_hasta', 'checkout_desde', '
                    checkout_hasta'] as $filtro): ?>
                        <?php if (!empty($_GET[$filtro])): ?>
                            <input type="hidden" name="<?php echo $filtro; ?>" value="<?php echo($_GET[$filtro]); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <div>
                        <button type="submit">Aplicar Filtros</button>
                    </div>
                </form>

            </div>
                <div class="filtros">
                <h3> Filtrar Hospedajes</h3>
                <form method="GET" action="">
                    <div class="filtros-grid">
                        <div>
                            <label>Nombre</label>
                            <input type="text" name="nombre_hospedaje" 
                                    value="<?php echo($_GET['nombre_hospedaje'] ?? ''); ?>" 
                                    placeholder="Nombre hospedaje">
                        </div>
                        <div>
                            <label>Ubicación</label>
                            <input type="text" name="ubicacion_hospedaje" 
                                    value="<?php echo($_GET['ubicacion_hospedaje'] ?? ''); ?>" 
                                    placeholder="Ciudad/ubicación">
                        </div>
                        <div>
                            <label>Precio mínimo/noche</label>
                            <input type="number" name="precio_min_hospedaje" 
                                    value="<?php echo($_GET['precio_min_hospedaje'] ?? ''); ?>" 
                                    placeholder="0">
                        </div>
                        <div>
                            <label>Precio máximo/noche</label>
                            <input type="number" name="precio_max_hospedaje" 
                                    value="<?php echo($_GET['precio_max_hospedaje'] ?? ''); ?>" 
                                    placeholder="999999">
                        </div>
                        <div>
                            <label>Estrellas mínimas</label>
                            <input type="number" name="estrellas_min"
                                    value="<?php echo($_GET['estrellas_min'] ?? ''); ?>" 
                                    placeholder="1" min="1" max="5">
                        </div>
                        <div>
                            <label>Check-in desde</label>
                            <input type="date" name="checkin_desde" 
                                    value="<?php echo($_GET['checkin_desde'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Check-in hasta</label>
                            <input type="date" name="checkin_hasta" 
                                    value="<?php echo($_GET['checkin_hasta'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Check-out desde</label>
                            <input type="date" name="checkout_desde" 
                                    value="<?php echo($_GET['checkout_desde'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Check-out hasta</label>
                            <input type="date" name="checkout_hasta" 
                                    value="<?php echo($_GET['checkout_hasta'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Airbnb / Hotel </label>
                            <select name="tipo_hospedaje">
                                <option value="">Todos</option>
                                <option value=1 <?php echo (isset($_GET['tipo_hospedaje']) && $_GET['tipo_hospedaje'] == '1') ? 'selected' : ''; ?>>Airbnb</option>
                                <option value=2 <?php echo (isset($_GET['tipo_hospedaje']) && $_GET['tipo_hospedaje'] == '2') ? 'selected' : ''; ?>>Hotel</option>
                            </select>   
                        </div>     
                    </div>        
                    <?php foreach (['empresa_transporte', 'origen_transporte', 
                    'destino_transporte', 'precio_min_transporte', 'precio_max_transporte', 
                    'fecha_salida_desde', 'fecha_salida_hasta', 'capacidad_min_transporte', 'empresa_panorama', 
                    'nombre_panorama', 'ubicacion_panorama', 'precio_min_panorama', 'precio_max_panorama', 
                    'duracion_min_panorama', 'duracion_max_panorama', 'fecha_panorama_desde', 'fecha_panorama_hasta', 
                    'capacidad_min_panorama'] as $filtro): ?>
                        <?php if (!empty($_GET[$filtro])): ?>
                            <input type="hidden" name="<?php echo $filtro; ?>" value="<?php echo($_GET[$filtro]); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <div>
                        <button type="submit">Aplicar Filtros</button>
                    </div>
                </form>
            </div>

        <div>
            <a href="?">Limpiar Filtros</a>
        </div>  
        <h1>Crear Nuevo Viaje</h1>
        <div>
            <form action="procesar_crear_viaje.php" method="POST">
                <div>
                    <label for="nombre_viaje" > Nombre del viaje:</label>
                    <input type="text" id="nombre_viaje" name="nombre_viaje"  
                        placeholder="Ingresa el nombre de tu viaje" required>
                </div>
        <div>
            <h2>Transportes Disponibles</h2>
            <div class="section-content">
                <?php if (count($transportes) > 0): ?>
                    <div>
                        <strong><?php echo count($transportes); ?></strong> transportes encontrados
                    </div>
                    <?php foreach ($transportes as $transporte): ?>
                        <div class="reserva">
                            <input type="checkbox" name="transportes[]" value="<?php echo $transporte['id']; ?>">
                            <div class="reserva-info">
                                <div class="reserva-title">
                                    <?php echo ($transporte['empresa']); ?> - 
                                    <?php echo ($transporte['lugar_origen']); ?> → 
                                    <?php echo ($transporte['lugar_llegada']); ?>
                                </div>
                                <div class="reserva-details">
                                    <strong>Precio:</strong> <?php echo($transporte['precio_asiento']); ?> por asiento<br>
                                    <strong>Fecha salida:</strong><?php echo($transporte['fecha_salida']); ?><br>
                                    <strong>Fecha llegada:</strong> <?php echo($transporte['fecha_llegada']); ?><br>
                                    <strong>Capacidad:</strong> <?php echo($transporte['capacidad']); ?> personas<br>
                                    <strong>Tiempo estimado:</strong> <?php echo($transporte['tiempo_estimado']); ?> minutos
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div>No hay transportes disponibles con los filtros seleccionados.</div>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <h2>Panoramas Disponibles</h2>            
            <div>
                <strong><?php echo count($panoramas); ?></strong> panoramas encontrados
            </div>
            
            <div class="section-content">
                <?php if (count($panoramas) > 0): ?>
                    <?php foreach ($panoramas as $panorama): ?>
                        <div class="reserva">
                            <input type="checkbox" name="panoramas[]" value="<?php echo $panorama['id']; ?>">
                            <div class="reserva-info">
                                <div class="reserva-title">
                                    <?php echo htmlspecialchars($panorama['nombre']); ?> - 
                                    <?php echo htmlspecialchars($panorama['empresa']); ?>
                                </div>
                                <div class="reserva-details">
                                    <strong>Descripción:</strong> <?php echo($panorama['descripcion']); ?><br>
                                    <strong>Ubicación:</strong> <?php echo($panorama['ubicacion']); ?><br>
                                    <strong>Precio:</strong> $<?php echo($panorama['precio_persona']); ?> por persona<br>
                                    <strong>Duración:</strong> <?php echo($panorama['duracion']); ?> horas<br>
                                    <strong>Capacidad:</strong> <?php echo($panorama['capacidad']); ?> personas<br>
                                    <strong>Fecha:</strong> <?php echo date($panorama['fecha_panorama']); ?><br>
                                    <strong>Restricciones:</strong> <?php echo($panorama['restricciones']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div>No hay panoramas disponibles con los filtros seleccionados.</div>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <h2>Hospedajes Disponibles</h2>
            <div>
                <strong><?php echo count($hospedajes); ?></strong> hospedajes encontrados
            </div>
            
            <div class="section-content">
                <?php if (count($hospedajes) > 0): ?>
                    <?php foreach ($hospedajes as $hospedaje): ?>
                        <div class="reserva">
                            <input type="checkbox" name="hospedajes[]" value="<?php echo $hospedaje['id']; ?>">
                            <div class="reserva-info">
                                <div class="reserva-title">
                                    <?php echo($hospedaje['nombre_hospedaje']); ?> - 
                                    <?php echo($hospedaje['ubicacion']); ?>
                                </div>
                                <div class="reserva-details">
                                    <strong>Precio:</strong> $<?php echo($hospedaje['precio_noche']); ?> por noche<br>
                                    <strong>Estrellas:</strong> <?php echo('⭐' . $hospedaje['estrellas']); ?><br>
                                    <strong>Check-in:</strong> <?php echo($hospedaje['fecha_checkin']); ?><br>
                                    <strong>Check-out:</strong> <?php echo($hospedaje['fecha_checkout']); ?><br>
                                    <strong>Comodidades:</strong> <?php echo($hospedaje['comodidades']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div>No hay hospedajes disponibles con los filtros seleccionados.</div>
                <?php endif; ?>
            </div>

            <div>
                <label for="participantes">Participantes (separados por comas, con su edad)</label>
                <input type="text" name="participantes" placeholder="Juan.20, Pedro.30, ...">
            </div>
            <button type="submit" class="btn-submit">Crear Viaje</button>
            </form>
        </div>
    </div>
</body>
</html>