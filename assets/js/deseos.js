document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.agregar-deseo').forEach(btn => {
        btn.addEventListener('click', () => {
            const idProducto = btn.dataset.id;
            const nombre = btn.dataset.nombre;

            fetch(`${BASE_URL}/ajax/deseos_agregar.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({producto_id: idProducto})
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    Swal.fire('¡Añadido!', `"${nombre}" fue agregado a tu lista de deseos.`, 'success');
                } else if (data.status === 'existe') {
                    Swal.fire('Info', `"${nombre}" ya está en tu lista.`, 'info');
                } else {
                    Swal.fire('Error', 'No se pudo agregar el producto.', 'error');
                }
            });
        });
    });
});
