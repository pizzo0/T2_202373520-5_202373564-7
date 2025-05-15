# GESCON

`README en proceso`

---

## Requerimientos

* [Docker](https://www.docker.com/)

---

## Descripción y supuestos

### Login y Signup

De aqui en adelante, cuando nos refiramos a `usuarios`, nos referiremos a `autores`, `revisores` y el `jefe de comite`.

Para la parte de inicio de sesion y registro. Desde `/login` pueden iniciar sesión todos (autores, revisores y el jefe de comite) y para registrarse, se hace desde `/signup`.

hicimos que de forma comun, solo puedas registrarte como usuario.
El jefe de comite (que se crea uno por defecto para nuestra base de datos) es el que puede crear los revisores desde `/gestion`. De todas formas, el registro en `/gesion` es practicamente igual que en `/signup`, solo que automaticamente se le asigna el rol de Revisor al usuario creado.

El proceso de `/signup` pide:
* `Rut`: Este es el PK y es UNICO para cada usuario.
* `Nombre`: Este puede ser lo que el usuario quiera.
* `Correo`: Este es UNICO para cada usuario.
* `Contraseña`: Debe contener al menos:
    - Una letra mayuscula.
    - Una letra minuscula.
    - Un numero.
    - Largo de 8 caracteres.

El proceso de `/login` se hace simplemente con el `correo` y la `contraseña`, para mas comodidad.

### CRUD

Los usuarios pueden:
* Crear articulo desde `/publicar`.
* Ver su perfil desde `/pefil` (visualizar sus datos).
* Actualizar cosas como `nombre`, `correo` y `contraseña`. Esto se hace desde `/perfil`.
* Pueden cerrar sesion o eliminar su cuenta. Tambien se hace desde `/perfil`.
    - Si es jefe de comite, no se le permitira eliminar su cuenta.

`CONTINUAR AQUI...`

* Los archivos `.sql` de la base de datos estan en `gescon/bd`.
    - `TABLES.sql` tiene todas las tablas necesarias.
    - `TRIGGERS.sql` tiene los triggers de las tablas.
    - `FUNCTIONS.sql` tiene las funciones de la base de datos.
        - En este caso solo hay uno.
    - `PROCEDURES.sql` tiene los procedimientos almacenados.
    - `VIEWS.sql` tiene los views.
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
* Los archivos `.js` estan todos en `gescon/js`. Estos pueden llegar a interactuar con las apis de la pagina mencionadas antes.
* Los archivos `.css` estan todos en `gescon/css`.
* Los assets estan todos en `gescon/assets`. Dentro encontramos:
    - `/svg` donde estan los `.svg`.
    - Y eso nada mas xd.
* El modelo conceptual y normalización esta en `normalización.pdf`.
* Los archivos pedidos estan en la carpeta `gescon`.

---

## Como utilizar

Con una terminal en la carpeta principal de la pagina, donde se encuentran los archivos `Dockerfile` y `docker-compose.yml`, ejecutar el comando:

```
docker-compose build
```

Esto creara los contenedores en Docker (Esto puede tardar un poco). Una vez con los contenedores listos, ejecutar el comando:

```
docker-compose up -d
```

El `-d` es para poder seguir utilizando la terminal sin ver el `log` de los contenedores. Si quieres verlo, simplemente no coloques `-d`.

Esto iniciara los contenedores de la pagina web en php (`www`), la base de datos con mySql (`db`) y el administrador de la base de datos (`phpmyadmin`).

Ahora tendras los siguientes accesos:
* Pagina web en [localhost:8081](localhost:8081)
* phpmyadmin en [localhost:8080](localhost:8080)

Ademas, tendras una nueva carpeta llamada `mysql-data`, en donde esta todo lo que almacena la base de datos.

Cuando termines de utilizar la pagina web, puedes cerrar los contenedores ejecutando el comando:

```
docker-compose down
```

Si iniciaste los contenedores sin `-d`, puedes cerrar los contenedores usando `ctrl + C`.

Luego, cuando quieras volver a usar la pagina (si la cerraste), solo debes ejecutar el segundo comando (`docker-compose up -d`).

> [!WARNING]
> Si en algun paso ocurre un error, lo mas probable es que tienes `Docker Desktop` cerrado. Abrelo para evitar errores.

---

## Integrantes
* Alejandro Cáceres [202373520-5] (P.200)
* Miguel Salamanca [202373564-7] (P.200)

---