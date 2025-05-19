<?php
require_once '../db/db.php';



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
    $base_path_array = array_slice($script_name_parts, 0, count($script_name_parts) - 2);
    define('BASE_URL', implode('/', $base_path_array));
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Cambiado a POST por seguridad
    $_SESSION['mensaje_error'] = "Acción no permitida.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje_error'] = "Debes iniciar sesión.";
    $producto_id_redirect = filter_input(INPUT_POST, 'producto_id_redirect', FILTER_VALIDATE_INT);
    header('Location: ' . BASE_URL . ($producto_id_redirect ? '/producto.php?id='.$producto_id_redirect : '/index.php'));
    exit;
}

$comentario_id = filter_input(INPUT_POST, 'comentario_id', FILTER_VALIDATE_INT);
$producto_id = filter_input(INPUT_POST, 'producto_id_redirect', FILTER_VALIDATE_INT); // Para redirigir correctamente


if (!$comentario_id || !$producto_id) {
    $_SESSION['mensaje_error'] = "Datos incorrectos para eliminar comentario.";
    header("Location: " . BASE_URL . ($producto_id ? "/producto.php?id=" . $producto_id : "/index.php") . "#comentarios" );
    exit;
}

// Verificar permisos
$stmt_check = $pdo->prepare("SELECT usuario_id FROM comentarios WHERE id = ?");
$stmt_check->execute([$comentario_id]);
$comentario = $stmt_check->fetch();

if (!$comentario) {
    $_SESSION['mensaje_error'] = "Comentario no encontrado.";
} else {
    $es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;
    if ($comentario['usuario_id'] != $_SESSION['usuario'] && !$es_admin) {
        $_SESSION['mensaje_error'] = "No tienes permiso para eliminar este comentario.";
    }
}

if (isset($_SESSION['mensaje_error'])) {
    header("Location: " . BASE_URL . "/producto.php?id=" . $producto_id . "#comentarios");
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ?");
    $stmt->execute([$comentario_id]);
    $_SESSION['mensaje_exito'] = "Comentario eliminado correctamente.";
} catch (PDOException $e) {
    $_SESSION['mensaje_error'] = "Error al eliminar el comentario.";
    // error_log("Error al eliminar comentario: " . $e->getMessage());
}

header("Location: " . BASE_URL . "/producto.php?id=" . $producto_id . "#comentarios");
exit;
?>