<?php
require_once 'utils.php';
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php?error=Debes iniciar sesión');
    exit();
}

$db = ConectarBD();
$usuario = $_SESSION['usuario'];
$stmt = $db ->prepare('SELECT correo FROM persona WHERE username = :usuario');
$stmt->bindParam(':usuario', $usuario);
if (!$stmt->execute()) {
    throw new PDOException();
} 
$correo_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$correo_usuario = $correo_usuario['correo'];


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Viaje</title>
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
    </style>
</head>
<body>
    <div class = "boton-volver">
        <button onclick="location.href='main.php'">Volver</button>
    </div>

    <div class="container">
        <h1>Detalles de Viaje</h1>
        <h2>Información general</h2>
        <?php
        try {
            $stmt = $db->prepare(   
            "SELECT 
                correo, 
                etiqueta, 
                participante_nombre,  
                participante_edad
            FROM usuario_agenda_participante 
            WHERE correo = :correo 
            ORDER BY etiqueta DESC"
            );
            $stmt->bindParam(':correo', $correo_usuario);
            $stmt->execute();
            
            $columnDisplayNames = array(
                'correo' => 'Correo Electrónico',
                'etiqueta' => 'Etiqueta de Viaje',
                'participante_nombre' => 'Nombre del Participante',
                'participante_edad' => 'Edad del Participante'
            );
            
            $columnCount = $stmt->columnCount();
            $columnNames = array();
            for ($i = 0; $i < $columnCount; $i++) {
                $meta = $stmt->getColumnMeta($i);
                $columnNames[] = $meta['name'];
            }
        }
        catch (PDOException $e) {
            $errorInfo = $e->getMessage();
            $_SESSION['error'] = $errorInfo;
            header('Location: main.php');
            exit(); 
        }

        echo "<table class = 'tabla-estandar'>";
        

        echo "<tr>";
        foreach ($columnNames as $column) {
            $displayName = isset($columnDisplayNames[$column]) ? $columnDisplayNames[$column] : $column;
            echo "<th>" . htmlspecialchars($displayName) . "</th>";
        }
        echo "</tr>";
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($fila as $valor) {
                echo "<td>" . htmlspecialchars($valor) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        ?>

        <!--VIAJE-->
        <h2>Viajes</h2> 
        <?php
        try {
            $query = "SELECT * FROM transporte_detallado WHERE correo_usuario = :correo 
            ORDER BY fecha_salida, viaje_etiqueta";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':correo', $correo_usuario);
            $stmt->execute();

            $columnCount = $stmt->columnCount();
            $columnNames = array();
            for ($i = 0; $i < $columnCount; $i++) {
                $meta = $stmt->getColumnMeta($i);
                $columnNames[] = $meta['name'];
            }
        }
        catch (PDOException $e) {
            $errorInfo = $e->getMessage();
            $_SESSION['error'] = $errorInfo;
            header('Location: main.php');
            exit(); 
        }

        echo "<table class = 'tabla-estandar'>";
        

        echo "<tr>";
        foreach ($columnNames as $column) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($fila as $valor) {
                echo "<td>" . htmlspecialchars($valor) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        ?>


        <!--PANORAMAS-->
        <h2>Panoramas</h2> 
        <?php
        try {
            $query = "SELECT * FROM panorama_detallado WHERE correo_usuario = :correo 
            ORDER BY fecha_panorama, viaje_etiqueta";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':correo', $correo_usuario);
            $stmt->execute();

            $columnCount = $stmt->columnCount();
            $columnNames = array();
            for ($i = 0; $i < $columnCount; $i++) {
                $meta = $stmt->getColumnMeta($i);
                $columnNames[] = $meta['name'];
            }
        }
        catch (PDOException $e) {
            $errorInfo = $e->getMessage();
            $_SESSION['error'] = $errorInfo;
            header('Location: main.php');
            exit(); 
        }

        echo "<table class = 'tabla-estandar'>";
        

        echo "<tr>";
        foreach ($columnNames as $column) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($fila as $valor) {
                echo "<td>" . htmlspecialchars($valor) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        ?>

        <!--HOSPEDAJES-->
        <h2>Hospedajes</h2> 
        <?php
        try {
            $query = "SELECT * FROM hospedaje_detallado WHERE correo_usuario = :correo 
            ORDER BY fecha_checkin, viaje_etiqueta";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':correo', $correo_usuario);
            $stmt->execute();

            $columnCount = $stmt->columnCount();
            $columnNames = array();
            for ($i = 0; $i < $columnCount; $i++) {
                $meta = $stmt->getColumnMeta($i);
                $columnNames[] = $meta['name'];
            }
        }
        catch (PDOException $e) {
            $errorInfo = $e->getMessage();
            $_SESSION['error'] = $errorInfo;
            header('Location: main.php');
            exit(); 
        }

        echo "<table class = 'tabla-estandar'>";
        

        echo "<tr>";
        foreach ($columnNames as $column) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($fila as $valor) {
                echo "<td>" . htmlspecialchars($valor) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        ?>

        
    </div>
</body>
</html>
