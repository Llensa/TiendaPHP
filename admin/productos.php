<?php
require_once '../db/db.php';
require_once '../helpers/Csrf.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario']) || !$_SESSION['es_admin']) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

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

$productos = $pdo->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll();
?>

<main class="container">
    <h2>Administrar Productos</h2>

    <?php if ($mensaje_exito): ?><p class="mensaje exito"><?= $mensaje_exito ?></p><?php endif; ?>
    <?php if (!empty($errores_form)): ?>
        <div class="errores-formulario"><?php foreach ($errores_form as $e): ?><p class="error-mensaje"><?= $e ?></p><?php endforeach; ?></div>
    <?php endif; ?>

    <div class="page-card">
    <h3>Nuevo Producto</h3>
    <form method="POST" action="<?= BASE_URL ?>/admin/productos.php" class="form-admin">
        <?= Csrf::inputField() ?>
        
        <div class="form-grupo">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre del producto" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
        </div>

        <div class="form-grupo">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" placeholder="Descripción completa" class="form-control" required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-grupo">
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" class="form-control" step="0.01" required value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
        </div>

        <div class="form-grupo">
            <label for="descuento">Promoción:</label>
            <select name="descuento" id="descuento" class="form-control">
                <option value="">-- Sin descuento --</option>
                <?php foreach ([10,15,20,25,30,40,50,70] as $d): ?>
                    <option value="<?= $d ?>" <?= ($_POST['descuento'] ?? '') == $d ? 'selected' : '' ?>><?= $d ?>%</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-grupo">
            <label>Imagen del producto:</label>
            <div id="drop-zone" class="drop-zone">Arrastrá o seleccioná una imagen</div>
            <input type="file" id="imagen" name="imagen_original_input" hidden accept="image/*">
            <input type="hidden" name="imagenGuardada" id="imagenGuardada" value="<?= htmlspecialchars($_POST['imagenGuardada'] ?? '') ?>">
            <div id="preview-imagen-container" style="margin-top:10px;">
                <?php if (!empty($_POST['imagenGuardada'])): ?>
                    <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($_POST['imagenGuardada']) ?>" style="max-width:100px; max-height:100px;">
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn-3" style="margin-top:15px;">Agregar Producto</button>
    </form>
</div>

    </div>

    <h3>Productos Existentes</h3>
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
