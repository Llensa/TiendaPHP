<?php
require_once 'db/db.php';
require_once 'helpers/Csrf.php';
include 'includes/header.php';

$direcciones = [];

if (isset($_SESSION['usuario'])) {
  $stmt = $pdo->prepare("
    SELECT 
        id, 
        CONCAT_WS(' ', calle, numero, IFNULL(apartamento, '')) AS direccion, 
        ciudad, 
        codigo_postal 
    FROM direcciones 
    WHERE usuario_id = ?");

    $stmt->execute([$_SESSION['usuario']]);
    $direcciones = $stmt->fetchAll();
}
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
                <tbody></tbody>
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

            <?php if (!empty($direcciones)): ?>
            <div class="form-grupo">
                <label for="direccionGuardada">Seleccionar dirección guardada:</label>
                <select id="direccionGuardada" class="form-control">
                    <option value="">-- Seleccioná una dirección --</option>
                    <?php foreach ($direcciones as $dir): ?>
                        <option value="<?= htmlspecialchars($dir['direccion']) ?>|<?= htmlspecialchars($dir['ciudad']) ?>|<?= htmlspecialchars($dir['codigo_postal']) ?>">
                            <?= htmlspecialchars($dir['direccion']) ?> - <?= htmlspecialchars($dir['ciudad']) ?> (CP: <?= htmlspecialchars($dir['codigo_postal']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <form id="formCheckout" method="POST" action="#">
                <?= Csrf::inputField() ?>
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
                        <label for="mesExp">Mes (MM):</label>
                        <input type="text" id="mesExp" name="mesExp" maxlength="2" class="form-control" placeholder="MM" required>
                        <span class="error-js-mensaje"></span>
                    </div>
                    <div class="form-grupo" style="flex:1;">
                        <label for="anioExp">Año (AAAA):</label>
                        <input type="text" id="anioExp" name="anioExp" maxlength="4" class="form-control" placeholder="AAAA" required>
                        <span class="error-js-mensaje"></span>
                    </div>
                </div>

                <div class="form-grupo" style="margin-top:15px;">
                    <label for="cvc">CVC:</label>
                    <input type="text" id="cvc" name="cvc" class="form-control" placeholder="XXX" required>
                    <span class="error-js-mensaje"></span>
                </div>

                <button type="submit" class="btn-3 btn-1" style="width:100%; margin-top:20px;">Realizar Pedido (Simulado)</button>
            </form>
        </div>

        <a href="<?= BASE_URL ?>/index.php" class="btn-3 btn-cancelar" style="margin-top:20px; display:block; text-align:center;">Seguir Comprando</a>
    </div>
</main>

<script>
    const BASE_URL = "<?= BASE_URL ?>";

    document.addEventListener('DOMContentLoaded', () => {
        const direccionGuardada = document.getElementById('direccionGuardada');
        if (direccionGuardada) {
            direccionGuardada.addEventListener('change', () => {
                const valor = direccionGuardada.value;
                if (valor) {
                    const [dir, ciudad, cp] = valor.split('|');
                    document.getElementById('direccionEnvio').value = dir;
                    document.getElementById('ciudad').value = ciudad;
                    document.getElementById('codigoPostal').value = cp;
                }
            });
        }
    });
</script>
<script src="<?= BASE_URL ?>/assets/js/checkout.js"></script>
<?php include 'includes/footer.php'; ?>
