CREATE EXTENSION IF NOT EXISTS unaccent;
CREATE TABLE Persona (
    nombre VARCHAR(50),
    correo VARCHAR(50) PRIMARY KEY NOT NULL,
    contrasena VARCHAR(50),
    username VARCHAR(50) UNIQUE,
    telefono_contacto VARCHAR(20),
    run VARCHAR(12),
    dv CHAR(1),
    CONSTRAINT run_unico UNIQUE (run, dv)
);

CREATE OR REPLACE FUNCTION limpiar_run_trigger()
RETURNS TRIGGER AS $$
BEGIN
    NEW.run := REGEXP_REPLACE(NEW.run, '[^0-9]', '', 'g'); 
    NEW.dv := UPPER(REGEXP_REPLACE(NEW.dv, '[^0-9kK]', '', 'g'));
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER persona_limpiar_run
BEFORE INSERT OR UPDATE ON Persona
FOR EACH ROW EXECUTE FUNCTION limpiar_run_trigger();

CREATE TABLE Empleado (
    correo VARCHAR(50) PRIMARY KEY,
    jornada VARCHAR(20) NOT NULL CHECK (jornada IN ('Nocturno', 'Diurno')),
    isapre VARCHAR(20) NOT NULL,
    contrato VARCHAR(100) NOT NULL CHECK (contrato IN ('Part time', 'Full time')),
    FOREIGN KEY (correo) REFERENCES Persona(correo) ON DELETE CASCADE
);

CREATE OR REPLACE FUNCTION normalizar_isapre()
RETURNS trigger AS $$
BEGIN
  NEW.isapre := lower(unaccent(NEW.isapre));
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_normalizar_isapre
BEFORE INSERT OR UPDATE ON Empleado
FOR EACH ROW
EXECUTE FUNCTION normalizar_isapre();


CREATE TABLE Usuario (
    correo VARCHAR(50) PRIMARY KEY,
    puntos INTEGER NOT NULL DEFAULT 0,  
    FOREIGN KEY (correo) REFERENCES Persona(correo) ON DELETE CASCADE
);

CREATE OR REPLACE FUNCTION corregir_puntos_negativos()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.puntos < 0 THEN
        NEW.puntos := ABS(NEW.puntos);
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER usuario_corregir_puntos
BEFORE INSERT OR UPDATE ON Usuario
FOR EACH ROW EXECUTE FUNCTION corregir_puntos_negativos();


CREATE TABLE Agenda (
    id INTEGER  NOT NULL PRIMARY KEY,
    correo_usuario VARCHAR(50) NOT NULL,
    etiqueta VARCHAR(50),
    FOREIGN KEY (correo_usuario) REFERENCES Usuario(correo) ON DELETE CASCADE
);

CREATE TABLE Seguro (
    id SERIAL NOT NULL PRIMARY KEY,
    correo_usuario VARCHAR(50) NOT NULL,
    reserva_id INTEGER NOT NULL,
    valor INTEGER NOT NULL,
    clausula TEXT NOT NULL, 
    empresa VARCHAR(50) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    FOREIGN KEY (correo_usuario) REFERENCES Usuario(correo) ON DELETE CASCADE
);


CREATE TABLE Reserva (
    id INTEGER NOT NULL PRIMARY KEY,
    agenda_id INTEGER,
    fecha DATE NOT NULL,
    monto INTEGER NOT NULL,
    cantidad_personas INTEGER NOT NULL,
    estado_disponibilidad VARCHAR(20) NOT NULL,
    puntos_booked INTEGER DEFAULT 0,
    FOREIGN KEY (agenda_id) REFERENCES Agenda(id) ON DELETE SET NULL
);

CREATE OR REPLACE FUNCTION corregir_valores_negativos()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.monto < 0 THEN
        NEW.monto := ABS(NEW.monto);
    END IF;
    
    IF NEW.puntos_booked < 0 THEN
        NEW.puntos_booked := ABS(NEW.puntos_booked);
    END IF;
    
    IF NEW.cantidad_personas < 0 THEN
        NEW.cantidad_personas := ABS(NEW.cantidad_personas);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER reserva_corregir_valores_negativos
BEFORE INSERT OR UPDATE ON Reserva
FOR EACH ROW EXECUTE FUNCTION corregir_valores_negativos();



CREATE TABLE Review (
    id SERIAL PRIMARY KEY,
    correo_usuario VARCHAR(50) NOT NULL,
    reserva_id INTEGER NOT NULL,
    estrellas INTEGER NOT NULL CHECK (estrellas BETWEEN 1 AND 5),
    descripcion TEXT,
    FOREIGN KEY (correo_usuario) REFERENCES Usuario(correo) ON DELETE CASCADE,
    FOREIGN KEY (reserva_id) REFERENCES Reserva(id) ON DELETE CASCADE
);

--------------------------------------------------------

CREATE TABLE Panorama (
    id INTEGER PRIMARY KEY,
    empresa VARCHAR(100) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ubicacion TEXT NOT NULL,
    duracion INTEGER NOT NULL,
    precio_persona INTEGER NOT NULL,
    capacidad INTEGER NOT NULL,
    restricciones TEXT[],
    fecha_panorama TIMESTAMP NOT NULL,
    FOREIGN KEY (id) REFERENCES Reserva(id) ON DELETE CASCADE
);

