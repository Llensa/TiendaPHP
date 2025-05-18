<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode(BASE_URL . '/pedidos.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT id, creado_en, total, estado FROM pedidos WHERE usuario_id = ? ORDER BY creado_en DESC");
$stmt->execute([$_SESSION['usuario']]);
$pedidos = $stmt->fetchAll();
?>

<main class="container">
    <div class="page-card">
        <h2>Mis Pedidos</h2>
        <p>Aquí podrás ver el historial de tus pedidos.</p>

        <?php if (empty($pedidos)): ?>
            <div class="placeholder-content">
                <p><em>No tenés pedidos aún. ¡Empezá a comprar!</em></p>
            </div>
        <?php else: ?>
            <div class="order-grid">
                <?php foreach ($pedidos as $pedido): ?>
                    <?php
                    $stmtDetalle = $pdo->prepare("
                      SELECT dp.producto_id, dp.cantidad, dp.precio_unitario, p.nombre, p.imagen 
                      FROM detalles_pedido dp 
                      JOIN productos p ON p.id = dp.producto_id 
                      WHERE dp.pedido_id = ?
                    ");
                    $stmtDetalle->execute([$pedido['id']]);
                    $detalles = $stmtDetalle->fetchAll();
                    $resumenRapido = array_slice($detalles, 0, 2);
                    ?>
                    <div class="pedido-item">
                        <div class="order-header toggle-detalle" data-target="detalle-<?= $pedido['id'] ?>" role="button" tabindex="0">
                            <strong>Pedido #<?= $pedido['id'] ?></strong>
                            <span class="fecha-pedido"><?= date('d/m/Y H:i', strtotime($pedido['creado_en'])) ?></span>
                        </div>
                        <div style="display: flex; gap: 10px; margin: 10px 0;">
                            <?php foreach ($resumenRapido as $res): ?>
                                <div style="text-align:center;">
                                    <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($res['imagen']) ?>" alt="<?= htmlspecialchars($res['nombre']) ?>" class="mini-img">
                                    <small>x<?= $res['cantidad'] ?></small>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($detalles) > 2): ?>
                                <div style="display:flex; align-items:center;">+<?= count($detalles) - 2 ?> más</div>
                            <?php endif; ?>
                        </div>
                        <p class="order-total">Total: $<?= number_format($pedido['total'], 2, ',', '.') ?></p>
                        <p class="order-estado">Estado: <strong><?= ucfirst($pedido['estado']) ?></strong></p>

                        <div class="detalle-pedido" id="detalle-<?= $pedido['id'] ?>" style="display:none;">
                            <table class="detalle-tabla">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Imagen</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detalles as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['nombre']) ?></td>
                                            <td>
                                                <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" class="mini-img">
                                            </td>
                                            <td><?= $item['cantidad'] ?></td>
                                            <td>$<?= number_format($item['precio_unitario'], 2, ',', '.') ?></td>
                                            <td>$<?= number_format($item['cantidad'] * $item['precio_unitario'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/perfil.php" class="btn-3" style="margin-top:20px;">Volver al Perfil</a>
        <a href="<?= BASE_URL ?>/checkout.php" class="btn-3">Hacer otro pedido</a>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-detalle').forEach(el => {
        el.addEventListener('click', () => {
            const targetId = el.dataset.target;
            const detalle = document.getElementById(targetId);
            if (detalle) {
                detalle.style.display = (detalle.style.display === 'none') ? 'block' : 'none';
            }
        });

        el.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') el.click();
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>