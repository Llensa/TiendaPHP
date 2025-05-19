<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.id, p.nombre, p.precio, p.imagen 
    FROM lista_deseos d 
    JOIN productos p ON d.producto_id = p.id 
    WHERE d.usuario_id = ?
");
$stmt->execute([$_SESSION['usuario_id']]);
$deseos = $stmt->fetchAll();
?>

<main class="container">
  <div class="page-card">
    <h2>Mi Lista de Deseos</h2>

    <?php if (empty($deseos)): ?>
        <div class="placeholder-content">
            <p><em>No tenés productos en tu lista de deseos aún.</em></p>
        </div>
    <?php else: ?>
        <div class="grid wishlist-grid">
            <?php foreach ($deseos as $p): ?>
                <div class="wishlist-item" data-id="<?= $p['id'] ?>">
                    <div class="wishlist-img">
                        <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                    </div>
                    <div class="wishlist-info">
                        <h3><?= htmlspecialchars($p['nombre']) ?></h3>
                        <p class="precio">$<?= number_format($p['precio'], 2, ',', '.') ?></p>
                        <a href="<?= BASE_URL ?>/producto.php?id=<?= $p['id'] ?>" class="btn-3 btn-sm">Ver producto</a>
                        <button class="btn-3 btn-sm btn-deseo btn-agregar-deseo" data-id="<?= $p['id'] ?>">💔 Quitar</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>/perfil.php" class="btn-3" style="margin-top:20px;">Volver al perfil</a>
  </div>
</main>

<script src="<?= BASE_URL ?>/assets/js/deseos.js"></script>
<?php include 'includes/footer.php'; ?>