CREATE OR REPLACE FUNCTION panorama_corregir_precio()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.precio_persona < 0 THEN
        NEW.precio_persona := ABS(NEW.precio_persona);
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER panorama_corregir_precio
BEFORE INSERT OR UPDATE ON Panorama 
FOR EACH ROW EXECUTE FUNCTION panorama_corregir_precio();



CREATE TABLE Participante (
    id SERIAL PRIMARY KEY,
    id_panorama INTEGER NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    edad INTEGER,
    FOREIGN KEY (id_panorama) REFERENCES Panorama(id) ON DELETE CASCADE
);
CREATE OR REPLACE FUNCTION corregir_edad()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.edad < 0 THEN
        NEW.edad := ABS(NEW.edad);
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER participante_corregir_edad
BEFORE INSERT OR UPDATE ON Participante
FOR EACH ROW EXECUTE FUNCTION corregir_edad();

--------------------------------------------------------------------

-- Tabla Hospedaje (entidad base)
CREATE TABLE Hospedaje (
    id INTEGER PRIMARY KEY,
    nombre VARCHAR(100),
    ubicacion TEXT NOT NULL,
    precio_noche INTEGER NOT NULL,
    estrellas INTEGER CHECK (estrellas BETWEEN 1 AND 5),
    comodidades TEXT[],
    fecha_checkin TIMESTAMP NOT NULL,
    fecha_checkout TIMESTAMP, 
    FOREIGN KEY (id) REFERENCES Reserva(id) ON DELETE CASCADE
);

CREATE OR REPLACE FUNCTION hospedaje_corregir_precio()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.precio_noche < 0 THEN
        NEW.precio_noche := ABS(NEW.precio_noche);
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER hospedaje_corregir_precio
BEFORE INSERT OR UPDATE ON Hospedaje 
FOR EACH ROW EXECUTE FUNCTION hospedaje_corregir_precio();


-- Tabla Hotel
CREATE TABLE Hotel (
    id INTEGER PRIMARY KEY,
    politicas TEXT[],
    FOREIGN KEY (id) REFERENCES Hospedaje(id) ON DELETE CASCADE
);

-- Tabla Habitacion
CREATE TABLE Habitacion (
    id SERIAL PRIMARY KEY,
    hotel_id INTEGER NOT NULL,
    numero_habitacion VARCHAR(10) NOT NULL,
    tipo VARCHAR(50) CHECK (tipo IN ('Sencilla', 'Doble', 'Matrimonial', 'Triple', 'Cuadruple', 'Suite')),
    CONSTRAINT habitacion_unica UNIQUE (hotel_id, numero_habitacion),
    FOREIGN KEY (hotel_id) REFERENCES Hotel(id) ON DELETE CASCADE
);

-- Tabla Airbnb
CREATE TABLE Airbnb (
    id INTEGER PRIMARY KEY,
    nombre_anfitrion VARCHAR(100) NOT NULL,
    contacto_anfitrion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    piezas INTEGER NOT NULL,
    camas INTEGER NOT NULL,
    banos INTEGER NOT NULL,
    FOREIGN KEY (id) REFERENCES Hospedaje(id) ON DELETE CASCADE
);
--------------------------------------------------------------------------------------

CREATE TABLE Transporte (
    id INTEGER PRIMARY KEY,
    correo_empleado VARCHAR(100) NOT NULL,
    lugar_origen VARCHAR(100),
    lugar_llegada VARCHAR(100),
    capacidad INTEGER,
    tiempo_estimado INTEGER NOT NULL, 
    precio_asiento INTEGER NOT NULL,
    empresa VARCHAR(100),
    fecha_salida TIMESTAMP NOT NULL,
    fecha_llegada TIMESTAMP,
    FOREIGN KEY (correo_empleado) REFERENCES Empleado(correo) ON DELETE CASCADE,
    FOREIGN KEY (id) REFERENCES Reserva(id) ON DELETE CASCADE
);

CREATE OR REPLACE FUNCTION transporte_corregir_precio()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.precio_asiento < 0 THEN
        NEW.precio_asiento := ABS(NEW.precio_asiento);
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER transporte_corregir_precio
BEFORE INSERT OR UPDATE ON Transporte 
FOR EACH ROW EXECUTE FUNCTION transporte_corregir_precio();





-- Tabla Tren
CREATE TABLE Tren (
    id INTEGER PRIMARY KEY,
    comodidades TEXT[] NOT NULL,
    paradas TEXT[] NOT NULL,
    FOREIGN KEY (id) REFERENCES Transporte(id) ON DELETE CASCADE
);

-- Tabla Bus
CREATE TABLE Bus (
    id INTEGER PRIMARY KEY,
    comodidades TEXT[],
    tipo VARCHAR(50) NOT NULL CHECK (tipo IN ('Normal', 'Semi-cama', 'Cama')),
    FOREIGN KEY (id) REFERENCES Transporte(id) ON DELETE CASCADE
);

-- Tabla Avion
CREATE TABLE Avion (
    id INTEGER PRIMARY KEY,
    clase VARCHAR(20) NOT NULL,
    escalas TEXT[],
    FOREIGN KEY (id) REFERENCES Transporte(id) ON DELETE CASCADE
);