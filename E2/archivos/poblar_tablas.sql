BEGIN;

CREATE TEMP TABLE temp_personas (
    nombre VARCHAR(50),
    correo VARCHAR(50),
    contrasena VARCHAR(50),
    username VARCHAR(50),
    telefono_contacto VARCHAR(20),
    run INTEGER,
    puntos INTEGER,
    jornada VARCHAR(20),
    isapre VARCHAR(20),
    contrato VARCHAR(100),
    dv CHAR(1)
);

CREATE TEMP TABLE temp_reservas_agendas (
    agenda_id INT,
    etiqueta VARCHAR(100),
    id INT,
    fecha DATE,
    monto INT,
    cantidad_personas INT,
    estado_disponibilidad VARCHAR(50),
    puntos INT,
    correo_empleado VARCHAR(100),
    lugar_origen VARCHAR(100),
    lugar_llegada VARCHAR(100),
    capacidad INT,
    tiempo_estimado INT,
    precio_asiento INT,
    empresa VARCHAR(100),
    fecha_salida DATE,
    fecha_llegada DATE,
    tipo_bus VARCHAR(50),
    comodidades TEXT[],
    escalas TEXT[],
    clase VARCHAR(50),
    paradas TEXT[],
    nombre_hospedaje VARCHAR(100),
    ubicacion VARCHAR(100),
    precio_noche INT,
    estrellas INT,
    fecha_checkin DATE,
    fecha_checkout DATE,
    politicas TEXT[],
    nombre_anfitrion VARCHAR(100),
    contacto_anfitrion VARCHAR(100),
    descripcion_airbnb TEXT,
    piezas INT,
    camas INT,
    banos INT,
    nombre_panorama VARCHAR(100),
    duracion INTEGER,
    precio_persona INTEGER,
    restricciones TEXT[],
    fecha_panorama DATE
);

CREATE TEMP TABLE temp_seguros_reviews (
    correo_usuario VARCHAR(255),
    puntos INTEGER,
    reserva_id INTEGER,
    tipo_seguro VARCHAR(100),
    valor_seguro INTEGER,
    clausula TEXT,
    empresa_seguro VARCHAR(100),
    estrellas INTEGER,
    descripcion TEXT
);

CREATE TEMP TABLE temp_participantes (
    panorama_id INTEGER,
    nombre VARCHAR(100),
    edad INTEGER
);

CREATE TEMP TABLE temp_habitaciones (
    hotel_id INTEGER,
    numero_habitacion VARCHAR(10),
    tipo VARCHAR(50)
);

---DESCARTADOS-----
CREATE TEMP TABLE personas_descartados (
    nombre VARCHAR(50),
    correo VARCHAR(50),
    contrasena VARCHAR(50),
    username VARCHAR(50),
    telefono_contacto VARCHAR(20),
    run INTEGER,
    dv CHAR(1)
);

CREATE TEMP TABLE usuarios_descartados (
    correo VARCHAR(50),
    puntos INTEGER
);

CREATE TEMP TABLE empleados_descartados (
    correo VARCHAR(50),
    jornada VARCHAR(20),
    isapre VARCHAR(20),
    contrato VARCHAR(100)
);

CREATE TEMP TABLE reservas_descartados (
    id INTEGER,
    agenda_id INTEGER,
    fecha DATE,
    monto INTEGER,
    cantidad_personas INTEGER,
    estado_disponibilidad VARCHAR(20),
    puntos_booked INTEGER
);

CREATE TEMP TABLE agendas_descartados (
    id INTEGER,
    correo_usuario VARCHAR(50),
    etiqueta VARCHAR(50)
);

CREATE TEMP TABLE reviews_descartados (
    id SERIAL PRIMARY KEY,
    correo_usuario VARCHAR(50),
    reserva_id INTEGER,
    estrellas INTEGER,
    descripcion TEXT
);

CREATE TEMP TABLE seguros_descartados (
    id SERIAL PRIMARY KEY,
    correo_usuario VARCHAR(50),
    reserva_id INTEGER,
    valor INTEGER,
    clausula TEXT, 
    empresa VARCHAR(50),
    tipo VARCHAR(50)
);

