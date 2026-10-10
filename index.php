<?php
require_once 'conexion.php';

// Filtros de busqueda
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Consulta de productos desde la base de datos MySQL (tabla PRODUCTO)
$sql = "SELECT id_producto, nombre, descripcion, precio, stock FROM PRODUCTO";
if (!empty($buscar)) {
    $buscar_escapado = $conexion->real_escape_string($buscar);
    $sql .= " WHERE nombre LIKE '%$buscar_escapado%' OR descripcion LIKE '%$buscar_escapado%'";
}
$sql .= " ORDER BY id_producto ASC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Catalogo de Productos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header class="encabezado">
        <div class="contenedor-cabecera">
            <div class="logotipo">
                <h1>Tienda <span>TechStore</span></h1>
            </div>
            <nav class="navegacion-principal">
                <a href="index.php" class="enlace-nav activo">Catalogo</a>
                <a href="productos.php" class="enlace-nav">Productos</a>
                <a href="clientes.php" class="enlace-nav">Clientes</a>
                <a href="compras.php" class="enlace-nav">Compras</a>
                <a href="reportes.php" class="enlace-nav">Reportes</a>
            </nav>
        </div>
    </header>

    <main class="contenedor">

        <!-- Panel de Busqueda y Filtros -->
        <section class="panel-filtros">
            <h2>Busqueda y Filtros de Productos</h2>
            <form method="GET" action="index.php" class="formulario-filtros">
                <div class="campo-filtro">
                    <label for="buscar">Buscar por nombre o descripcion:</label>
                    <input type="text" id="buscar" name="buscar" value="<?php echo htmlspecialchars($buscar); ?>" placeholder="Ej: Nitro, Teclado, Monitor, SSD...">
                </div>

                <div class="acciones-filtro">
                    <button type="submit" class="boton-primario">Buscar</button>
                    <a href="index.php" class="boton-secundario">Limpiar</a>
                </div>
            </form>
        </section>

        <!-- Catalogo de Productos -->
        <section class="seccion-catalogo">
            <h2 class="titulo-seccion">Productos en Base de Datos (TIENDA)</h2>
            <div class="grilla-productos">
                <?php 
                if ($resultado && $resultado->num_rows > 0) {
                    while ($producto = $resultado->fetch_assoc()) {
                ?>
                    <article class="tarjeta-producto">
                        <span class="etiqueta-categoria">Stock: <?php echo $producto['stock']; ?> un.</span>
                        <h3 class="nombre-producto"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="descripcion-producto"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <div class="pie-tarjeta">
                            <span class="precio-producto">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?> CLP</span>
                            <a href="compras.php" class="boton-agregar">
                                Comprar Ahora
                            </a>
                        </div>
                    </article>
                <?php 
                    }
                } else {
                    echo "<p class='mensaje-vacio'>No hay productos disponibles en la base de datos o coincidentes con la busqueda.</p>";
                }
                ?>
            </div>
        </section>

    </main>

    <footer class="pie-pagina">
        <p>TechStore Online &copy; 2026 - <span>Programacion Web II</span> - Integracion PHP y MySQL</p>
    </footer>

</body>
</html>
<?php
$conexion->close();
?>
