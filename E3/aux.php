    if (!empty($participantes_array)) {
        foreach ($participantes_array as $participante) {
            $datos = explode('.', $participante);
            
            if (count($datos) === 2) {
                $nombre = trim($datos[0]);
                $edad = intval($datos[1]);
                $stmt = $db->prepare('INSERT INTO participantes (panorama_id, nombre, edad) VALUES (:panorama_id, :nombre, :edad)');
                $stmt->bindParam(':panorama_id', $panorama_id);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':edad', $edad);
                
                if (!$stmt->execute()) {
                    throw new PDOException('Error al insertar participante: ' . $nombre);
                }
            }
            else {
                throw new PDOException();
            }
        }
    }