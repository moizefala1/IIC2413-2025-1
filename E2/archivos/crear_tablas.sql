
---CAMBIAR CORREO A CORREO_EMPLEADO, CORREO_USUARIO SEGUN CORRESPONDENCIA-------------
--- ESTANDARIZAR ID A xxxxx-ID SEGUN CORRESPONDENCIA ------------------------------



CREATE TABLE Persona (
    nombre VARCHAR(50),
    correo VARCHAR(50) PRIMARY KEY NOT NULL,
    contrasena VARCHAR(50),
    username VARCHAR(50) UNIQUE,
    telefono_contacto VARCHAR(20),
    run INTEGER NOT NULL CHECK (run >= 0),
    dv CHAR(1) NOT NULL
);

----------------------------------------------------------

CREATE TABLE Empleado (
    correo VARCHAR(50) PRIMARY KEY,
    jornada VARCHAR(20) CHECK (jornada IN ('Nocturno', 'Diurno')),
    isapre VARCHAR(20) NOT NULL,
    contrato VARCHAR(100) NOT NULL CHECK (contrato IN ('Part time', 'Full time')),
    FOREIGN KEY (correo) REFERENCES Persona(correo) ON DELETE CASCADE
);

-----------------------------------------------------------

CREATE TABLE Usuario (
    correo VARCHAR(50) PRIMARY KEY,
    puntos INTEGER NOT NULL CHECK (puntos >= 0),  
    FOREIGN KEY (correo) REFERENCES Persona(correo) ON DELETE CASCADE
);

-----------------------------------------------------------------

CREATE TABLE Agenda (
    id INTEGER  NOT NULL PRIMARY KEY,
    correo_usuario VARCHAR(50) NOT NULL,
    etiqueta VARCHAR(50) NOT NULL,
    FOREIGN KEY (correo_usuario) REFERENCES Usuario(correo) ON DELETE CASCADE
);
------------------------------------------------------------
CREATE TABLE Reserva (
    id INTEGER NOT NULL PRIMARY KEY,
    agenda_id INTEGER,
    fecha DATE,
    monto INTEGER CHECK (monto >= 0),
    cantidad_personas INTEGER CHECK (cantidad_personas > 0),
    estado_disponibilidad VARCHAR(20) NOT NULL,
    puntos_booked INTEGER CHECK (puntos_booked >= 0),
    FOREIGN KEY (agenda_id) REFERENCES Agenda(id) ON DELETE CASCADE
);

---------------------------------------------------------
CREATE TABLE Seguro (
    id SERIAL NOT NULL PRIMARY KEY,
    correo_usuario VARCHAR(50) NOT NULL,
    reserva_id INTEGER NOT NULL,
    valor INTEGER,
    clausula TEXT, 
    empresa VARCHAR(50),
    tipo VARCHAR(50),
    FOREIGN KEY (correo_usuario) REFERENCES Usuario(correo) ON DELETE CASCADE,
    FOREIGN KEY (reserva_id) REFERENCES Reserva(id) ON DELETE CASCADE,
    CONSTRAINT seguro_unica UNIQUE (correo_usuario, tipo, reserva_id)
);

---------------------------------------------------------

CREATE TABLE Review (
    id SERIAL PRIMARY KEY,
    correo_usuario VARCHAR(50) NOT NULL,
    reserva_id INTEGER NOT NULL,
    estrellas INTEGER NOT NULL CHECK (estrellas BETWEEN 1 AND 5),
    descripcion TEXT,
    FOREIGN KEY (correo_usuario) REFERENCES Usuario(correo) ON DELETE CASCADE,
    FOREIGN KEY (reserva_id) REFERENCES Reserva(id) ON DELETE CASCADE,
    CONSTRAINT review_unica UNIQUE (correo_usuario, reserva_id)
);

--------------------------------------------------------
-------------- HASTA ACA ESTA BIEN -------------------


CREATE TABLE Panorama (
    id INTEGER PRIMARY KEY,
    empresa VARCHAR(100) ,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ubicacion TEXT,
    duracion INTEGER,
    precio_persona INTEGER NOT NULL,
    capacidad INTEGER,
    restricciones TEXT[],
    fecha_panorama TIMESTAMP NOT NULL,
    FOREIGN KEY (id) REFERENCES Reserva(id) ON DELETE CASCADE
);


---------------------------------------------------------

CREATE TABLE Participante (
    id_panorama INTEGER NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    edad INTEGER CHECK (edad >= 0),
    PRIMARY KEY (id_panorama, nombre),
    FOREIGN KEY (id_panorama) REFERENCES Panorama(id) ON DELETE CASCADE
);




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

----------------------------------------------------------

-- Tabla Hotel
CREATE TABLE Hotel (
    id INTEGER PRIMARY KEY,
    politicas TEXT[],
    FOREIGN KEY (id) REFERENCES Hospedaje(id) ON DELETE CASCADE
);

-----------------------------------------------------------
-- Tabla Habitacion
CREATE TABLE Habitacion (
    hotel_id INTEGER NOT NULL,
    numero_habitacion VARCHAR(10) NOT NULL,
    tipo VARCHAR(50) CHECK (tipo IN ('Sencilla', 'Doble', 'Matrimonial', 'Triple', 'Cuadruple', 'Suite')),
    PRIMARY KEY (hotel_id, numero_habitacion),
    FOREIGN KEY (hotel_id) REFERENCES Hotel(id) ON DELETE CASCADE
);

-----------------------------------------------------------

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

----------------------------------------------------------

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