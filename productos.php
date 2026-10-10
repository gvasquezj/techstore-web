<?php
require_once 'conexion.php';

$mensaje = '';
$tipo_alerta = '';

// Procesar envio del formulario de productos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio      = trim($_POST['precio']);
    $stock       = trim($_POST['stock']);

    // Validacion en servidor
    if ($nombre === '' || $descripcion === '' || $precio === '' || $stock === '') {
        $mensaje = 'Error: Todos los campos son obligatorios.';
        $tipo_alerta = 'error';
    } elseif (!is_numeric($precio) || $precio <= 0) {
        $mensaje = 'Error: El precio debe ser un numero positivo.';
        $tipo_alerta = 'error';
    } elseif (!is_numeric($stock) || $stock < 0) {
        $mensaje = 'Error: El stock debe ser mayor o igual a cero.';
        $tipo_alerta = 'error';
    } else {
        // Inserción en la tabla PRODUCTO
        $sql = "INSERT INTO PRODUCTO (nombre, descripcion, precio, stock) VALUES ('$nombre', '$descripcion', $precio, $stock)";
        if ($conexion->query($sql) === TRUE) {
            $mensaje = "Producto '$nombre' registrado exitosamente en la base de datos.";
            $tipo_alerta = 'exito';
        } else {
            $mensaje = "Error al insertar en la base de datos: " . $conexion->error;
            $tipo_alerta = 'error';
        }
    }
}

// Consulta simple para visualizar todos los productos registrados
$sql_select = "SELECT id_producto, nombre, descripcion, precio, stock FROM PRODUCTO ORDER BY id_producto ASC";
$resultado = $conexion->query($sql_select);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Gestion de Productos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/validaciones.js"></script>
</head>
<body>

    <header class="encabezado">
        <div class="contenedor-cabecera">
            <div class="logotipo">
                <h1>Tienda <span>TechStore</span></h1>
            </div>
            <nav class="navegacion-principal">
                <a href="index.php" class="enlace-nav">Catalogo</a>
                <a href="productos.php" class="enlace-nav activo">Productos</a>
                <a href="clientes.php" class="enlace-nav">Clientes</a>
                <a href="compras.php" class="enlace-nav">Compras</a>
                <a href="reportes.php" class="enlace-nav">Reportes</a>
            </nav>
        </div>
    </header>

    <main class="contenedor">

        <?php if (!empty($mensaje)): ?>
            <div class="<?php echo ($tipo_alerta === 'exito') ? 'mensaje-exito' : 'mensaje-error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de Registro de Producto -->
        <section class="panel-formulario">
            <h2 class="titulo-seccion">Registrar Nuevo Producto</h2>
            <form action="productos.php" method="POST" onsubmit="return validarProducto();" class="formulario-registro">
                <div class="grupo-campo">
                    <label for="nombre_producto">Nombre del Producto:</label>
                    <input type="text" id="nombre_producto" name="nombre" placeholder="Ej: Monitor Gamer 24" required>
                </div>

                <div class="grupo-campo">
                    <label for="descripcion_producto">Descripcion:</label>
                    <textarea id="descripcion_producto" name="descripcion" rows="3" placeholder="Detalles y especificaciones del producto..." required></textarea>
                </div>

                <div class="fila-campos">
                    <div class="grupo-campo">
                        <label for="precio_producto">Precio ($ CLP):</label>
                        <input type="number" id="precio_producto" name="precio" placeholder="Ej: 149990" min="1" required>
                    </div>

                    <div class="grupo-campo">
                        <label for="stock_producto">Stock Inicial:</label>
                        <input type="number" id="stock_producto" name="stock" placeholder="Ej: 15" min="0" required>
                    </div>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton-primario">Guardar Producto</button>
                    <button type="reset" class="boton-secundario">Limpiar</button>
                </div>
            </form>
        </section>

        <!-- Tabla de Productos Registrados (Consulta simple) -->
        <section class="panel-tabla">
            <h2 class="titulo-seccion">Productos en Base de Datos (Tabla PRODUCTO)</h2>
            <div class="tabla-responsiva">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripcion</th>
                            <th>Precio</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $fila['id_producto']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($fila['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                                    <td>$<?php echo number_format($fila['precio'], 0, ',', '.'); ?> CLP</td>
                                    <td><span class="insignia-stock"><?php echo $fila['stock']; ?> un.</span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="celda-vacia">No hay productos registrados en la base de datos.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>
</html>
<?php
$conexion->close();
?>
