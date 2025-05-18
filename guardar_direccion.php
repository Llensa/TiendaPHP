<?php
require_once 'db/db.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$calle = trim($_POST['calle'] ?? '');
$numero = trim($_POST['num'] ?? '');
$apartamento = trim($_POST['apar'] ?? '');
$ciudad = trim($_POST['ci'] ?? '');
$provincia = trim($_POST['prov'] ?? '');
$codigo_postal = trim($_POST['codigo_postal'] ?? '');
$pais = trim($_POST['pais_form'] ?? 'Argentina');
$es_predeterminada = isset($_POST['es_predeterminada']) ? 1 : 0;

// Si se marca como predeterminada, limpiamos las otras
if ($es_predeterminada) {
    $pdo->prepare("UPDATE direcciones SET es_predeterminada = 0 WHERE usuario_id = ?")
        ->execute([$usuario_id]);
}

if (!empty($_POST['id'])) {
    // Editar dirección
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("UPDATE direcciones 
        SET calle=?, numero=?, apartamento=?, ciudad=?, provincia=?, codigo_postal=?, pais=?, es_predeterminada=? 
        WHERE id=? AND usuario_id=?");
    $stmt->execute([$calle, $numero, $apartamento, $ciudad, $provincia, $codigo_postal, $pais, $es_predeterminada, $id, $usuario_id]);
} else {
    // Agregar dirección
    $stmt = $pdo->prepare("INSERT INTO direcciones 
        (usuario_id, calle, numero, apartamento, ciudad, provincia, codigo_postal, pais, es_predeterminada) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $calle, $numero, $apartamento, $ciudad, $provincia, $codigo_postal, $pais, $es_predeterminada]);
}

// Devolvemos toda la vista nuevamente
$stmt = $pdo->prepare("SELECT * FROM direcciones WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$direcciones = $stmt->fetchAll();

foreach ($direcciones as $dir): ?>
<div class="direccion-card" data-id="<?= $dir['id'] ?>">
    <h4>Dirección:</h4>
    <p><?= htmlspecialchars($dir['calle']) ?> <?= htmlspecialchars($dir['numero']) ?><?= $dir['apartamento'] ? ', ' . htmlspecialchars($dir['apartamento']) : '' ?></p>
    <p><?= htmlspecialchars($dir['ciudad']) ?>, <?= htmlspecialchars($dir['provincia']) ?>, <?= htmlspecialchars($dir['pais']) ?> - CP: <?= htmlspecialchars($dir['codigo_postal']) ?></p>
    <?php if (!empty($dir['es_predeterminada'])): ?>
        <small><strong>✔ Dirección Predeterminada</strong></small>
    <?php endif; ?>
    <div class="acciones">
        <button class="btn-direccion editar btn-editar-direccion" data-id="<?= $dir['id'] ?>">Editar</button>
        <button class="btn-direccion eliminar btn-eliminar-direccion" data-id="<?= $dir['id'] ?>">Eliminar</button>
    </div>
</div>
<?php endforeach; ?>