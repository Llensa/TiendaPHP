<?php
require_once '../db/db.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || !$_SESSION['es_admin']) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$stmt = $pdo->query("
    SELECT c.*, u.nombre AS usuario, p.nombre AS producto
    FROM comentarios c
    JOIN usuarios u ON c.usuario_id = u.id
    JOIN productos p ON c.producto_id = p.id
    ORDER BY c.creado_en DESC
");
$comentarios = $stmt->fetchAll();
?>

<main class="container">
    <h2 style="margin-bottom: 25px;">🗨️ Moderación de Comentarios</h2>
     <a href="<?= BASE_URL ?>/admin/indexAdmin.php" class="btn-3" style="margin: top 10px;px;">Volver a administracion</a>
     <hr class="separator-line">

    <?php if (empty($comentarios)): ?>
        <p class="mensaje error">No hay comentarios aún.</p>
    <?php else: ?>
        <div class="comentarios-admin-grid">
            <?php foreach ($comentarios as $c): ?>
                <div class="comentario-card">
                    <div class="comentario-header">
                        <span class="comentario-usuario">👤 <?= htmlspecialchars($c['usuario']) ?></span>
                        <span class="comentario-producto">🛒 <?= htmlspecialchars($c['producto']) ?></span>
                        <span class="comentario-fecha"><?= date('d/m/Y H:i', strtotime($c['creado_en'])) ?></span>
                    </div>
                    <p class="comentario-texto"><?= nl2br(htmlspecialchars($c['contenido'])) ?></p>
                    <!-- Si querés opción de borrar -->
                    <!-- <a href="eliminar_comentario.php?id=<?= $c['id'] ?>" class="btn-3 btn-eliminar" onclick="return confirm('¿Eliminar este comentario?')">Eliminar</a> -->
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
