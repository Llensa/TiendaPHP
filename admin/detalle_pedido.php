<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../db/db.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$pedido_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($pedido_id <= 0) {
    echo "<main class='container'><p class='mensaje error'>ID de pedido no válido.</p></main>";
    include '../includes/footer.php';
    exit;
}

// 🧾 Procesar actualización de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_estado'])) {
    $nuevo_estado = $_POST['nuevo_estado'];
    $stmtUpdate = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmtUpdate->execute([$nuevo_estado, $pedido_id]);
    $_SESSION['mensaje_exito_admin'] = "Estado actualizado a '$nuevo_estado'.";
    header("Location: detalle_pedido.php?id=$pedido_id");
    exit;
}

// 🧠 Obtener datos del pedido
$stmtPedido = $pdo->prepare("
    SELECT p.*, u.nombre AS nombre_usuario
    FROM pedidos p
    JOIN usuarios u ON u.id = p.usuario_id
    WHERE p.id = ?
");
$stmtPedido->execute([$pedido_id]);
$pedido = $stmtPedido->fetch();

if (!$pedido) {
    echo "<main class='container'><p class='mensaje error'>Pedido no encontrado.</p></main>";
    include '../includes/footer.php';
    exit;
}

// 🧾 Obtener productos del pedido
$stmtProductos = $pdo->prepare("
    SELECT dp.*, pr.nombre AS nombre_producto, pr.imagen
    FROM detalles_pedido dp
    JOIN productos pr ON pr.id = dp.producto_id
    WHERE dp.pedido_id = ?
");
$stmtProductos->execute([$pedido_id]);
$productos = $stmtProductos->fetchAll();
?>

<main class="container">
    <div class="page-card">
        <h2>Detalles del Pedido #<?= $pedido['id'] ?></h2>

        <p><strong>Usuario:</strong> <?= htmlspecialchars($pedido['nombre_usuario']) ?></p>
        <p><strong>Nombre Envío:</strong> <?= htmlspecialchars($pedido['nombre_completo']) ?></p>
        <p><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion']) ?>, <?= htmlspecialchars($pedido['ciudad']) ?> (CP <?= htmlspecialchars($pedido['codigo_postal']) ?>)</p>
        <p><strong>Estado actual:</strong> <?= htmlspecialchars(ucfirst($pedido['estado'])) ?></p>
        <p><strong>Total:</strong> $<?= number_format($pedido['total'], 2, ',', '.') ?></p>
        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></p>

        <form method="POST" style="margin: 20px 0;">
            <label for="nuevo_estado"><strong>Cambiar estado:</strong></label>
            <select name="nuevo_estado" id="nuevo_estado" class="form-control" style="max-width: 300px; display:inline-block;">
                <option value="pendiente" <?= $pedido['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="enviado" <?= $pedido['estado'] === 'enviado' ? 'selected' : '' ?>>Enviado</option>
            </select>
            <button type="submit" class="btn-3">Actualizar Estado</button>
        </form>

        <hr>

        <h3>Productos</h3>
        <table class="detalle-tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Imagen</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                        <td>
                            <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre_producto']) ?>" class="mini-img">
                        </td>
                        <td>$<?= number_format($prod['precio_unitario'], 2) ?></td>
                        <td><?= $prod['cantidad'] ?></td>
                        <td>$<?= number_format($prod['precio_unitario'] * $prod['cantidad'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="<?= BASE_URL ?>/admin/pedidosAdmin.php" class="btn-3" style="margin-top:20px;">⬅ Volver a pedidos</a>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
