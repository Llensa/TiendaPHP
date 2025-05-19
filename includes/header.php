<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/tienda');
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tienda de Auriculares</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
  <?php
    // Cargar admin.css solo en páginas de admin
    if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
        echo '<link rel="stylesheet" href="' . BASE_URL . '/assets/css/admin.css">';
    }
  ?>
   <script>
    const BASE_URL = "<?= BASE_URL ?>";
  </script>
</head>
<body>

<div class="header-top">
  <div class="menu container">
    <a href="<?= BASE_URL ?>/index.php" class="logo">
        <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo Tienda" style="height: 40px;">
    </a>

    <input type="checkbox" id="menu-toggle" />
    <label for="menu-toggle" class="menu-icono-label">
      <img src="<?= BASE_URL ?>/assets/images/menu.png" class="menu-icono" alt="Menú Principal">
    </label>

    <nav class="navbar">
      <ul>
        <li><a href="<?= BASE_URL ?>/index.php">Inicio</a></li>
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <li><a href="<?= BASE_URL ?>/perfil.php">Perfil</a></li>
          <?php if (!empty($_SESSION['es_admin'])): ?>
            <li><a href="<?= BASE_URL ?>/admin/indexAdmin.php">Admin</a></li>
          <?php endif; ?>
          <li><a href="<?= BASE_URL ?>/auth/logout.php">Salir</a></li>
        <?php else: ?>
          <li><a href="<?= BASE_URL ?>/auth/login.php">Login</a></li>
          <li><a href="<?= BASE_URL ?>/auth/register.php">Registro</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="submenu-container">
        <ul>
            <li class="submenu">
                <img src="<?= BASE_URL ?>/assets/images/car.svg" id="icono-carrito" alt="Carrito de Compras">
                <span id="contador-carrito" class="contador-items-carrito">0</span>
                <div id="dropdown-carrito">
                    <table id="tabla-lista-carrito">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Cant.</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                    <p class="total-carrito-p">Total: <span id="carrito-total-precio">$0.00</span></p>
                    <div class="carrito-botones">
                        <a href="#" id="btn-vaciar-carrito" class="btn-3 vaciar">Vaciar carrito</a>
                        <a href="<?= BASE_URL ?>/checkout.php" id="btn-proceder-pago" class="btn-3 pagar" style="display:none;">Proceder al Pago</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
  </div>
</div>

<?php if (!empty($promociones)): ?>
<section class="swiper-container container promo-slider">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <?php foreach ($promociones as $promo): 
                $descuento = $promo['descuento'];
                $precioOriginal = $promo['precio'];
                $precioFinal = $precioOriginal - ($precioOriginal * $descuento / 100);
            ?>
            <div class="swiper-slide">
                <div class="slider-content">
                    <div class="slider-info">
                        <h2><?= htmlspecialchars($promo['nombre']) ?></h2>
                        <p>
                            <del>$<?= number_format($precioOriginal, 2) ?></del>
                            <strong style="color:#a972ff;">$<?= number_format($precioFinal, 2) ?></strong>
                        </p>
                        <a href="<?= BASE_URL ?>/producto.php?id=<?= $promo['id'] ?>" class="btn-3">Información</a>
                    </div>
                    <div class="slider-img">
                        <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($promo['imagen']) ?>" alt="<?= htmlspecialchars($promo['nombre']) ?>">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>
<?php endif; ?>