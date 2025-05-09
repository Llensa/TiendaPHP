<?php
require_once '../db/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    // Asumiendo que db.php está en una carpeta 'db' en la raíz, y este archivo está en 'comentarios'
    // Necesitamos definir BASE_URL si no está ya definido por header.php (que no se incluye aquí)
    // Esta es una forma simple, podrías tener un config.php global para BASE_URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
    // Asumiendo que la estructura es /nombre_proyecto/comentarios/guardar.php
    // y que nombre_proyecto es tu BASE_URL (sin el / inicial)
    // Esto es una simplificación, ajusta según tu configuración de BASE_URL
    $base_path_array = array_slice($script_name_parts, 0, count($script_name_parts) - 2);
    define('BASE_URL', implode('/', $base_path_array));
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje_error'] = "Debes iniciar sesión para comentar.";
    $redirect_url = isset($_POST['producto_id']) ? BASE_URL . "/producto.php?id=" . intval($_POST['producto_id']) : BASE_URL . "/index.php";
    header("Location: " . BASE_URL . "/auth/login.php?redirect=" . urlencode($redirect_url));
    exit;
}

$contenido = trim($_POST['contenido'] ?? '');
$producto_id = filter_var($_POST['producto_id'] ?? 0, FILTER_VALIDATE_INT);
$usuario_id = (int)$_SESSION['usuario'];

if (empty($contenido)) {
    $_SESSION['mensaje_error'] = "El comentario no puede estar vacío.";
} else if (!$producto_id || $producto_id <= 0) {
    $_SESSION['mensaje_error'] = "Producto no válido para comentar.";
}

if (isset($_SESSION['mensaje_error'])) {
    $redirect_url = ($producto_id > 0) ? BASE_URL . "/producto.php?id=" . $producto_id . "#form-comentario" : BASE_URL . "/index.php";
    header("Location: " . $redirect_url);
    exit;
}


try {
    $stmt = $pdo->prepare("INSERT INTO comentarios (producto_id, usuario_id, contenido, creado_en) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$producto_id, $usuario_id, $contenido]);
    $_SESSION['mensaje_exito'] = "Comentario añadido con éxito.";
} catch (PDOException $e) {
    // Considera loggear $e->getMessage() en un archivo de errores del servidor
    $_SESSION['mensaje_error'] = "Error al guardar el comentario. Inténtalo más tarde.";
}

header("Location: " . BASE_URL . "/producto.php?id=" . $producto_id . "#comentarios");
exit;
?>