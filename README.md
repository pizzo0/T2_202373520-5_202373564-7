# GESCON

App web con `PHP` y `MySQL`

## Descripción y supuestos

De aqui en adelante, cuando digamos `usuarios` nos referimos a `autores`, `revisores` y `jefe de comite`. Tambien, cuando digamos `miembro(s) de comite` o solo `miembro(s)`, nos referiremos a `revisores` y `jefe de comite`.

### Login y Signup

Desde `/login` pueden iniciar sesión los `usuarios` y para registrarse, se hace desde `/signup`.

La base de datos siempre tendra un `jefe de comite` por defecto. Se puede iniciar sesion a esta cuenta con:
* `correo` : admin@gescon.com
* `contraseña` : Admin0

hicimos que de forma comun, solo puedas registrarte como `autor`.
El `jefe de comite` es el que puede crear los `revisores` desde `/gestion`. El registro en `/gesion` es practicamente igual que en `/signup`, solo que automaticamente se le asigna el rol de Revisor al usuario creado.

El proceso de `/signup` pide:
* `Rut`: Este es el PK y es UNICO para cada usuario.
* `Nombre`: Este puede ser lo que el usuario quiera.
* `Correo`: Este es UNICO para cada usuario.
* `Contraseña`: Debe contener al menos:
    - Una letra mayuscula.
    - Una letra minuscula.
    - Un numero.
    - Largo de 6 caracteres minimo.

El proceso de `/login` se hace simplemente con el `correo` y la `contraseña`.

### Pagina principal

#### Buscar y busqueda avanzada

En este caso, definimos que un articulo estara en estado `revisado` o `evaluado` cuando este tenga 3 formularios/revisiones hechas.

La pagina tiene una barra de busqueda en `/` (`index`/`inicio`) y `/buscar`. La primera (`/`) te redirige a `/buscar`. Todos los usuarios pueden hacer esto.

Esta barra de busqueda filtra a partir del titulo de los articulos.

En `/buscar` puedes ver todos los articulos de la pagina, tanto los `evaluados` como los `no evaluados`. Aqui se muestra el titulo, resumen, topicos y autores de cada articulo. Por defecto, esta muestra los `evaluados`, pero el `usuario` puede quitar este filtro si quiere.

Los articulos `evaluados` se muestran con un icono de "verificado".

En `/buscar` tambien se puede hacer una `busqueda avanzada`. Se dispone de 9 filtros en total:
* Filtro para `ordenar por` fecha de publicacion, nombre de los autores de contacto y titulo de los articulos.
* Filtro por `ID del Articulo`, de forma que uno puede buscar la `id` de un articulo en especifico.
* Filtro por `contacto`, donde se puede filtrar por el nombre del autor de contacto.
* Filtro por `autor`, donde busca que alguno de los autores de los articulos tenga un nombre similar.
* Filtro por `Topico`, donde uno puede seleccionar alguno de los topicos que hay en la pagina, mostrando solo articulos que tengan ese topico.
* Filtro por `Revisor`, que es similar al filtro por `autor`, solo filtra por revisores.
* Filtro por `fecha desde` y `fecha hasta`, de forma que uno puede seleccionar una fecha minima y maxima donde pueden haberse publicado los articulos. Es posible simplemente dejar una de estas casillas vacias si uno quiere (o ambas vacias obviamente xD).
* Filtro si esta `revisado`, de forma que se muestren todos los articulos si este esta apagado, o solo los `evaluados` si este esta encendido. Por defecto este estara activado al abrir `/buscar`!

#### Perfil

Los usuarios pueden:
* (C) Crear articulo desde `/publicar`.
* (R) Ver su perfil desde `/pefil` (visualizar sus datos).
* (U) Actualizar cosas como `nombre`, `correo` y `contraseña`. Esto se hace desde `/perfil`.
* (D) Pueden cerrar sesion o eliminar su cuenta. Tambien se hace desde `/perfil`, en las `opciones`.
    - Si es jefe de comite, no se le permitira eliminar su cuenta.

