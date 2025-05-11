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
// Script para cargar el carrito en el checkout
document.addEventListener('DOMContentLoaded', function() {
    const cuerpoTablaCheckout = document.querySelector('#tabla-checkout-carrito tbody');
    const totalCheckoutElemento = document.getElementById('total-checkout-carrito');
    const carritoGuardado = localStorage.getItem('miTiendaCarrito');
    let carritoCheckout = [];

    if (carritoGuardado) {
        carritoCheckout = JSON.parse(carritoGuardado);
    }

    if (cuerpoTablaCheckout && totalCheckoutElemento) {
        if (carritoCheckout.length === 0) {
            cuerpoTablaCheckout.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px 0;">No hay productos para procesar.</td></tr>';
            totalCheckoutElemento.textContent = '$0.00';
            // Ocultar formulario de pago si el carrito está vacío
             const formPago = document.getElementById('formulario-pago');
             if(formPago) formPago.style.display = 'none';
        } else {
            let totalGeneralCheckout = 0;
            carritoCheckout.forEach(prod => {
                const row = document.createElement('tr');
                const subtotalItem = prod.precio * prod.cantidad;
                totalGeneralCheckout += subtotalItem;
                row.innerHTML = `
                    <td>${prod.titulo}</td>
                    <td style="text-align:center;">${prod.cantidad}</td>
                    <td style="text-align:right;">$${prod.precio.toFixed(2)}</td>
                    <td style="text-align:right;">$${subtotalItem.toFixed(2)}</td>
                `;
                cuerpoTablaCheckout.appendChild(row);
            });
            totalCheckoutElemento.textContent = `$${totalGeneralCheckout.toFixed(2)}`;
        }
    }

    const formCheckoutSubmit = document.getElementById('formCheckout');
    if(formCheckoutSubmit){
        formCheckoutSubmit.addEventListener('submit', function(e){
            e.preventDefault();
            // Aquí iría la validación de este formulario con validaciones.js adaptado o nueva lógica
            // Y luego el envío de datos al backend para procesar el pedido
            alert('Pedido simulado realizado! Gracias por tu compra. (Esta es una demo, no se procesó ningún pago real).');
            // Limpiar carrito después de "comprar"
            localStorage.removeItem('miTiendaCarrito');
            // Redirigir a una página de agradecimiento o al perfil
            window.location.href = '<?= BASE_URL ?>/pedidos.php?status=exito';
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>