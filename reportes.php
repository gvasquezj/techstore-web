<?php
require_once 'conexion.php';

// Consulta SQL Avanzada: Contar el numero de compras por cliente y filtrar aquellos con mas de dos compras registradas
$sql_avanzada = "SELECT c.id_cliente, 
                        c.nombre, 
                        c.email, 
                        c.direccion, 
                        COUNT(co.id_compra) AS total_compras, 
                        SUM(co.total) AS monto_acumulado
                 FROM CLIENTE c
                 INNER JOIN COMPRA co ON c.id_cliente = co.id_cliente
                 GROUP BY c.id_cliente, c.nombre, c.email, c.direccion
                 HAVING COUNT(co.id_compra) > 2
                 ORDER BY total_compras DESC, monto_acumulado DESC";

$resultado_avanzado = $conexion->query($sql_avanzada);

// Consulta SQL Compleja de Disponibilidad de Productos (Stock y Demanda de Clientes)
$sql_disponibilidad = "SELECT p.id_producto,
                              p.nombre,
                              p.precio,
                              p.stock,
                              IFNULL(SUM(co.cantidad), 0) AS unidades_vendidas,
                              COUNT(co.id_compra) AS veces_comprado
                       FROM PRODUCTO p
                       LEFT JOIN COMPRA co ON p.id_producto = co.id_producto
                       GROUP BY p.id_producto, p.nombre, p.precio, p.stock
                       ORDER BY p.stock ASC";

$resultado_disponibilidad = $conexion->query($sql_disponibilidad);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Reportes y Consultas Avanzadas</title>
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
                <a href="compras.php" class="enlace-nav">Compras</a>
                <a href="reportes.php" class="enlace-nav activo">Reportes</a>
            </nav>
        </div>
    </header>

    <main class="contenedor">

        <!-- Reporte 1: Clientes con Mas de Dos Compras Registradas -->
        <section class="panel-tabla">
            <div class="cabecera-panel-reporte">
                <h2 class="titulo-seccion">Clientes Frecuentes con Mas de Dos Compras Registradas</h2>
                <p class="subtitulo-seccion">
                    Consulta avanzada en MySQL con union relacional (INNER JOIN), agrupamiento (GROUP BY) y condicion de agregacion (HAVING COUNT > 2).
                </p>
            </div>

            <div class="tabla-responsiva">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID Cliente</th>
                            <th>Nombre del Cliente</th>
                            <th>Email de Contacto</th>
                            <th>Direccion</th>
                            <th>Cantidad de Compras</th>
                            <th>Monto Total Acumulado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_avanzado && $resultado_avanzado->num_rows > 0): ?>
                            <?php while ($fila = $resultado_avanzado->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $fila['id_cliente']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($fila['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($fila['email']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['direccion']); ?></td>
                                    <td>
                                        <span class="insignia-destacada">
                                            <?php echo $fila['total_compras']; ?> compras
                                        </span>
                                    </td>
                                    <td>
                                        <strong>$<?php echo number_format($fila['monto_acumulado'], 0, ',', '.'); ?> CLP</strong>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="celda-vacia">No se encontraron clientes con mas de dos compras registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Reporte 2: Disponibilidad de Productos y Demanda de Clientes -->
        <section class="panel-tabla" style="margin-top: 30px;">
            <div class="cabecera-panel-reporte">
                <h2 class="titulo-seccion">Disponibilidad de Productos en Tienda (Stock y Demanda)</h2>
                <p class="subtitulo-seccion">
                    Analisis de inventario cruzando catálogo de productos y registros de compras (LEFT JOIN y funciones SUM/COUNT).
                </p>
            </div>

            <div class="tabla-responsiva">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID Producto</th>
                            <th>Nombre del Producto</th>
                            <th>Precio Unitario</th>
                            <th>Stock Disponible</th>
                            <th>Unidades Vendidas</th>
                            <th>Estado de Disponibilidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_disponibilidad && $resultado_disponibilidad->num_rows > 0): ?>
                            <?php while ($prod = $resultado_disponibilidad->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $prod['id_producto']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($prod['nombre']); ?></strong></td>
                                    <td>$<?php echo number_format($prod['precio'], 0, ',', '.'); ?> CLP</td>
                                    <td><?php echo $prod['stock']; ?> unidades</td>
                                    <td><?php echo $prod['unidades_vendidas']; ?> unidades</td>
                                    <td>
                                        <?php if ($prod['stock'] > 5): ?>
                                            <span class="estado-disponible">Disponible</span>
                                        <?php elseif ($prod['stock'] > 0): ?>
                                            <span class="estado-bajo">Ultimas unidades</span>
                                        <?php else: ?>
                                            <span class="estado-agotado">Agotado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="celda-vacia">No hay datos de productos disponibles.</td>
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
