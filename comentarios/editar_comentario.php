<?php
require_once '../db/db.php';


include '../includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$comentario_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$producto_id_redirect = filter_input(INPUT_GET, 'producto_id', FILTER_VALIDATE_INT);


if (!$comentario_id || !$producto_id_redirect) {
    $_SESSION['mensaje_error'] = "Solicitud no válida.";
    header('Location: ' . BASE_URL . ($producto_id_redirect ? '/producto.php?id='.$producto_id_redirect : '/index.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM comentarios WHERE id = ?");
$stmt->execute([$comentario_id]);
$comentario = $stmt->fetch();

if (!$comentario) {
    $_SESSION['mensaje_error'] = "Comentario no encontrado.";
    header('Location: ' . BASE_URL . '/producto.php?id=' . $producto_id_redirect);
    exit;
}

// Verificar permisos: solo el dueño del comentario o un admin pueden editar
$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;
if ($comentario['usuario_id'] != $_SESSION['usuario'] && !$es_admin) {
    $_SESSION['mensaje_error'] = "No tienes permiso para editar este comentario.";
    header('Location: ' . BASE_URL . '/producto.php?id=' . $producto_id_redirect);
    exit;
}
?>

<main class="container">
    <div class="page-card form-autenticacion" style="max-width: 700px;">
        <h2>Editar Comentario</h2>
        <form method="POST" action="<?= BASE_URL ?>/comentarios/guardar_edicion_comentario.php" id="formEditarComentario">
            <input type="hidden" name="comentario_id" value="<?= $comentario_id ?>">
            <input type="hidden" name="producto_id" value="<?= $producto_id_redirect ?>">
            <div class="form-grupo">
                <label for="contenidoEditar">Tu comentario:</label>
                <textarea id="contenidoEditar" name="contenido" class="form-control" rows="5" required><?= htmlspecialchars($comentario['contenido']) ?></textarea>
                <span class="error-js-mensaje" id="error-contenido-editar"></span>
            </div>
            <button type="submit" class="btn-3">Guardar Cambios</button>
            <a href="<?= BASE_URL ?>/producto.php?id=<?= $producto_id_redirect ?>#comentario-<?= $comentario_id ?>" class="btn-3 btn-cancelar" style="margin-left:10px;">Cancelar</a>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>