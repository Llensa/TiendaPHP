document.addEventListener("DOMContentLoaded", () => {
    if (typeof Swiper !== 'undefined') {
        const swiper1Exists = document.querySelector(".mySwiper-1");
        if (swiper1Exists) {
            new Swiper(".mySwiper-1", {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                pagination: { el: ".swiper-pagination", clickable: true },
                navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            });
        }

        const swiper2Exists = document.querySelector(".mySwiper-2");
        if (swiper2Exists) {
            new Swiper(".mySwiper-2", {
                slidesPerView: 3,
                spaceBetween: 30,
                loop: true,
                navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
                breakpoints: { 0: { slidesPerView: 1 }, 520: { slidesPerView: 2 }, 950: { slidesPerView: 3 } }
            });
        }

        const swiperHeroExists = document.querySelector(".swiperHero");
        if (swiperHeroExists) {
             new Swiper(".swiperHero", {
               loop: true,
               autoplay: { delay: 5000, disableOnInteraction: false },
               navigation: {
               nextEl: ".swiper-button-next",
               prevEl: ".swiper-button-prev"
                }
            });
        }

         document.querySelectorAll('.toggle-detalle').forEach(item => {
         item.addEventListener('click', () => {
        const targetId = item.dataset.target;
        const detalle = document.getElementById(targetId);
        if (detalle) detalle.style.display = (detalle.style.display === 'none') ? 'block' : 'none';
            });
        });
    }

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
            
            const originalText = e.target.textContent;
            e.target.textContent = '¡Añadido!';
            e.target.disabled = true; // Deshabilitar temporalmente
            setTimeout(() => {
                e.target.textContent = originalText;
                e.target.disabled = false; // Rehabilitar
            }, 1500);

            if (dropdownCarrito && !dropdownCarrito.classList.contains("visible")) {
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
            cuerpoTablaCarrito.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 20px 0;">Tu carrito está vacío</td></tr>';
            if (btnProcederPago) btnProcederPago.style.display = 'none';
        } else {
            carritoProductos.forEach(prod => {
                const row = document.createElement('tr');
                const precioTotalItem = prod.precio * prod.cantidad;
                totalGeneral += precioTotalItem;
                row.innerHTML = `
                    <td><img src="${prod.imagen}" width="60" alt="${prod.titulo}"></td>
                    <td>${prod.titulo}</td>
                    <td>$${prod.precio.toFixed(2)}</td>
                    <td class="cantidad-controls">
                        <button class="btn-cantidad-carrito btn-decrementar" data-id="${prod.id}" aria-label="Disminuir cantidad">-</button>
                        <span class="cantidad-numero">${prod.cantidad}</span>
                        <button class="btn-cantidad-carrito btn-incrementar" data-id="${prod.id}" aria-label="Aumentar cantidad">+</button>
                    </td>
                    <td><a href="#" class="borrar-item-carrito" data-id="${prod.id}" title="Eliminar item" aria-label="Eliminar ${prod.titulo}">×</a></td>
                `;
                cuerpoTablaCarrito.appendChild(row);
            });
            if (btnProcederPago) btnProcederPago.style.display = 'inline-block';
        }

        if (contadorCarritoElemento) {
            const totalItems = carritoProductos.reduce((sum, prod) => sum + prod.cantidad, 0);
            contadorCarritoElemento.textContent = totalItems;
            contadorCarritoElemento.style.display = totalItems > 0 ? 'inline-flex' : 'none';
        }
        if (carritoTotalPrecioElemento) {
            carritoTotalPrecioElemento.textContent = `$${totalGeneral.toFixed(2)}`;
        }
    }

    function manejarClickEnTablaCarrito(e) {
        const target = e.target;
        const productoId = target.dataset.id;

        if (target.classList.contains('borrar-item-carrito') || 
            target.classList.contains('btn-incrementar') || 
            target.classList.contains('btn-decrementar')) {
            e.preventDefault(); 
        } else {
            return; 
        }
        
        const productoIndex = carritoProductos.findIndex(prod => prod.id === productoId);

        if (target.classList.contains('borrar-item-carrito')) {
            if (productoIndex > -1) carritoProductos.splice(productoIndex, 1);
        } else if (target.classList.contains('btn-incrementar')) {
            if (productoIndex > -1) carritoProductos[productoIndex].cantidad++;
        } else if (target.classList.contains('btn-decrementar')) {
            if (productoIndex > -1 && carritoProductos[productoIndex].cantidad > 1) {
                carritoProductos[productoIndex].cantidad--;
            } else if (productoIndex > -1 && carritoProductos[productoIndex].cantidad === 1) {
                // Opcional: si quieres que se elimine al llegar a 0 con el botón de decrementar
                // carritoProductos.splice(productoIndex, 1); 
            }
        }
        
        actualizarVisualizacionCarritoCompleta();
        guardarCarritoEnLocalStorage();
    }

    function vaciarCarritoCompleto(e) {
        if (e) e.preventDefault();
        carritoProductos = [];
        actualizarVisualizacionCarritoCompleta();
        guardarCarritoEnLocalStorage();
        if (dropdownCarrito && dropdownCarrito.classList.contains('visible')) {
           // Opcional: No cerrar el carrito al vaciarlo, para que el usuario vea el mensaje "vacío"
           // dropdownCarrito.classList.remove('visible');
        }
        return false;
    }

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

    if(dropdownCarrito) {
        dropdownCarrito.addEventListener('click', (e) => e.stopPropagation());
    }

    document.addEventListener('click', (e) => {
        if (dropdownCarrito && dropdownCarrito.classList.contains('visible') &&
            !dropdownCarrito.contains(e.target) &&
            (iconoCarrito && !iconoCarrito.contains(e.target))) {
            dropdownCarrito.classList.remove('visible');
        }
    });
    
       

    cargarCarritoDesdeLocalStorage();
});