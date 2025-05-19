<?php
require_once 'db/db.php';
include 'includes/header.php';

// Productos normales
$por_pagina = 12;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $por_pagina;

$stmt = $pdo->prepare("SELECT * FROM productos ORDER BY id DESC LIMIT ?, ?");
$stmt->bindValue(1, $inicio, PDO::PARAM_INT);
$stmt->bindValue(2, $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll();

// Productos en promoción para el slider
$stmt_slider = $pdo->query("SELECT * FROM productos WHERE promocion = 1 AND descuento > 0 ORDER BY id DESC LIMIT 5");
$slider_promos = $stmt_slider->fetchAll();

// Nuevos productos
$stmt_nuevos = $pdo->query("SELECT * FROM productos ORDER BY id DESC LIMIT 4");
$nuevos_productos = $stmt_nuevos->fetchAll();
?>

<?php if (!empty($slider_promos)): ?>
<section class="swiper swiperHero">
  <div class="swiper-wrapper">
    <?php foreach ($slider_promos as $promo): 
      $tienePromo = $promo['promocion'] && $promo['descuento'] > 0;
      $precioFinal = $tienePromo ? $promo['precio'] * (1 - $promo['descuento'] / 100) : $promo['precio'];
    ?>
      <div class="swiper-slide">
        <div class="swiper-slide-inner">
          <div class="swiper-slide-texto">
            <h2><?= htmlspecialchars($promo['nombre']) ?></h2>

            <?php if ($tienePromo): ?>
              <p class="precio-original">$<?= number_format($promo['precio'], 2, ',', '.') ?></p>
              <p class="precio-descuento">
                $<?= number_format($precioFinal, 2, ',', '.') ?>
                <span class="etiqueta-descuento">-<?= $promo['descuento'] ?>%</span>
              </p>
            <?php else: ?>
              <p class="precio">$<?= number_format($promo['precio'], 2, ',', '.') ?></p>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/producto.php?id=<?= $promo['id'] ?>" class="btn-3">Información</a>
          </div>
          <div class="swiper-slide-imagen">
            <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($promo['imagen']) ?>" alt="<?= htmlspecialchars($promo['nombre']) ?>">
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="swiper-button-next"></div>
  <div class="swiper-button-prev"></div>
</section>
<?php endif; ?>


<hr class="separator-line">

<?php if (!empty($nuevos_productos)): ?>
<section class="products container" id="lista-nuevos">
  <h2>Nuevos Productos</h2>
  <div class="swiper mySwiper-2">
    <div class="swiper-wrapper">
      <?php foreach ($nuevos_productos as $prod): ?>
        <?php
          $precioFinal = $prod['precio'];
          if ($prod['promocion'] && $prod['descuento'] > 0) {
              $precioFinal = $prod['precio'] * (1 - $prod['descuento'] / 100);
          }
        ?>
        <div class="swiper-slide">
          <div class="product">
            <a href="<?= BASE_URL ?>/producto.php?id=<?= $prod['id'] ?>">
              <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>">
            </a>
            <div class="product-txt">
              <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
              <p class="descripcion-corta"><?= substr(htmlspecialchars($prod['descripcion']), 0, 50) . '...' ?></p>
              <?php if ($prod['promocion'] && $prod['descuento'] > 0): ?>
                <p class="precio">
                  <span class="precio-original">$<?= number_format($prod['precio'], 2, ',', '.') ?></span>
                  <span class="precio-descuento">$<?= number_format($precioFinal, 2, ',', '.') ?></span>
                  <span class="etiqueta-descuento">-<?= $prod['descuento'] ?>%</span>
                </p>
              <?php else: ?>
                <p class="precio">$<?= number_format($prod['precio'], 2, ',', '.') ?></p>
              <?php endif; ?>
              <button class="agregar-carrito btn-3"
                  data-id="<?= $prod['id'] ?>"
                  data-nombre="<?= htmlspecialchars($prod['nombre']) ?>"
                  data-precio="<?= $precioFinal ?>"
                  data-imagen="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>">
                  Agregar al carrito
              </button>
              <button class="btn-3 btn-deseo btn-agregar-deseo" data-id="<?= $prod['id'] ?>">
                🤍 Agregar a Deseos
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>
</section>
<hr class="separator-line">
<?php endif; ?>

<section class="products container" id="lista-general-productos">
  <h2>Nuestros Productos</h2>
  <div class="product-filters">
  <form method="GET" action="">
    <div class="filter-row">
      <div class="input-group">
        <svg viewBox="0 0 24 24" aria-hidden="true" class="search-icon">
          <path
            d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"
          />
        </svg>
        <input type="text" name="buscar" class="input-search" placeholder="Buscar producto..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
      </div>

      <select name="orden" class="select-orden">
        <option value="">Ordenar por</option>
        <option value="precio_asc" <?= ($_GET['orden'] ?? '') === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
        <option value="precio_desc" <?= ($_GET['orden'] ?? '') === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
      </select>

      <button type="submit" class="btn-3">Aplicar</button>
    </div>
  </form>
</div>
  <div class="product-grid">
    <?php foreach ($productos as $prod): ?>
      <?php
        $precioFinal = $prod['precio'];
        if ($prod['promocion'] && $prod['descuento'] > 0) {
            $precioFinal = $prod['precio'] * (1 - $prod['descuento'] / 100);
        }
      ?>
      <div class="product">
        <a href="<?= BASE_URL ?>/producto.php?id=<?= $prod['id'] ?>">
          <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>">
        </a>
        <div class="product-txt">
          <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
          <p class="descripcion-corta"><?= substr(htmlspecialchars($prod['descripcion']), 0, 50) . '...' ?></p>
          <?php if ($prod['promocion'] && $prod['descuento'] > 0): ?>
            <p class="precio">
              <span class="precio-original">$<?= number_format($prod['precio'], 2, ',', '.') ?></span>
              <span class="precio-descuento">$<?= number_format($precioFinal, 2, ',', '.') ?></span>
              <span class="etiqueta-descuento">-<?= $prod['descuento'] ?>%</span>
            </p>
          <?php else: ?>
            <p class="precio">$<?= number_format($prod['precio'], 2, ',', '.') ?></p>
          <?php endif; ?>
          <button class="agregar-carrito btn-3"
              data-id="<?= $prod['id'] ?>"
              data-nombre="<?= htmlspecialchars($prod['nombre']) ?>"
              data-precio="<?= $precioFinal ?>"
              data-imagen="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>">
              Agregar al carrito
          </button>
          <button class="btn-3 btn-deseo btn-agregar-deseo" data-id="<?= $prod['id'] ?>">
            🤍 Agregar a Deseos
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>