document.addEventListener('DOMContentLoaded', function () {
    const checkoutProductosContainer = document.querySelector('#checkout-productos');
    const totalCheckoutElemento = document.getElementById('total-checkout-carrito');
    const formCheckout = document.getElementById('formCheckout');
    const formPagoContainer = document.getElementById('formulario-pago'); // Contenedor del formulario
    const direccionGuardadaSelect = document.getElementById('direccionGuardada');

    const inputNombreCompleto = document.getElementById('nombreCompleto');
    const inputDireccionEnvio = document.getElementById('direccionEnvio');
    const inputCiudad = document.getElementById('ciudad');
    const inputCodigoPostal = document.getElementById('codigoPostal');
    const inputNumeroTarjeta = document.getElementById('numeroTarjeta');
    const inputMesExp = document.getElementById('mesExp');
    const inputAnioExp = document.getElementById('anioExp');
    const inputCvc = document.getElementById('cvc');
    const submitButton = formCheckout ? formCheckout.querySelector('button[type="submit"]') : null;


    const carritoGuardado = localStorage.getItem('miTiendaCarrito');
    let carrito = carritoGuardado ? JSON.parse(carritoGuardado) : [];

    // ---------------------
    // HELPERS PARA ERRORES
    // ---------------------
    function mostrarError(inputElement, mensaje) {
        const errorSpan = inputElement.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains('error-js-mensaje')) {
            errorSpan.textContent = mensaje;
            inputElement.classList.add('input-error'); // Opcional: para estilizar el input
        } else {
            // Fallback si no hay span (aunque deberían estar)
            console.warn("No se encontró span de error para:", inputElement);
            alert(mensaje); // Mantener alerta como fallback si el span no existe
        }
    }

    function limpiarError(inputElement) {
        const errorSpan = inputElement.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains('error-js-mensaje')) {
            errorSpan.textContent = '';
            inputElement.classList.remove('input-error');
        }
    }

    function limpiarTodosLosErrores() {
        if (!formCheckout) return;
        const errorSpans = formCheckout.querySelectorAll('.error-js-mensaje');
        errorSpans.forEach(span => span.textContent = '');
        const errorInputs = formCheckout.querySelectorAll('.input-error');
        errorInputs.forEach(input => input.classList.remove('input-error'));
    }


    // ---------------------
    // RENDER CHECKOUT
    // ---------------------
    function renderCarritoCheckout() {
        if (!checkoutProductosContainer || !totalCheckoutElemento) {
            console.error("Elementos del DOM para el resumen del carrito no encontrados.");
            return;
        }

        if (carrito.length === 0) {
            checkoutProductosContainer.innerHTML = '<div class="checkout-empty-message">No hay productos en el carrito.</div>';
            totalCheckoutElemento.textContent = '$0.00';
            if (formPagoContainer) formPagoContainer.style.display = 'none'; // Ocultar formulario de pago
            return;
        }

        let totalGeneral = 0;
        checkoutProductosContainer.innerHTML = ''; // Limpiar antes de renderizar

        carrito.forEach(item => {
            const subtotal = parseFloat(item.precio) * parseInt(item.cantidad);
            totalGeneral += subtotal;

            const productItemDiv = document.createElement('div');
            productItemDiv.classList.add('checkout-item');

            // Asumiendo que item.imagen y item.titulo existen. item.precio es el precio unitario.
            productItemDiv.innerHTML = `
                <div class="checkout-col-product">
                    <img src="${item.imagen}" alt="${item.titulo}" class="checkout-img">
                    <div class="checkout-info">
                        <p class="checkout-title">${item.titulo}</p>
                    </div>
                </div>
                <div class="checkout-col-quantity">${item.cantidad}</div>
                <div class="checkout-col-price">$${parseFloat(item.precio).toFixed(2)}</div>
                <div class="checkout-col-subtotal">$${subtotal.toFixed(2)}</div>
            `;
            checkoutProductosContainer.appendChild(productItemDiv);
        });

        totalCheckoutElemento.textContent = `$${totalGeneral.toFixed(2)}`;
        if (formPagoContainer) formPagoContainer.style.display = 'block'; // Mostrar formulario de pago
    }

    renderCarritoCheckout();

    // ---------------------
    // FORMATEO INPUTS PAGO
    // ---------------------
    if (inputNumeroTarjeta) {
        inputNumeroTarjeta.addEventListener('input', (e) => {
            limpiarError(inputNumeroTarjeta);
            let valor = e.target.value.replace(/\D/g, ''); // Solo números
            if (valor.length > 16) valor = valor.slice(0, 16); // Max 16 dígitos
            const bloques = valor.match(/.{1,4}/g); // Agrupar de a 4
            e.target.value = bloques ? bloques.join(' ') : '';
        });
        inputNumeroTarjeta.addEventListener('keypress', (e) => { // Prevenir no numéricos
            if (!/\d/.test(e.key)) e.preventDefault();
        });
    }

    if (inputMesExp) {
        inputMesExp.addEventListener('input', () => limpiarError(inputMesExp));
        inputMesExp.addEventListener('keypress', (e) => { // Prevenir no numéricos
            if (!/\d/.test(e.key)) e.preventDefault();
        });
    }
    if (inputAnioExp) {
        inputAnioExp.addEventListener('input', () => limpiarError(inputAnioExp));
        inputAnioExp.addEventListener('keypress', (e) => { // Prevenir no numéricos
            if (!/\d/.test(e.key)) e.preventDefault();
        });
    }

    if (inputCvc) {
        inputCvc.addEventListener('input', (e) => {
            limpiarError(inputCvc);
            let valor = e.target.value.replace(/\D/g, ''); // Solo números
            if (valor.length > 3) valor = valor.slice(0, 3); // Max 3 dígitos
            e.target.value = valor;
        });
        inputCvc.addEventListener('keypress', (e) => { // Prevenir no numéricos
            if (!/\d/.test(e.key)) e.preventDefault();
        });
    }

    // ---------------------
    // FORM SUBMIT
    // ---------------------
    if (formCheckout) {
        formCheckout.addEventListener('submit', async function (e) {
            e.preventDefault();
            limpiarTodosLosErrores();
            let esValido = true;

            // Obtener valores y validar
            const nombre = inputNombreCompleto.value.trim();
            const direccion = inputDireccionEnvio.value.trim();
            const ciudad = inputCiudad.value.trim();
            const codigoPostal = inputCodigoPostal.value.trim();
            const numeroTarjetaInput = inputNumeroTarjeta.value.trim();
            const tarjetaNumerica = numeroTarjetaInput.replace(/\s+/g, '');
            const mes = inputMesExp.value.trim();
            const anio = inputAnioExp.value.trim();
            const cvc = inputCvc.value.trim();
            const csrfToken = formCheckout.querySelector('input[name="csrf_token"]').value;


            if (!nombre) {
                mostrarError(inputNombreCompleto, 'El nombre completo es requerido.');
                esValido = false;
            }
            if (!direccion) {
                mostrarError(inputDireccionEnvio, 'La dirección de envío es requerida.');
                esValido = false;
            }
            if (!ciudad) {
                mostrarError(inputCiudad, 'La ciudad es requerida.');
                esValido = false;
            }
            if (!codigoPostal) {
                mostrarError(inputCodigoPostal, 'El código postal es requerido.');
                esValido = false;
            }
            if (carrito.length === 0) {
                alert('🛑 No hay productos en el carrito para procesar el pedido.');
                esValido = false;
            }

            // Validaciones de pago
            if (!/^\d{16}$/.test(tarjetaNumerica)) {
                mostrarError(inputNumeroTarjeta, 'Número de tarjeta inválido. Deben ser 16 dígitos.');
                esValido = false;
            }
            if (!/^\d{2}$/.test(mes) || parseInt(mes) < 1 || parseInt(mes) > 12) {
                mostrarError(inputMesExp, 'Mes inválido (MM, 01-12).');
                esValido = false;
            }
            const anioActual = new Date().getFullYear();
            const mesActual = new Date().getMonth() + 1; // JS Month es 0-11

            if (!/^\d{4}$/.test(anio) || parseInt(anio) < anioActual || (parseInt(anio) === anioActual && parseInt(mes) < mesActual) ) {
                mostrarError(inputAnioExp, 'Fecha de expiración inválida o pasada.');
                esValido = false;
            }
            
            if (!/^\d{3}$/.test(cvc)) { // Exactamente 3 dígitos para CVC
                mostrarError(inputCvc, 'CVC inválido. Deben ser 3 dígitos.');
                esValido = false;
            }

            if (!esValido) return;

            // Si todo es válido, proceder con el envío
            if(submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Procesando...';
            }

            const datosPedido = {
                nombreCompleto: nombre,
                direccionEnvio: direccion,
                ciudad: ciudad,
                codigoPostal: codigoPostal,
                // NO envíes datos de tarjeta de crédito crudos así a tu backend en producción real
                // a menos que cumplas con PCI-DSS. Esto es para simulación.
                // Para una implementación real, usarías un proveedor de pagos (Stripe, PayPal, MercadoPago)
                // que tokeniza la tarjeta en el cliente.
                // tarjetaInfo: { /* datos simulados o tokenizados */ },
                carrito: carrito,
                csrf_token: csrfToken
            };

            try {
                const response = await fetch('procesar_pedido.php', { // Asegúrate que procesar_pedido.php exista
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(datosPedido)
                });

                const result = await response.json();

                if (result.status === 'ok' && result.pedido_id) {
                    let resumenProductos = carrito.map(p => `  - ${p.titulo} x${p.cantidad} ($${(parseFloat(p.precio) * p.cantidad).toFixed(2)})`).join('\n');
                    alert(`🎉 ¡Pedido #${result.pedido_id} realizado con éxito!\n\nResumen:\n${resumenProductos}\n\nTotal: $${totalCheckoutElemento.textContent.replace('$', '')}`);
                    localStorage.removeItem('miTiendaCarrito');
                    carrito = []; // Limpiar carrito local
                    // Redirigir a una página de éxito o de mis pedidos
                    window.location.href = `${BASE_URL}/pedidos.php?status=exito&id=${result.pedido_id}`;
                } else {
                    // Manejar errores específicos del backend
                    alert('❌ Error: ' + (result.msg || 'Hubo un problema al procesar tu pedido. Por favor, intenta de nuevo.'));
                }
            } catch (error) {
                console.error('Error en fetch:', error);
                alert('⚠️ Error de conexión al intentar procesar el pedido. Revisa tu conexión e intenta de nuevo.');
            } finally {
                if(submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Realizar Pedido (Simulado)';
                }
            }
        });
    }

    // ---------------------
    // AUTOCOMPLETAR DIRECCIÓN GUARDADA
    // ---------------------
    if (direccionGuardadaSelect) {
        direccionGuardadaSelect.addEventListener('change', () => {
            const valor = direccionGuardadaSelect.value;
            if (valor) {
                const [dir, ciu, cp] = valor.split('|');
                if (inputDireccionEnvio) inputDireccionEnvio.value = dir || '';
                if (inputCiudad) inputCiudad.value = ciu || '';
                if (inputCodigoPostal) inputCodigoPostal.value = cp || '';
                // Limpiar errores de estos campos si se autocompletan
                if (inputDireccionEnvio) limpiarError(inputDireccionEnvio);
                if (inputCiudad) limpiarError(inputCiudad);
                if (inputCodigoPostal) limpiarError(inputCodigoPostal);
            } else {
                // Opcional: Limpiar campos si se deselecciona una dirección
                // if (inputDireccionEnvio) inputDireccionEnvio.value = '';
                // if (inputCiudad) inputCiudad.value = '';
                // if (inputCodigoPostal) inputCodigoPostal.value = '';
            }
        });
    }
});