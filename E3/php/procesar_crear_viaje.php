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

$nombre_viaje = $_POST['nombre_viaje'] ;
$transportes = $_POST['transportes'] ?? [];
$panoramas = $_POST['panoramas'] ?? [];
$hospedajes = $_POST['hospedajes'] ?? [];
$participantes = $_POST['participantes'] ?? [];
?>

<DOCtype html>
<html>
<head>
    <div>
    <?php echo '<pre>';
    print_r($nombre_viaje);
    print_r($transportes);
    print_r($panoramas);
    print_r($hospedajes);
    print_r($participantes);
    echo '</pre>';
    ?>
</div>
</head>
<body>
<html>

<?php
//try {
//    $db = ConectarBD();
//    $db->beginTransaction();
//
//    $db ->commit();
//    $_SESSION['success'] = 'Agenda creada correctamente.';
//    header('Location: crear_viaje.php');
//    exit();
//}
//catch (PDOException $e) {
//    $db->rollBack();
//    $_SESSION['error'] = 'No se pudo crear el viaje.';
//    header('Location: crear_viaje.php');
//    exit(); 
//}
//?>