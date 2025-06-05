<?php
session_start();
require_once 'utils.php';

$usuario = filter_var($_POST['usuario'], FILTER_SANITIZE_STRING);
$contrasena = filter_var($_POST['contrasena'], FILTER_SANITIZE_STRING);

$db = conectarBD();

$query = "SELECT * FROM persona WHERE username = :usuario AND contrasena = :contrasena";
$stmt = $db->prepare($query);
$stmt->bindParam(':usuario', $usuario);
$stmt->bindParam(':contrasena', $contrasena);
$stmt->execute();

$resultado = $stmt->fetch();

if ($resultado) {
    $_SESSION['usuario'] = $usuario;
    header('Location: main.php');
    exit();
} else {
    header('Location: index.php?error=Usuario no existe o contrasena errónea');
    exit();
}
?>