En el perfil, a parte de lo mecionado ya, el `usuario` puede ver todos sus articulos (articulos donde es autor), tanto evaluados como no evaluados. Este tiene a disposicion un filtro `Solo mis articulos evaluados` para ver solo los articulos evaluados o todos.

A parte, si un `miembro de comite` accede a su perfil, este puede ver en otra `tab` los articulos que tiene para revisar. Este tiene a disposicion dos filtros, donde solo uno puede estar activo al mismo tiempo:
* `Solo articulo no revisados por mi`, el cual permite ver los articulos que aun NO ha revisado.
* `No mostrar articulos evaluados`, el cual le permite ver los articulos que aun NO han sido evaluados.
    - Solo uno puede estar activo, ya que al filtrar por el primero, de por si ya no se muestran los articulos evaluados. El segundo filtro es mas algo extra, por si solo quieres ver los articulos NO evaluados.
    - Es facil quitar esta limitacion de un filtro a la vez, pero no lo haremos :P

#### Publicar

Aqui, el `usuario` podra crear un articulo, donde debe ingresar:
* `Titulo` : Este DEBE ser unico para los autores del articulo. Es decir, ninguno de los autores puede tener ya un articulo con el mismo titulo. No puede estar vacio.
* `Resumen` : Un simple resumen del articulo. No puede estar vacio.
* `Topicos` : Un articulo no puede no tener topicos. Si no, no se puede publicar. Minimo debe tener UN topico.
* `Autores` : Estos autores DEBEN estar registrados en la pagina, pudiendo ser cualquier `usuario`.
    - Para agregar como `autor` al articulo, se debe ingresar el correo del `usuario`.
    - Un articulo tiene la limitación de que el `usuario` que esta publicando el articulo DEBE ser `autor`, por lo que no puede eliminarse a si mismo.
    - La pagina y la base de datos no permite la repeticion de `autores`.
* `Autor de contacto` : Se puede seleccionar como autor a cualquiera de los autores que se ingresen.
    - Si se intenta enviar el articulo sin seleccionar el autor de contacto, este no se publicara y mostrara un error como notificacion.
        - El `autor de contacto` sera quien "recibe" el supuesto correo con la contraseña del articulo.
        - Por comodidad, todos los articulos creados desde la pagina tendran la clave `1` por defecto. La implementacion que genera una clave para la entrega estara comentada.
* Cuando se publica un articulo, se le asigna una `fecha limite` al mismo. Esta `fecha limite` es una semana posterior a la publicacion del articulo, por lo que los `autores` tendran una semana para editar o eliminar el articulo. Luego de pasar este tiempo, ya queda a los `miembros de comite` asignados para revisar el articulo hacer la evaluacion del mismo. Mas sobre esto luego.

#### Articulo y edicion

En `/articulo/X` uno puede ver la información de un articulo con `id = X`. Esta informacion siendo:
* `Titulo`
* `calidad`, `originalidad` y `valoracion` promedio si es que el articulo esta `evaluado`. Mas sobre esto luego.
* `Fecha de publicacion`
* `Fecha de edicion` (en la parte de `/edicion` se explicara que es esto)
* `Resumen`
* `Topicos`
* `Contacto`
* `Autor(es)`
* `Revisor(es)`
* Notar que `/articulo` por si solo no existe, mostrando la pagina `404`.
    - Tambien, si se da un `/articulo/X` donde `id = X` no existe, se mostrara la pagina `404` tambien.

Si un `miembro de comite` que esta asignado para revisar el articulo accede aqui desde `/buscar` o `/perfil` (o de cualquier parte en verdad), este podra:
* Hacer su revision.
* Cada `miembro de comite` asignado para revisar puede hacer solo una revision por articulo asignado.
* En la revision, podra:
    - Dar una nota de `calidad`. Este es obligatorio.
    - Dar una nota de `originalidad`. Este es obligatorio.
    - Dar una nota de `valoracion`. Este es obligatorio.
    - Dar `argumentos` sobre su valoracion. Este es obligatorio.
    - Dar `comentarios`. Este es opcional.
