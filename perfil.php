<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode(BASE_URL . '/perfil.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT nombre, email, fecha_registro FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario']]);
$usuario = $stmt->fetch();

// Guardar nombre de usuario en sesión para saludo en admin/index.php si es admin
if ($usuario) {
    $_SESSION['nombre_usuario'] = $usuario['nombre'];
}
?>

<main class="container">
    <div class="perfil-card">
        <div class="perfil-header">
            <img src="<?= BASE_URL ?>/assets/images/avatar.png" alt="Avatar Usuario" class="avatar-perfil">
            <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
            <p><?= htmlspecialchars($usuario['email']) ?></p>
            <?php if (!empty($usuario['fecha_registro'])): ?>
                <small>Miembro desde: <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></small>
            <?php endif; ?>
        </div>

        <div class="perfil-account-links">
            <h3>Mi Cuenta</h3>
            <ul>
                <li><a href="<?= BASE_URL ?>/pedidos.php">Mis Pedidos</a></li>
                <li><a href="<?= BASE_URL ?>/lista_deseos.php">Mi Lista de Deseos</a></li>
                <li><a href="<?= BASE_URL ?>/direcciones.php">Mis Direcciones</a></li>
                <li><a href="<?= BASE_URL ?>/detalles_cuenta.php">Detalles de la Cuenta</a></li>
                <li><a href="<?= BASE_URL ?>/auth/logout.php" class="btn-3 btn-logout">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>