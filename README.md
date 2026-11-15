# Agenda Personal de Contactos

Aplicación web creada en PHP y MySQL que permite a cada usuario registrarse, iniciar sesión y administrar su propia lista de contactos (agregar, ver, editar y eliminar) de manera privada y segura.

## Herramientas utilizadas
* **Programación:** PHP 8
* **Base de datos:** MySQL
* **Diseño:** HTML5 y Bootstrap 5
* **Servidor local:** XAMPP (Apache)

## Qué se necesita para ejecutarlo
* Tener instalado **XAMPP** (con Apache y MySQL activos).
* Un navegador web (Chrome, Edge, Brave, etc.).

## Cómo instalar y probar el proyecto
1. Descarga o clona esta carpeta dentro de `htdocs` en tu instalación de XAMPP (`htdocs/agenda-contactos`).
2. Abre el panel de XAMPP y enciende **Apache** y **MySQL**.
3. Abre **MySQL Workbench** o **phpMyAdmin** y ejecuta el archivo `database.sql` para que se creen la base de datos y las tablas.
4. Revisa el archivo `config/db.php` y coloca la contraseña de tu servidor MySQL si tienes una configurada.
5. Entra en tu navegador a la dirección: `http://localhost/agenda-contactos/` y crea tu cuenta para empezar a usar la agenda.