* Estas revisiones pueden hacerse una vez llegado a la `fecha limite` del articulo.
* Cuando los TRES `miembros` asignados hayan mandado sus revisiones, el articulo se considerara como `evaluado`.
    - Al estar en estado `evaluado`, todos podran ver el promedio de `calidad`, `originalidad` y `valoracion` de las TRES revisiones.
    - El estado se cumple solo cuando tiene TRES revisiones, por lo que es necesario que tenga TRES `miembros de comite` asignados para revisar el articulo para lograr esto.
* El `miembro` podra eliminar o editar su revision cuando quiera.
* Como extra, el `jefe de comite` podra eliminar cualquier revision que quiera.
* Los `autores` del articulo, los `miembros de comite` asignados al articulo y el `jefe de comite` podran revisar cada revision individualmente como consulta.

Desde `/articulo/X`, si eres `autor` del articulo, se puede acceder a `/editar/X`. Aqui, una vez ingresada la clave del articulo solicitada correctamente al ingresar, se podra editar el articulo.
* Antes de ingresar la clave, en la ventana de verificacion, se mostrara la `fecha limite` del articulo.
    - Al pasar esta `fecha limite`, los `autores` no podran editar ni eliminar el articulo.
* Aqui el `autor` puede editar:
    - `titulo`
    - `resumen`
    - `autores`
    - `autor de contacto`
        - Estos tendran las mismas restricciones que al publicar.
        - Las unicas diferencias son:
            - El `autor` que este editando el articulo, no podra eliminarse a si mismo.
            - No se pueden cambiar los topicos del articulo para no romper los `miembros de comite` asignados al articulo.
* Aqui tambien el autor puede eliminar el articulo.
* Se le dara 10 minutos al `autor` para modificar su articulo. Luego de esos 10 minutos, al recargar la pagina, se solicitara por la clave nuevamente.
* Si al recargar aun no pasan los 10 minutos, se mostrara con una notificacion del tiempo restante.
* Notar que `/editar` por si solo no existe, mostrando la pagina `404`.
    - Esta parte de la pagina SOLO puede ser accedida por todos los `autores` del articulo. Si alguien intenta acceder a `/editar/X` y no es `autor` del articulo con `id = X`, entonces mostrara la pagina `404`.

Basicamente, con esto los `autores` podran crear, leer, actualizar y eliminar sus propios articulos (`CRUD`), pero con algunas restricciones ya mencionadas.

#### Gestion y asignacion

En la pagina existe `/gestion`, donde solo el `jefe de comite` puede acceder, teniendo control completo sobre los `revisores`. Esta parte se divide en tres:
* `/gestion` : Aqui el `jefe de comite` puede:
    - (C) Crear `revisores`.
    - (R) Ver la información de cada `miembro de comite`, como especialidades, articulos asignados y su información (rut, correo y nombre).
    - (U) Puede modificar las especialidades, articulos asignados (a partir de la `id`) y la informacion de los `miembros de comite`.
        - La parte de `asignar un articulo a un revisor` es esto.
        - Si al modificar las especialidades, se le quita una especialidad que tenia y este estaba en algun articulo con este topico, si ninguna especialidad mas que tiene coincide con los topicos del articulo, entonces este sera desasignado del articulo automaticamente.
        - Obviamente el `miembro de comite` puede modificar su informacion si este quiere como cualquier `usuario` tambien xd.
            - Lo unico que no puede cambiar posteriormente el `jefe de comite`, es la contraseña del `miembro de comite`.
    - (D) Puede eliminar `miembros de comite` si este quiere.
        - Si tienen articulos asignados en estado de revision, esto no se podra hacer.
* `/gestion/topicos` : Aqui el `jefe de comite` puede crear y eliminar topicos si quiere.
* `/gestion/asignacion` : Aqui el `jefe de comite` puede `asignar revisores a un articulo`.
    - Si algun articulo tiene menos de TRES revisores, entonces aparece resaltado.
    - El `jefe de comite` tiene los mismos filtros que hay en `/buscar`, con la inclusion de uno extra:
        - Filtro `necesita revisores`, mostrando solo los articulos que tienen menos de TRES revisores (los que estan resaltados) si este esta activado.

