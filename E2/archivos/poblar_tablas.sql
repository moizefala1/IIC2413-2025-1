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
    duracion VARCHAR(50),
    precio_persona DECIMAL(10,2),
    restricciones TEXT,
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



\copy temp_personas FROM '../csv/personas.csv' DELIMITER ',' CSV HEADER;
\copy temp_reservas_agendas FROM '../csv/agenda_reserva.csv' DELIMITER ',' CSV HEADER;
\copy temp_seguros_reviews FROM '../csv/review_seguro.csv' DELIMITER ',' CSV HEADER;

------------------------------

DO $$
DECLARE
  tupla temp_personas%ROWTYPE;
BEGIN
  FOR tupla IN SELECT * FROM temp_personas LOOP
    BEGIN
      INSERT INTO Persona (nombre, correo, contrasena, username, telefono_contacto, run, dv)
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
      INSERT INTO personas_descartados (nombre, correo, contrasena, username, telefono_contacto, run, dv)
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
        SELECT 
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
        RAISE NOTICE 'Error inserting agenda with id %', tupla.agenda_id;
        RAISE NOTICE 'Error message: %', SQLERRM;
            INSERT INTO agendas_descartados(id, correo_usuario, etiqueta)
            VALUES (tupla.agenda_id, tupla.correo_usuario, tupla.etiqueta);
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


COMMIT;


