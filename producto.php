<?php
require_once 'db/db.php';
include 'includes/header.php';

// ... (código existente para cargar producto sin cambios) ...
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
      <p><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p> <p class="precio">$<?= number_format($producto['precio'], 2, ',', '.') ?></p>
      <button class="btn-3 agregar-carrito"
          data-id="<?= $producto['id'] ?>"
          data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
          data-precio="<?= $producto['precio'] ?>"
          data-imagen="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($producto['imagen']) ?>">
          Agregar al carrito
      </button>
      <a href="<?= BASE_URL ?>/index.php" class="btn-3 btn-volver" style="margin-top:10px; display:inline-block;">Volver al inicio</a>
    </div>
  </div>
</main>

<?php
$stmt_comentarios = $pdo->prepare("
    SELECT c.id AS comentario_id, c.contenido, c.creado_en, c.editado_en, c.usuario_id, u.nombre AS nombre_usuario
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
      <div class="comentario-item" id="comentario-<?= $com['comentario_id'] ?>">
        <div class="comentario-autor-fecha">
            <strong><?= htmlspecialchars($com['nombre_usuario']) ?></strong>
            <small>
                <?= date("d/m/Y H:i", strtotime($com['creado_en'])) ?>
                <?php if ($com['editado_en']): ?>
                    (editado el <?= date("d/m/Y H:i", strtotime($com['editado_en'])) ?>)
                <?php endif; ?>
            </small>
        </div>
        <p><?= nl2br(htmlspecialchars($com['contenido'])) ?></p>
        <?php if (isset($_SESSION['usuario']) && ($_SESSION['usuario'] == $com['usuario_id'] || (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true))): ?>
            <div class="comentario-acciones">
                <a href="<?= BASE_URL ?>/comentarios/editar_comentario.php?id=<?= $com['comentario_id'] ?>&producto_id=<?= $id ?>" class="btn-accion-comentario editar">Editar</a>
                <form method="POST" action="<?= BASE_URL ?>/comentarios/eliminar_comentario.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este comentario?');">
                    <input type="hidden" name="comentario_id" value="<?= $com['comentario_id'] ?>">
                    <input type="hidden" name="producto_id_redirect" value="<?= $id ?>">
                    <button type="submit" class="btn-accion-comentario eliminar">Eliminar</button>
                </form>
            </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['usuario'])): ?>
    <form method="POST" action="<?= BASE_URL ?>/comentarios/guardar.php" id="form-comentario" class="form-comentario">
      <label for="contenidoComentario" class="sr-only">Tu comentario:</label> <textarea id="contenidoComentario" name="contenido" placeholder="Deja tu comentario..." required class="form-control"></textarea>
      <span class="error-js-mensaje" id="error-contenido-comentario"></span> <input type="hidden" name="producto_id" value="<?= $id ?>">
      <button type="submit" class="btn-3">Enviar Comentario</button>
    </form>
  <?php else: ?>
    <p>Debes <a href="<?= BASE_URL ?>/auth/login.php?redirect=<?= urlencode(BASE_URL . '/producto.php?id=' . $id . '#form-comentario') ?>">iniciar sesión</a> para dejar un comentario.</p>
  <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>