<?php
require_once '../db/db.php';
require_once '../includes/header.php';

$mensaje = "";
$token = $_GET['token'] ?? '';

if ($token) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE token = ? AND verificado = 0");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $stmt = $pdo->prepare("UPDATE usuarios SET verificado = 1, token = NULL WHERE id = ?");
        $stmt->execute([$usuario['id']]);
        $mensaje = "¡Cuenta verificada exitosamente! Ya podés iniciar sesión.";
    } else {
        $mensaje = "Token inválido o la cuenta ya fue verificada.";
    }
} else {
    $mensaje = "Falta el token de verificación.";
}
?>

<main class="container">
  <div class="form-autenticacion">
    <h2>Verificación</h2>
    <p><?= htmlspecialchars($mensaje) ?></p>
    <p><a href="<?= BASE_URL ?>/auth/login.php" class="btn-3">Iniciar Sesión</a></p>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