CREATE TEMP TABLE transportes_descartados (
    id INTEGER,
    correo_empleado VARCHAR(100),
    lugar_origen VARCHAR(100),
    lugar_llegada VARCHAR(100),
    capacidad INTEGER,
    tiempo_estimado INTEGER, 
    precio_asiento INTEGER,
    empresa VARCHAR(100),
    fecha_salida DATE,
    fecha_llegada DATE
);

CREATE TEMP TABLE trenes_descartados (
    id INTEGER,
    comodidades TEXT[],
    paradas TEXT[]
);

CREATE TEMP TABLE buses_descartados (
    id INTEGER,
    comodidades TEXT[],
    tipo VARCHAR(50)
);

CREATE TEMP TABLE aviones_descartados (
    id INTEGER,
    clase VARCHAR(20),
    escalas TEXT[]
);

CREATE TEMP TABLE panoramas_descartados (
    id INTEGER,
    empresa VARCHAR(100),
    nombre VARCHAR(100),
    descripcion TEXT,
    ubicacion TEXT,
    duracion INTEGER,
    precio_persona INTEGER,
    capacidad INTEGER,
    restricciones TEXT[],
    fecha_panorama DATE
);

CREATE TEMP TABLE participantes_descartados (
    id_panorama INTEGER,
    nombre VARCHAR(100),
    edad INTEGER
);

CREATE TEMP TABLE hospedajes_descartados (
    id INTEGER,
    nombre VARCHAR(100),
    ubicacion TEXT,
    precio_noche INTEGER,
    estrellas INTEGER,
    comodidades TEXT[],
    fecha_checkin DATE,
    fecha_checkout DATE 

);

CREATE TEMP TABLE airbnb_descartados (
    id INTEGER,
    nombre_anfitrion VARCHAR(100),
    contacto_anfitrion VARCHAR(100),
    descripcion TEXT,
    piezas INTEGER,
    camas INTEGER,
    banos INTEGER
);

CREATE TEMP TABLE hoteles_descartados (
    id INTEGER,
    politicas TEXT[]
);

CREATE TEMP TABLE habitaciones_descartados (
    hotel_id INTEGER,
    numero_habitacion VARCHAR(10),
    tipo VARCHAR(50)
);

\copy temp_personas FROM '../csv/personas.csv' DELIMITER ',' CSV HEADER;
\copy temp_reservas_agendas FROM '../csv/agenda_reserva.csv' DELIMITER ',' CSV HEADER;
\copy temp_seguros_reviews FROM '../csv/review_seguro.csv' DELIMITER ',' CSV HEADER;
\copy temp_participantes FROM '../csv/participantes.csv' DELIMITER ',' CSV HEADER; 
\copy temp_habitaciones FROM '../csv/habitaciones.csv' DELIMITER ',' CSV HEADER;
------------------------------

DO $$
DECLARE
  tupla temp_personas%ROWTYPE;
BEGIN
  FOR tupla IN SELECT * FROM temp_personas LOOP
    BEGIN
      INSERT INTO Persona(nombre, correo, contrasena, username, telefono_contacto, run, dv)
      VALUES (
        tupla.nombre,
        tupla.correo,
        tupla.contrasena,
        tupla.username,
        tupla.telefono_contacto,
        tupla.run,
        tupla.dv
      );
    EXCEPTION WHEN others THEN
      INSERT INTO personas_descartados(nombre, correo, contrasena, username, telefono_contacto, run, dv)
      VALUES (
        tupla.nombre,
        tupla.correo, 
        tupla.contrasena,
        tupla.username,
        tupla.telefono_contacto,
        tupla.run,
        tupla.dv
      );
    END;
  END LOOP;
END $$;

\copy personas_descartados TO '../descartados/personas_descartados.csv' DELIMITER ',' CSV HEADER;

-----------------------------------------------------------
DO $$
DECLARE
  tupla temp_personas%ROWTYPE;
BEGIN
  FOR tupla IN 
    SELECT * FROM temp_personas WHERE correo IN (SELECT correo FROM Persona)
    AND isapre IS NULL
    AND jornada IS NULL
    AND contrato IS NULL
    LOOP
    BEGIN
      INSERT INTO Usuario (correo, puntos)
      VALUES (
        tupla.correo,
        tupla.puntos
      );
    EXCEPTION WHEN others THEN
      INSERT INTO usuarios_descartados(correo, puntos)
      VALUES (
        tupla.correo,
        tupla.puntos
      );
    END;
  END LOOP;
