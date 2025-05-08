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

\copy temp_personas FROM '../csv/personas.csv' DELIMITER ',' CSV HEADER;

INSERT INTO Persona (nombre, correo, contrasena, username, telefono_contacto, rut, dv)
SELECT nombre, correo, contrasena, username, telefono_contacto, run, dv
FROM temp_personas

----AÑADIR  EL CODIGO PARA ESCRIBIR LO QUE NO CUMPLE LAS RESTRICCIONES EN UN CSV