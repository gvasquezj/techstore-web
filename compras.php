<?php
require_once 'conexion.php';

$mensaje = '';
$tipo_alerta = '';

// Procesar insercion de compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente  = (int)$_POST['id_cliente'];
    $id_producto = (int)$_POST['id_producto'];
    $cantidad    = (int)$_POST['cantidad'];
    $fecha       = trim($_POST['fecha']);

    if ($id_cliente <= 0 || $id_producto <= 0 || $cantidad <= 0 || empty($fecha)) {
        $mensaje = 'Error: Por favor completa todos los campos del formulario de compra.';
        $tipo_alerta = 'error';
    } else {
        // Consultar el precio y stock actual del producto seleccionado
        $sql_prod = "SELECT precio, stock, nombre FROM PRODUCTO WHERE id_producto = $id_producto";
        $res_prod = $conexion->query($sql_prod);

        if ($res_prod && $res_prod->num_rows > 0) {
            $prod = $res_prod->fetch_assoc();

            if ($prod['stock'] < $cantidad) {
                $mensaje = "Error: Stock insuficiente. El producto '" . $prod['nombre'] . "' tiene solo " . $prod['stock'] . " unidades disponibles.";
                $tipo_alerta = 'error';
            } else {
                $precio_unitario = (int)$prod['precio'];
                $total = $precio_unitario * $cantidad;

                // Insertar en la tabla COMPRA
                $sql_insert = "INSERT INTO COMPRA (cantidad, total, fecha, id_producto, id_cliente) 
                               VALUES ($cantidad, $total, '$fecha', $id_producto, $id_cliente)";

                if ($conexion->query($sql_insert) === TRUE) {
                    // Actualizar el stock del producto
                    $nuevo_stock = $prod['stock'] - $cantidad;
                    $conexion->query("UPDATE PRODUCTO SET stock = $nuevo_stock WHERE id_producto = $id_producto");

                    $mensaje = "Compra registrada con éxito. Total a pagar: $" . number_format($total, 0, ',', '.') . " CLP.";
                    $tipo_alerta = 'exito';
                } else {
                    $mensaje = "Error al registrar la compra: " . $conexion->error;
                    $tipo_alerta = 'error';
                }
            }
        } else {
            $mensaje = 'Error: El producto seleccionado no existe.';
            $tipo_alerta = 'error';
        }
    }
}

// Cargar listas de clientes y productos disponibles para el formulario
$clientes_lista = $conexion->query("SELECT id_cliente, nombre FROM CLIENTE ORDER BY nombre ASC");
$productos_lista = $conexion->query("SELECT id_producto, nombre, precio, stock FROM PRODUCTO WHERE stock > 0 ORDER BY nombre ASC");

// Consulta simple para mostrar todas las compras registradas
$sql_compras = "SELECT c.id_compra, c.cantidad, c.total, c.fecha, c.id_producto, c.id_cliente,
                       p.nombre AS nombre_producto, p.precio AS precio_unitario,
                       cli.nombre AS nombre_cliente
                FROM COMPRA c
                INNER JOIN PRODUCTO p ON c.id_producto = p.id_producto
                INNER JOIN CLIENTE cli ON c.id_cliente = cli.id_cliente
                ORDER BY c.id_compra ASC";
$resultado_compras = $conexion->query($sql_compras);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Registro de Compras</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header class="encabezado">
        <div class="contenedor-cabecera">
            <div class="logotipo">
                <h1>Tienda <span>TechStore</span></h1>
            </div>
            <nav class="navegacion-principal">
                <a href="index.php" class="enlace-nav">Catalogo</a>
                <a href="productos.php" class="enlace-nav">Productos</a>
                <a href="clientes.php" class="enlace-nav">Clientes</a>
                <a href="compras.php" class="enlace-nav activo">Compras</a>
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

        <!-- Formulario de Registro de Compra -->
        <section class="panel-formulario">
            <h2 class="titulo-seccion">Registrar Operacion de Compra</h2>
            <form action="compras.php" method="POST" class="formulario-registro">
                <div class="fila-campos">
                    <div class="grupo-campo">
                        <label for="id_cliente">Seleccionar Cliente:</label>
                        <select id="id_cliente" name="id_cliente" required>
                            <option value="">-- Seleccionar Cliente --</option>
                            <?php if ($clientes_lista && $clientes_lista->num_rows > 0): ?>
                                <?php while ($cli = $clientes_lista->fetch_assoc()): ?>
                                    <option value="<?php echo $cli['id_cliente']; ?>">
                                        #<?php echo $cli['id_cliente']; ?> - <?php echo htmlspecialchars($cli['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="grupo-campo">
                        <label for="id_producto">Seleccionar Producto:</label>
                        <select id="id_producto" name="id_producto" required>
                            <option value="">-- Seleccionar Producto --</option>
                            <?php if ($productos_lista && $productos_lista->num_rows > 0): ?>
                                <?php while ($prod = $productos_lista->fetch_assoc()): ?>
                                    <option value="<?php echo $prod['id_producto']; ?>">
                                        <?php echo htmlspecialchars($prod['nombre']); ?> ($<?php echo number_format($prod['precio'], 0, ',', '.'); ?> CLP - Disp: <?php echo $prod['stock']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="fila-campos">
                    <div class="grupo-campo">
                        <label for="cantidad">Cantidad de Unidades:</label>
                        <input type="number" id="cantidad" name="cantidad" min="1" value="1" required>
                    </div>

                    <div class="grupo-campo">
                        <label for="fecha">Fecha de Compra:</label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton-primario">Registrar Compra</button>
                    <button type="reset" class="boton-secundario">Limpiar</button>
                </div>
            </form>
        </section>

        <!-- Tabla de Compras Registradas (Consulta simple) -->
        <section class="panel-tabla">
            <h2 class="titulo-seccion">Historial de Operaciones de Compra (Tabla COMPRA)</h2>
            <div class="tabla-responsiva">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID Compra</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Precio Unit.</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_compras && $resultado_compras->num_rows > 0): ?>
                            <?php while ($fila = $resultado_compras->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $fila['id_compra']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($fila['fecha'])); ?></td>
                                    <td><strong><?php echo htmlspecialchars($fila['nombre_cliente']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($fila['nombre_producto']); ?></td>
                                    <td>$<?php echo number_format($fila['precio_unitario'], 0, ',', '.'); ?> CLP</td>
                                    <td><?php echo $fila['cantidad']; ?></td>
                                    <td><strong>$<?php echo number_format($fila['total'], 0, ',', '.'); ?> CLP</strong></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="celda-vacia">No hay operaciones de compra registradas en la base de datos.</td>
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
