# Entrega 1 - Bases de datos IIC2413

### Datos del Alumno
| **Nombre Completo** | **Número de Alumno** |
|---------------------|----------------------|
| Álvaro Panozo       |             24664057 |


### Credenciales de acceso

| **Usuario Uc**               | **Valor** |
|------------------------------|-----------|
| lvaro.panozo@estudiante.uc.cl| 24664057  |

### 1. Modelo E/R 

![Diagrama E/R](E1\Diagrama_E-R_BDD.svg)

<!-- Tienes que agregar la ruta de tu imagen donde dice diagrama.png, tiene que ser en formato svg para no perder calidad -->

### 2. Identificación de entidades débiles y justificación

#### 2.1 Agenda
En el diagrama la entidad __Agenda__ es débil porque depende de usuario para poder respetar el principio de unicidad, ademas que sin __Usuario__ no puede existir __Agenda__

### 2.2 Seguro
La entidad __Seguro__ es debil porque depende de __Usuario__ y __Reserva__ para poder mantener la unicidad. 

### 2.3 Review
En el caso de la entidad __Review__, es muy similar al caso de __Seguro__, tambien depende de __Reserva__ y __Usuario__ para que su representacion unica.

### 2.4 Invitados
__Invitados__ es debil respecto a __Usuario__ y __Panorama__, ya que logicamente un usuario tiene varios invitados, por lo que la unica forma de representarlos a cada uno individualmente es usando el correo de quien los invito, la id_reserva a la cual lo invito,y el nombre del invitado.

### 2.5 Habitacion
__Habitacion__ es debil respecto a __Hotel__ ya que si bien tiene un numero de habitacion unico, el numero puede ser el mismo en otro hotel, por lo que para mantener la unicidad necesitamos, tambien, del hotel

### 3. Identificación de llaves primerarias/compuesta y justificación

#### 3.1 Persona
- La llave primaria en la __Persona__ es el _correo_ porque no pueden existir dos personas con registradas con el mismo correo, y como rut y dv estan separados en dos atributos distintos, el correo es ideal (y el unico) que nos permite evitar la duplicidad.

### 3.2 Reserva
- La llave primaria de __Reserva__ es __id_reserva__. Se decide agregar esta surrogate key debido a que reserva no puede ser debil ni con usuario ni con agenda (particularmente no depende de ninguna de estas dos para existir), y sus atributos por si solos no son unicos, inclusive la llave compuesta de todos sus atributos (no especifica enunciado) por lo que para evitar usar todos los atributos como llave compuesta, que podria llevar a anomalias y a violar el principio de unicidad, se le añade esta surrogate key.
(cabe destacar que el enunciado no especifica la prohibicion de nuevos atributos)

#### 3.3 Agenda
- La llave compuesta en la __Agenda__ está compuesta por _correo_ y _etiqueta_ porque pueden existir muchas agendas con la misma etiqueta, pero una agenda esta asociada a solo un usuario, por lo que la tupla (correo, etiqueta) nos permite mantener la unicidad

### 3.4 Seguros
-La llave compuesta en __Seguros__ esta dada por _correo_, __id_reserva__ y _tipo_, ya que un seguro esta asociado a solamente un usuario y a una reserva, y los tipos de seguros son unicos dentro de estos, por lo que con el id, el correo, y el tipo de seguro, seriamos capaces de representar a cada uno individualmente sin repetidos.

### 3.5 Review
-La llave en review es simplemente __id_reserva__ y __correo__, sin necesidad de atributos dentro de la review, esto debido a que un usuario solo puede dejar una review a una reserva, por lo que estos dos atributos son suficientes (y los minimos) para mantener la unicidad

### 3.6 Habitaciones
-En este caso, la llave compusta es  (id_reserva, numero_habitacion), ya que podrian haber varios hoteles con el mismo numero de habitacion, por lo que requerrimos identificar al hotel para representar de manera unica a una habitacion

