<?php
require_once '../includes/header.php';
?>

<main class="container">
  <div class="form-autenticacion">
    <h2>Revisá tu correo</h2>
    <p>Te enviamos un enlace de verificación a tu correo electrónico.</p>
    <p>Por favor, hacé clic en el enlace para activar tu cuenta antes de iniciar sesión.</p>
    <p>¿Ya verificaste? <a href="<?= BASE_URL ?>/auth/login.php" class="btn-3">Iniciar sesión</a></p>
  </div>
</main>

<?php require_once '../includes/footer.php'; ?>
