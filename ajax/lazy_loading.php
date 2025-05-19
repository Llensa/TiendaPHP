<?php
require_once '../db/db.php';

$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
$limite = 8;

$stmt = $pdo->prepare("SELECT * FROM productos ORDER BY id DESC LIMIT ?, ?");
$stmt->bindValue(1, $offset, PDO::PARAM_INT);
$stmt->bindValue(2, $limite, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($productos);
