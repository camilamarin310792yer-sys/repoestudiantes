# University Hall — Gestión de Alumnos

Aplicativo web en **PHP + MySQL** para registrar, consultar, editar y eliminar alumnos.

## Campos
- Nombre
- Identificación (única)
- Teléfono

## Requisitos
- PHP 8.x o superior recomendado
- MySQL/MariaDB
- Extensión PDO MySQL habilitada
- Servidor Apache, Nginx o hosting compatible con PHP

## Instalación
1. Sube todos los archivos al hosting.
2. Verifica que `config.php` tenga las credenciales proporcionadas.
3. Abre `index.php` desde el navegador.
4. El sistema crea automáticamente la tabla `alumnos` si no existe.

No es necesario importar un archivo SQL.

## Estructura
- `index.php`: interfaz y operaciones CRUD.
- `config.php`: conexión PDO y creación automática de la tabla.
- `assets/style.css`: diseño responsive.
- `assets/university-hall.svg`: imagen local del hall universitario.
- `README.md`: documentación.

## Seguridad
Para producción se recomienda mover las credenciales fuera del directorio público o utilizar variables de entorno del hosting. Las consultas de datos usan sentencias preparadas.

## Nota
La estética está inspirada en una recepción universitaria clásica de estilo académico, sin utilizar material oficial de una universidad.
