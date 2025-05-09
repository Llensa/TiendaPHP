<?php
require_once '../db/db.php';
session_start();

// Validar sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Validar datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenido = trim($_POST['contenido']);
    $producto_id = (int) $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario'];

    if ($contenido !== '') {
        $stmt = $pdo->prepare("INSERT INTO comentarios (contenido, usuario_id, producto_id, creado_en) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$contenido, $usuario_id, $producto_id]);
    }
}

// Redirigir de vuelta al producto
header("Location: ../producto.php?id=" . $producto_id);
exit;
