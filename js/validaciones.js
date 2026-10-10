// Validaciones en JavaScript para el proyecto TechStore
// Modulo: validaciones.js (Actualizado Semana 8 - Colaboracion GitHub)

// Validacion del formulario de productos
function validarProducto() {
    let nombre = document.getElementById('nombre_producto').value.trim();
    let descripcion = document.getElementById('descripcion_producto').value.trim();
    let precio = document.getElementById('precio_producto').value.trim();
    let stock = document.getElementById('stock_producto').value.trim();

    if (nombre === '' || descripcion === '' || precio === '' || stock === '') {
        alert('Por favor completa todos los campos del producto.');
        return false;
    }

    if (isNaN(precio) || Number(precio) <= 0) {
        alert('El precio debe ser un numero positivo.');
        return false;
    }

    if (isNaN(stock) || Number(stock) < 0) {
        alert('El stock debe ser un numero mayor o igual a cero.');
        return false;
    }

    return true;
}

// Validacion del formulario de clientes
function validarCliente() {
    let nombre = document.getElementById('nombre_cliente').value.trim();
    let email = document.getElementById('email_cliente').value.trim();
    let direccion = document.getElementById('direccion_cliente').value.trim();

    if (nombre === '' || email === '' || direccion === '') {
        alert('Por favor completa todos los campos del cliente.');
        return false;
    }

    let patronCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!patronCorreo.test(email)) {
        alert('Por favor ingresa un correo electronico valido.');
        return false;
    }

    return true;
}

// Validacion y calculo dinamico para la gestion de pedidos (Semana 8)
function calcularTotalPedido() {
    let selectorProducto = document.getElementById('id_producto');
    let campoCantidad = document.getElementById('cantidad_pedido');
    let campoTotal = document.getElementById('total_estimado');

    if (!selectorProducto || !campoCantidad || !campoTotal) return;

    let opcionSeleccionada = selectorProducto.options[selectorProducto.selectedIndex];
    let precio = Number(opcionSeleccionada.getAttribute('data-precio')) || 0;
    let cantidad = Number(campoCantidad.value) || 0;

    let total = precio * cantidad;
    campoTotal.value = '$' + total.toLocaleString('es-CL') + ' CLP';
}

function validarPedido() {
    let cliente = document.getElementById('id_cliente').value;
    let selectorProducto = document.getElementById('id_producto');
    let cantidad = Number(document.getElementById('cantidad_pedido').value);

    if (cliente === '') {
        alert('Por favor selecciona un cliente registrado.');
        return false;
    }

    if (selectorProducto.value === '') {
        alert('Por favor selecciona un producto del catalogo.');
        return false;
    }

    let opcionSeleccionada = selectorProducto.options[selectorProducto.selectedIndex];
    let stockDisponible = Number(opcionSeleccionada.getAttribute('data-stock')) || 0;

    if (cantidad <= 0) {
        alert('La cantidad de unidades debe ser al menos 1.');
        return false;
    }

    if (cantidad > stockDisponible) {
        alert('La cantidad solicitada (' + cantidad + ') supera el stock disponible (' + stockDisponible + ').');
        return false;
    }

    return true;
}
