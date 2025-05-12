<?php
require_once '../db/db.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario']) || !$_SESSION['es_admin']) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$stmt = $pdo->query("
    SELECT c.*, u.nombre AS usuario, p.nombre AS producto
    FROM comentarios c
    JOIN usuarios u ON c.usuario_id = u.id
    JOIN productos p ON c.producto_id = p.id
    ORDER BY c.creado_en DESC
");
$comentarios = $stmt->fetchAll();
?>

<main class="container">
  <h2>Moderación de Comentarios</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Usuario</th><th>Producto</th><th>Comentario</th><th>Fecha</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($comentarios as $c): ?>
        <tr>
          <td><?= $c['usuario'] ?></td>
          <td><?= $c['producto'] ?></td>
          <td><?= nl2br(htmlspecialchars($c['contenido'])) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($c['creado_en'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

<?php include '../includes/footer.php'; ?>
