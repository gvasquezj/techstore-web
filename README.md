# TechStore - Aplicacion de Comercio Electronico

Proyecto web desarrollado para la asignatura **Programacion Web II** (Instituto Profesional IACC).

## Descripcion
TechStore es una aplicacion web full stack desarrollada en PHP, JavaScript, HTML5, CSS3 y MySQL. Permite la busqueda y filtrado de productos tecnologicos, registro de clientes, procesamiento y gestion de pedidos con calculo de totales, e integracion con pasarelas de pago seguras.

## Estructura del Proyecto
- `conexion.php`: Modulo de conexion orientada a objetos con MySQLi y manejo de errores.
- `index.php`: Catalogo principal con busqueda y filtrado por nombre y descripcion.
- `productos.php`: Formulario y registro de nuevos productos en la base de datos.
- `clientes.php`: Formulario y registro de clientes con validacion de correo y direccion.
- `pedidos.php`: Modulo de gestion de pedidos, seleccion de pasarelas de pago y actualizacion de stock.
- `compras.php`: Registro directo de compras y tabla de operaciones.
- `reportes.php`: Reportes analiticos con consultas avanzadas (GROUP BY y HAVING).
- `tienda.sql`: Script DDL/DML para crear e inicializar la base de datos `TIENDA`.
- `js/validaciones.js`: Funciones de validacion de campos y calculo dinamico de pedidos.
- `css/estilos.css`: Hoja de estilos con diseno responsivo y estructurado.

## Requisitos y Despliegue Local
1. Servidor web local XAMPP (Apache y MySQL activos).
2. Importar el script `tienda.sql` en phpMyAdmin (`http://localhost/phpmyadmin/`).
3. Colocar la carpeta en `htdocs` y acceder mediante `http://localhost/html/` o `http://localhost/tienda/`.
