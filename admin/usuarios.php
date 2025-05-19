<?php
require_once '../db/db.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['es_admin'] !== true) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$usuarios = $pdo->query("SELECT id, nombre, email, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC")->fetchAll();
?>

<main class="container">
    <h2>👥 Gestión de Usuarios</h2>
     <a href="<?= BASE_URL ?>/admin/indexAdmin.php" class="btn-3" style="margin-top:20px;">Volver a administracion</a>
    <hr class="separator-line">
    <?php if (empty($usuarios)): ?>
        <p class="mensaje error">No hay usuarios registrados aún.</p>
    <?php else: ?>
        <div class="usuarios-admin-grid">
            <?php foreach ($usuarios as $u): ?>
                <div class="usuario-card">
                    <h3><?= htmlspecialchars($u['nombre']) ?></h3>
                    <p>📧 <strong>Email:</strong> <?= htmlspecialchars($u['email']) ?></p>
                    <p>🔐 <strong>Rol:</strong> <?= $u['rol'] === 'admin' ? 'Administrador' : 'Cliente' ?></p>
                    <p>📅 <strong>Registrado:</strong> <?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
