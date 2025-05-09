document.addEventListener("DOMContentLoaded", () => {
    // Swiper (sin cambios)
    if (typeof Swiper !== 'undefined') {
        new Swiper(".mySwiper-1", {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        });

        new Swiper(".mySwiper-2", {
            slidesPerView: 3,
            spaceBetween: 30,
            loop: true,
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            breakpoints: { 0: { slidesPerView: 1 }, 520: { slidesPerView: 2 }, 950: { slidesPerView: 3 } }
        });
    }

    // Carrito
    const dropdownCarrito = document.getElementById('dropdown-carrito');
    const cuerpoTablaCarrito = document.querySelector('#tabla-lista-carrito tbody');
    const btnVaciarCarrito = document.getElementById('btn-vaciar-carrito');
    const iconoCarrito = document.getElementById('icono-carrito');
    const contadorCarritoElemento = document.getElementById('contador-carrito');
    const carritoTotalPrecioElemento = document.getElementById('carrito-total-precio');
    const btnProcederPago = document.getElementById('btn-proceder-pago');

    let carritoProductos = [];

    function guardarCarritoEnLocalStorage() {
        localStorage.setItem('miTiendaCarrito', JSON.stringify(carritoProductos));
    }

    function cargarCarritoDesdeLocalStorage() {
        const carritoGuardado = localStorage.getItem('miTiendaCarrito');
        if (carritoGuardado) {
            carritoProductos = JSON.parse(carritoGuardado);
        }
        actualizarVisualizacionCarritoCompleta();
    }

    function comprarElemento(e) {
        if (e.target.classList.contains("agregar-carrito")) {
            e.preventDefault();
            leerDatosElementoDesdeBoton(e.target);
            e.target.textContent = '¡Añadido!';
            setTimeout(() => {
                e.target.textContent = 'Agregar al carrito';
            }, 1500);

            // ✅ Mostrar carrito automáticamente al añadir producto
            if (!dropdownCarrito.classList.contains("visible")) {
                dropdownCarrito.classList.add("visible");
            }
        }
    }

    function leerDatosElementoDesdeBoton(boton) {
        const infoElemento = {
            imagen: boton.dataset.imagen,
            titulo: boton.dataset.nombre,
            precio: parseFloat(boton.dataset.precio),
            id: boton.dataset.id,
            cantidad: 1
        };

        const productoExistenteIndex = carritoProductos.findIndex(prod => prod.id === infoElemento.id);

        if (productoExistenteIndex > -1) {
            carritoProductos[productoExistenteIndex].cantidad++;
        } else {
            carritoProductos.push(infoElemento);
        }
        actualizarVisualizacionCarritoCompleta();
        guardarCarritoEnLocalStorage();
    }

    function actualizarVisualizacionCarritoCompleta() {
        if (!cuerpoTablaCarrito) return;
        cuerpoTablaCarrito.innerHTML = '';
        let totalGeneral = 0;

        if (carritoProductos.length === 0) {
            cuerpoTablaCarrito.innerHTML = `<tr><td colspan="5" style="text-align:center;">Tu carrito está vacío</td></tr>`;
            if (btnProcederPago) btnProcederPago.style.display = 'none';
        } else {
            carritoProductos.forEach(prod => {
                const row = document.createElement('tr');
                const precioTotalItem = prod.precio * prod.cantidad;
                totalGeneral += precioTotalItem;
                row.innerHTML = `
                    <td><img src="${prod.imagen}" width="70" alt="${prod.titulo}"></td>
                    <td>${prod.titulo}</td>
                    <td>$${prod.precio.toFixed(2)}</td>
                    <td>
                        <button class="btn-cantidad-carrito btn-decrementar" data-id="${prod.id}">-</button>
                        ${prod.cantidad}
                        <button class="btn-cantidad-carrito btn-incrementar" data-id="${prod.id}">+</button>
                    </td>
                    <td><a href="#" class="borrar-item-carrito" data-id="${prod.id}" title="Eliminar item">×</a></td>
                `;
                cuerpoTablaCarrito.appendChild(row);
            });
            if (btnProcederPago) btnProcederPago.style.display = 'inline-block';
        }

        const totalItems = carritoProductos.reduce((sum, prod) => sum + prod.cantidad, 0);
        contadorCarritoElemento.textContent = totalItems;
        contadorCarritoElemento.style.display = totalItems > 0 ? 'inline-flex' : 'none';

        carritoTotalPrecioElemento.textContent = `$${totalGeneral.toFixed(2)}`;
    }

    function manejarClickEnTablaCarrito(e) {
        e.preventDefault();
        const target = e.target;
        const productoId = target.dataset.id;

        if (target.classList.contains('borrar-item-carrito')) {
            carritoProductos = carritoProductos.filter(prod => prod.id !== productoId);
        } else if (target.classList.contains('btn-incrementar')) {
            const producto = carritoProductos.find(prod => prod.id === productoId);
            if (producto) producto.cantidad++;
        } else if (target.classList.contains('btn-decrementar')) {
            const producto = carritoProductos.find(prod => prod.id === productoId);
            if (producto && producto.cantidad > 1) {
                producto.cantidad--;
            }
        } else {
            return;
        }

        actualizarVisualizacionCarritoCompleta();
        guardarCarritoEnLocalStorage();
    }

    function vaciarCarritoCompleto(e) {
        if (e) e.preventDefault();
        carritoProductos = [];
        actualizarVisualizacionCarritoCompleta();
        guardarCarritoEnLocalStorage();
        return false;
    }

    // Event Listeners
    document.body.addEventListener('click', comprarElemento);

    if (cuerpoTablaCarrito) {
        cuerpoTablaCarrito.addEventListener('click', manejarClickEnTablaCarrito);
    }

    if (btnVaciarCarrito) {
        btnVaciarCarrito.addEventListener('click', vaciarCarritoCompleto);
    }

    if (iconoCarrito && dropdownCarrito) {
        iconoCarrito.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownCarrito.classList.toggle('visible');
        });
    }

    // ❗ Evitar que se cierre al hacer clic dentro
    dropdownCarrito?.addEventListener('click', (e) => e.stopPropagation());

    // ❗ Cerrar solo si se hace clic fuera del carrito
    document.addEventListener('click', (e) => {
        if (dropdownCarrito.classList.contains('visible') &&
            !dropdownCarrito.contains(e.target) &&
            !iconoCarrito.contains(e.target)) {
            dropdownCarrito.classList.remove('visible');
        }
    });

    cargarCarritoDesdeLocalStorage();
});
