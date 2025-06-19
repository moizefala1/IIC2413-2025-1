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






try {
    $db = ConectarBD();
    $db->beginTransaction();
    if (!empty($participantes)) {
        if (strpos($participantes, ',') === false) {
        throw new PDOException(' Formato de participantes incorrecto. Debe ser "Nombre.Edad,Nombre.Edad, ..."');
        }
        $participantes_array = explode(',', $participantes);
        $cantidad_participantes = count($participantes_array);
        //Verificar formato d eparticipantes
        if ($cantidad_participantes > 1) {
            foreach ($participantes_array as $participante) {
                $participante = trim($participante);
                $participante = explode('.', $participante);
                if (count($participante) < 2) {
                    throw new PDOException(' Formato de participantes incorrecto. Debe ser "Nombre.Edad,Nombre.Edad, ..."');
                }

                $nombre = $participante[0];
                $edad = $participante[1];
                if (!is_numeric($edad) || $edad < 0 || empty($edad)) { 
                    throw new PDOException(' Formato de participantes incorrecto. Debe ser "Nombre.Edad,Nombre.Edad, ..."');
                }
                if (empty($nombre) || !preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
                    throw new PDOException(' Formato de participantes incorrecto. Debe ser "Nombre.Edad,Nombre.Edad, ..."');
                }
            }
        }
    }
    else {
        $cantidad_participantes = 1;
    }

    $stmt = $db ->prepare('SELECT correo FROM persona WHERE username = :usuario');
    $stmt->bindParam(':usuario', $usuario);
    
    if (!$stmt->execute()) {
        throw new PDOException();
    } 
    $correo_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $correo_usuario = $correo_usuario['correo'];

    $stmt = $db->query('SELECT id FROM agenda ORDER BY id DESC LIMIT 1');
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $id['id'];
    $nuevo_id = $id + 1;

    $stmt = $db->prepare('INSERT INTO agenda (id, correo_usuario, etiqueta) VALUES (:id, :correo_usuario, :etiqueta)'); 
    $stmt->bindParam(':id', $nuevo_id);
    $stmt->bindParam(':correo_usuario', $correo_usuario);
    $stmt->bindParam(':etiqueta', $nombre_viaje);

    if (!$stmt->execute()) {
        throw new PDOException();
    }
    $agenda_id = $nuevo_id;


 
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

    ///////PANORAMAS
    if(!empty($panoramas)) {
        foreach ($panoramas as $panorama) {
            $stmt = $db->prepare('SELECT * FROM panorama WHERE id = :id');
            $stmt->bindParam(':id', $panorama);
            
            if (!$stmt->execute()) {
                throw new PDOException();
            }        
            $datos_panorama = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = $db->prepare('UPDATE reserva SET 
            fecha = :fecha,
            monto = :monto,
            cantidad_personas = :cantidad_personas,
            estado_disponibilidad = :estado_disponibilidad,
            puntos = :puntos,
            agenda_id = :agenda_id
            WHERE id = :id');

            $monto = intval($datos_panorama['precio_persona']) * $cantidad_participantes; ;
            $puntos = $monto / 1000; 
            $no_disponible = 'No disponible';

            $stmt->bindParam(':fecha', $datos_panorama['fecha_panorama']);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':cantidad_personas', $cantidad_participantes);
            $stmt->bindParam(':estado_disponibilidad', $no_disponible);
            $stmt->bindParam(':puntos', $puntos);
            $stmt->bindParam(':agenda_id', $agenda_id);
            $stmt->bindParam(':id', $datos_panorama['id']);
            
            if (!$stmt->execute()) {
                throw new PDOException();
            }

            if ($cantidad_participantes > 1) {
                foreach ($participantes_array as $participante) {
                    $participante = trim($participante);
                    $participante = explode('.', $participante);
                    if (count($participante) < 2) {
                        throw new PDOException(' Formato de participantes incorrecto. Debe ser "Nombre.Edad,Nombre.Edad, ..."');
                    }

                    $nombre = $participante[0];
                    $edad = $participante[1];
                    if (!is_numeric($edad) || $edad < 0 || empty($edad)) { 
                        throw new PDOException(' Formato de participantes incorrecto. Debe ser "Nombre.Edad,Nombre.Edad, ..."');
                    }
                    if (empty($nombre) || !preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
                        throw new PDOException(' Formato de participantes incorrecto. Debe ser "Nombre.Edad,Nombre.Edad, ..."');
                    }


                    $stmt = $db->query('SELECT id FROM participante ORDER BY id DESC LIMIT 1');
                    $id = $stmt->fetch(PDO::FETCH_ASSOC);
                    $id = $id['id'];
                    $nuevo_id = $id + 1;

                    $stmt = $db->prepare('INSERT INTO participante (id, panorama_id, nombre, edad) VALUES  
                    (:id, :panorama_id, :nombre, :edad)');
                    $stmt->bindParam(':id', $nuevo_id);
                    $stmt->bindParam(':panorama_id', $panorama);
                    $stmt->bindParam(':nombre', $nombre);
                    $stmt->bindParam(':edad', $edad);
                    
                    if (!$stmt->execute()) {
                        throw new PDOException();
                    }
                }
            }
        }
    }


    ///////HOSPEDAJES
    if(!empty($hospedajes)) {
        foreach ($hospedajes as $hospedaje) {
            $stmt = $db->prepare('SELECT * FROM hospedaje WHERE id = :id');
            $stmt->bindParam(':id', $hospedaje);
            
            if (!$stmt->execute()) {
                throw new PDOException();
            }        
            $datos_hospedaje = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = $db->prepare('UPDATE reserva SET 
            fecha = :fecha,
            monto = :monto,
            cantidad_personas = :cantidad_personas,
            estado_disponibilidad = :estado_disponibilidad,
            puntos = :puntos,
            agenda_id = :agenda_id
            WHERE id = :id');

            
            $fecha_inicio = new DateTime($datos_hospedaje['fecha_checkin']);
            $fecha_fin = new DateTime($datos_hospedaje['fecha_checkout']);
            $diferencia_dias = $fecha_inicio->diff($fecha_fin);
            $dias_totales = $diferencia_dias->days; // NO SE SUMA UNO YA QUE ES PRECIO POR NOCHE !!!

            $monto = intval($datos_hospedaje['precio_noche']) * $dias_totales;
            $puntos = $monto / 1000; 

            $no_disponible = 'No disponible';

            $stmt->bindParam(':fecha', $datos_hospedaje['fecha_checkin']);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':cantidad_personas', $cantidad_participantes);
            $stmt->bindParam(':estado_disponibilidad', $no_disponible);
            $stmt->bindParam(':puntos', $puntos);
            $stmt->bindParam(':agenda_id', $agenda_id);
            $stmt->bindParam(':id', $datos_hospedaje['id']);
            
            if (!$stmt->execute()) {
                throw new PDOException();
            }
        }
    }

    ////// FUNCIONANDO
    $db->query("SELECT calcular_puntos_usuario($agenda_id)");
    /// No existe forma de crear un trigger sobre una transaccion... no que se me ocurra al menos
    $db ->commit();   
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