END $$;

\copy usuarios_descartados TO '../descartados/usuarios_descartados.csv' DELIMITER ',' CSV HEADER;

------------------------------

DO $$
DECLARE
  tupla temp_personas%ROWTYPE;
BEGIN
  FOR tupla IN 
    SELECT * FROM temp_personas WHERE correo IN (SELECT correo FROM Persona) 
    AND correo NOT IN (SELECT correo FROM Usuario) AND correo NOT IN (SELECT correo FROM usuarios_descartados)
    LOOP
    BEGIN
      INSERT INTO Empleado (correo, jornada, isapre, contrato)
      VALUES (
        tupla.correo,
        tupla.jornada,
        tupla.isapre,
        tupla.contrato
      );
    EXCEPTION WHEN others THEN
      INSERT INTO empleados_descartados(correo, jornada, isapre, contrato)
      VALUES (
        tupla.correo,
        tupla.jornada,
        tupla.isapre,
        tupla.contrato
      );
    END;
  END LOOP;
END $$;

\copy empleados_descartados TO '../descartados/empleados_descartados.csv' DELIMITER ',' CSV HEADER;

--AGENDAS--
DO $$
DECLARE
    tupla RECORD;
BEGIN
    FOR tupla IN 
        SELECT DISTINCT ON (t1.agenda_id)
            t1.agenda_id, 
            t2.correo_usuario, 
            t1.etiqueta
        FROM 
            temp_reservas_agendas t1
        JOIN 
            temp_seguros_reviews t2 ON t1.id = t2.reserva_id
    LOOP
        BEGIN
            INSERT INTO Agenda(id, correo_usuario, etiqueta)
            VALUES (tupla.agenda_id, tupla.correo_usuario, tupla.etiqueta);
            
        EXCEPTION WHEN OTHERS THEN
            IF NOT EXISTS (
                SELECT 1 FROM agendas_descartados 
                WHERE id = tupla.agenda_id
            ) THEN
                INSERT INTO agendas_descartados(id, correo_usuario, etiqueta)
                VALUES (tupla.agenda_id, tupla.correo_usuario, tupla.etiqueta);
            END IF;
        END;
    END LOOP;
END $$;
\copy agendas_descartados TO '../descartados/agendas_descartados.csv' DELIMITER ',' CSV HEADER;

------------------------------
DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN 
    SELECT * FROM temp_reservas_agendas LOOP
    BEGIN
      INSERT INTO Reserva (id, agenda_id, fecha, monto, cantidad_personas, estado_disponibilidad, puntos_booked)
      VALUES (
        tupla.id,
        tupla.agenda_id,
        tupla.fecha,
        tupla.monto,
        tupla.cantidad_personas,
        tupla.estado_disponibilidad,
        tupla.puntos
      );
    EXCEPTION WHEN others THEN
      INSERT INTO reservas_descartados(id, agenda_id, fecha, monto, cantidad_personas, estado_disponibilidad, puntos_booked)
      VALUES (
        tupla.id,
        tupla.agenda_id,
        tupla.fecha,
        tupla.monto,
        tupla.cantidad_personas,
        tupla.estado_disponibilidad,
        tupla.puntos
      );
    END;
  END LOOP;
END $$;

\copy reservas_descartados TO '../descartados/reservas_descartados.csv' DELIMITER ',' CSV HEADER;

------------------------------
DO $$
DECLARE
  tupla temp_seguros_reviews%ROWTYPE;
BEGIN
  FOR tupla IN
  SELECT * FROM temp_seguros_reviews WHERE estrellas IS NULL AND descripcion IS NULL LOOP
  BEGIN
    INSERT INTO Seguro(correo_usuario, reserva_id, valor, clausula, empresa, tipo)
    VALUES(
      tupla.correo_usuario,
      tupla.reserva_id,
      tupla.valor_seguro,
      tupla.clausula,
      tupla.empresa_seguro,
      tupla.tipo_seguro
    );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO seguros_descartados(correo_usuario, reserva_id, valor, clausula, empresa, tipo)
    VALUES(
      tupla.correo_usuario,
      tupla.reserva_id,
      tupla.valor_seguro,
      tupla.clausula,
      tupla.empresa_seguro,
      tupla.tipo_seguro
    );
    END;
  END LOOP;
