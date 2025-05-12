<?php
require_once 'db/db.php';
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje_error'] = "Debes iniciar sesión para realizar un pedido.";
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode(BASE_URL . '/checkout.php'));
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$nombre = trim($data['nombreCompleto'] ?? '');
$direccion = trim($data['direccionEnvio'] ?? '');
$ciudad = trim($data['ciudad'] ?? '');
$cp = trim($data['codigoPostal'] ?? '');
$carrito = $data['carrito'] ?? [];

if (empty($nombre) || empty($direccion) || empty($ciudad) || empty($cp) || empty($carrito)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos.']);
    exit;
}

try {
    $pdo->beginTransaction();
    $total = 0;
    foreach ($carrito as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, nombre_completo, direccion, ciudad, codigo_postal, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['usuario'], $nombre, $direccion, $ciudad, $cp, $total]);
    $pedido_id = $pdo->lastInsertId();

    $stmtDetalle = $pdo->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    foreach ($carrito as $item) {
        $stmtDetalle->execute([$pedido_id, $item['id'], $item['cantidad'], $item['precio']]);
    }

    $pdo->commit();

    echo json_encode(['status' => 'ok', 'pedido_id' => $pedido_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Error al guardar pedido.']);
}
?>
