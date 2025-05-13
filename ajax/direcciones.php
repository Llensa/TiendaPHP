<?php
require_once '../db/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$usuario_id = $_SESSION['usuario'] ?? null;
if (!$usuario_id) exit(json_encode(['status' => 'unauthorized']));

switch ($method) {
    case 'GET':
        $stmt = $pdo->prepare("SELECT * FROM direcciones WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        echo json_encode(['status' => 'ok', 'direcciones' => $stmt->fetchAll()]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $direccion = trim($data['direccion'] ?? '');
        $preferida = $data['preferida'] ?? false;

        if ($preferida) {
            $pdo->prepare("UPDATE direcciones SET preferida = 0 WHERE usuario_id = ?")->execute([$usuario_id]);
        }

        $stmt = $pdo->prepare("INSERT INTO direcciones (usuario_id, direccion, preferida) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $direccion, $preferida ? 1 : 0]);
        echo json_encode(['status' => 'ok']);
        break;

    case 'PATCH': // preferida toggle
        $data = json_decode(file_get_contents("php://input"), true);
        $direccion_id = $data['id'] ?? 0;

        $pdo->prepare("UPDATE direcciones SET preferida = 0 WHERE usuario_id = ?")->execute([$usuario_id]);
        $pdo->prepare("UPDATE direcciones SET preferida = 1 WHERE id = ? AND usuario_id = ?")->execute([$direccion_id, $usuario_id]);

        echo json_encode(['status' => 'ok']);
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $id = $data['id'] ?? 0;
        $pdo->prepare("DELETE FROM direcciones WHERE id = ? AND usuario_id = ?")->execute([$id, $usuario_id]);
        echo json_encode(['status' => 'ok']);
        break;
}
