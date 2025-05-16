<?php
require_once 'db/db.php';
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

$nombre = trim($datos['nombreCompleto'] ?? '');
$direccion = trim($datos['direccionEnvio'] ?? '');
$ciudad = trim($datos['ciudad'] ?? '');
$cp = trim($datos['codigoPostal'] ?? '');
$carrito = $datos['carrito'] ?? [];

if (!$nombre || !$direccion || !$ciudad || !$cp || !$carrito || count($carrito) == 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
    exit;
}

session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Usuario no autenticado']);
    exit;
}

try {
    $pdo->beginTransaction();

    $total = 0;
    foreach ($carrito as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, nombre, direccion, ciudad, codigo_postal, total, estado, creado_en) VALUES (?, ?, ?, ?, ?, ?, 'pendiente', NOW())");
    $stmt->execute([$_SESSION['usuario'], $nombre, $direccion, $ciudad, $cp, $total]);
    $pedido_id = $pdo->lastInsertId();

    $stmtDetalle = $pdo->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");

    foreach ($carrito as $item) {
        $stmtDetalle->execute([
            $pedido_id,
            $item['id'],
            $item['cantidad'],
            $item['precio']
        ]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'ok', 'pedido_id' => $pedido_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'msg' => 'Error al guardar pedido: ' . $e->getMessage()]);
}
