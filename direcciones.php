<?php
// Asumimos que db.php y la sesión se manejan de forma centralizada o aquí
// y que BASE_URL se define o se puede definir aquí.
require_once 'db/db.php'; // Asegúrate de que esta ruta sea correcta
// include 'includes/session_handler.php'; // Si tienes un manejador de sesión central

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Definición de BASE_URL ---
// Si no está definida como constante global, la definimos aquí.
// Ajusta esto si BASE_URL ya está definida en un config.php global.
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    // Usar SCRIPT_NAME para ser más robusto con URL rewriting
    $script_path = dirname($_SERVER['SCRIPT_NAME']);
    // Si SCRIPT_NAME es solo "/", dirname devuelve ".", así que ajustamos.
    $path = ($script_path === '.' || $script_path === '/') ? '' : $script_path;
    define('BASE_URL', rtrim($protocol . $host . $path, '/'));
}
// --- Fin Definición de BASE_URL ---

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php'); // Ajusta la ruta a tu login
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Ya no necesitamos cargar las direcciones aquí, JS lo hará.
// $stmt = $pdo->prepare("SELECT id, calle, numero, apartamento, ciudad, provincia, codigo_postal, pais, es_predeterminada FROM direcciones WHERE usuario_id = ? ORDER BY es_predeterminada DESC, id DESC");
// $stmt->execute([$usuario_id]);
// $direcciones = $stmt->fetchAll();

include 'includes/header.php'; // Tu header HTML
?>

<main class="container">
    <div class="page-card">
        <h2>Mis Direcciones</h2>

        <div id="lista-direcciones">
            <p>Cargando direcciones...</p>
        </div>

        <hr class="separator-line" style="margin-top: 30px; margin-bottom: 30px;">
        
        <div id="form-direccion-container">
            <h3 id="form-direccion-titulo">Agregar Nueva Dirección</h3>
            <form id="form-direccion" class="form-autenticacion">
                <input type="hidden" name="id" id="direccion-id"> <div class="form-grupo">
                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle" required class="form-control">
                </div>
                <div class="form-grupo">
                    <label for="num">Número:</label>
                    <input type="text" id="num" name="numero" required class="form-control"> </div>
                <div class="form-grupo">
                    <label for="apar">Apartamento (Dpto, Piso, etc. - opcional):</label>
                    <input type="text" id="apar" name="apartamento" class="form-control"> </div>
                <div class="form-grupo">
                    <label for="ci">Ciudad:</label>
                    <input type="text" id="ci" name="ciudad" required class="form-control"> </div>
                <div class="form-grupo">
                    <label for="prov">Provincia:</label>
                    <input type="text" id="prov" name="provincia" required class="form-control"> </div>
                <div class="form-grupo">
                    <label for="codigo_postal_form">Código Postal:</label> <input type="text" id="codigo_postal_form" name="codigo_postal" required class="form-control">
                </div>
                <div class="form-grupo">
                    <label for="pais_form">País:</label>
                    <input type="text" id="pais_form" name="pais" value="Argentina" required class="form-control"> </div>

                <div class="form-grupo" style="margin-top: 20px;">
                    <label class="container-button"> <input type="checkbox" id="es_predeterminada" name="es_predeterminada_check"> <div class="checkmark"></div>
                        <span style="margin-left: 8px;">Establecer como dirección predeterminada</span> </label>
                </div>

                <button type="submit" class="btn-3 btn-1" style="margin-top: 20px; width: 100%;">Guardar Dirección</button>
                <button type="button" id="btn-cancelar-edicion" class="btn-3 btn-cancelar" style="margin-top: 10px; width: 100%; display: none;">Cancelar Edición</button>
            </form>
        </div>
    </div>
</main>

<?php
include 'includes/footer.php'; // Tu footer HTML
// unset($_SESSION['form_data']); // Limpiar datos del formulario si los usabas para repoblar
?>
<script>
  // Pasar BASE_URL a JavaScript
  const GLOBAL_BASE_URL = "<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>";
</script>
<script src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/js/direcciones.js"></script>