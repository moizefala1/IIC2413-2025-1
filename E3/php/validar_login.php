<?php
session_start();
require_once 'utils.php';

$usuario = filter_var($_POST['usuario'], FILTER_SANITIZE_STRING);
$contrasena = filter_var($_POST['contrasena'], FILTER_SANITIZE_STRING);

$db = conectarBD();

$queryUsuario = "SELECT * FROM persona WHERE username = :usuario";
$stmtUsuario = $db->prepare($queryUsuario);
$stmtUsuario->bindParam(':usuario', $usuario);
$stmtUsuario->execute();

$usuarioExiste = $stmtUsuario->fetch();

if (!$usuarioExiste) {
    header('Location: index.php?error=Usuario no existe');
    exit();
}
$queryLogin = "SELECT * FROM persona 
              WHERE username = :usuario 
              AND contrasena = :contrasena
              AND correo IN (SELECT correo FROM usuario)";

$stmtLogin = $db->prepare($queryLogin);
$stmtLogin->bindParam(':usuario', $usuario);
$stmtLogin->bindParam(':contrasena', $contrasena);
$stmtLogin->execute();

$resultado = $stmtLogin->fetch();

if ($resultado) {
    $_SESSION['usuario'] = $usuario;
    header('Location: main.php');
    exit();
} else {
    header('Location: index.php?error=Contraseña incorrecta');
    exit();
}
?>