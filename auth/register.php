<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../db/db.php';
// require_once '../vendor/autoload.php'; // Ya está en enviar_verificacion.php
include '../includes/header.php';

$errores = [];
$nombre_form = ''; // Para repoblar
$email_form = '';  // Para repoblar

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $nombre_form = $nombre;
    $email_form = $email;

    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($email)) $errores[] = "El correo electrónico es obligatorio.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo electrónico no válido.";
    if (empty($password)) $errores[] = "La contraseña es obligatoria.";
    elseif (strlen($password) < 6) $errores[] = "La contraseña debe tener al menos 6 caracteres.";


    // Verificar si el email ya existe ANTES de intentar insertar
    if (empty($errores)) {
        $stmt_check_email = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt_check_email->execute([$email]);
        if ($stmt_check_email->fetch()) {
            $errores[] = "El correo electrónico ya está registrado. Por favor, intentá con otro o iniciá sesión.";
        }
    }

    if (empty($errores)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));
        $rol_defecto = 'cliente'; // Rol por defecto

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, token, verificado, rol, fecha_registro) VALUES (?, ?, ?, ?, 0, ?, NOW())");
            $stmt->execute([$nombre, $email, $hashed, $token, $rol_defecto]);

            require_once 'enviar_verificacion.php';
            $resultado = enviarCorreoVerificacion($email, $nombre, $token);

            if ($resultado === true) {
                header('Location: mensaje_verifica_correo.php');
                exit;
            } else {
                // Aunque el usuario se registró, el correo falló. Podrías loggear esto.
                $errores[] = "Se creó tu cuenta, pero no se pudo enviar el correo de verificación: $resultado. Contactá a soporte.";
            }
        } catch (PDOException $e) {
            // El error de duplicado ya se debería haber capturado antes. Esto es por si acaso.
            if (str_contains($e->getMessage(), 'Integrity constraint') || $e->errorInfo[1] == 1062) {
                $errores[] = "El correo electrónico ya está registrado.";
            } else {
                $errores[] = "Error en la base de datos. Por favor, intentá más tarde.";
                 error_log("Error DB Register: " . $e->getMessage()); // Log real del error
            }
        }
    }
}
?>

<main class="container">
  <div class="form-autenticacion card-register" id="formRegisterContainer"> <h2>Crear una cuenta</h2>

    <?php if (!empty($errores)): ?>
      <div class="errores-formulario">
        <?php foreach ($errores as $e): ?>
          <p class="error-mensaje"><?= $e ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/auth/register.php" id="formRegister"> <div class="form-grupo">
        <label for="nombreRegister">Nombre completo</label>
        <input type="text" id="nombreRegister" name="nombre" placeholder="Tu nombre" required class="form-control" value="<?= htmlspecialchars($nombre_form) ?>">
        <span class="error-js-mensaje" id="error-nombreRegister"></span>
      </div>

      <div class="form-grupo">
        <label for="emailRegister">Correo electrónico</label>
        <input type="email" id="emailRegister" name="email" placeholder="ejemplo@correo.com" required class="form-control" value="<?= htmlspecialchars($email_form) ?>">
        <span class="error-js-mensaje" id="error-emailRegister"></span>
      </div>

      <div class="form-grupo">
        <label for="passwordRegister">Contraseña</label>
        <input type="password" id="passwordRegister" name="password" placeholder="Mínimo 6 caracteres" required class="form-control">
        <span class="error-js-mensaje" id="error-passwordRegister"></span>
      </div>

      <button type="submit" class="btn-3">Registrarme</button>
      <p>¿Ya tienes una cuenta? <a href="<?= BASE_URL ?>/auth/login.php">Inicia sesión</a></p>
    </form>
  </div>
</main>

<?php include '../includes/footer.php'; ?>