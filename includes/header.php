<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/tienda');
}
if (session_status() === PHP_SESSION_NONE) { // Asegurar que la sesión inicie si no lo ha hecho
    session_start();
}
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
</head>
<body>

<header class="header">
  <div class="menu container">
    <a href="<?= BASE_URL ?>/index.php" class="logo">
        <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo Tienda" style="height: 40px;"> </a>
    <input type="checkbox" id="menu-toggle" />
    <label for="menu-toggle" class="menu-icono-label">
      <img src="<?= BASE_URL ?>/assets/images/menu.png" class="menu-icono" alt="Menú Principal">
    </label>

    <nav class="navbar">
      <ul>
        <li><a href="<?= BASE_URL ?>/index.php">Inicio</a></li>
        <?php if (isset($_SESSION['usuario'])): ?>
          <li><a href="<?= BASE_URL ?>/perfil.php">Perfil</a></li>
          <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true): ?>
            <li><a href="<?= BASE_URL ?>/admin/productos.php">Admin</a></li>
          <?php endif; ?>
          <li><a href="<?= BASE_URL ?>/auth/logout.php">Salir</a></li>
        <?php else: ?>
          <li><a href="<?= BASE_URL ?>/auth/login.php">Login</a></li>
          <li><a href="<?= BASE_URL ?>/auth/register.php">Registro</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="submenu-container"> <ul>
            <li class="submenu"> <img src="<?= BASE_URL ?>/assets/images/car.svg" id="icono-carrito" alt="Carrito de Compras">
                <span id="contador-carrito" class="contador-items-carrito">0</span>
                <div id="dropdown-carrito"> <table id="tabla-lista-carrito">
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

  <?php // Slider principal solo en index.php
  // Podrías tener una variable $is_home_page o similar para controlar esto
  // O verificar la URL actual. Para simplificar, se mostrará si estamos en index.php.
  $current_page = basename($_SERVER['PHP_SELF']);
  if ($current_page === 'index.php'):
  ?>
  <div class="header-content container">
      <div class="swiper mySwiper-1">
          <div class="swiper-wrapper">
              <div class="swiper-slide">
                  <div class="header-info">
                      <div class="header-txt">
                          <h1>Audífonos Pro X</h1>
                          <div class="precios">
                              <p class="precio-1">$299.99</p> <p class="precio-2">$249.99</p> </div>
                          <a href="<?= BASE_URL ?>/producto.php?id=1" class="btn-1">Información</a> </div>
                      <div class="header-img">
                          <img src="<?= BASE_URL ?>/assets/images/bg1.png" alt="Audífonos Pro X"> </div>
                  </div>
              </div>
              <div class="swiper-slide">
                  <div class="header-info">
                      <div class="header-txt">
                          <h1>Sonido Inmersivo Z</h1>
                          <div class="precios">
                              <p class="precio-1">$199.99</p>
                              <p class="precio-2">$159.99</p>
                          </div>
                          <a href="<?= BASE_URL ?>/producto.php?id=2" class="btn-1">Información</a>
                      </div>
                      <div class="header-img">
                          <img src="<?= BASE_URL ?>/assets/images/bg2.png" alt="Sonido Inmersivo Z">
                      </div>
                  </div>
              </div>
          </div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-pagination"></div>
      </div>
  </div>
  <?php endif; ?>

</header>