END $$;

\copy seguros_descartados TO '../descartados/seguros_descartados.csv' DELIMITER ',' CSV HEADER;
----------------------
DO $$
DECLARE
  tupla temp_seguros_reviews%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_seguros_reviews WHERE tipo_seguro IS NULL AND valor_seguro IS NULL AND empresa_seguro IS NULL 
    AND clausula IS NULL LOOP
    BEGIN
      INSERT INTO Review(correo_usuario, reserva_id, estrellas, descripcion)
      VALUES(
        tupla.correo_usuario,
        tupla.reserva_id,
        tupla.estrellas,
        tupla.descripcion
      );
      EXCEPTION WHEN others THEN
      INSERT INTO reviews_descartados(correo_usuario, reserva_id, estrellas, descripcion)
      VALUES(
        tupla.correo_usuario,
        tupla.reserva_id,
        tupla.estrellas,
        tupla.descripcion
      );
    END;
  END LOOP;
END $$;

\copy reviews_descartados TO '../descartados/reviews_descartados.csv' DELIMITER ',' CSV HEADER;


-------------------------------------

DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id FROM Reserva) AND ubicacion IS NULL AND precio_noche IS NULL 
    AND fecha_checkin IS NULL AND nombre_panorama IS NULL AND precio_persona IS NULL AND fecha_panorama IS NULL 
    AND nombre_hospedaje IS NULL AND descripcion_airbnb IS NULL AND duracion IS NULL AND (restricciones IS NULL OR restricciones = '{}')
    AND estrellas IS NULL AND fecha_checkout IS NULL
    LOOP
    BEGIN
    INSERT INTO Transporte(id, correo_empleado, lugar_origen, lugar_llegada, capacidad, tiempo_estimado, 
    precio_asiento, empresa, fecha_salida, fecha_llegada)
      VALUES(
        tupla.id,
        tupla.correo_empleado,
        tupla.lugar_origen,
        tupla.lugar_llegada,
        tupla.capacidad,
        tupla.tiempo_estimado,
        tupla.precio_asiento,
        tupla.empresa,
        tupla.fecha_salida,
        tupla.fecha_llegada
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO transportes_descartados(id, correo_empleado, lugar_origen, lugar_llegada, capacidad, tiempo_estimado, 
    precio_asiento, empresa, fecha_salida, fecha_llegada)
      VALUES(
        tupla.id,
        tupla.correo_empleado,
        tupla.lugar_origen,
        tupla.lugar_llegada,
        tupla.capacidad,
        tupla.tiempo_estimado,
        tupla.precio_asiento,
        tupla.empresa,
        tupla.fecha_salida,
        tupla.fecha_llegada
      );
    END;
  END LOOP;
END $$;

\copy transportes_descartados TO '../descartados/transportes_descartados.csv' DELIMITER ',' CSV HEADER;

----------------

DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id FROM Transporte) AND (escalas = '{}' OR escalas IS NULL)
    AND tipo_bus IS NULL and clase IS NULL
    LOOP
    BEGIN
    INSERT INTO Tren(id, comodidades, paradas)
      VALUES(
        tupla.id,
        tupla.comodidades,
        tupla.paradas
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO trenes_descartados(id, comodidades, paradas)
      VALUES(
        tupla.id,
        tupla.comodidades,
        tupla.paradas
      );
    END;
  END LOOP;
END $$;

\copy trenes_descartados TO '../descartados/trenes_descartados.csv' DELIMITER ',' CSV HEADER;
-------------------------------

DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id FROM Transporte) AND id NOT IN (SELECT id FROM Tren)
    AND id NOT IN (SELECT id from trenes_descartados) AND (escalas = '{}' OR escalas IS NULL) AND clase IS NULL
    LOOP
    BEGIN
    INSERT INTO Bus(id, comodidades, tipo)
      VALUES(
        tupla.id,
        tupla.comodidades,
        tupla.tipo_bus
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO buses_descartados(id, comodidades, tipo)
      VALUES(
        tupla.id,
        tupla.comodidades,
        tupla.tipo_bus
      );
    END;
  END LOOP;
END $$;

\copy buses_descartados TO '../descartados/buses_descartados.csv' DELIMITER ',' CSV HEADER;
----------------------------------
DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id FROM Transporte) AND id NOT IN (SELECT id FROM Bus)
    AND id NOT IN (SELECT id from buses_descartados) AND id NOT IN (SELECT id FROM Tren) 
    AND id NOT IN (SELECT id FROM trenes_descartados)
    LOOP
    BEGIN
    INSERT INTO Avion(id, clase, escalas)
      VALUES(
        tupla.id,
        tupla.clase,
        tupla.escalas
      );
    EXCEPTION WHEN others THEN
    INSERT INTO aviones_descartados(id, clase, escalas)
      VALUES(
        tupla.id,
        tupla.clase,
        tupla.escalas
      );
    END;
  END LOOP;
