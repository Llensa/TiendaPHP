<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/tienda'); // ajustá si tu carpeta tiene otro nombre
}
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tienda de Auriculares</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
</head>
<body>

<header class="header">
  <div class="menu container">
    <a href="<?= BASE_URL ?>/index.php" class="logo">
      <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo">
      <div>
    <ul>
        <li class="submenu">
            <img src="<?= BASE_URL ?>/assets/images/car.svg" id="img-carrito" alt="Carrito">
            <div id="carrito">
                <table id="lista-carrito">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <a href="#" id="vaciar-carrito" class="btn-3">Vaciar carrito</a>
            </div>
        </li>
    </ul>
</div>


    </a>

    <input type="checkbox" id="menu" />
    <label for="menu">
      <img src="<?= BASE_URL ?>/assets/images/menu.png" class="menu-icono" alt="Menú">
    </label>

    <nav class="navbar">
      <ul>
        <li><a href="<?= BASE_URL ?>/index.php">Inicio</a></li>
        <?php if (isset($_SESSION['usuario'])): ?>
          <li><a href="<?= BASE_URL ?>/perfil.php">Perfil</a></li>
          <li><a href="<?= BASE_URL ?>/admin/productos.php">Admin</a></li>
          <li><a href="<?= BASE_URL ?>/auth/logout.php">Salir</a></li>
        <?php else: ?>
          <li><a href="<?= BASE_URL ?>/auth/login.php">Login</a></li>
          <li><a href="<?= BASE_URL ?>/auth/register.php">Registro</a></li>
        <?php endif; ?>
      </ul>
    </nav>
    <div class="submenu">
        <img src="<?= BASE_URL ?>/assets/images/car.svg" id="img-carrito" alt="Carrito" style="cursor:pointer;">
        <div id="carrito">
         <table id="lista-carrito">
          <thead>
          <tr>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Precio</th>
          <th>Cant.</th>
          <th></th>
         </tr>
        </thead>
         <tbody></tbody>
         </table>
         <a href="#" id="vaciar-carrito" class="btn-3">Vaciar carrito</a>
              </div>
    </div>

  </div>
</header>
