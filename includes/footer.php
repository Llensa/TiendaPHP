<footer class="footer-section"> <div class="footer-content container">
    <div class="footer-links">
      <h4>Enlaces Rápidos</h4>
      <ul>
        <li><a href="<?= BASE_URL ?>/index.php">Inicio</a></li>
        <li><a href="#">Política de privacidad</a></li>
        <li><a href="#">Términos y condiciones</a></li>
        <li><a href="#">Sobre Nosotros</a></li>
      </ul>
    </div>
    <div class="footer-social">
      <h4>Síguenos</h4>
      <ul>
        <li><a href="#" target="_blank"><img src="<?= BASE_URL ?>/assets/images/facebook.png" alt="Facebook"></a></li>
        <li><a href="#" target="_blank"><img src="<?= BASE_URL ?>/assets/images/X.png" alt="X"></a></li>
        <li><a href="#" target="_blank"><img src="<?= BASE_URL ?>/assets/images/instagram.png" alt="Instagram"></a></li>
      </ul>
    </div>
    <div class="footer-info">
        <h4>&copy; <?= date('Y') ?> Tu Tienda de Auriculares</h4>
        <p>Todos los derechos reservados.</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
<script src="<?= BASE_URL ?>/assets/js/validaciones.js"></script>
<?php
     $current_page_admin = basename($_SERVER['PHP_SELF']);
     if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true && ($current_page_admin === 'productos.php' || $current_page_admin === 'editar.php')) {
         echo '<script src="' . BASE_URL . '/assets/js/dragdrop.js"></script>';
     }
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['usuario'])): ?>
    <script src="<?= BASE_URL ?>/assets/js/deseos.js"></script>
<?php endif; ?>



</body>
</html>