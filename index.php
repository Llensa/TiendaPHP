<?php
require_once 'db/db.php';
include 'includes/header.php';

// Obtener los productos desde la base de datos
$stmt = $pdo->query("SELECT * FROM productos");
$productos = $stmt->fetchAll();
?>

<main class="container">
  <h2>Nuestros Productos</h2>
  <div class="grid">
    <?php foreach ($productos as $prod): ?>
      <div class="product">
        <img src="assets/images/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>">
        <div class="product-txt">
          <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
          <p><?= htmlspecialchars($prod['descripcion']) ?></p>
          <p class="precio">$<?= $prod['precio'] ?></p>
          <a href="producto.php?id=<?= $prod['id'] ?>" class="btn-3">Ver más</a>
          <a href="#" class="btn-3 agregar-carrito" data-id="<?= $prod['id'] ?>">Agregar al carrito</a>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
