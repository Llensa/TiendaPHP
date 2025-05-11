<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode(BASE_URL . '/direcciones.php'));
    exit;
}
// Lógica para cargar/guardar direcciones iría aquí
?>
<main class="container">
    <div class="page-card">
        <h2>Mis Direcciones de Envío</h2>
        <div class="placeholder-content">
            <p><em>Funcionalidad de gestión de direcciones en construcción.</em></p>
            <p><strong>Dirección Principal:</strong> Calle Falsa 123, Ciudad, Provincia.</p>
        </div>
        <a href="<?= BASE_URL ?>/perfil.php" class="btn-3" style="margin-top:20px;">Volver al Perfil</a>
    </div>
</main>
<?php include 'includes/footer.php'; ?>