<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../db/db.php';
include '../includes/header.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($password)) {
        $errores[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email no válido.";
    }

    // Si no hay errores, registrar
    if (empty($errores)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $email, $hashed]);

        $_SESSION['usuario'] = $pdo->lastInsertId();
        header('Location: ../perfil.php');
        exit;
    }
}
?>

<main class="container">
  <h2>Registro</h2>

  <?php if (!empty($errores)): ?>
    <div class="errores">
      <?php foreach ($errores as $e): ?>
        <p style="color: red;"><?= $e ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="nombre" placeholder="Tu nombre" required><br><br>
    <input type="email" name="email" placeholder="Correo electrónico" required><br><br>
    <input type="password" name="password" placeholder="Contraseña" required><br><br>
    <button type="submit" class="btn-3">Registrarme</button>
  </form>
</main>

<?php include '../includes/footer.php'; ?>