De lo anterior, hay algunas cosas a tener en cuenta:
* La pagina y la base de datos solo permite asignar TRES `miembros de comite`.
* La base de datos intentara asignar los TRES `miembros de comite` de forma `automatica`. Si no encuentra los TRES, entonces asignara los que pueda.
* Al asignar un `miembro de comite` a un articulo, no se puede asignar si este es autor del mismo. La pagina no permite hacer esto, pero si se llegara a hacer, de todas formas la base de datos no permite hacerlo.
* La pagina y la base de datos no permite asignar `miembros de comite` a articulos donde sus especialidades no coincidan con los del articulo. Minimo debe tener una especialidad/topico del articulo. De lo contrario, no se permitira hacer la asignacion.
* Para cada articulo se da una lista con los posibles `miembros de comite` que se pueden asignar al articulo teniendo en cuenta lo anterior.
* El `jefe de comite` puede hacer que la base de datos haga la asignacion `automaticamente` si este quiere. Al hacerlo, se intentara encontrar los TRES `miembros de comite` para asignarlos al articulo, considerando las restricciones.
    - Es posible que la asignacion automatica no asigne nada o simplemente no asigne los TRES `miembros de comite` si es que no hay mas posibles `miembros de comite` para el articulo.
* Al pasar de la fecha limite, solo se podra `asignar miembros de comite` (si no existen los TRES). No se podra eliminar (`desasignar`) los que ya estan asignados.

Con todo lo anterior, el `jefe de comite` puede asignar, reasignar y eliminar `miembros de comite` a los articulos como el quiera :)

### Archivos

* Los archivos `.sql` de la base de datos estan en `gescon/bd`.
    - `0_INIT.sql` solo inicializa el uso de la base de datos `gescon`.
    - `1_TABLES.sql` tiene todas las tablas necesarias.
    - `2_TRIGGERS.sql` tiene los triggers de las tablas.
    - `3_FUNCTIONS.sql` tiene las funciones de la base de datos.
        - En este caso solo hay uno.
    - `4_PROCEDURES.sql` tiene los procedimientos almacenados.
    - `5_VIEWS.sql` tiene los views.
        - En este caso solo hay uno.
* Los archivos `.php` estan todos en `gescon/php`, a excepcion de `index.php`, el cual es necesario que este fuera para que la pagina funcione correctamente. Ademas, estan las siguientes subcarpetas dentro:
    - `/api` tiene las apis necesarias para interactuar con javascript.
    - `/config` tiene la configuración de la pagina web.
        - `config.php` tiene la configuración.
        - `func.php` tiene las funciones que se usan.
    - `/template` tiene la base con la cual se construye cada parte de la pagina web.
    - `/view.php` tiene mas que nada la estructura de cada parte de la pagina web. No deben tener nada relacionado con eliminar, insertar o actualizar algo de la base de datos. Como mucho leen algo de la pase de datos.
    - Dentro de cada uno existen los siguientes tipos de `.php`:
        - `X.view.php` estos estan en `/view.php` y son los que hacen lo que se menciona en el mismo punto.
            - La excepcion a esto es `template.view.php`, que esta en `/template`.
        - `X.Y.view.php` si el `view.php` tiene algo mas a parte del nombre (`X`), entonces es un componente del mismo. Estos componentes pueden ser utilizados en otros (`Z.view.php`), pero principalmente son para el nombre que tienen (`X`).
        - Los demas, si tienen algun punto en el nombre (a parte del para el formato `.php`), es solo para que se vea mas bonito en verdad xD.
        - Tambien hay algunos `componentes.X.php`, que son componentes para los demas php en general. Iban a haber mas, pero nos dio paja :v.
* Los archivos `.js` estan todos en `gescon/js`. Estos pueden llegar a interactuar con las apis de la pagina mencionadas antes.
* Los archivos `.css` estan todos en `gescon/css`.
* Los assets estan todos en `gescon/assets`. Dentro encontramos:
    - `/svg` donde estan los `.svg`.
    - Y eso nada mas xd.
