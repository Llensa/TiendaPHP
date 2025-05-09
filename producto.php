<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<p class='container'>Producto no especificado.</p>";
    include 'includes/footer.php';
    exit;
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    echo "<p class='container'>Producto no encontrado.</p>";
    include 'includes/footer.php';
    exit;
}
?>

<main class="container">
  <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
  <div class="product">
    <img src="assets/images/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
    <div class="product-txt">
      <p><?= htmlspecialchars($producto['descripcion']) ?></p>
      <p class="precio">$<?= $producto['precio'] ?></p>
      <a href="#" class="agregar-carrito btn-3" data-id="<?= $producto['id'] ?>"data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"data-precio="<?= $producto['precio'] ?>"
         data-imagen="assets/images/<?= htmlspecialchars($producto['imagen']) ?>"></a>
         <a href="#" class="btn-3 agregar-carrito" data-id="<?= $prod['id'] ?>">Agregar al carrito</a>


      <a href="index.php" class="btn-3">Volver al inicio</a>
    </div>
  </div>
</main>

<?php
// Obtener comentarios del producto
$stmt = $pdo->prepare("
    SELECT c.contenido, c.creado_en, u.nombre
    FROM comentarios c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.producto_id = ?
    ORDER BY c.creado_en DESC
");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();
?>

<section class="container">
  <h3>Comentarios</h3>

  <?php foreach ($comentarios as $com): ?>
    <div style="background:#eee; padding:10px; margin-bottom:10px;">
      <strong><?= htmlspecialchars($com['nombre']) ?></strong><br>
      <small><?= $com['creado_en'] ?></small>
      <p><?= nl2br(htmlspecialchars($com['contenido'])) ?></p>
    </div>
  <?php endforeach; ?>

  <?php if (isset($_SESSION['usuario'])): ?>
    <form method="POST" action="comentarios/guardar.php">
      <textarea name="contenido" placeholder="Deja tu comentario" required></textarea><br>
      <input type="hidden" name="producto_id" value="<?= $id ?>">
      <button type="submit" class="btn-3">Enviar</button>
    </form>
  <?php else: ?>
    <p><a href="auth/login.php">Inicia sesión</a> para comentar.</p>
  <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>

