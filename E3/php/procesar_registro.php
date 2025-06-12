<?php
session_start();
require_once 'utils.php';

$usuario = $_POST['nombre_usuario'] ?? '';
$clave = $_POST['clave'] ?? '';
$repetir_clave = $_POST['repetir_clave'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$run = $_POST['run'] ?? '';
$dv = $_POST['dv'] ?? '';
$nombre = $_POST['nombre_real'] ?? '';
$email = $_POST['email'] ?? '';

$_SESSION['form_data'] = $_POST;


if ($clave !== $repetir_clave) {
    $_SESSION['error'] = 'Las contraseñas no coinciden';
    header('Location: registro.php');
    exit();
}

if (empty($usuario) || empty($clave) || empty($email) || empty($nombre) || empty($run) || empty($dv)) {
    $_SESSION['error'] = 'Todos los campos son obligatorios';
    header('Location: registro.php');
    exit();
}

if (!is_numeric($run) || strlen($run) < 7 || strlen($run) > 8) {
    $_SESSION['error'] = 'El RUN debe ser numérico y tener entre 7 y 8 dígitos';
    header('Location: registro.php');
    exit();
}

$dv = strtoupper($dv);
if (!preg_match('/^[0-9K]$/', $dv)) {
    $_SESSION['error'] = 'El DV debe ser un número del 0-9 o la letra K';
    header('Location: registro.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'El correo electrónico no es válido';
    header('Location: registro.php');
    exit();
}


try {
    $db = conectarBD();
    
    $stmt = $db->prepare("SELECT correo FROM persona WHERE correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->fetch()) {
        $_SESSION['error'] = 'El correo ya existe';
        header('Location: registro.php');
        exit();
    }

    $stmt = $db->prepare("SELECT username FROM persona WHERE username = :username");
    $stmt->bindParam(':username', $usuario);
    $stmt->execute();

    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Ese usuario ya existe';
        header('Location: registro.php');
        exit();
    }

    $db->beginTransaction();
    $stmt = $db->prepare("
        INSERT INTO persona (correo, nombre, contrasena, username, telefono_contacto, run, dv)
        VALUES (:correo, :nombre, :contrasena, :username, :telefono_contacto, :run, :dv)
    ");
    
    $stmt->bindParam(':username', $usuario);
    $stmt->bindParam(':contrasena', $contrasena);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $email);
    $stmt->bindParam(':telefono_contacto', $telefono);
    $stmt->bindParam(':run', $run);
    $stmt->bindParam(':dv', $dv);
    
    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        throw new PDOException();
    }
    $stmt = $db->prepare("
        INSERT INTO usuario (correo, puntos)
        VALUES (:correo, 0)
    ");
    $stmt->bindParam(':correo', $email);
    
    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        throw new PDOException();
    }

    $db->commit();
    unset($_SESSION['form_data']);
    $_SESSION['success'] = 'Usuario registrado correctamente';
    header('Location: index.php');
    exit();

} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $error_generico = 'Error al registrar el usuario. Por favor verifica tus datos.';
    $_SESSION['error'] = $error_generico;
    header('Location: registro.php');
    exit();
}
?>