<?php
require_once '../db/db.php';



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    // Copia la definición de BASE_URL de guardar.php si es necesario
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
    $base_path_array = array_slice($script_name_parts, 0, count($script_name_parts) - 2);
    define('BASE_URL', implode('/', $base_path_array));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje_error'] = "Debes iniciar sesión.";
    // Redirigir a login o a la página del producto si tenemos producto_id
    $producto_id_redirect = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
    header('Location: ' . BASE_URL . ($producto_id_redirect ? '/producto.php?id='.$producto_id_redirect : '/index.php'));
    exit;
}

$comentario_id = filter_input(INPUT_POST, 'comentario_id', FILTER_VALIDATE_INT);
$producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
$contenido = trim($_POST['contenido'] ?? '');

if (empty($contenido)) {
    $_SESSION['mensaje_error'] = "El comentario no puede estar vacío.";
} else if (!$comentario_id || !$producto_id) {
     $_SESSION['mensaje_error'] = "Datos incompletos para editar el comentario.";
}


if (isset($_SESSION['mensaje_error'])) {
    // Redirigir de vuelta al formulario de edición o a la página del producto
    $redirect_url = ($comentario_id && $producto_id) ? BASE_URL . "/comentarios/editar_comentario.php?id=" . $comentario_id . "&producto_id=" . $producto_id : BASE_URL . "/index.php";
    header("Location: " . $redirect_url);
    exit;
}

// Verificar permisos antes de actualizar
$stmt_check = $pdo->prepare("SELECT usuario_id FROM comentarios WHERE id = ?");
$stmt_check->execute([$comentario_id]);
$comentario_original = $stmt_check->fetch();

if (!$comentario_original) {
    $_SESSION['mensaje_error'] = "Comentario no encontrado para actualizar.";
} else {
    $es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;
    if ($comentario_original['usuario_id'] != $_SESSION['usuario'] && !$es_admin) {
        $_SESSION['mensaje_error'] = "No tienes permiso para modificar este comentario.";
    }
}

if (isset($_SESSION['mensaje_error'])) {
    header("Location: " . BASE_URL . "/producto.php?id=" . $producto_id . "#comentarios");
    exit;
}


try {
    $stmt = $pdo->prepare("UPDATE comentarios SET contenido = ?, editado_en = NOW() WHERE id = ?");
    $stmt->execute([$contenido, $comentario_id]);
    $_SESSION['mensaje_exito'] = "Comentario actualizado con éxito.";
} catch (PDOException $e) {
    $_SESSION['mensaje_error'] = "Error al actualizar el comentario.";
    // error_log("Error al actualizar comentario: " . $e->getMessage());
}

header("Location: " . BASE_URL . "/producto.php?id=" . $producto_id . "#comentario-" . $comentario_id); // Añadido #comentario-id para ancla
exit;
?>