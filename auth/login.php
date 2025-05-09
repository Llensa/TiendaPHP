<?php
require_once '../db/db.php';
include '../includes/header.php'; // header.php llama a session_start()

$errores = [];
$redirect_url = BASE_URL . '/perfil.php'; // Redirección por defecto

if (isset($_GET['redirect'])) {
    $decoded_redirect = urldecode($_GET['redirect']);
    if (substr($decoded_redirect, 0, strlen(BASE_URL)) === BASE_URL || substr($decoded_redirect, 0, 1) === '/') {
        $redirect_url = htmlspecialchars($decoded_redirect, ENT_QUOTES, 'UTF-8');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Por favor, introduce un correo electrónico válido.";
    }
    if (empty($password)) {
        $errores[] = "Por favor, introduce tu contraseña.";
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT id, password FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario'] = $usuario['id'];

            // Administrador por email (solo para pruebas)
            $_SESSION['es_admin'] = ($email === 'admin@example.com');

            header('Location: ' . $redirect_url);
            exit;
        } else {
            $errores[] = "Correo electrónico o contraseña incorrectos.";
        }
    }
}
?>

<main class="container">
  <div class="form-autenticacion card-login">
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($errores)): ?>
      <div class="errores-formulario">
        <?php foreach ($errores as $e): ?>
          <p class="error-mensaje"><?= $e ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/auth/login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>">
      <div class="form-grupo">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="tu@correo.com" required class="form-control" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
      </div>
      <div class="form-grupo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Tu contraseña" required class="form-control">
      </div>
      <button type="submit" class="btn-3">Entrar</button>
      <p>¿No tienes cuenta? <a href="<?= BASE_URL ?>/auth/register.php">Regístrate aquí</a></p>
    </form>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
