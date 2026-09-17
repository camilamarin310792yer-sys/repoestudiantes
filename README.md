# Harvard Hall — Sistema de Gestión de Alumnos

Aplicativo web en PHP + MySQL para administrar alumnos de una institución educativa.

## Funciones

- Registrar alumnos.
- Consultar/directorio de alumnos.
- Editar alumnos.
- Eliminar alumnos.
- Validar que la identificación no esté duplicada.
- Crear automáticamente la tabla `alumnos` si no existe.
- Diseño responsive inspirado en un hall universitario clásico.

## Campos

- Nombre
- Identificación
- Teléfono

## Base de datos

El archivo `config.php` contiene las credenciales suministradas:

- Host: `mysql-yerissas.alwaysdata.net`
- Usuario: `yerissas`
- Base de datos: `yerissas_alumnos`

> Por seguridad, se recomienda cambiar la contraseña si el proyecto se comparte públicamente.

## Instalación en AlwaysData

1. Crea o verifica la base de datos `yerissas_alumnos`.
2. Sube todos los archivos a la carpeta pública de tu sitio.
3. Verifica que el servidor tenga PHP y extensión MySQLi.
4. Abre `index.php` desde tu dominio.
5. Al cargar el aplicativo, `config.php` crea automáticamente la tabla `alumnos` si todavía no existe.

No es necesario ejecutar un SQL separado.

## Estructura

```text
aplicativo_alumnos_colegio/
├── index.php
├── config.php
├── estilos.css
└── README.md
```

## Nota sobre las imágenes

La interfaz utiliza fotografías remotas de Unsplash como fondo visual. Se requiere conexión a Internet para que esas imágenes se carguen.

## Seguridad

Este proyecto está pensado para una entrega académica. Para producción se recomienda añadir autenticación, protección CSRF, variables de entorno para credenciales y controles de acceso.
