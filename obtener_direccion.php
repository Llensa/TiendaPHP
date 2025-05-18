<?php
require_once 'db/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    http_response_code(403);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM direcciones WHERE id = ? AND usuario_id = ?");
$stmt->execute([$id, $usuario_id]);

$direccion = $stmt->fetch(PDO::FETCH_ASSOC);

if ($direccion) {
    echo json_encode($direccion);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'No encontrada']);
}