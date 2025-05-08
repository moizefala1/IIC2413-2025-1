CREATE TEMP TABLE temp_personas (
    nombre VARCHAR(50),
    correo VARCHAR(50),
    contrasena VARCHAR(50),
    username VARCHAR(50),
    telefono_contacto VARCHAR(20),
    run VARCHAR(12),
    puntos INTEGER,
    jornada VARCHAR(20),
    isapre VARCHAR(20),
    contrato VARCHAR(100),
    dv CHAR(1)
);

CREATE TEMP TABLE personas_descartadas (
    nombre VARCHAR(50),
    correo VARCHAR(50),
    contrasena VARCHAR(50),
    username VARCHAR(50),
    telefono_contacto VARCHAR(20),
    run VARCHAR(12),
    dv CHAR(1)
);


\copy temp_personas FROM '../csv/personas.csv' DELIMITER ',' CSV HEADER;

DO $$
DECLARE
  record temp_personas%ROWTYPE;
BEGIN
  FOR record IN SELECT * FROM temp_personas LOOP
    BEGIN
      INSERT INTO Persona (nombre, correo, contrasena, username, telefono_contacto, run, dv)
      VALUES (
        record.nombre,
        record.correo,
        record.contrasena,
        record.username,
        record.telefono_contacto,
        record.run,
        record.dv
      );
    EXCEPTION WHEN others THEN
      INSERT INTO personas_descartadas (nombre, correo, contrasena, username, telefono_contacto, run, dv)
      VALUES (
        record.nombre,
        record.correo,
        record.contrasena,
        record.username,
        record.telefono_contacto,
        record.run,
        record.dv
      );
    END;
  END LOOP;
END $$;

\copy personas_descartadas TO '../descartados/personas_descartadas.csv' DELIMITER ',' CSV HEADER;