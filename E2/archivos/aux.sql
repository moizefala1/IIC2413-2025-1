

-----------------------------------------------------
-- 2. PROCESAMIENTO DEL ARCHIVO agenda_reserva.csv
-----------------------------------------------------
CREATE TEMP TABLE temp_agenda_reserva (
    agenda_id INTEGER,
    etiqueta VARCHAR(50),
    id INTEGER, -- Reserva ID
    fecha DATE,
    monto INTEGER,
    cantidad_personas INTEGER,
    estado_disponibilidad VARCHAR(20),
    puntos INTEGER,
    correo_empleado VARCHAR(100),
    lugar_origen VARCHAR(100),
    lugar_llegada VARCHAR(100),
    capacidad INTEGER,
    tiempo_estimado INTEGER,
    precio_asiento DECIMAL(10,2),
    empresa VARCHAR(100),
    fecha_salida TIMESTAMP,
    fecha_llegada TIMESTAMP,
    tipo_bus VARCHAR(50),
    comodidades TEXT[],
    escalas TEXT[],
    clase VARCHAR(20),
    paradas TEXT[],
    nombre_hospedaje VARCHAR(100),
    ubicacion TEXT,
    precio_noche INTEGER,
    estrellas INTEGER,
    fecha_checkin TIMESTAMP,
    fecha_checkout TIMESTAMP,
    politicas TEXT[],
    nombre_anfitrion VARCHAR(100),
    contacto_anfitrion VARCHAR(100),
    descripcion_airbnb TEXT,
    piezas INTEGER,
    camas INTEGER,
    banos INTEGER,
    nombre_panorama VARCHAR(100),
    duracion INTEGER,
    precio_persona INTEGER,
    restricciones TEXT[],
    fecha_panorama TIMESTAMP
);

-- Cargamos los datos combinados
COPY temp_agenda_reserva FROM '../csv/agenda_reserva.csv' DELIMITER ',' CSV HEADER NULL AS '';

-- Insertamos en tabla Agenda
INSERT INTO Agenda(id, correo_usuario, etiqueta)
SELECT DISTINCT agenda_id, correo_empleado, etiqueta  -- Asumimos que correo_empleado es el correo_usuario para la agenda
FROM temp_agenda_reserva
WHERE agenda_id IS NOT NULL;

-- Insertamos en tabla Reserva
INSERT INTO Reserva(id, agenda_id, fecha, monto, cantidad_personas, estado_disponibilidad, puntos_booked)
SELECT id, agenda_id, fecha, monto, cantidad_personas, estado_disponibilidad, puntos
FROM temp_agenda_reserva
WHERE id IS NOT NULL;

-- Insertamos en tabla Transporte (si tiene datos de transporte)
INSERT INTO Transporte(id, correo_empleado, lugar_origen, lugar_llegada, capacidad, 
                      tiempo_estimado, precio_asiento, empresa, fecha_salida, fecha_llegada)
SELECT id, correo_empleado, lugar_origen, lugar_llegada, capacidad, 
       tiempo_estimado, precio_asiento, empresa, fecha_salida, fecha_llegada
FROM temp_agenda_reserva
WHERE id IS NOT NULL AND lugar_origen IS NOT NULL AND lugar_llegada IS NOT NULL;

-- Insertamos en tabla Bus (si tiene tipo_bus)
INSERT INTO Bus(id, comodidades, tipo)
SELECT id, comodidades, tipo_bus
FROM temp_agenda_reserva
WHERE id IS NOT NULL AND tipo_bus IS NOT NULL;

-- Insertamos en tabla Tren (si tiene paradas)
INSERT INTO Tren(id, comodidades, paradas)
SELECT id, comodidades, paradas
FROM temp_agenda_reserva
WHERE id IS NOT NULL AND paradas IS NOT NULL;

-- Insertamos en tabla Avion (si tiene clase)
INSERT INTO Avion(id, clase, escalas)
SELECT id, clase, escalas
FROM temp_agenda_reserva
WHERE id IS NOT NULL AND clase IS NOT NULL;

-- Insertamos en tabla Hospedaje (si tiene nombre_hospedaje)
INSERT INTO Hospedaje(id, nombre, ubicacion, precio_noche, estrellas, 
                     comodidades, fecha_checkin, fecha_checkout)