### 3.7 Invitados
-Finalmente, para invitados usaremos la tupla (correo, nombre_invitado, id_reserva), ya que  un usuario tiene asignados varios invitados, por lo que para represntarlos de manera unica necesitamos el correo de quien lo invito, la id_reserva del panorama al cual lo invito, y su nombre.


### 4. Explicación cardinalidades modelo E/R

#### 4.1 Entidad_1 y Entidad_2 {1, 0 a n}

1 instancia de __Usuario__ puede tener n instancias de __Agenda__. 

1 instancia de __Usuario__ puede tomar n instancia de __Reservas__.

1 instancia de  __Usuario__ puede dejar 1 instancia de __Review__ a 1 instancia de __Reserva__

1 instancia de __Usuario__ puede tomar n instancias de __Seguros__ para 1 instancia de __Reserva__

1 instancia de __Usuario__ puede tener a n instancias de __Invitados__ para 1 instancia de __Panorama__

1 instancia de __Reserva__ puede estar en 0,1 instancia de __Agenda__

1 instancia de __Hotel__ tiene n __Habitaciones__ 

1 instancia de __Trabajador__ tiene asignada 1 instancia de __Transporte__

### 5. Identificación de jerarquías

En el diagrama se da que la entidad __Persona__ es entidad padre de __Usuario__ y __Trabajador__ porque ambas entidades comparten los mismos atributos que __Persona__, y cada entidad hija le agrega atributos a los originales, ademas de compartir el mismo atributo como PK (correo). Vale decir que es total, porque todas las personas son o __Trabajadores__ o __Usuario__ y disyuntiva, ya que a pesar que en efectos practicos un __Trabajador__ puede ser __Usuario__, necesitaria usar otro correo, por lo que para nuestra BDD serian personas distintas.

Tambien se da que __Transporte__ es padre de __Bus__, __Avion__, __Tren__, y de igual manera, es porque comparten atributos en común, y la PK de cada una es el mismo atributo (id_reserva), ademas que cada entidad hija le añade atributos unicos. Tambien es total y disyuntiva, ya que por lo que se entiende del enunciado, un trasporte es un bus, un avion, "O" un tren. Disyuntiva ya que trivialmente un bus no puede ser un avion o un tren, y viceversa.

Similar es el caso con __Reserva__, la cual es la entidad padre de __Transporte__, __Panorama__, __Hospedaje__. Cada tipo de reserva comparte los atributos de la entidad padre, y logicamente el atributo que es la PK. Cada una añade sus atributos propios. Vale agregar que de igual forma forma es total y disyuntiva, ya que una reserva de un __Transporte__ no puede ser una reserva de un __Hospedaje__ a la vez, y viceserva. Total, ya que se nos indica que una reserva puede ser __Transporte__, __Hospedaje__, "O" __Panorama__

Finalmente el caso de  __Hospedaje__ que es padre de __Hotel__ y __Airbnb__. De igual forma, comparten los mismos atributos y su mismo atributo es PK, pero cada uno le añade ,sus atributos

### 6. Esquema Relacional

__Persona__: (nombre: string, <u>correo</u>: string, contrasña: string, nombre_de_usuario: string, teléfono_de_contacto: string, rut: int, dv: string)
__Empleado__: (<u>correo</u>: string, contrato: string, isapre: string, jornada: string) as T
__Usuario__ : (<u>correo</u>: string, puntos_booked: int) as U
__Agenda__: (<u>etiqueta</u>: string, <u>U.correo</u>: str)

__Reserva__: (<u>id_reserva</u>:int, monto: int, fecha: date, puntos_a_añadir: int, cantidad_de_personas: int, estado_disponibilidad: string) as R

__Review__: (<u>R.id_reserva</u>: int, <u>U.correo</u>: string, estrellas: int, descripcion: string) 

__Seguro__: (<u>R.id_reserva</u>: int, <u>U.correo</u>: string, <u>tipo</u>: string, valor: int, clausula: string, emprsa:string)

