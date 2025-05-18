<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../db/db.php';
require_once '../includes/config.php';
include '../includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] !== true) {
    echo "<main class='container'><p class='mensaje error'>Acceso denegado. No tienes permisos de administrador.</p></main>";
    include '../includes/footer.php';
    exit;
}

// Ejemplo de estadísticas (puedes expandir esto)
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_comentarios = $pdo->query("SELECT COUNT(*) FROM comentarios")->fetchColumn();

?>
<main class="container admin-dashboard">
    <h2>Panel de Administración</h2>
    <p>Bienvenido al panel de control, <?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Admin') ?>.</p> <div class="admin-stats-grid">
        <div class="stat-card">
            <h3>Productos</h3>
            <p><?= $total_productos ?></p>
            <a href="<?= BASE_URL ?>/admin/productosAdmin.php" class="btn-3 btn-sm">Gestionar Productos</a>
        </div>
        <div class="stat-card">
            <h3>Usuarios</h3>
            <p><?= $total_usuarios ?></p>
            <a href="<?= BASE_URL ?>/admin/usuarios.php" class="btn-3 btn-sm">Gestionar Usuarios</a>
        </div>
        <div class="stat-card">
            <h3>Comentarios</h3>
            <p><?= $total_comentarios ?></p>
            <a href="<?= BASE_URL ?>/admin/comentarios.php" class="btn-3 btn-sm">Moderar Comentarios</a>
        </div>
        <div class="stat-card">
            <h3>Pedidos</h3>
            <p><?= $total_comentarios ?></p>
            <a href="<?= BASE_URL ?>/admin/pedidosAdmin.php" class="btn-3 btn-sm">Gestionar pedidos</a>
        </div>
        </div>
</main>

<?php include '../includes/footer.php'; ?>
