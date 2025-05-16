<?php
require_once 'db/db.php';
include 'includes/header.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("
    SELECT id, calle, numero, apartamento, ciudad, provincia, codigo_postal, pais, es_predeterminada 
    FROM direcciones 
    WHERE usuario_id = ?
");
$stmt->execute([$usuario_id]);
$direcciones = $stmt->fetchAll();
?>

<main class="container">
    <div class="page-card">
        <h2>Mis Direcciones</h2>

        <div id="lista-direcciones">
            <?php foreach ($direcciones as $dir): ?>
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
        </div>

        <hr>
        <h3>Agregar / Editar Dirección</h3>

        <form id="form-direccion" class="form-autenticacion">
            <input type="hidden" name="id" id="direccion-id">

            <div class="form-grupo">
                <label for="calle">Calle:</label>
                <input type="text" id="calle" name="calle" required class="form-control">
            </div>
            <div class="form-grupo">
                <label for="num">Número:</label>
                <input type="text" id="num" name="num" required class="form-control">
            </div>
            <div class="form-grupo">
                <label for="apar">Apartamento (opcional):</label>
                <input type="text" id="apar" name="apar" class="form-control">
            </div>
            <div class="form-grupo">
                <label for="ci">Ciudad:</label>
                <input type="text" id="ci" name="ci" required class="form-control">
            </div>
            <div class="form-grupo">
                <label for="prov">Provincia:</label>
                <input type="text" id="prov" name="prov" required class="form-control">
            </div>
            <div class="form-grupo">
                <label for="codigo_postal">Código Postal:</label>
                <input type="text" id="codigo_postal" name="codigo_postal" required class="form-control">
            </div>
            <div class="form-grupo">
                <label for="pais_form">País:</label>
                <input type="text" id="pais_form" name="pais_form" value="Argentina" required class="form-control">
            </div>

            <div class="form-grupo" style="margin-top: 20px;">
                <label class="container-button">
                    <input type="checkbox" id="es_predeterminada" name="es_predeterminada" value="1">
                    <div class="checkmark"></div>
                    <span style="margin-left: 100px;">Establecer como dirección predeterminada</span>
                </label>
            </div>

            <button type="submit" class="btn-3" style="margin-top: 20px;">Guardar Dirección</button>
        </form>
    </div>
</main>

<?php
include 'includes/footer.php';
unset($_SESSION['form_data']); // Limpiar datos del formulario
?>
