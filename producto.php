<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<p class='container'>Producto no especificado.</p>";
    include 'includes/footer.php';
    exit;
}

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if (!$id || $id <=0) {
    echo "<p class='container'>ID de producto no válido.</p>";
    include 'includes/footer.php';
    exit;
}

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
  <div class="product-detail-layout">
    <div class="product-image-container">
        <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="product-detail-image">
    </div>
    <div class="product-txt product-detail-info">
      <p><?= htmlspecialchars($producto['descripcion']) ?></p>
      <p class="precio">$<?= number_format($producto['precio'], 2, ',', '.') ?></p>
      <button class="btn-3 agregar-carrito"
          data-id="<?= $producto['id'] ?>"
          data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
          data-precio="<?= $producto['precio'] ?>"
          data-imagen="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($producto['imagen']) ?>">
          Agregar al carrito
      </button>
      <a href="<?= BASE_URL ?>/index.php" class="btn-3" style="margin-top:10px; display:inline-block;">Volver al inicio</a>
    </div>
  </div>
</main>

<?php
$stmt_comentarios = $pdo->prepare("
    SELECT c.contenido, c.creado_en, u.nombre AS nombre_usuario
    FROM comentarios c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.producto_id = ?
    ORDER BY c.creado_en DESC
");
$stmt_comentarios->execute([$id]);
$comentarios = $stmt_comentarios->fetchAll();
?>

<section class="container" id="comentarios">
  <h3>Comentarios</h3>

  <?php if (isset($_SESSION['mensaje_exito'])): ?>
    <p class="mensaje exito"><?= $_SESSION['mensaje_exito'] ?></p>
    <?php unset($_SESSION['mensaje_exito']); ?>
  <?php endif; ?>
  <?php if (isset($_SESSION['mensaje_error'])): ?>
    <p class="mensaje error"><?= $_SESSION['mensaje_error'] ?></p>
    <?php unset($_SESSION['mensaje_error']); ?>
  <?php endif; ?>

  <?php if (empty($comentarios)): ?>
    <p>Aún no hay comentarios para este producto. ¡Sé el primero!</p>
  <?php else: ?>
    <?php foreach ($comentarios as $com): ?>
      <div class="comentario-item">
        <strong><?= htmlspecialchars($com['nombre_usuario']) ?></strong>
        <small><?= date("d/m/Y H:i", strtotime($com['creado_en'])) ?></small>
        <p><?= nl2br(htmlspecialchars($com['contenido'])) ?></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['usuario'])): ?>
    <form method="POST" action="<?= BASE_URL ?>/comentarios/guardar.php" id="form-comentario" class="form-comentario">
      <textarea name="contenido" placeholder="Deja tu comentario..." required class="form-control"></textarea><br>
      <input type="hidden" name="producto_id" value="<?= $id ?>">
      <button type="submit" class="btn-3">Enviar Comentario</button>
    </form>
  <?php else: ?>
    <p>Debes <a href="<?= BASE_URL ?>/auth/login.php?redirect=<?= urlencode(BASE_URL . '/producto.php?id=' . $id . '#form-comentario') ?>">iniciar sesión</a> para dejar un comentario.</p>
  <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>