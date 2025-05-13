<?php
require_once 'db/db.php';
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'msg' => 'Debes iniciar sesión para realizar un pedido.']);
    exit;
}

$dataRaw = file_get_contents('php://input');
if (!$dataRaw) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'No se recibió data.']);
    exit;
}

$data = json_decode($dataRaw, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'JSON inválido.']);
    exit;
}

// 📦 Campos esperados desde el frontend
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

    // 🧮 Calcular total
    $total = 0;
    foreach ($carrito as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    // 💾 Insertar en `pedidos`
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, nombre_completo, direccion, ciudad, codigo_postal, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['usuario'],
        $nombre,
        $direccion,
        $ciudad,
        $cp,
        $total
    ]);
    $pedido_id = $pdo->lastInsertId();

    // 🧾 Insertar en `detalles_pedido`
    $stmtDetalle = $pdo->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?, ?)");

foreach ($carrito as $item) {
    $stmtDetalle->execute([
        $pedido_id,
        $item['id'],
        $item['titulo'],       // 👈 Este valor viene del localStorage
        $item['cantidad'],
        $item['precio']
    ]);
}

    $pdo->commit();
    echo json_encode(['status' => 'ok', 'pedido_id' => $pedido_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Error interno: ' . $e->getMessage()
    ]);
}
