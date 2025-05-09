<?php
require_once '../db/db.php';
include '../includes/header.php'; // header.php llama a session_start()

$errores = [];
$redirect_url = BASE_URL . '/perfil.php'; // Default redirect

if (isset($_GET['redirect'])) {
    // Basic validation for redirect URL to prevent open redirect vulnerabilities
    // Ensure it's a relative path within your site or starts with your BASE_URL
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
        // $stmt = $pdo->prepare("SELECT id, password, rol FROM usuarios WHERE email = ?"); // Si tienes columna 'rol'
        $stmt = $pdo->prepare("SELECT id, password FROM usuarios WHERE email = ?"); // Original
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario'] = $usuario['id'];
            
            // --- Lógica de Administrador (EJEMPLO) ---
            // DEBES ADAPTAR ESTO. Por ejemplo, si tienes una columna 'rol' en tu tabla 'usuarios':
            // if ($usuario['rol'] === 'admin') {
            //     $_SESSION['es_admin'] = true;
            // }
            // Ejemplo simple basado en email (SOLO PARA PRUEBAS, NO PARA PRODUCCIÓN):
            if ($email === 'admin@example.com') {
                $_SESSION['es_admin'] = true;
            } else {
                $_SESSION['es_admin'] = false; // Asegurarse de que no quede de una sesión anterior
            }
            // --- Fin Lógica de Administrador ---

            header('Location: ' . $redirect_url);
            exit;
        } else {
            $errores[] = "Correo electrónico o contraseña incorrectos.";
        }
    }
}
?>

<main class="container">
  <h2>Iniciar Sesión</h2>

  <?php if (!empty($errores)): ?>
    <div class="errores-formulario">
      <?php foreach ($errores as $e): ?>
        <p class="error-mensaje"><?= $e ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_URL ?>/auth/login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" class="form-autenticacion">
    <div class="form-grupo">
      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" placeholder="tu@correo.com" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" class="form-control">
    </div>
    <div class="form-grupo">
      <label for="password">Contraseña:</label>
      <input type="password" id="password" name="password" placeholder="Tu contraseña" required class="form-control">
    </div>
    <button type="submit" class="btn-3">Entrar</button>
  </form>
  <p>¿No tienes cuenta? <a href="<?= BASE_URL ?>/auth/register.php">Regístrate aquí</a></p>
</main>

<?php include '../includes/footer.php'; ?>