SELECT id, nombre_hospedaje, ubicacion, precio_noche, estrellas, 
       comodidades, fecha_checkin, fecha_checkout
FROM temp_agenda_reserva
WHERE id IS NOT NULL AND nombre_hospedaje IS NOT NULL;

-- Insertamos en tabla Hotel (si tiene politicas)
INSERT INTO Hotel(id, politicas)
SELECT id, politicas
FROM temp_agenda_reserva
WHERE id IS NOT NULL AND politicas IS NOT NULL;

-- Insertamos en tabla Airbnb (si tiene nombre_anfitrion)
INSERT INTO Airbnb(id, nombre_anfitrion, contacto_anfitrion, descripcion, piezas, camas, banos)
SELECT id, nombre_anfitrion, contacto_anfitrion, descripcion_airbnb, piezas, camas, banos
FROM temp_agenda_reserva
WHERE id IS NOT NULL AND nombre_anfitrion IS NOT NULL;

-- Insertamos en tabla Panorama (si tiene nombre_panorama)
INSERT INTO Panorama(id, empresa, nombre, descripcion, ubicacion, duracion, 
                    precio_persona, capacidad, restricciones, fecha_panorama)
SELECT id, empresa, nombre_panorama, NULL, ubicacion, duracion, 
       precio_persona, capacidad, restricciones, fecha_panorama
FROM temp_agenda_reserva
WHERE id IS NOT NULL AND nombre_panorama IS NOT NULL;

-----------------------------------------------------
-- 3. PROCESAMIENTO DEL ARCHIVO habitaciones.csv
-----------------------------------------------------
CREATE TEMP TABLE temp_habitaciones (
    hotel_id INTEGER,
    numero_habitacion VARCHAR(10),
    tipo VARCHAR(50)
);

-- Cargamos los datos
COPY temp_habitaciones FROM '../csv/habitaciones.csv' DELIMITER ',' CSV HEADER;

-- Insertamos en tabla Habitacion
INSERT INTO Habitacion(hotel_id, numero_habitacion, tipo)
SELECT hotel_id, numero_habitacion, tipo
FROM temp_habitaciones;

-----------------------------------------------------
-- 4. PROCESAMIENTO DEL ARCHIVO participantes.csv
-----------------------------------------------------
CREATE TEMP TABLE temp_participantes (
    panorama_id INTEGER,
    nombre VARCHAR(100),
    edad INTEGER
);

-- Cargamos los datos
COPY temp_participantes FROM '../csv/participantes.csv' DELIMITER ',' CSV HEADER;

-- Insertamos en tabla Participante
INSERT INTO Participante(id_panorama, nombre, edad)
SELECT panorama_id, nombre, edad
FROM temp_participantes;

-----------------------------------------------------
-- 5. PROCESAMIENTO DEL ARCHIVO review_seguro.csv
-----------------------------------------------------
CREATE TEMP TABLE temp_review_seguro (
    correo_usuario VARCHAR(50),
    puntos INTEGER,
    reserva_id INTEGER,
    tipo_seguro VARCHAR(50),
    valor_seguro INTEGER,
    clausula TEXT,
    empresa_seguro VARCHAR(50),
    estrellas INTEGER,
    descripcion TEXT
);

-- Cargamos los datos
COPY temp_review_seguro FROM '../csv/review_seguro.csv' DELIMITER ',' CSV HEADER;

-- Insertamos en tabla Seguro
INSERT INTO Seguro(correo_usuario, reserva_id, valor, clausula, empresa, tipo)
SELECT correo_usuario, reserva_id, valor_seguro, clausula, empresa_seguro, tipo_seguro
FROM temp_review_seguro
WHERE tipo_seguro IS NOT NULL;

-- Insertamos en tabla Review
INSERT INTO Review(correo_usuario, reserva_id, estrellas, descripcion)
SELECT correo_usuario, reserva_id, estrellas, descripcion
FROM temp_review_seguro
WHERE estrellas IS NOT NULL;

-- Actualizar puntos del usuario (sumando los puntos del review_seguro)
UPDATE Usuario u
SET puntos = u.puntos + rs.puntos
FROM temp_review_seguro rs
WHERE u.correo = rs.correo_usuario;


COMMIT;