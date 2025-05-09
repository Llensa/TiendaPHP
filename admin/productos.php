<?php
require_once '../db/db.php';
include '../includes/header.php';

// Insertar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $imagen = $_POST['imagen'];

    if ($nombre && $precio && $imagen) {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $imagen]);
        header("Location: productos.php");
        exit;
    } else {
        echo "<p style='color:red;'>Todos los campos son obligatorios</p>";
    }
}

$productos = $pdo->query("SELECT * FROM productos")->fetchAll();
?>

<link rel="stylesheet" href="../assets/css/admin.css">

<main class="container">
  <h2>Administrar Productos</h2>

  <h3>Nuevo Producto</h3>
  <form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required><br><br>
    <textarea name="descripcion" placeholder="Descripción" required></textarea><br><br>
    <input type="number" name="precio" placeholder="Precio" required step="0.01"><br><br>

    <div id="drop-zone" class="drop-zone">
      Arrastra una imagen aquí o haz clic para seleccionar
      <input type="file" id="imagen" hidden>
    </div>
    <input type="hidden" name="imagen" id="imagenGuardada"><br>

    <button type="submit" class="btn-3">Agregar</button>
  </form>

  <hr>

  <h3>Productos Existentes</h3>
  <div class="grid">
    <?php foreach ($productos as $prod): ?>
      <div class="product">
        <img src="../assets/images/<?= $prod['imagen'] ?>" alt="<?= $prod['nombre'] ?>">
        <h3><?= $prod['nombre'] ?></h3>
        <p>$<?= $prod['precio'] ?></p>
        <a href="editar.php?id=<?= $prod['id'] ?>" class="btn-3">Editar</a>
        <a href="eliminar.php?id=<?= $prod['id'] ?>" class="btn-3" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<script src="../assets/js/dragdrop.js"></script>

<?php include '../includes/footer.php';
$stmt = $pdo->prepare("SELECT c.contenido, c.creado_en, u.nombre
FROM comentarios c
JOIN usuarios u ON c.usuario_id = u.id
WHERE c.producto_id = ?
ORDER BY c.creado_en DESC");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();
?>


