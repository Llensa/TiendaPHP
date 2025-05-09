<?php
require_once 'db/db.php';
include 'includes/header.php';

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: auth/login.php');
    exit;
}

// Carga los datos del usuario desde la base
$stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario']]);
$usuario = $stmt->fetch();
?>

<main class="container">
  <h2>Mi Perfil</h2>
  <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
  <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
  <p><a href="auth/logout.php" class="btn-3">Cerrar Sesión</a></p>
</main>

<?php include 'includes/footer.php'; ?>

