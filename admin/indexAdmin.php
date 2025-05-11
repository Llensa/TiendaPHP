<?php
require_once '../db/db.php';
include '../includes/header.php';

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
            <a href="<?= BASE_URL ?>/admin/productos.php" class="btn-3 btn-sm">Gestionar Productos</a>
        </div>
        <div class="stat-card">
            <h3>Usuarios</h3>
            <p><?= $total_usuarios ?></p>
            <a href="#" class="btn-3 btn-sm">Gestionar Usuarios (Próximamente)</a>
        </div>
        <div class="stat-card">
            <h3>Comentarios</h3>
            <p><?= $total_comentarios ?></p>
            <a href="#" class="btn-3 btn-sm">Moderar Comentarios (Próximamente)</a>
        </div>
        </div>
</main>

<?php include '../includes/footer.php'; ?>