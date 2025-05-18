<?php
require_once 'db/db.php';
include 'includes/header.php';

// Productos normales
$stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC LIMIT 8");
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