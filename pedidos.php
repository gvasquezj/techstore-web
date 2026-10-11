<?php
// Script de creacion e insercion de datos
// Base de datos: TIENDA
// Proyecto: TechStore (Semana 8 - Colaboracion GitHub)

$servidor   = "localhost";
$usuario    = "root";
$clave      = "";
$basedatos  = "TIENDA";

// 1. Conectar al servidor MySQL
$conexion_temp = new mysqli($servidor, $usuario, $clave);
if ($conexion_temp->connect_error) {
    die("Error conectando al servidor MySQL: " . $conexion_temp->connect_error);
}

// 2. Crear base de datos si no existe
$conexion_temp->query("CREATE DATABASE IF NOT EXISTS TIENDA CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conexion_temp->close();

// 3. Conectar a la base de datos TIENDA
require_once 'conexion.php';

// Mensaje de estado
$mensaje = "";
$tipo_mensaje = "";

// 4. Procesar registro de nuevo pedido (Simulacion de compra / pasarela)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear_pedido') {
    $id_cliente  = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
    $id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
    $cantidad    = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
    $metodo_pago = isset($_POST['metodo_pago']) ? trim($_POST['metodo_pago']) : 'Webpay Plus';

    if ($id_cliente > 0 && $id_producto > 0 && $cantidad > 0) {
        // Consultar stock y precio actual del producto
        $consulta_prod = $conexion->prepare("SELECT precio, stock, nombre FROM PRODUCTO WHERE id_producto = ?");
        $consulta_prod->bind_param("i", $id_producto);
        $consulta_prod->execute();
        $resultado_prod = $consulta_prod->get_result();

        if ($resultado_prod && $fila_prod = $resultado_prod->fetch_assoc()) {
            if ($fila_prod['stock'] >= $cantidad) {
                $total_pedido = $fila_prod['precio'] * $cantidad;
                $fecha_actual = date('Y-m-d');

                // Insertar en la tabla COMPRA (registro oficial del pedido)
                $consulta_insert = $conexion->prepare("INSERT INTO COMPRA (cantidad, total, fecha, id_producto, id_cliente) VALUES (?, ?, ?, ?, ?)");
                $consulta_insert->bind_param("iisii", $cantidad, $total_pedido, $fecha_actual, $id_producto, $id_cliente);
                
                if ($consulta_insert->execute()) {
                    // Actualizar stock del producto
                    $nuevo_stock = $fila_prod['stock'] - $cantidad;
                    $actualizar_stock = $conexion->prepare("UPDATE PRODUCTO SET stock = ? WHERE id_producto = ?");
                    $actualizar_stock->bind_param("ii", $nuevo_stock, $id_producto);
                    $actualizar_stock->execute();

                    $mensaje = "Pedido procesado exitosamente por $" . number_format($total_pedido, 0, ',', '.') . " CLP mediante " . htmlspecialchars($metodo_pago) . ". Stock actualizado.";
                    $tipo_mensaje = "exito";
                } else {
                    $mensaje = "Error al registrar el pedido: " . $conexion->error;
                    $tipo_mensaje = "error";
                }
            } else {
                $mensaje = "Stock insuficiente para '" . htmlspecialchars($fila_prod['nombre']) . "'. Disponible: " . $fila_prod['stock'] . " un.";
                $tipo_mensaje = "error";
            }
        } else {
            $mensaje = "El producto seleccionado no existe.";
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "Por favor completa todos los campos del pedido correctamente.";
        $tipo_mensaje = "error";
    }
}

// Consultar clientes para el selector
$clientes_res = $conexion->query("SELECT id_cliente, nombre, email FROM CLIENTE ORDER BY nombre ASC");

// Consultar productos para el selector
$productos_res = $conexion->query("SELECT id_producto, nombre, precio, stock FROM PRODUCTO WHERE stock > 0 ORDER BY nombre ASC");

// Consultar lista de pedidos (COMPRA con JOIN a CLIENTE y PRODUCTO)
$sql_pedidos = "SELECT co.id_compra, co.fecha, co.cantidad, co.total, 
                       c.nombre AS nombre_cliente, c.email AS email_cliente,
                       p.nombre AS nombre_producto, p.precio AS precio_unitario
                FROM COMPRA co
                INNER JOIN CLIENTE c ON co.id_cliente = c.id_cliente
                INNER JOIN PRODUCTO p ON co.id_producto = p.id_producto
                ORDER BY co.id_compra DESC";
$resultado_pedidos = $conexion->query($sql_pedidos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Gestion de Pedidos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/validaciones.js" defer></script>
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
                <a href="pedidos.php" class="enlace-nav activo">Pedidos</a>
                <a href="compras.php" class="enlace-nav">Historial Compras</a>
                <a href="reportes.php" class="enlace-nav">Reportes</a>
            </nav>
        </div>
    </header>

    <main class="contenedor">

        <?php if (!empty($mensaje)): ?>
            <div class="<?php echo ($tipo_mensaje === 'exito') ? 'mensaje-exito' : 'mensaje-error'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Panel de Creacion de Nuevo Pedido -->
        <section class="panel-formulario">
            <h2>Crear Nuevo Pedido de Compra</h2>
            <p class="subtitulo-seccion">Modulo de integracion y gestion de pedidos en linea para clientes registrados.</p>

            <form action="pedidos.php" method="POST" class="formulario-registro" onsubmit="return validarPedido();">
                <input type="hidden" name="accion" value="crear_pedido">

                <div class="fila-campos">
                    <div class="grupo-campo">
                        <label for="id_cliente">Seleccionar Cliente Registrado:</label>
                        <select name="id_cliente" id="id_cliente" required>
                            <option value="">-- Selecciona un cliente --</option>
                            <?php if ($clientes_res && $clientes_res->num_rows > 0): ?>
                                <?php while ($cli = $clientes_res->fetch_assoc()): ?>
                                    <option value="<?php echo $cli['id_cliente']; ?>">
                                        #<?php echo $cli['id_cliente']; ?> - <?php echo htmlspecialchars($cli['nombre']); ?> (<?php echo htmlspecialchars($cli['email']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="grupo-campo">
                        <label for="id_producto">Seleccionar Producto:</label>
                        <select name="id_producto" id="id_producto" required onchange="calcularTotalPedido();">
                            <option value="" data-precio="0" data-stock="0">-- Selecciona un producto --</option>
                            <?php if ($productos_res && $productos_res->num_rows > 0): ?>
                                <?php while ($pro = $productos_res->fetch_assoc()): ?>
                                    <option value="<?php echo $pro['id_producto']; ?>" data-precio="<?php echo $pro['precio']; ?>" data-stock="<?php echo $pro['stock']; ?>">
                                        <?php echo htmlspecialchars($pro['nombre']); ?> - $<?php echo number_format($pro['precio'], 0, ',', '.'); ?> CLP (Stock: <?php echo $pro['stock']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="fila-campos">
                    <div class="grupo-campo">
                        <label for="cantidad_pedido">Cantidad de Unidades:</label>
                        <input type="number" id="cantidad_pedido" name="cantidad" min="1" value="1" required oninput="calcularTotalPedido();">
                    </div>

                    <div class="grupo-campo">
                        <label for="metodo_pago">Metodo de Pago (Pasarela Segura):</label>
                        <select name="metodo_pago" id="metodo_pago" required>
                            <option value="Webpay Plus (Transbank)">Webpay Plus (Debito / Credito)</option>
                            <option value="Transferencia Bancaria">Transferencia Electronica</option>
                            <option value="MercadoPago">MercadoPago Online</option>
                        </select>
                    </div>

                    <div class="grupo-campo">
                        <label>Total Estimado ($ CLP):</label>
                        <input type="text" id="total_estimado" value="$0 CLP" readonly style="background-color: #f1f5f9; font-weight: bold; color: #1e293b;">
                    </div>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton-primario">Confirmar y Procesar Pedido</button>
                    <button type="reset" class="boton-secundario">Limpiar Formulario</button>
                </div>
            </form>
        </section>

        <!-- Tabla de Gestion y Seguimiento de Pedidos -->
        <section class="panel-tabla">
            <h2>Listado General de Pedidos</h2>
            <p class="subtitulo-seccion">Seguimiento de compras procesadas, montos totales y estados de entrega.</p>

            <div class="tabla-responsiva">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>N° Pedido</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Producto Solicitado</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Monto Total</th>
                            <th>Estado Pedido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_pedidos && $resultado_pedidos->num_rows > 0): ?>
                            <?php while ($pedido = $resultado_pedidos->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $pedido['id_compra']; ?></strong></td>
                                    <td><?php echo date("d/m/Y", strtotime($pedido['fecha'])); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($pedido['nombre_cliente']); ?></strong><br>
                                        <small style="color: #64748b;"><?php echo htmlspecialchars($pedido['email_cliente']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($pedido['nombre_producto']); ?></td>
                                    <td><?php echo $pedido['cantidad']; ?> un.</td>
                                    <td>$<?php echo number_format($pedido['precio_unitario'], 0, ',', '.'); ?> CLP</td>
                                    <td><strong style="color: #0f172a;">$<?php echo number_format($pedido['total'], 0, ',', '.'); ?> CLP</strong></td>
                                    <td><span class="estado-disponible">Completado</span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="celda-vacia">No hay pedidos registrados en el sistema.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <footer class="pie-pagina">
        <p>TechStore Online &copy; 2026 - <span>Programacion Web II</span> - Modulo de Gestion de Pedidos Colaborativo</p>
    </footer>

</body>
</html>
<?php
$conexion->close();
?>