* El modelo conceptual y normalización esta en `normalización.pdf`.
* Los archivos pedidos estan en la carpeta `gescon`.

## Requerimientos

* [Docker](https://www.docker.com/)
* [Python](https://www.python.org/)
    - Version utilizada para las pruebas: [3.13.0](https://www.python.org/downloads/release/python-3130/)

## Como utilizar

Abrir una terminal en la carpeta donde se encuentran los archivos `GENERAR-DATA.py`, `requirements.txt`, `Dockerfile` y `docker-compose.yml`.

Primero debemos generar la data de la base de datos. Para esto, necesitamos python, donde usaremos el modulo `faker` para crear informacion aleatoria. Puedes instalar el modulo mas rapido ejecutando desde la terminal el comando:
```bash
pip instal -r requirements.txt
```

Una vez instalado, podemos ejecutar el script de python que nos creara los datos de nuestra base de datos. Ejecutar el comando:
```bash
python GENERAR-DATA.py
```
o
```bash
python3 GENERAR-DATA.py
```
Esto nos creara un archivo `6_DATA.sql` en `/gescon/bd`. Este tendra la informacion que tendra nuestra base de datos para probarla.

Cosas a tener en cuentra:
* Todos los `usuarios` dentran de `contraseña`: `USer00`
* Todos los `articulos` tendran de `contraseña`: `1`
* No se van a crear mas `jefes de comite` a partir de este script, solo existira el que se crea por defecto.

Una vez hecho esto, podemos crear nuestros contenedores con `docker`. Ejecutamos el comando:
```bash
docker-compose up --build -d
```

Esto hara lo siguiente:
* Creara los contenedores en Docker (Esto puede tardar un poco).
* Inicia los contenedores sin mostrar el `log` (en segundo plano). Si quieres verlo, simplemente no coloques `-d`.
* Esto iniciara los servidores de la pagina web en php (`www`), la base de datos con mySql (`db`) y el administrador de la base de datos (`phpmyadmin`).
* Ahora tendras los siguientes accesos:
    - Pagina web en [localhost:8081](localhost:8081)
    - phpmyadmin en [localhost:8080](localhost:8080)
* Si iniciaste los contenedores sin `-d`, puedes cerrar los contenedores usando `ctrl + C`.
* Tendras una nueva carpeta llamada `mysql-data`, en donde esta todo lo que almacena la base de datos.

Tambien puedes usar el comando:
```bash
docker-compose build
```
Pero con este simplemente construiras los containers, no los iniciaras.

Cuando termines de utilizar la pagina web, puedes cerrar los contenedores ejecutando el comando:
```bash
docker-compose down
```

Notar que en el futuro, si quieres volver a iniciar los contenedores (una vez ya hayas construido los mismos, o si usaste `docker-compose build` y quieres iniciar los contenedores), puedes iniciarlos con el comando:
```bash
docker-compose up -d
```
Aqui tambien puedes ignorar el `-d` si quieres.

Para eliminar los datos cuando ya creaste la base de datos, puedes ejecutar el comando:
```bash
docker-compose down -v
```
Para asegurarnos de que la base de datos se borre correctamente, es recomendable eliminar tambien `/mysql-data`. OJO, debes ejecutar el comando antes de hacer esto.

> [!WARNING]
> Si en algun paso ocurre un error, lo mas probable es que tienes `Docker Desktop` cerrado. Abrelo para evitar errores.
> NOTAR TAMBIEN, que al iniciar los contenedores desde cero, puede que algunas partes de la pagina den "Fatal error". Es normal, hay que esperar a que la base de datos termine de asignar todos los datos que se crearon. Esto no suele tardar mucho de todas formas. Si quieres ver bien esto, puedes simplemente no colocar `-d` al construir los containers y asi podras ver todo el proceso.

## Integrantes
* Alejandro Cáceres [202373520-5] (P.200)
* Miguel Salamanca [202373564-7] (P.200)