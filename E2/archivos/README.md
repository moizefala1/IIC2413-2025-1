# Análisis de Restricciones de Integridad

## 1. Llaves Primarias (PRIMARY KEY)

### Identificadores Únicos
- `Persona`: correo (PRIMARY KEY)
  - Necesaria para identificar unívocamente a cada persona en el sistema
- `Reserva`: id (PRIMARY KEY)
  - Fundamental para mantener un registro único de cada reserva
- `Seguro`: id (SERIAL PRIMARY KEY)
  - Asegura identificación única y autoincremental de seguros
- `Review`: id (SERIAL PRIMARY KEY)
  - Permite identificar unívocamente cada reseña
- `Hospedaje`, `Hotel`, `Airbnb`: id (PRIMARY KEY)
  - Garantiza identificación única de cada alojamiento
- `Habitacion`: (hotel_id, numero_habitacion)
  - Clave compuesta necesaria ya que una habitación se identifica por su número dentro de un hotel específico

## 2. Llaves Foráneas (FOREIGN KEY)

### Referencias entre Entidades
- `Empleado` → `Persona`
  - Asegura que cada empleado sea una persona válida
  (correo_empleado)
- `Usuario` → `Persona`
  - Garantiza que cada usuario corresponda a una persona registrada
  (correo_usuario)
- `Agenda` → `Usuario`
  - Vincula cada agenda con un usuario existente
  (correo_usuario)
- `Seguro` → `Usuario`, `Reserva`
  - Conecta seguros con usuarios y reservas válidas
  (correo_usuario, reserva_id)
- `Review` → `Usuario`, `Reserva`
  - Asocia reseñas con usuarios y reservas existentes
  (correo_usuario, reserva_id)
- `Hotel`, `Airbnb` → `Hospedaje`
  - Implementa herencia entre tipos de alojamiento
  (id)
  (Analogamente para todas las tablas que heredan de `Reserva`, y las que heredan de estas mismas.)
- `Habitacion` → `Hotel`
  - Vincula habitaciones con hoteles existentes

## 3. Restricciones NOT NULL (sin considerar PK ni FK)

### Datos Obligatorios
- `Persona`: correo, run, dv
- `Empleado`: isapre, contrato
- `Usuario`: puntos
- `Agenda`: etiqueta
- `Reserva`:estado_disponibilidad
- `Seguro`:
- `Review`: estrellas
- `Panorama`: nombre, precio_persona, fecha_panorama
- `Participante`:
- `Hospedaje`: ubicacion, precio_noche, estrellas, fecha_checkin
- `Hotel`: 
- `Habitacion`: 
- `Airbnb`:
- `Transporte`: correo_empleado tiempo_estimado, precio_asiento, empresa, fecha_salida
- `Tren`: comodidades, paradas
- `Bus`: comodidades, tipo
- `Avion`: clase, escalas

Estos valores se decidieron tomando en consideracion las palabras que se usaban en el enunciado, tomando "tienen" como posibilidad de nulo, pero "exige", "deben" "se pide", y etc para atributos no nulos. Tambien se tuvo en consideracion todas las discussions que hubieron, aclarando cambios respecto al enunciado original. En caso de herencias, se escogió arbitrariamente atribtos no nulos para poder diferenciar entre quienes heredan (como por ejemplo en `Transporte`, donde habia que diferenciar entre `bus`, `tren` y `avion`). De no especificarse  lo contrario, y no ser necesario, se tomaron atributos nulos. 

## 4. Restricciones UNIQUE

### Valores Únicos
- `Persona`: username
  - Evita duplicidad de nombres de usuario
- `seguro_unica`: (correo_usuario, tipo, reserva_id)
  - Previene múltiples seguros del mismo tipo para una reserva
- `review_unica`: (correo_usuario, reserva_id)
  - Limita a una reseña por usuario por reserva

## 5. Restricciones CHECK

### Validación de Datos
- `Usuario`: puntos >= 0
  - Asegura puntos no negativos
- `Reserva`: 
  - monto >= 0
  - cantidad_personas > 0
- `Review`: estrellas BETWEEN 1 AND 5
  - Rango válido de calificaciones
- `Empleado`: jornada IN ('Nocturno', 'Diurno')
  - Valores permitidos para jornada laboral
- `Habitacion`: tipo IN ('Sencilla', 'Doble', 'Matrimonial', 'Triple', 'Cuadruple', 'Suite')
  - Tipos válidos de habitación

Estas restricciones son fundamentales para mantener la consistencia e integridad de los datos, prevenir anomalías y asegurar que la información almacenada sea válida y confiable.

## 6 EJECUCION

Para la ejecucion de este codigo, se deben utilizar los siguientes comandos:

En Sites/E2/archivos:
```bash
$ psql -f crear_tablas.sql
$ psql -f poblar_tablas.sql
```

Analogamente para ejecutar los archivos de consultas, se deben utilizar los siguientes comandos en Sites/E2/consultas:

```bash
$ psql -f 1a.sql
$ psql -f 2a.sql
$ psql -f 3a.sql
...
```