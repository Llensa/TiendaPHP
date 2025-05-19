<?php
require_once 'db/db.php';
require_once 'helpers/Csrf.php';
include 'includes/header.php';

if (session_status() == PHP_SESSION_NONE) session_start();

// Si el usuario no está logueado, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/auth/login.php?redirect=" . urlencode(BASE_URL . "/checkout.php"));
    exit;
}

$nombreCompleto = $_SESSION['nombre_usuario'] ?? '';
$direcciones = [];

try {
    $stmt = $pdo->prepare("SELECT id, calle, numero, apartamento, ciudad, provincia, codigo_postal, pais FROM direcciones WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener direcciones: " . $e->getMessage());
}

// BASE_URL fallback
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$base = "$protocol://$host$path";
if (!defined('BASE_URL')) define('BASE_URL', $base);
?>

<main class="container">
    <div class="page-card">
        <h2>Procesar Pago</h2>

        <div id="resumen-carrito-checkout">
            <h3>Resumen de tu Pedido</h3>
            <div class="checkout-item-header">
                <div class="checkout-col-product">Producto</div>
                <div class="checkout-col-quantity">Cantidad</div>
                <div class="checkout-col-price">Precio Unit.</div>
                <div class="checkout-col-subtotal">Subtotal</div>
            </div>
            <div id="checkout-productos"></div>
            <div class="checkout-total">
                <strong>Total:</strong>
                <span id="total-checkout-carrito">$0.00</span>
            </div>
        </div>

        <hr class="separator-line">

        <div id="formulario-pago" class="form-autenticacion" style="margin-top: 20px;">
            <h3>Información de Envío y Pago</h3>

            <?php if (!empty($direcciones)): ?>
                <div class="form-grupo">
                    <label for="direccionGuardada">Seleccionar dirección guardada:</label>
                    <select id="direccionGuardada" class="form-control">
                        <option value="">-- Seleccioná una dirección --</option>
                        <?php foreach ($direcciones as $dir): 
                            $direccionTexto = $dir['calle'] . ' ' . $dir['numero'];
                            if (!empty($dir['apartamento'])) $direccionTexto .= ', ' . $dir['apartamento'];
                            $valueOption = implode('|', [
                                htmlspecialchars($direccionTexto, ENT_QUOTES, 'UTF-8'),
                                htmlspecialchars($dir['ciudad'], ENT_QUOTES, 'UTF-8'),
                                htmlspecialchars($dir['codigo_postal'], ENT_QUOTES, 'UTF-8')
                            ]);
                        ?>
                        <option value="<?= $valueOption ?>">
                            <?= htmlspecialchars($direccionTexto) ?> - <?= htmlspecialchars($dir['ciudad']) ?> (CP: <?= htmlspecialchars($dir['codigo_postal']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <form id="formCheckout" method="POST" action="#"> 
                <?= Csrf::inputField() ?>
                <div class="form-grupo">
                    <label for="nombreCompleto">Nombre Completo:</label>
                    <input type="text" id="nombreCompleto" name="nombreCompleto" class="form-control" required value="<?= htmlspecialchars($nombreCompleto) ?>">
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

                <h4 style="margin-top: 30px; margin-bottom: 15px; color: #a972ff;">Detalles del Pago (Simulado)</h4>

                <div class="form-grupo">
                    <label for="numeroTarjeta">Número de Tarjeta:</label>
                    <input type="text" id="numeroTarjeta" name="numeroTarjeta" class="form-control" placeholder="XXXX XXXX XXXX XXXX" required maxlength="19">
                    <span class="error-js-mensaje"></span>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-grupo" style="flex: 1;">
                        <label for="mesExp">Mes (MM):</label>
                        <input type="text" id="mesExp" name="mesExp" maxlength="2" class="form-control" placeholder="MM" required>
                        <span class="error-js-mensaje"></span>
                    </div>
                    <div class="form-grupo" style="flex: 1;">
                        <label for="anioExp">Año (AAAA):</label>
                        <input type="text" id="anioExp" name="anioExp" maxlength="4" class="form-control" placeholder="AAAA" required>
                        <span class="error-js-mensaje"></span>
                    </div>
                </div>

                <div class="form-grupo" style="margin-top: 15px;">
                    <label for="cvc">CVC:</label>
                    <input type="text" id="cvc" name="cvc" class="form-control" placeholder="XXX" required maxlength="4">
                    <span class="error-js-mensaje"></span>
                </div>

                <button type="submit" class="btn-3 btn-1" style="width: 100%; margin-top: 20px;">
                    Realizar Pedido (Simulado)
                </button>
            </form>
        </div>

        <a href="<?= BASE_URL ?>/index.php" class="btn-3 btn-cancelar" style="margin-top: 20px; display: block; text-align: center;">
            Seguir Comprando
        </a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
  const BASE_URL = "<?= htmlspecialchars(BASE_URL) ?>";
</script>
<script src="<?= BASE_URL ?>/assets/js/checkout.js"></script>
