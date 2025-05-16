document.addEventListener('DOMContentLoaded', function () {
    // Cambiado para apuntar al nuevo contenedor de div
    const checkoutProductosContainer = document.querySelector('#checkout-productos');
    const totalCheckoutElemento = document.getElementById('total-checkout-carrito');
    const formCheckout = document.getElementById('formCheckout');
    const formPago = document.getElementById('formulario-pago');
    const direccionGuardada = document.getElementById('direccionGuardada');

    const carritoGuardado = localStorage.getItem('miTiendaCarrito');
    let carrito = carritoGuardado ? JSON.parse(carritoGuardado) : [];

  function renderCarritoCheckout() {
  if (!checkoutProductosContainer || !totalCheckoutElemento) return;

  if (carrito.length === 0) {
    checkoutProductosContainer.innerHTML = '<div class="checkout-empty-message">No hay productos en el carrito.</div>';
    totalCheckoutElemento.textContent = '$0.00';
    if (formPago) formPago.style.display = 'none';
    return;
  }

  let total = 0;
  checkoutProductosContainer.innerHTML = ''; // Limpiar contenido previo

  carrito.forEach(item => {
    const subtotal = item.precio * item.cantidad;
    total += subtotal;

    const productItemDiv = document.createElement('div');
    productItemDiv.classList.add('checkout-item');

    // HTML del item en el checkout
    productItemDiv.innerHTML = `
      <div class="checkout-col-product">
        <img src="${item.imagen}" alt="${item.titulo}" class="checkout-img">
        <div class="checkout-info">
          <p class="checkout-title">${item.titulo}</p>
        </div>
      </div>
      <div class="checkout-col-quantity">${item.cantidad}</div>
      <div class="checkout-col-price">$${item.precio.toFixed(2)}</div>
      <div class="checkout-col-subtotal">$${subtotal.toFixed(2)}</div>
    `;

    checkoutProductosContainer.appendChild(productItemDiv);
  });

  totalCheckoutElemento.textContent = `$${total.toFixed(2)}`;
  if (formPago) formPago.style.display = 'block';
}


    renderCarritoCheckout();

    if (formCheckout) {
        formCheckout.addEventListener('submit', async function (e) {
            e.preventDefault();

            const nombre = document.getElementById('nombreCompleto').value.trim();
            const direccion = document.getElementById('direccionEnvio').value.trim();
            const ciudad = document.getElementById('ciudad').value.trim();
            const codigoPostal = document.getElementById('codigoPostal').value.trim();
            const numeroTarjeta = document.getElementById('numeroTarjeta').value.trim();
            const mes = document.getElementById('mesExp').value.trim();
            const anio = document.getElementById('anioExp').value.trim();
            const cvc = document.getElementById('cvc').value.trim();

            if (!nombre || !direccion || !ciudad || !codigoPostal || !numeroTarjeta || !mes || !anio || !cvc || carrito.length === 0) {
                alert('🛑 Completá todos los campos y asegurate de tener productos en el carrito.');
                return;
            }

            if (!/^\d{2}$/.test(mes) || parseInt(mes) < 1 || parseInt(mes) > 12) {
                alert('🗓️ Mes de expiración inválido. Usá formato MM.');
                return;
            }

            const anioActual = new Date().getFullYear();
            if (!/^\d{4}$/.test(anio) || parseInt(anio) < anioActual) {
                alert('🗓️ Año inválido. Debe ser 4 dígitos y no estar en el pasado.');
                return;
            }

            if (!/^\d{3,4}$/.test(cvc)) {
                alert('🔐 CVC inválido. Usá 3 o 4 dígitos.');
                return;
            }

            const datos = {
                nombreCompleto: nombre,
                direccionEnvio: direccion,
                ciudad,
                codigoPostal,
                carrito
            };

            try {
                const res = await fetch('procesar_pedido.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datos)
                });

                const result = await res.json();

                if (result.status === 'ok') {
                    let resumen = carrito.map(p => `🛒 ${p.nombre} x${p.cantidad} - $${(p.precio * p.cantidad).toFixed(2)}`).join('\n');
                    alert(`🎉 Pedido realizado con éxito. ID: #${result.pedido_id}\n\nResumen:\n${resumen}`);
                    localStorage.removeItem('miTiendaCarrito');
                    window.location.href = BASE_URL + '/pedidos.php?status=exito';
                } else {
                    alert('❌ Error: ' + (result.msg || 'Hubo un problema al procesar tu pedido.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('⚠️ Error al conectar con el servidor. Intenta de nuevo.');
            }
        });
    }

    if (direccionGuardada) {
        direccionGuardada.addEventListener('change', () => {
            const valor = direccionGuardada.value;
            if (valor) {
                const [dir, ciudad, cp] = valor.split('|');
                document.getElementById('direccionEnvio').value = dir;
                document.getElementById('ciudad').value = ciudad;
                document.getElementById('codigoPostal').value = cp;
            }
        });
    }
});