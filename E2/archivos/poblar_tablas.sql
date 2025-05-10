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

CREATE TEMP TABLE personas_descartadas (
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

\copy temp_personas FROM '../csv/personas.csv' DELIMITER ',' CSV HEADER;

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
      INSERT INTO personas_descartadas (nombre, correo, contrasena, username, telefono_contacto, run, dv)
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

\copy personas_descartadas TO '../descartados/personas_descartadas.csv' DELIMITER ',' CSV HEADER;

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
------------------------------
COMMIT;

-----HASTA ACA ESTA BIEN -------------------