__Hospedaje__:(<u>id_reserva</u>: int, nombre_hospedaje: string, ubicacion: string, precio_por_noche: int, estrellas: int, fecha_check_in: date, fecha_check_out: date) as Hos
__Comodidades__ (<u>Hos.id_reserva</u>: int, comodidad: string) **
__Hotel__: (<u>id_reserva</u>: int) as H
__Normativas_hotel__: (<u>H.id_reserva</u>: int, normativa: string) **
__Habitacion__: (<u>H.id_reserva</u>: int, <u>numero_habitacion</u>: int, tipo: string)
__Airbnb__: (<u>id_reserva</u>: int, nombre_anfitrion: string, contacto_anfitrion: string, descripcion_airbnb: string, cantidad_piezas: int, cantidad_camas: int, cantidad_baños: int)

__Panorama__: (<u>id_reserva</u>: int, nombre_panorama: string, empresa_panorama: string, ubicacion_panorama: string, duracion: int, precio: int, capacidad_panorama: int, fecha_panorama: date) as P
__Resticciones_panorama__:(<u>P.id_reserva</u>: int, restriccion: string) **
__Invitados__(<u>U.correo</u>: string,<u>P.id_reserva</u>: int ,<u>nombre_invitado</u>: string, categoria: string)

__Transporte__: (<u>id_reserva</u>: int, T.correo: string, lugar_origen: string, lugar_llegada: string, capacidad: int, tiempo_estimado: int, precio_transporte: int, fecha_salida: date, fecha_llegada: date) as Trans

__Bus__:(<u>Trans.id_reserva</u>: int, tipo_bus: string) as B
__Comodidades_bus__: (<u>B.id_reserva</u>: int, comodidad: string) **

__Avion__:(<u>Trans.id_reserva</u>: int, clase_avion: string) as A
__Escala_aviones__: (<u>A.id_reserva </u>: int, escala)

__Tren__: (<u>Trans.id_reserva</u>: int) as Tren *
__Comodidades_tren__: (<u>Tren.id_reserva</u>, comodidad: string)**
__Paradas_tren__: (<u>Tren.id_reserva</u>, parada: string) **



### 7. Justificación de tablas de relaciones

Todas las tablas con ** son producto del principio de datos atomicos. Ya que hay atributos multivaluados, se decide crear una tabla con referencia a la PK de la entidad, y ahi dar cada atributo necesario. 
Luego, podemos notar que * solo tiene la PK como atributo, esto debido a que, ademas de los atributos multivaluados, no tiene ningun otro. Aun asi se mantiene la tabla, para evitar anomalias y mantener la semantica.

Las tablas son validas ya que cada tabla que relaciona dos o mas entidades cuenta con una FK 
(que seria la PK de la entidad referenciada) para poder referenciarse en la tabla. Por 
ejemplo, invitados cuenta con el correo del usuario quien lo invito, y la ID de la reserva a 
la que fue invitado.

### 8. Justificación sobre la consistencia del diseño del esquema relacional y normalización en BCNF

# Fidelidad
El modelo relacional respeta fielmente el modelo E/R original, conservando jerarquías, relaciones entre entidades y estructuras de dependencia como las entidades débiles. Por lo que el dominio esta correctamente representado.

# Redundancia: 
Al descomponer atributos multivaluados (por ejemplo, comodidades, normativas, restricciones, paradas, etc.) en tablas separadas, se evita almacenar múltiples valores en una misma celda.
Anomalías: Al diseñar todas las tablas en BCNF, se eliminan dependencias funcionales, por lo tanto se evitan las anomalías de inserción, actualización y eliminación.

# Simplicidad y claridad: 
El uso de llaves compuestas cuando corresponde (por ejemplo, en __Agenda__, __Review__, __Invitados__) y el uso de claves sustitutas cuando es necesario (como en Reserva), permite un esquema que es más simple de consultar y mantener, sin sacrificar la unicidad de los registros.

# Buena elección de llaves primarias:
Las llaves fueron escogidas considerando criterios de unicidad mínima y estabilidad (por ejemplo, evitando el uso de RUT por estar dividido en dos atributos), y cuando no fue posible usar atributos existentes, se optó por llaves sustitutas como id_reserva.