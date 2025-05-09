<?php
require_once '../db/db.php';
include '../includes/header.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['usuario'] = $usuario['id'];
        header('Location: ../perfil.php');
        exit;
    } else {
        $errores[] = "Correo o contraseña incorrectos.";
    }
}
?>

<main class="container">
  <h2>Iniciar Sesión</h2>

  <?php if (!empty($errores)): ?>
    <div class="errores">
      <?php foreach ($errores as $e): ?>
        <p style="color: red;"><?= $e ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <input type="email" name="email" placeholder="Correo electrónico" required><br><br>
    <input type="password" name="password" placeholder="Contraseña" required><br><br>
    <button type="submit" class="btn-3">Entrar</button>
  </form>
</main>

<?php include '../includes/footer.php'; ?>
