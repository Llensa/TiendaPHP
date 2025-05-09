// assets/js/script.js

document.addEventListener("DOMContentLoaded", () => {
    // Swiper
    new Swiper(".mySwiper-1", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    new Swiper(".mySwiper-2", {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            0: { slidesPerView: 1 },
            520: { slidesPerView: 2 },
            950: { slidesPerView: 3 }
        }
    });

    // Carrito
    const carrito = document.getElementById('carrito');
    const lista = document.querySelector('#lista-carrito tbody');
    const vaciarCarritoBtn = document.getElementById('vaciar-carrito');
    const secciones = ['lista-1', 'lista-2', 'lista-3'].map(id => document.getElementById(id)).filter(el => el);

    let carritoProductos = [];

    secciones.forEach(section => {
        section.addEventListener('click', comprarElemento);
    });

    carrito.addEventListener('click', eliminarElemento);
    vaciarCarritoBtn.addEventListener('click', vaciarCarrito);

    function comprarElemento(e) {
        e.preventDefault();
        if (e.target.classList.contains("agregar-carrito")) {
            const elemento = e.target.closest('.categorie, .product');
            leerDatosElemento(elemento);
        }
    }

    function leerDatosElemento(elemento) {
        const precioElemento = elemento.querySelector('.precio-1, .precio');

        const infoElemento = {
            imagen: elemento.querySelector('img').src,
            titulo: elemento.querySelector('h3').textContent,
            precio: precioElemento ? precioElemento.textContent : '',
            id: elemento.querySelector('a').getAttribute('data-id'),
            cantidad: 1
        };

        const existe = carritoProductos.some(prod => prod.id === infoElemento.id);

        if (existe) {
            carritoProductos = carritoProductos.map(prod => {
                if (prod.id === infoElemento.id) {
                    prod.cantidad++;
                }
                return prod;
            });
        } else {
            carritoProductos.push(infoElemento);
        }

        actualizarCarrito();
    }

    function actualizarCarrito() {
        lista.innerHTML = '';

        carritoProductos.forEach(prod => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="${prod.imagen}" width="80"></td>
                <td>${prod.titulo}</td>
                <td>${prod.precio}</td>
                <td>${prod.cantidad}</td>
                <td><a href="#" class="borrar" data-id="${prod.id}">x</a></td>
            `;
            lista.appendChild(row);
        });
    }

    function eliminarElemento(e) {
        e.preventDefault();
        if (e.target.classList.contains('borrar')) {
            const id = e.target.getAttribute('data-id');
            carritoProductos = carritoProductos.filter(prod => prod.id !== id);
            actualizarCarrito();
        }
    }

    function vaciarCarrito() {
        carritoProductos = [];
        actualizarCarrito();
        return false;
    }
});
