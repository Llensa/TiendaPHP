<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode(BASE_URL . '/pedidos.php'));
    exit;
}
?>

<main class="container">
    <div class="page-card">
        <h2>Mis Pedidos</h2>
        <p>Aquí podrás ver el historial de tus pedidos.</p>
        <div class="placeholder-content">
            <p><em>Funcionalidad de pedidos en construcción.</em></p>
            <p>Ejemplo de pedido:</p>
            <ul class="order-list">
                <li>
                    <div class="order-header"><strong>Pedido #1001</strong> - Fecha: 01/05/2025 - Total: $150.00</div>
                    <div class="order-details">Estado: Enviado</div>
                </li>
                <li>
                    <div class="order-header"><strong>Pedido #985</strong> - Fecha: 20/04/2025 - Total: $75.50</div>
                    <div class="order-details">Estado: Entregado</div>
                </li>
            </ul>
        </div>
        <a href="<?= BASE_URL ?>/perfil.php" class="btn-3" style="margin-top:20px;">Volver al Perfil</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>