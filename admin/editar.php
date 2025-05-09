<?php
require_once '../db/db.php';
include '../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) exit("ID no válido");

$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$prod = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=? WHERE id=?");
    $stmt->execute([$nombre, $descripcion, $precio, $id]);
    header("Location: productos.php");
    exit;
}
?>

<main class="container">
  <h2>Editar Producto</h2>
  <form method="POST">
    <input type="text" name="nombre" value="<?= $prod['nombre'] ?>" required><br><br>
    <textarea name="descripcion" required><?= $prod['descripcion'] ?></textarea><br><br>
    <input type="number" name="precio" value="<?= $prod['precio'] ?>" step="0.01" required><br><br>
    <button type="submit" class="btn-3">Guardar Cambios</button>
  </form>
</main>

<?php include '../includes/footer.php'; ?>
