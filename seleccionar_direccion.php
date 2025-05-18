<?php
session_start();
require 'conexion.php'; // Ajusta si tu conexión se llama distinto

if (isset($_POST['direccion_id'], $_SESSION['usuario_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM direcciones WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$_POST['direccion_id'], $_SESSION['usuario_id']]);
    $direccion = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($direccion) {
        $_SESSION['direccion_predeterminada'] = $direccion;
    }
}

header('Location: direcciones.php');
exit;