<?php
require_once '../db/db.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir BASE_URL si no está (para redirección)
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
    $base_path_array = array_slice($script_name_parts, 0, count($script_name_parts) - 2);
    define('BASE_URL', implode('/', $base_path_array));
}


if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    // Podrías redirigir con un mensaje de error
    $_SESSION['mensaje_error_admin'] = "Acceso denegado.";
    header('Location: ' . BASE_URL . '/admin/productos.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id && $id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['mensaje_exito_admin'] = "Producto eliminado correctamente.";
        } catch (PDOException $e) {
            $_SESSION['mensaje_error_admin'] = "Error al eliminar el producto: " . $e->getMessage();
        }
    } else {
        $_SESSION['mensaje_error_admin'] = "ID de producto no válido para eliminar.";
    }
} else {
    $_SESSION['mensaje_error_admin'] = "No se especificó ID de producto para eliminar.";
}

header('Location: ' . BASE_URL . '/admin/productos.php');
exit;
?>