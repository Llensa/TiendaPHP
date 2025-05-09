<?php
require_once 'db/db.php';
include 'includes/header.php';

// Obtener los productos desde la base de datos
$stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC LIMIT 8"); // Limitar para no sobrecargar
$productos = $stmt->fetchAll();

// Podrías tener una categoría "Promociones" o "Nuevos"
$stmt_promos = $pdo->query("SELECT * FROM productos WHERE promocion = 1 LIMIT 4"); // Asumiendo una columna 'promocion'
$promociones = $stmt_promos->fetchAll();

$stmt_nuevos = $pdo->query("SELECT * FROM productos ORDER BY id DESC LIMIT 4");
$nuevos_productos = $stmt_nuevos->fetchAll();

?>

<hr class="separator-line">

<?php if (!empty($promociones)): ?>
<section class="promos container" id="lista-promociones">
    <h2>Promociones</h2>
    <div class="categories">
        <?php foreach ($promociones as $promo): ?>
        <div class="categorie">
            <div class="categorie-1">
                <h3><?= htmlspecialchars($promo['nombre']) ?></h3>
                <div class="precios">
                    <p class="precio-2">$<?= number_format($promo['precio'], 2, ',', '.') ?></p>
                </div>
                <button class="agregar-carrito btn-3"
                    data-id="<?= $promo['id'] ?>"
                    data-nombre="<?= htmlspecialchars($promo['nombre']) ?>"
                    data-precio="<?= $promo['precio'] ?>"
                    data-imagen="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($promo['imagen']) ?>">
                    Agregar al carrito
                </button>
            </div>
            <div class="categorie-img">
                <a href="<?= BASE_URL ?>/producto.php?id=<?= $promo['id'] ?>">
                    <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($promo['imagen']) ?>" alt="<?= htmlspecialchars($promo['nombre']) ?>">
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<hr class="separator-line">
<?php endif; ?>


<?php if (!empty($nuevos_productos)): ?>
<section class="products container" id="lista-nuevos">
    <h2>Nuevos Productos</h2>
    <div class="swiper mySwiper-2">
        <div class="swiper-wrapper">
            <?php foreach ($nuevos_productos as $prod): ?>
            <div class="swiper-slide">
                <div class="product">
                    <a href="<?= BASE_URL ?>/producto.php?id=<?= $prod['id'] ?>">
                        <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>">
                    </a>
                    <div class="product-txt">
                        <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
                        <p class="descripcion-corta"><?= substr(htmlspecialchars($prod['descripcion']), 0, 50) . '...' ?></p>
                        <p class="precio">$<?= number_format($prod['precio'], 2, ',', '.') ?></p>
                        <button class="agregar-carrito btn-3"
                            data-id="<?= $prod['id'] ?>"
                            data-nombre="<?= htmlspecialchars($prod['nombre']) ?>"
                            data-precio="<?= $prod['precio'] ?>"
                            data-imagen="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>">
                            Agregar al carrito
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
  <div class="product-grid"> <?php foreach ($productos as $prod): ?>
      <div class="product">
        <a href="<?= BASE_URL ?>/producto.php?id=<?= $prod['id'] ?>">
            <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>">
        </a>
        <div class="product-txt">
          <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
          <p class="descripcion-corta"><?= substr(htmlspecialchars($prod['descripcion']), 0, 50) . '...' ?></p>
          <p class="precio">$<?= number_format($prod['precio'], 2, ',', '.') ?></p>
          <button class="agregar-carrito btn-3"
              data-id="<?= $prod['id'] ?>"
              data-nombre="<?= htmlspecialchars($prod['nombre']) ?>"
              data-precio="<?= $prod['precio'] ?>"
              data-imagen="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($prod['imagen']) ?>">
              Agregar al carrito
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>