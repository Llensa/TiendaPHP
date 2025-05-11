<?php
require_once 'db/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode(BASE_URL . '/detalles_cuenta.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario']]);
$usuario = $stmt->fetch();

// Lógica para actualizar detalles iría aquí si se envía un formulario
?>
<main class="container">
    <div class="page-card form-autenticacion" style="max-width: 600px;"> <h2>Detalles de mi Cuenta</h2>
        <form method="POST" action="#"> <div class="form-grupo">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                <span class="error-js-mensaje" id="error-nombre-detalle"></span>
            </div>
            <div class="form-grupo">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                 <span class="error-js-mensaje" id="error-email-detalle"></span>
            </div>
            <div class="form-grupo">
                <label for="password_actual">Contraseña Actual (para cambios):</label>
                <input type="password" id="password_actual" name="password_actual" class="form-control">
            </div>
            <div class="form-grupo">
                <label for="nueva_password">Nueva Contraseña (opcional):</label>
                <input type="password" id="nueva_password" name="nueva_password" class="form-control">
                 <span class="error-js-mensaje" id="error-nueva-password"></span>
            </div>
             <div class="form-grupo">
                <label for="confirmar_nueva_password">Confirmar Nueva Contraseña:</label>
                <input type="password" id="confirmar_nueva_password" name="confirmar_nueva_password" class="form-control">
            </div>
            <button type="submit" class="btn-3">Guardar Cambios</button>
        </form>
        <a href="<?= BASE_URL ?>/perfil.php" class="btn-3 btn-cancelar" style="margin-top:20px; display:block; text-align:center;">Volver al Perfil</a>
    </div>
</main>
<?php include 'includes/footer.php'; ?>