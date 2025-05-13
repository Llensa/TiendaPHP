<?php
require_once '../db/db.php';
require_once '../helpers/Flash.php';

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Debes iniciar sesión para agregar a la lista de deseos.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$producto_id = filter_var($data['producto_id'] ?? 0, FILTER_VALIDATE_INT);

if (!$producto_id) {
    echo json_encode(['status' => 'error', 'msg' => 'ID inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM deseos WHERE usuario_id = ? AND producto_id = ?");
    $stmt->execute([$_SESSION['usuario'], $producto_id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Ya está en tu lista de deseos.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO deseos (usuario_id, producto_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['usuario'], $producto_id]);

    echo json_encode(['status' => 'ok', 'msg' => 'Producto agregado a la lista de deseos.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Error al guardar en lista de deseos.']);
}
