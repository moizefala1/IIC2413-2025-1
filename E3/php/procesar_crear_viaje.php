<?php
session_start();
require_once 'utils.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: crear_viaje.php');
    exit();
}

$nombre_viaje = $_POST['nombre_viaje'] ?? '';
$transportes = $_POST['transportes'] ?? [];
$panoramas = $_POST['panoramas'] ?? [];
$hospedajes = $_POST['hospedajes'] ?? [];
$participantes = $_POST['participantes'] ?? [];
$usuario = $_SESSION['usuario'] ?? '';

$participantes_array = explode(',', $participantes);
$cantidad_participantes = count($participantes_array);

try {
    $db = ConectarBD();
    $db->beginTransaction();


    $stmt = $db ->prepare('SELECT correo FROM persona WHERE username = :usuario');
    $stmt->bindParam(':usuario', $usuario);
    
    if (!$stmt->execute()) {
        throw new PDOException();
    } 
    $correo_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $correo_usuario = $correo_usuario['correo'];

    $stmt = $db->prepare('INSERT INTO agenda (correo_usuario, etiqueta) VALUES (:correo_usuario, :etiqueta)'); 
    $stmt->bindParam(':correo_usuario', $correo_usuario);
    $stmt->bindParam(':etiqueta', $nombre_viaje);

    if (!$stmt->execute()) {
        throw new PDOException();
    }
    $agenda_id = $db->lastInsertId();



    // TRANSPORTES
    if(!empty($transportes)) {
        foreach ($transportes as $transporte) {
            $stmt = $db->prepare('SELECT * FROM transporte WHERE id = :id');
            $stmt->bindParam(':id', $transporte);
            
            if (!$stmt->execute()) {
                throw new PDOException();
            }        
            $datos_transporte = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = $db->prepare('UPDATE reserva SET 
            fecha = :fecha,
            monto = :monto,
            cantidad_personas = :cantidad_personas,
            estado_disponibilidad = :estado_disponibilidad,
            puntos = :puntos,
            agenda_id = :agenda_id
            WHERE id = :id');

            $monto = intval($datos_transporte['precio_asiento']) * $cantidad_participantes; ;
            $puntos = $monto / 1000; 
            $no_disponible = 'No disponible';

            $stmt->bindParam(':fecha', $datos_transporte['fecha_salida']);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':cantidad_personas', $cantidad_participantes);
            $stmt->bindParam(':estado_disponibilidad', $no_disponible);
            $stmt->bindParam(':puntos', $puntos);
            $stmt->bindParam(':agenda_id', $agenda_id);
            $stmt->bindParam(':id', $datos_transporte['id']);

            if (!$stmt->execute()) {
                throw new PDOException();
            }

        }
    }
////// FUNCIONANDO

    $db ->commit();
    //CREAR TRIGGER PARA ESTE COMITT, CON SP PARA CALCULAR PUNTOS DE LA AGENDA ENTERA Y AÑADIRLOS A USUARIO
    $_SESSION['success'] = 'Agenda creada correctamente.';
    header('Location: crear_viaje.php');
    exit();
}
catch (PDOException $e) {
    $db->rollBack();
    $errorInfo = $e->getMessage();
    $_SESSION['error'] = 'No se pudo crear el viaje.' . $errorInfo;
    header('Location: crear_viaje.php');
    exit(); 
}
?>