END $$;
\copy aviones_descartados TO '../descartados/aviones_descartados.csv' DELIMITER ',' CSV HEADER;
--------------------
DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id FROM Reserva) AND id NOT IN (SELECT id FROM Transporte) 
    AND id NOT IN (SELECT id from transportes_descartados) AND nombre_hospedaje IS NULL AND precio_noche IS NULL AND
    estrellas IS NULL AND  (comodidades = '{}' OR comodidades IS NULL) AND fecha_checkin IS NULL and fecha_checkout IS NULL
    LOOP
    BEGIN
    INSERT INTO Panorama(id, empresa, nombre, descripcion, ubicacion, duracion, precio_persona, capacidad, restricciones, fecha_panorama)
    VALUES(
      tupla.id,
      tupla.empresa,
      tupla.nombre_panorama,
      tupla.descripcion_airbnb,
      tupla.ubicacion,
      tupla.duracion,
      tupla.precio_persona,
      tupla.capacidad,
      tupla.restricciones,
      tupla.fecha_panorama
      );
    EXCEPTION WHEN OTHERS THEN

    INSERT INTO panoramas_descartados(id, empresa, nombre, descripcion, ubicacion, duracion, precio_persona, capacidad, restricciones, fecha_panorama)
    VALUES(
      tupla.id,
      tupla.empresa,
      tupla.nombre_panorama,
      tupla.descripcion_airbnb,
      tupla.ubicacion,
      tupla.duracion,
      tupla.precio_persona,
      tupla.capacidad,
      tupla.restricciones,
      tupla.fecha_panorama
      );
    END;
  END LOOP;
END $$;

