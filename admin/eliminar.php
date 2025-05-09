<?php
require_once '../db/db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}
header('Location: productos.php');
