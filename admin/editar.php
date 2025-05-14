<?php
require_once '../db/db.php';
require_once '../helpers/Csrf.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    echo "<main class='container'><p class='mensaje error'>Acceso denegado. No tienes permisos de administrador.</p></main>";
    include '../includes/footer.php';
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    echo "<main class='container'><p class='mensaje error'>ID de producto no válido.</p></main>";
    include '../includes/footer.php';
    exit;
}

$stmt_select = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt_select->execute([$id]);
$prod = $stmt_select->fetch();

if (!$prod) {
    echo "<main class='container'><p class='mensaje error'>Producto no encontrado.</p></main>";
    include '../includes/footer.php';
    exit;
}

$errores_form = [];
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT);
    $descuento = isset($_POST['descuento']) && is_numeric($_POST['descuento']) ? intval($_POST['descuento']) : 0;
    $promocion = $descuento > 0 ? 1 : 0;
    $imagen_guardada = trim($_POST['imagenGuardada']);
    $imagen_final_a_usar = !empty($imagen_guardada) ? $imagen_guardada : $prod['imagen'];

    if (empty($nombre)) $errores_form[] = "El nombre es obligatorio.";
    if (empty($descripcion)) $errores_form[] = "La descripción es obligatoria.";
    if ($precio === false || $precio <= 0) $errores_form[] = "El precio debe ser un número válido y mayor que cero.";

    $precio_promocion = $promocion ? $precio - ($precio * $descuento / 100) : null;

    if (empty($errores_form)) {
        try {
            $stmt_update = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=?, promocion=?, descuento=?, precio_promocion=? WHERE id=?");
            $stmt_update->execute([
                $nombre,
                $descripcion,
                $precio,
                $imagen_final_a_usar,
                $promocion,
                $descuento,
                $precio_promocion,
                $id
            ]);

            $mensaje_exito = "✅ Producto actualizado exitosamente.";
            $stmt_select->execute([$id]);
            $prod = $stmt_select->fetch();

        } catch (PDOException $e) {
            $errores_form[] = "Error al actualizar el producto: " . $e->getMessage();
        }
    }
}
?>

<main class="container">
  <h2>Editar Producto: <?= htmlspecialchars($prod['nombre']) ?></h2>

  <?php if (!empty($errores_form)): ?>
    <div class="errores-formulario">
      <?php foreach ($errores_form as $e): ?>
        <p class="error-mensaje"><?= $e ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($mensaje_exito): ?>
    <p class="mensaje exito"><?= $mensaje_exito ?></p>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_URL ?>/admin/editar.php?id=<?= $id ?>" class="form-admin">
    <div class="form-grupo">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($prod['nombre']) ?>" required>
    </div>

    <div class="form-grupo">
      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" name="descripcion" required><?= htmlspecialchars($prod['descripcion']) ?></textarea>
    </div>

    <div class="form-grupo">
      <label for="precio">Precio:</label>
      <input type="number" id="precio" name="precio" value="<?= htmlspecialchars($prod['precio']) ?>" step="0.01" required>
    </div>

    <div class="form-grupo">
      <label for="descuento">Promoción:</label>
      <select name="descuento" id="descuento" class="form-control">
        <option value="0">-- Sin descuento --</option>
        <?php foreach ([10, 15, 20, 25, 30, 40, 50, 70] as $val): ?>
          <option value="<?= $val ?>" <?= $prod['descuento'] == $val ? 'selected' : '' ?>><?= $val ?>%</option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-grupo">
        <label>Imagen actual:</label>
        <?php if(!empty($prod['imagen'])): ?>
            <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>" style="max-width:150px; max-height:150px;">
        <?php else: ?>
            <p>No hay imagen asignada.</p>
        <?php endif; ?>

        <label>Cambiar imagen:</label>
        <div id="drop-zone" class="drop-zone">
            Arrastrá una imagen aquí o hacé clic para seleccionar
        </div>
        <input type="file" id="imagen" name="imagen_original_input" hidden accept="image/*">
        <input type="hidden" name="imagenGuardada" id="imagenGuardada" value="">
        <div id="preview-imagen-container" style="margin-top:10px;"></div>
    </div>

    <button type="submit" class="btn-3">Guardar Cambios</button>
    <a href="<?= BASE_URL ?>/admin/productos.php" class="btn-3 btn-cancelar" style="margin-left:10px;">Cancelar</a>
  </form>
</main>

<script src="<?= BASE_URL ?>/assets/js/dragdrop.js"></script>
<?php include '../includes/footer.php'; ?>
