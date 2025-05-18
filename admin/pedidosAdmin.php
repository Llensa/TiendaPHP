<?php
require_once '../db/db.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$stmt = $pdo->query("
    SELECT
        p.id,
        p.usuario_id,
        u.nombre AS nombre_usuario,
        p.nombre_completo,
        p.total,
        p.fecha_pedido,
        p.estado
    FROM pedidos p
    JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.fecha_pedido DESC
");

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container">
    <h2>📦 Gestión de Pedidos</h2>

    <?php if (empty($pedidos)): ?>
        <p class="mensaje info">No hay pedidos registrados.</p>
    <?php else: ?>
        <div class="pedidos-admin-grid">
            <?php foreach ($pedidos as $pedido): ?>
                <div class="pedido-card">
                    <h3>Pedido #<?= htmlspecialchars($pedido['id']) ?></h3>
                    <p>👤 <strong>Usuario:</strong> <?= htmlspecialchars($pedido['nombre_usuario']) ?></p>
                    <p>🚚 <strong>Envío a:</strong> <?= htmlspecialchars($pedido['nombre_completo']) ?></p>
                    <p>💲 <strong>Total:</strong> $<?= number_format($pedido['total'], 2) ?></p>
                    <p>🏷️ <strong>Estado:</strong> <?= htmlspecialchars($pedido['estado']) ?></p>
                    <p class="pedido-fecha">📅 <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></p>
                    <a href="<?= BASE_URL ?>/admin/detalle_pedido.php?id=<?= $pedido['id'] ?>" class="btn-3 btn-sm">Ver Detalles</a>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
