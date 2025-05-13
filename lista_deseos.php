<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.id, p.nombre, p.precio, p.imagen 
    FROM deseos d 
    JOIN productos p ON d.producto_id = p.id 
    WHERE d.usuario_id = ?
");
$stmt->execute([$_SESSION['usuario']]);
$deseos = $stmt->fetchAll();
?>

<main class="container">
  <div class="page-card">
    <h2>Mi Lista de Deseos</h2>
    <?php if (empty($deseos)): ?>
        <p>No hay productos en tu lista de deseos aún.</p>
    <?php else: ?>
        <ul class="wishlist-items">
            <?php foreach ($deseos as $p): ?>
                <li>
                    <strong><?= htmlspecialchars($p['nombre']) ?></strong> — $<?= number_format($p['precio'], 2, ',', '.') ?>
                    <a href="<?= BASE_URL ?>/producto.php?id=<?= $p['id'] ?>" class="btn-3 btn-sm" style="float:right;">Ver producto</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/perfil.php" class="btn-3" style="margin-top:20px;">Volver al perfil</a>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
