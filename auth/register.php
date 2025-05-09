<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../db/db.php';
require_once '../vendor/autoload.php';
include '../includes/header.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        $errores[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo electrónico no válido.";
    }

    if (empty($errores)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, token, verificado) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$nombre, $email, $hashed, $token]);

            require_once 'enviar_verificacion.php';
            $resultado = enviarCorreoVerificacion($email, $nombre, $token);

            if ($resultado === true) {
                header('Location: mensaje_verifica_correo.php');
                exit;
            } else {
                $errores[] = "No se pudo enviar el correo de verificación: $resultado";
            }
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Integrity constraint')) {
                $errores[] = "El correo ya está registrado.";
            } else {
                $errores[] = "Error en la base de datos: " . $e->getMessage();
            }
        }
    }
}
?>

<main class="container">
  <div class="form-autenticacion card-register">
    <h2>Crear una cuenta</h2>

    <?php if (!empty($errores)): ?>
      <div class="errores-formulario">
        <?php foreach ($errores as $e): ?>
          <p class="error-mensaje"><?= $e ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-grupo">
        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required class="form-control" value="<?= isset($nombre) ? htmlspecialchars($nombre) : '' ?>">
      </div>

      <div class="form-grupo">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required class="form-control" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
      </div>

      <div class="form-grupo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Tu contraseña" required class="form-control">
      </div>

      <button type="submit" class="btn-3">Registrarme</button>
      <p>¿Ya tienes una cuenta? <a href="<?= BASE_URL ?>/auth/login.php">Inicia sesión</a></p>
    </form>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
