<?php
require_once '../db/db.php';
require_once '../helpers/Csrf.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || !$_SESSION['es_admin']) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

// Filtro de búsqueda y ordenamiento dinámico
$condiciones = [];
$params = [];

if (!empty($_GET['buscar'])) {
    $condiciones[] = "nombre LIKE ?";
    $params[] = '%' . $_GET['buscar'] . '%';
}

$ordenSQL = "ORDER BY id DESC";
if (!empty($_GET['orden'])) {
    if ($_GET['orden'] === 'precio_asc') {
        $ordenSQL = "ORDER BY precio ASC";
    } elseif ($_GET['orden'] === 'precio_desc') {
        $ordenSQL = "ORDER BY precio DESC";
    }
}

$sql = "SELECT * FROM productos";
if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(' AND ', $condiciones);
}
$sql .= " $ordenSQL";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();


$errores_form = [];
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
        $errores_form[] = "Token CSRF inválido.";
    } else {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $precio = filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT);
        $descuento = is_numeric($_POST['descuento']) ? intval($_POST['descuento']) : null;
        $promocion = $descuento > 0 ? 1 : 0;
        $imagen = trim($_POST['imagenGuardada']);

        if (empty($nombre)) $errores_form[] = "El nombre es obligatorio.";
        if (empty($descripcion)) $errores_form[] = "La descripción es obligatoria.";
        if ($precio === false || $precio <= 0) $errores_form[] = "El precio debe ser válido.";
        if (empty($imagen)) $errores_form[] = "La imagen es obligatoria.";

        if (empty($errores_form)) {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen, promocion, descuento) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $imagen, $promocion, $descuento]);
            $mensaje_exito = "✅ Producto agregado correctamente.";
            $_POST = [];
        }
    }
}


?>

<main class="container">
    <h2>Administrar Productos</h2>
    <a href="<?= BASE_URL ?>/admin/indexAdmin.php" class="btn-3" style="margin-top:20px;">Volver a administracion</a>
    <hr class="separator-line">
    <?php if ($mensaje_exito): ?><p class="mensaje exito"><?= $mensaje_exito ?></p><?php endif; ?>
    <?php if (!empty($errores_form)): ?>
        <div class="errores-formulario"><?php foreach ($errores_form as $e): ?><p class="error-mensaje"><?= $e ?></p><?php endforeach; ?></div>
    <?php endif; ?>

   <div class="page-card">
  <h3 class="admin-form-title">🛠️ Nuevo Producto</h3>

  <form method="POST" action="<?= BASE_URL ?>/admin/productos.php" class="form-admin" enctype="multipart/form-data">
      <?= Csrf::inputField() ?>

      <div class="form-grid">
          <div class="form-grupo">
              <label for="nombre">Nombre:</label>
              <input type="text" id="nombre" name="nombre" placeholder="Nombre del producto" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
          </div>

          <div class="form-grupo">
              <label for="precio">Precio:</label>
              <input type="number" id="precio" name="precio" class="form-control" step="0.01" required value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
          </div>

          <div class="form-grupo">
              <label for="descuento">Descuento:</label>
              <select name="descuento" id="descuento" class="form-control">
                  <option value="">-- Sin descuento --</option>
                  <?php foreach ([10,15,20,25,30,40,50,70] as $d): ?>
                      <option value="<?= $d ?>" <?= ($_POST['descuento'] ?? '') == $d ? 'selected' : '' ?>><?= $d ?>%</option>
                  <?php endforeach; ?>
              </select>
          </div>
      </div>

      <div class="form-grupo">
          <label for="descripcion">Descripción:</label>
          <textarea id="descripcion" name="descripcion" placeholder="Descripción completa" class="form-control" required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
      </div>

      <div class="form-grupo">
          <label>Imagen del producto:</label>
          <div id="drop-zone" class="drop-zone">📁 Arrastrá o seleccioná una imagen</div>
          <input type="file" id="imagen" name="imagen_original_input" hidden accept="image/*">
          <input type="hidden" name="imagenGuardada" id="imagenGuardada" value="<?= htmlspecialchars($_POST['imagenGuardada'] ?? '') ?>">
          <div id="preview-imagen-container">
              <?php if (!empty($_POST['imagenGuardada'])): ?>
                  <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($_POST['imagenGuardada']) ?>" style="max-width:100px;">
              <?php endif; ?>
          </div>
      </div>

      <button type="submit" class="btn-3 btn-guardar-producto">💾 Guardar Producto</button>
  </form>
</div>


    </div>
  <div class="product-filters">
  <form method="GET" action="">
    <div class="filter-row">
      <div class="input-group">
        <svg viewBox="0 0 24 24" aria-hidden="true" class="search-icon">
          <path
            d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"
          />
        </svg>
        <input type="text" name="buscar" class="input-search" placeholder="Buscar producto..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
      </div>

      <select name="orden" class="select-orden">
        <option value="">Ordenar por</option>
        <option value="precio_asc" <?= ($_GET['orden'] ?? '') === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
        <option value="precio_desc" <?= ($_GET['orden'] ?? '') === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
      </select>

      <button type="submit" class="btn-3">Aplicar</button>
    </div>
  </form>
</div>

</div>
               
    <h2>Productos Existentes</h2>
    <?php if (empty($productos)): ?>
        <p>No hay productos cargados aún.</p>
    <?php else: ?>
        <div class="product-grid admin-product-grid">
            <?php foreach ($productos as $p): ?>
                <div class="product admin-product-item">
                    <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($p['imagen']) ?>" alt="">
                    <h3><?= htmlspecialchars($p['nombre']) ?></h3>
                    <p class="precio">
                        <?php if ($p['promocion']): ?>
                            <span class="precio-original tachado">$<?= number_format($p['precio'], 2, ',', '.') ?></span>
                            <span class="precio-descuento">$<?= number_format($p['precio'] * (1 - $p['descuento']/100), 2, ',', '.') ?></span>
                        <?php else: ?>
                            $<?= number_format($p['precio'], 2, ',', '.') ?>
                        <?php endif; ?>
                    </p>
                    <div class="admin-product-actions">
                        <a href="<?= BASE_URL ?>/admin/editar.php?id=<?= $p['id'] ?>" class="btn-3 btn-editar">Editar</a>
                        <a href="<?= BASE_URL ?>/admin/eliminar.php?id=<?= $p['id'] ?>" class="btn-3 btn-eliminar" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script src="<?= BASE_URL ?>/assets/js/dragdrop.js"></script>
<?php include '../includes/footer.php'; ?>