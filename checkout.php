<?php
require_once 'db/db.php';


include 'includes/header.php';

// if (!isset($_SESSION['usuario'])) {
// header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode(BASE_URL . '/checkout.php'));
// exit;
// } // Podrías requerir login para el checkout
?>

<main class="container">
    <div class="page-card">
        <h2>Procesar Pago</h2>

        <div id="resumen-carrito-checkout">
            <h3>Resumen de tu Pedido</h3>
            <table id="tabla-checkout-carrito">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right; font-weight:bold;">Total:</td>
                        <td id="total-checkout-carrito" style="font-weight:bold;">$0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <hr class="separator-line">

        <div id="formulario-pago" class="form-autenticacion" style="margin-top:20px;">
             <h3>Información de Envío y Pago</h3>
            <p style="text-align:center; margin-bottom:20px;"><em>Integración con pasarela de pagos y formulario de envío en construcción.</em></p>
            <form id="formCheckout">
                <div class="form-grupo">
                    <label for="nombreCompleto">Nombre Completo:</label>
                    <input type="text" id="nombreCompleto" name="nombreCompleto" class="form-control" required>
                    <span class="error-js-mensaje"></span>
                </div>
                <div class="form-grupo">
                    <label for="direccionEnvio">Dirección de Envío:</label>
                    <input type="text" id="direccionEnvio" name="direccionEnvio" class="form-control" required>
                    <span class="error-js-mensaje"></span>
                </div>
                <div class="form-grupo">
                    <label for="ciudad">Ciudad:</label>
                    <input type="text" id="ciudad" name="ciudad" class="form-control" required>
                    <span class="error-js-mensaje"></span>
                </div>
                <div class="form-grupo">
                    <label for="codigoPostal">Código Postal:</label>
                    <input type="text" id="codigoPostal" name="codigoPostal" class="form-control" required>
                    <span class="error-js-mensaje"></span>
                </div>
                
                <h4 style="margin-top:30px; margin-bottom:15px; color:#a972ff;">Detalles del Pago (Simulado)</h4>
                <div class="form-grupo">
                    <label for="numeroTarjeta">Número de Tarjeta:</label>
                    <input type="text" id="numeroTarjeta" name="numeroTarjeta" class="form-control" placeholder="XXXX XXXX XXXX XXXX" required>
                     <span class="error-js-mensaje"></span>
                </div>
                <div style="display:flex; gap:15px;">
                    <div class="form-grupo" style="flex:1;">
                        <label for="fechaExpiracion">Fecha Exp. (MM/AA):</label>
                        <input type="text" id="fechaExpiracion" name="fechaExpiracion" class="form-control" placeholder="MM/AA" required>
                        <span class="error-js-mensaje"></span>
                    </div>
                    <div class="form-grupo" style="flex:1;">
                        <label for="cvc">CVC:</label>
                        <input type="text" id="cvc" name="cvc" class="form-control" placeholder="XXX" required>
                        <span class="error-js-mensaje"></span>
                    </div>
                </div>
                <button type="submit" class="btn-3 btn-1" style="width:100%; margin-top:10px;">Realizar Pedido (Simulado)</button>
            </form>
        </div>
        <a href="<?= BASE_URL ?>/index.php" class="btn-3 btn-cancelar" style="margin-top:20px; display:block; text-align:center;">Seguir Comprando</a>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cuerpoTablaCheckout = document.querySelector('#tabla-checkout-carrito tbody');
    const totalCheckoutElemento = document.getElementById('total-checkout-carrito');
    const formCheckout = document.getElementById('formCheckout');
    const formPago = document.getElementById('formulario-pago');

    const carritoGuardado = localStorage.getItem('miTiendaCarrito');
    let carrito = carritoGuardado ? JSON.parse(carritoGuardado) : [];

    function renderCarritoCheckout() {
        if (!cuerpoTablaCheckout || !totalCheckoutElemento) return;

        if (carrito.length === 0) {
            cuerpoTablaCheckout.innerHTML = '<tr><td colspan="4" style="text-align:center;">No hay productos para procesar.</td></tr>';
            totalCheckoutElemento.textContent = '$0.00';
            if (formPago) formPago.style.display = 'none';
            return;
        }

        let total = 0;
        cuerpoTablaCheckout.innerHTML = ''; // limpia
        carrito.forEach(prod => {
            const subtotal = prod.precio * prod.cantidad;
            total += subtotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${prod.titulo}</td>
                <td style="text-align:center;">${prod.cantidad}</td>
                <td style="text-align:right;">$${prod.precio.toFixed(2)}</td>
                <td style="text-align:right;">$${subtotal.toFixed(2)}</td>
            `;
            cuerpoTablaCheckout.appendChild(row);
        });

        totalCheckoutElemento.textContent = `$${total.toFixed(2)}`;
        if (formPago) formPago.style.display = 'block';
    }

    renderCarritoCheckout();

    // VALIDACIÓN + ENVÍO DE PEDIDO REAL
    if (formCheckout) {
        formCheckout.addEventListener('submit', async function (e) {
            e.preventDefault();

            const nombre = document.getElementById('nombreCompleto').value.trim();
            const direccion = document.getElementById('direccionEnvio').value.trim();
            const ciudad = document.getElementById('ciudad').value.trim();
            const codigoPostal = document.getElementById('codigoPostal').value.trim();
            const numeroTarjeta = document.getElementById('numeroTarjeta').value.trim();
            const fechaExpiracion = document.getElementById('fechaExpiracion').value.trim();
            const cvc = document.getElementById('cvc').value.trim();

            // Validación mínima
            if (!nombre || !direccion || !ciudad || !codigoPostal || !numeroTarjeta || !fechaExpiracion || !cvc || carrito.length === 0) {
                alert('Por favor, completá todos los campos y asegurate de tener productos en el carrito.');
                return;
            }

            const datos = {
                nombreCompleto: nombre,
                direccionEnvio: direccion,
                ciudad,
                codigoPostal,
                carrito
            };

            try {
                const res = await fetch('procesar_pedido.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datos)
                });

                const result = await res.json();

                if (result.status === 'ok') {
                    alert('🎉 Pedido realizado con éxito. ID: #' + result.pedido_id);
                    localStorage.removeItem('miTiendaCarrito');
                    window.location.href = '<?= BASE_URL ?>/pedidos.php?status=exito';
                } else {
                    alert('❌ Error: ' + (result.msg || 'Hubo un problema al procesar tu pedido.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('⚠️ Error al conectar con el servidor. Intenta de nuevo.');
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>