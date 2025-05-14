<?php
require_once '../db/db.php';
session_start(); // 💥 IMPORTANTE para acceder a $_SESSION
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Método inválido']);
    exit;
}

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Iniciá sesión para agregar a deseos']);
    exit;
}

$raw = json_decode(file_get_contents('php://input'), true);
$producto_id = intval($raw['producto_id'] ?? 0);

if (!$producto_id || $producto_id <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'ID no válido.']);
    exit;
}

// Verificar si ya está en deseos
$stmt = $pdo->prepare("SELECT 1 FROM lista_deseos WHERE usuario_id = ? AND producto_id = ?");
$stmt->execute([$_SESSION['usuario'], $producto_id]);

if ($stmt->fetch()) {
    // Ya está → eliminar
    $pdo->prepare("DELETE FROM lista_deseos WHERE usuario_id = ? AND producto_id = ?")
        ->execute([$_SESSION['usuario'], $producto_id]);

    echo json_encode([
        'status' => 'ok',
        'accion' => 'eliminado',
        'msg' => 'Producto eliminado de tu lista de deseos'
    ]);
    exit;
}

// Insertar nuevo
$stmt = $pdo->prepare("INSERT INTO lista_deseos (usuario_id, producto_id) VALUES (?, ?)");
$stmt->execute([$_SESSION['usuario'], $producto_id]);

echo json_encode([
    'status' => 'ok',
    'accion' => 'agregado',
    'msg' => 'Producto añadido a tu lista de deseos'
]);