\copy panoramas_descartados TO '../descartados/panoramas_descartados.csv' DELIMITER ',' CSV HEADER;
----------------------------------
DO $$
DECLARE
  tupla temp_participantes%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_participantes
    LOOP
    BEGIN
    INSERT INTO Participante(id_panorama, nombre, edad)
    VALUES(
      tupla.panorama_id,
      tupla.nombre,
      tupla.edad
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO participantes_descartados(id_panorama, nombre, edad)
    VALUES(
      tupla.panorama_id,
      tupla.nombre,
      tupla.edad
      );
    END;
  END LOOP;
END $$;

\copy participantes_descartados TO '../descartados/participantes_descartados.csv' DELIMITER ',' CSV HEADER;
------------------------------------------
DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id from Reserva) AND id NOT IN (SELECT id FROM Transporte) 
    AND id NOT IN (SELECT id FROM transportes_descartados) AND id NOT IN (SELECT id FROM Panorama) 
    AND id NOT IN (SELECT id FROM panoramas_descartados) 
    LOOP
    BEGIN
    INSERT INTO Hospedaje(id, nombre, ubicacion, precio_noche, estrellas, comodidades, fecha_checkin, fecha_checkout)
    VALUES(
      tupla.id,
      tupla.nombre_hospedaje,
      tupla.ubicacion,
      tupla.precio_noche,
      tupla.estrellas,
      tupla.comodidades,
      tupla.fecha_checkin,
      tupla.fecha_checkout
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO hospedajes_descartados(id, nombre, ubicacion, precio_noche, estrellas, comodidades, fecha_checkin, fecha_checkout)
    VALUES(
      tupla.id,
      tupla.nombre_hospedaje,
      tupla.ubicacion,
      tupla.precio_noche,
      tupla.estrellas,
      tupla.comodidades,
      tupla.fecha_checkin,
      tupla.fecha_checkout
      );
    END;
  END LOOP;
END $$;
\copy hospedajes_descartados TO '../descartados/hospedajes_descartados.csv' DELIMITER ',' CSV HEADER;
------------------------------
DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id from Reserva) AND id NOT IN (SELECT id FROM Transporte) 
    AND id NOT IN (SELECT id FROM transportes_descartados) AND id NOT IN (SELECT id FROM Panorama) 
    AND id NOT IN (SELECT id FROM panoramas_descartados) 
    LOOP
    BEGIN
    INSERT INTO Hospedaje(id, nombre, ubicacion, precio_noche, estrellas, comodidades, fecha_checkin, fecha_checkout)
    VALUES(
      tupla.id,
      tupla.nombre_hospedaje,
      tupla.ubicacion,
      tupla.precio_noche,
      tupla.estrellas,
      tupla.comodidades,
      tupla.fecha_checkin,
      tupla.fecha_checkout
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO hospedajes_descartados(id, nombre, ubicacion, precio_noche, estrellas, comodidades, fecha_checkin, fecha_checkout)
    VALUES(
      tupla.id,
      tupla.nombre_hospedaje,
      tupla.ubicacion,
      tupla.precio_noche,
      tupla.estrellas,
      tupla.comodidades,
      tupla.fecha_checkin,
      tupla.fecha_checkout
      );
    END;
  END LOOP;
END $$;
\copy hospedajes_descartados TO '../descartados/hospedajes_descartados.csv' DELIMITER ',' CSV HEADER;
------------------------------------
DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id FROM Hospedaje) AND nombre_anfitrion IS NULL 
    AND contacto_anfitrion IS NULL AND descripcion_airbnb IS NULL AND piezas IS NULL AND camas IS NULL AND banos IS NULL
    LOOP
    BEGIN
    INSERT INTO Hotel(id, politicas)
    VALUES(
      tupla.id,
      tupla.politicas
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO hoteles_descartados(id, politicas)
    VALUES(
      tupla.id,
      tupla.politicas
      );
    END;
  END LOOP;
END $$;
\copy hoteles_descartados TO '../descartados/hoteles_descartados.csv' DELIMITER ',' CSV HEADER;

------------------------------
DO $$
DECLARE
  tupla temp_reservas_agendas%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_reservas_agendas WHERE id IN (SELECT id FROM Hospedaje) AND id NOT IN (SELECT id FROM Hotel)
    AND id NOT IN (SELECT id FROM hoteles_descartados)
    LOOP
    BEGIN
    INSERT INTO Airbnb(id, nombre_anfitrion, contacto_anfitrion, descripcion, piezas, camas, banos)
    VALUES(
      tupla.id,
      tupla.nombre_anfitrion,
      tupla.contacto_anfitrion,
      tupla.descripcion_airbnb,
      tupla.piezas,
      tupla.camas,
      tupla.banos
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO airbnb_descartados(id, nombre_anfitrion, contacto_anfitrion, descripcion, piezas, camas, banos)
    VALUES(
      tupla.id,
      tupla.nombre_anfitrion,
      tupla.contacto_anfitrion,
      tupla.descripcion_airbnb,
      tupla.piezas,
      tupla.camas,
      tupla.banos
      );
    END;
  END LOOP;
END $$;
\copy airbnb_descartados TO '../descartados/airbnb_descartados.csv' DELIMITER ',' CSV HEADER;
------------------------------
DO $$
DECLARE
  tupla temp_habitaciones%ROWTYPE;
BEGIN
  FOR tupla IN
    SELECT * FROM temp_habitaciones
    LOOP
    BEGIN
    INSERT INTO Habitacion(hotel_id, numero_habitacion, tipo)
    VALUES(
      tupla.hotel_id,
      tupla.numero_habitacion,
      tupla.tipo
      );
    EXCEPTION WHEN OTHERS THEN
    INSERT INTO habitaciones_descartados(hotel_id, numero_habitacion, tipo)
    VALUES(
      tupla.hotel_id,
      tupla.numero_habitacion,
      tupla.tipo
      );
    END;
  END LOOP;
END $$;
\copy habitaciones_descartados TO '../descartados/habitaciones_descartados.csv' DELIMITER ',' CSV HEADER;
------------------------------



COMMIT;


