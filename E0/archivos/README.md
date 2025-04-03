# Tarea E0 Proyecto BDD

## EJECUCIÓN

1.El programa se ejecuta automaticamente cuando se corre el archivo main.php en su entorno correspondiente, es decir, con el comando "php main.php" desde la terminal del servidor. Es importante mencionar que los archivos main.php y funciones.php deben mantenerse dentro de la carpeta "archivos" para su correcto funcionamiento.

## CONSIDERACIONES USUARIO

1.Como el rut no admite nulo, le asignamos un 0 en caso de no existir. Estandarizamos rut para que sea una string sin puntos.
2.El DV tampoco admite nulo, asi que le asignamos un 10 en caso de no existir, o de ser un valor no valido (que no sea 0-9 o K/k)
3.Como el correo es el identificador de la persona, en el caso de no estar en los dominios permitidos, no existir, o no tener al menos una letra antes del dominio, se considerara como un dato invalido. Para salvar la mayor cantidad de datos, el programa identifica si tiene dos arrobas, o si el dominio no termina en .com/.cl, y lo soluciona dependiendo del dominnio en cuestion (.cal para edubus, .cl para viajes y .com para cualquier otro caso)
3.Ya que nombre de usuario admite nulo, si es que el str nombre_usuario es vacia, se le asigna null
4.Lo mismo para la contraseña
5.Para el telefono, como no admite nulo, estandarizamos en "0 0000 0000" todos aquellos numeros que no existan. Luego, limpiamos los datos para darles el formato indicado en las reglas de negocio.
6.Como los puntos admiten nulo, simplemente les damos null si es que no existen. Establecemos que los puntos no pueden ser negativos, los estandarizamos a 0 en caso contrario
7.El codigo de agenda no puede ser nulo, por lo que de no existir establecemos "-1" como codigo de agenda.
8.La etiqueta puede ser nulo, por lo que le asignamos null en caso de de no existir
9.El codigo de reserva funciona de manera similar al codigo de agenda, por lo que de igual manera usamos -1 para identificar el nulo
10.Nos solicitan que la fecha cumpla el formato YYYY-MM-DD, por lo que limpiamos los datos. Admite nulo, por lo que establecemos null en el caso que no exista
11.Monto de reserva no admite nulo, establecemos -1 en caso de ser nulo, estandarizamos float como tipo de dato.
12.Cantidad de personas admite nulo, estandarizamos que el dato sea del tipo integer, y nulo en caso de no existir

## CONSIDERACIONES EMPLEADOS

1.Mantenemos la misma estandarizacion y formato para los datos compartidos entre ambos archivos.
2.Asignamos null para los elementos que lo permiten
3.Usaremos -1 para los numero de viajes que sean nulos
4.Para las fechas de llegadas, al no poder ser nulas, usaremos "0000-00-00" para referirnos a ellas.
5.Para el precio de asiento usaremos -1 en el caso de nulo
