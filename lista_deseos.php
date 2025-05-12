<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.* FROM lista_deseos ld
    JOIN productos p ON ld.producto_id = p.id
    WHERE ld.usuario_id = ?
");
$stmt->execute([$_SESSION['usuario']]);
$deseos = $stmt->fetchAll();
?>

<main class="container">
  <div class="page-card">
    <h2>Mi Lista de Deseos</h2>
    <?php if (empty($deseos)): ?>
        <p>No tienes productos en tu lista de deseos.</p>
    <?php else: ?>
        <div class="product-grid">
          <?php foreach ($deseos as $prod): ?>
            <div class="product">
              <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= $prod['nombre'] ?>">
              <div class="product-txt">
                <h3><?= $prod['nombre'] ?></h3>
                <p class="precio">$<?= number_format($prod['precio'], 2, ',', '.') ?></p>
                <button class="btn-3 agregar-carrito"
                  data-id="<?= $prod['id'] ?>"
                  data-nombre="<?= $prod['nombre'] ?>"
                  data-precio="<?= $prod['precio'] ?>"
                  data-imagen="<?= BASE_URL ?>/assets/images/<?= $prod['imagen'] ?>">
                  Agregar al carrito
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
    <?php endif; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
