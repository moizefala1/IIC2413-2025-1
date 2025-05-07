-- Tabla Persona (entidad base para Empleado y Usuario)
CREATE TABLE Persona (
    nombre VARCHAR(50) NOT NULL,
    correo VARCHAR(50) PRIMARY KEY,
    contrasena VARCHAR(50) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    telefono_contacto VARCHAR(20),
    rut VARCHAR(12)  NOT NULL,
    dv CHAR(1) NOT NULL
    CONSTRAINT rut_unico UNIQUE (rut, dv)
);

-- Tabla Empleado
CREATE TABLE Empleado (
    correo PRIMARY KEY,
    jornada VARCHAR(20) NOT NULL CHECK (jornada IN ('Nocturno', 'Diurno')),
    isapre VARCHAR(20) NOT NULL CHECK (isapre IN ('Más vida', 'Colmena', 'Consalud', 'Banmédica',
    'Fonasa')),
    contrato VARCHAR(100) NOT NULL CHECK (contrato IN ('Part time', 'Full time')),
    FOREIGN KEY (correo) REFERENCES Persona(correo) ON DELETE CASCADE
);

-------------------------- HASTA ACA
-- Tabla Usuario
CREATE TABLE Usuario (
    correo VARCHAR(50) PRIMARY KEY,
    puntos INTEGER DEFAULT 0 CHECK (puntos >= 0),
    FOREIGN KEY (correo) REFERENCES Persona(correo) ON DELETE CASCADE
);

-- Tabla Seguro
CREATE TABLE Seguro (
    id SERIAL PRIMARY KEY,
    correo_usuario VARCHAR(100) NOT NULL,
    reserva_id INTEGER NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    clausula TEXT,
    etiqueta VARCHAR(50)
);

-- Tabla Agenda
CREATE TABLE Agenda (
    id SERIAL PRIMARY KEY,
    correo_usuario VARCHAR(100) NOT NULL,
    etiqueta VARCHAR(50),
    estrellas INTEGER CHECK (estrellas BETWEEN 1 AND 5)
);


--------------------------------------------------------------
CREATE TABLE Reserva (
    id SERIAL PRIMARY KEY,
    agenda_id INTEGER,
    descripcion TEXT,
    fecha DATE NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    cantidad_personas INTEGER NOT NULL,
    estado_disponibilidad VARCHAR(20) NOT NULL,
    puntos_booked INTEGER DEFAULT 0,
    FOREIGN KEY (agenda_id) REFERENCES Agenda(id) ON DELETE SET NULL
);
--------------------------------------------------------

-- Tabla Panorama
CREATE TABLE Panorama (
    id SERIAL PRIMARY KEY,
    empresa VARCHAR(100) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ubicacion VARCHAR(200) NOT NULL,
    duracion INTEGER NOT NULL, -- en minutos
    precio_persona DECIMAL(10,2) NOT NULL,
    capacidad INTEGER NOT NULL,
    restricciones TEXT,
    fecha_panorama TIMESTAMP NOT NULL
);

-- Tabla Participante (relación N:M entre Usuario y Panorama)
CREATE TABLE Participante (
    id SERIAL PRIMARY KEY,
    panorama_id INTEGER NOT NULL,
    usuario_id INTEGER NOT NULL,
    id_pulsera VARCHAR(50),
    nombre VARCHAR(100) NOT NULL,
    edad INTEGER NOT NULL,
    FOREIGN KEY (panorama_id) REFERENCES Panorama(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
);

--------------------------------------------------------------------

-- Tabla Hospedaje (entidad base)
CREATE TABLE Hospedaje (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ubicacion TEXT NOT NULL,
    check_fecha TIMESTAMP NOT NULL,
    estrellas INTEGER CHECK (estrellas BETWEEN 1 AND 5),
    comodidades TEXT[],
    fecha_checkin TIMESTAMP NOT NULL,
    fecha_checkout TIMESTAMP NOT NULL
);

-- Tabla Hotel
CREATE TABLE Hotel (
    hospedaje_id INTEGER PRIMARY KEY,
    politicas TEXT,
    FOREIGN KEY (hospedaje_id) REFERENCES Hospedaje(id) ON DELETE CASCADE
);

-- Tabla Habitacion
CREATE TABLE Habitacion (
    id SERIAL PRIMARY KEY,
    hotel_id INTEGER NOT NULL,
    numero_habitacion VARCHAR(10) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    FOREIGN KEY (hotel_id) REFERENCES Hotel(hospedaje_id) ON DELETE CASCADE
);

-- Tabla Airbnb
CREATE TABLE Airbnb (
    hospedaje_id INTEGER PRIMARY KEY,
    nombre_anfitrion VARCHAR(100) NOT NULL,
    contacto_anfitrion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    piezas INTEGER NOT NULL,
    camas INTEGER NOT NULL,
    banos INTEGER NOT NULL,
    FOREIGN KEY (hospedaje_id) REFERENCES Hospedaje(id) ON DELETE CASCADE
);
--------------------------------------------------------------------------------------

CREATE TABLE Transporte (
    id SERIAL PRIMARY KEY,
    correo_empleado VARCHAR(100) NOT NULL,
    lugar_origen VARCHAR(100) NOT NULL,
    lugar_destino VARCHAR(100) NOT NULL,
    capacidad INTEGER NOT NULL,
    tiempo_estimado INTEGER NOT NULL, -- en minutos
    precio_asiento DECIMAL(10,2) NOT NULL,
    empresa VARCHAR(100) NOT NULL,
    fecha_llegada TIMESTAMP NOT NULL
);


------CAMBIAR PK DE TODOS ESTOS A COMODIDADES E ID TRANSPORTE 

-- Tabla Tren
CREATE TABLE Tren (
    transporte_id INTEGER PRIMARY KEY,
    comodidades TEXT[],
    paradas TEXT[],
    FOREIGN KEY (transporte_id) REFERENCES Transporte(id) ON DELETE CASCADE
);

-- Tabla Bus
CREATE TABLE Bus (
    transporte_id INTEGER PRIMARY KEY,
    comodidades TEXT[],
    FOREIGN KEY (transporte_id) REFERENCES Transporte(id) ON DELETE CASCADE
);

-- Tabla Avion
CREATE TABLE Avion (
    transporte_id INTEGER PRIMARY KEY,
    clase VARCHAR(20) NOT NULL,
    escala TEXT[],
    FOREIGN KEY (transporte_id) REFERENCES Transporte(id) ON DELETE CASCADE
);