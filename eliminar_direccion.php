<?php
require_once 'db/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    http_response_code(403);
    exit('No autorizado');
}

$usuario_id = $_SESSION['usuario_id'];
$id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM direcciones WHERE id = ? AND usuario_id = ?");
$stmt->execute([$id, $usuario_id]);

http_response_code(200);
echo "Dirección eliminada";