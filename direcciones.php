<?php
require_once 'db/db.php';


include 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dir = trim($_POST['calle'] ?? '');
    $num = trim($_POST['numero'] ?? '');
    $apar = trim($_POST['apartamento'] ?? '');
    $ci = trim($_POST['ciudad'] ?? '');
    $pais = trim($_POST['pais'] ?? '');
    $prov = trim($_POST['provincia'] ?? '');
    $cp = trim($_POST['codigo_postal'] ?? '');
    
    if ($dir) {
        $stmt = $pdo->prepare("INSERT INTO direcciones (usuario_id, calle, numero, apartamento, ciudad, pais, provincia, codigo_postal) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['usuario'], $dir,$num, $apar, $ci, $pais, $prov, $cp]);
        $_SESSION['mensaje_exito'] = "Dirección guardada correctamente.";
        header("Location: direcciones.php");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM direcciones WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario']]);
$direcciones = $stmt->fetchAll();
?>

<main class="container">
    <div class="page-card">
        <h2>Mis Direcciones</h2>

        <?php if (!empty($direcciones)): ?>
            <ul>
                <?php foreach ($direcciones as $d): ?>
                    <li><strong><?= htmlspecialchars($d['direccion']) ?></strong>, <?= $d['ciudad'] ?> (<?= $d['codigo_postal'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No tienes direcciones guardadas aún.</p>
        <?php endif; ?>

        <hr>
        <form method="POST" class="form-autenticacion">
            <h3>Agregar Dirección</h3>
            <input type="text" name="direccion" placeholder="Dirección completa" required class="form-control">
            <input type="text" name="num" placeholder="numero de casa" class="form-control">
            <input type="text" name="codigo_postal" placeholder="Código Postal" class="form-control">
            <input type="text" name="apar" placeholder="apartamento(opcional)" class="form-control">
            <input type="text" name="ci" placeholder="Ciudad" class="form-control">
            <input type="text" name="Pais" placeholder="Pais" class="form-control">
            <input type="text" name="Prov" placeholder="Provincia" class="form-control">
            <input type="text" name="codigo_postal" placeholder="Código Postal" class="form-control">

            <button type="submit" class="btn-3">Guardar</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>