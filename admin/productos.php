<?php
require_once '../db/db.php';
include '../includes/header.php'; // Contiene session_start() y define BASE_URL

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    echo "<main class='container'><p class='mensaje error'>Acceso denegado. No tienes permisos de administrador.</p></main>";
    include '../includes/footer.php';
    exit;
}

$errores_form = [];
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT);
    $imagen = trim($_POST['imagenGuardada']); // Cambiado de 'imagen' a 'imagenGuardada'

    if (empty($nombre)) $errores_form[] = "El nombre es obligatorio.";
    if (empty($descripcion)) $errores_form[] = "La descripción es obligatoria.";
    if ($precio === false || $precio <= 0) $errores_form[] = "El precio debe ser un número válido y mayor que cero.";
    if (empty($imagen)) $errores_form[] = "La imagen es obligatoria.";


    if (empty($errores_form)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $imagen]);
            // No redirigir inmediatamente para poder mostrar mensaje de éxito
            // header("Location: productos.php");
            // exit;
            $mensaje_exito = "Producto agregado exitosamente.";
            // Limpiar los campos del POST para que el formulario aparezca vacío después de agregar
            $_POST = array(); 
            $nombre = $descripcion = $precio = $imagen = '';
        } catch (PDOException $e) {
            $errores_form[] = "Error al agregar el producto: " . $e->getMessage();
        }
    }
}

$productos = $pdo->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll();
?>

<main class="container">
  <h2>Administrar Productos</h2>

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

  <h3>Nuevo Producto</h3>
  <form method="POST" action="<?= BASE_URL ?>/admin/productos.php" class="form-admin">
    <div class="form-grupo">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" placeholder="Nombre del producto" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
    </div>
    <div class="form-grupo">
      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" name="descripcion" placeholder="Descripción detallada" required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
    </div>
    <div class="form-grupo">
      <label for="precio">Precio:</label>
      <input type="number" id="precio" name="precio" placeholder="0.00" required step="0.01" value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
    </div>
    <div class="form-grupo">
        <label>Imagen del producto:</label>
        <div id="drop-zone" class="drop-zone">
            Arrastra una imagen aquí o haz clic para seleccionar
        </div>
        <input type="file" id="imagen" name="imagen_original_input" hidden accept="image/*"> <input type="hidden" name="imagenGuardada" id="imagenGuardada" value="<?= htmlspecialchars($_POST['imagenGuardada'] ?? '') ?>">
        <div id="preview-imagen-container" style="margin-top:10px;">
            <?php if (!empty($_POST['imagenGuardada'])): ?>
                <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($_POST['imagenGuardada']) ?>" alt="Previsualización" style="max-width:100px; max-height:100px;">
            <?php endif; ?>
        </div>
    </div>
    <button type="submit" class="btn-3">Agregar Producto</button>
  </form>

  <hr style="margin: 30px 0;">

  <h3>Productos Existentes</h3>
  <?php if (empty($productos)): ?>
    <p>No hay productos para mostrar.</p>
  <?php else: ?>
  <div class="grid admin-product-grid">
    <?php foreach ($productos as $prod): ?>
      <div class="product admin-product-item">
        <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>">
        <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
        <p>$<?= number_format($prod['precio'], 2, ',', '.') ?></p>
        <div class="admin-product-actions">
            <a href="<?= BASE_URL ?>/admin/editar.php?id=<?= $prod['id'] ?>" class="btn-3 btn-editar">Editar</a>
            <a href="<?= BASE_URL ?>/admin/eliminar.php?id=<?= $prod['id'] ?>" class="btn-3 btn-eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto? Esta acción no se puede deshacer.')">Eliminar</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>

<script src="<?= BASE_URL ?>/assets/js/dragdrop.js"></script>
<?php include '../includes/footer.php'; ?>