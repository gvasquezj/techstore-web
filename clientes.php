<?php
require_once 'conexion.php';

$mensaje = '';
$tipo_alerta = '';

// Procesar envio del formulario de clientes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']);
    $email     = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);

    // Validacion en servidor
    if ($nombre === '' || $email === '' || $direccion === '') {
        $mensaje = 'Error: Todos los campos son obligatorios.';
        $tipo_alerta = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Error: El formato de correo electronico no es valido.';
        $tipo_alerta = 'error';
    } else {
        // Inserción en la tabla CLIENTE
        $sql = "INSERT INTO CLIENTE (nombre, email, direccion) VALUES ('$nombre', '$email', '$direccion')";
        if ($conexion->query($sql) === TRUE) {
            $mensaje = "Cliente '$nombre' registrado exitosamente en la base de datos.";
            $tipo_alerta = 'exito';
        } else {
            $mensaje = "Error al insertar en la base de datos: " . $conexion->error;
            $tipo_alerta = 'error';
        }
    }
}

// Consulta simple para visualizar todos los clientes registrados
$sql_select = "SELECT id_cliente, nombre, email, direccion FROM CLIENTE ORDER BY id_cliente ASC";
$resultado = $conexion->query($sql_select);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Gestion de Clientes</title>
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
                <a href="productos.php" class="enlace-nav">Productos</a>
                <a href="clientes.php" class="enlace-nav activo">Clientes</a>
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

        <!-- Formulario de Registro de Cliente -->
        <section class="panel-formulario">
            <h2 class="titulo-seccion">Registrar Nuevo Cliente</h2>
            <form action="clientes.php" method="POST" onsubmit="return validarCliente();" class="formulario-registro">
                <div class="grupo-campo">
                    <label for="nombre_cliente">Nombre Completo:</label>
                    <input type="text" id="nombre_cliente" name="nombre" placeholder="Ej: Carlos Mendoza" required>
                </div>

                <div class="grupo-campo">
                    <label for="email_cliente">Correo Electronico:</label>
                    <input type="email" id="email_cliente" name="email" placeholder="Ej: carlos.mendoza@email.com" required>
                </div>

                <div class="grupo-campo">
                    <label for="direccion_cliente">Direccion de Envio:</label>
                    <input type="text" id="direccion_cliente" name="direccion" placeholder="Ej: Av. Providencia 1245, Depto 502" required>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton-primario">Guardar Cliente</button>
                    <button type="reset" class="boton-secundario">Limpiar</button>
                </div>
            </form>
        </section>

        <!-- Tabla de Clientes Registrados (Consulta simple) -->
        <section class="panel-tabla">
            <h2 class="titulo-seccion">Clientes en Base de Datos (Tabla CLIENTE)</h2>
            <div class="tabla-responsiva">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Correo Electronico</th>
                            <th>Direccion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $fila['id_cliente']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($fila['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($fila['email']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['direccion']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="celda-vacia">No hay clientes registrados en la base de datos.</td>
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
