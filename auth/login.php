<?php
require_once '../db/db.php';
include '../includes/header.php';

$errores = [];
$redirect_url = BASE_URL . '/perfil.php';

if (isset($_GET['redirect'])) {
    $decoded_redirect = urldecode($_GET['redirect']);
    if (substr($decoded_redirect, 0, strlen(BASE_URL)) === BASE_URL || substr($decoded_redirect, 0, 1) === '/') {
        $redirect_url = htmlspecialchars($decoded_redirect, ENT_QUOTES, 'UTF-8');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $email_form = $email; // Para repoblar el campo

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Por favor, introduce un correo electrónico válido.";
    }
    if (empty($password)) {
        $errores[] = "Por favor, introduce tu contraseña.";
    }

    if (empty($errores)) {
        // Incluir 'rol' y 'nombre' en la consulta
        $stmt = $pdo->prepare("SELECT id, nombre, password, rol, verificado FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            if ($usuario['verificado'] == 1) {
                if (password_verify($password, $usuario['password'])) {
                    $_SESSION['usuario'] = $usuario['id'];
                    $_SESSION['nombre_usuario'] = $usuario['nombre']; // Guardar nombre para saludos
                    $_SESSION['es_admin'] = ($usuario['rol'] === 'admin'); // Establecer si es admin
                    
                    header('Location: ' . $redirect_url);
                    exit;
                } else {
                    $errores[] = "Correo electrónico o contraseña incorrectos.";
                }
            } else {
                $errores[] = "Tu cuenta aún no ha sido verificada. Por favor, revisá tu correo electrónico.";
            }
        } else {
            $errores[] = "Correo electrónico o contraseña incorrectos.";
        }
    }
}
?>

<main class="container">
  <div class="form-autenticacion card-login" id="formLoginContainer"> <h2>Iniciar Sesión</h2>

    <?php if (!empty($errores)): ?>
      <div class="errores-formulario">
        <?php foreach ($errores as $e): ?>
          <p class="error-mensaje"><?= $e ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/auth/login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" id="formLogin"> <div class="form-grupo">
        <label for="emailLogin">Correo electrónico</label>
        <input type="email" id="emailLogin" name="email" placeholder="tu@correo.com" required class="form-control" value="<?= htmlspecialchars($email_form ?? '') ?>">
        <span class="error-js-mensaje" id="error-emailLogin"></span>
      </div>
      <div class="form-grupo">
        <label for="passwordLogin">Contraseña</label>
        <input type="password" id="passwordLogin" name="password" placeholder="Tu contraseña" required class="form-control">
        <span class="error-js-mensaje" id="error-passwordLogin"></span>
      </div>
      <button type="submit" class="btn-3">Entrar</button>
      <p>¿No tienes cuenta? <a href="<?= BASE_URL ?>/auth/register.php">Regístrate aquí</a></p>
    </form>
  </div>
</main>

<?php include '../includes/footer.php'; ?>