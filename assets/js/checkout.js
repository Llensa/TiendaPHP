document.addEventListener('DOMContentLoaded', function () {
    const cuerpoTablaCheckout = document.querySelector('#tabla-checkout-carrito tbody');
    const totalCheckoutElemento = document.getElementById('total-checkout-carrito');
    const formCheckout = document.getElementById('formCheckout');
    const formPago = document.getElementById('formulario-pago');

    const carritoGuardado = localStorage.getItem('miTiendaCarrito');
    let carrito = carritoGuardado ? JSON.parse(carritoGuardado) : [];

    function renderCarritoCheckout() {
        if (!cuerpoTablaCheckout || !totalCheckoutElemento) return;

        if (carrito.length === 0) {
            cuerpoTablaCheckout.innerHTML = '<tr><td colspan="4" style="text-align:center;">No hay productos para procesar.</td></tr>';
            totalCheckoutElemento.textContent = '$0.00';
            if (formPago) formPago.style.display = 'none';
            return;
        }

        let total = 0;
        cuerpoTablaCheckout.innerHTML = ''; // limpia
        carrito.forEach(prod => {
            const subtotal = prod.precio * prod.cantidad;
            total += subtotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${prod.titulo}</td>
                <td style="text-align:center;">${prod.cantidad}</td>
                <td style="text-align:right;">$${prod.precio.toFixed(2)}</td>
                <td style="text-align:right;">$${subtotal.toFixed(2)}</td>
            `;
            cuerpoTablaCheckout.appendChild(row);
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

            // Validación mínima
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
                    alert('🎉 Pedido realizado con éxito. ID: #' + result.pedido_id);
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
});
