function cargarDirecciones() {
    fetch(`${BASE_URL}/ajax/direcciones.php`)
        .then(res => res.json())
        .then(data => {
            const contenedor = document.getElementById('lista-direcciones');
            contenedor.innerHTML = '';
            data.direcciones.forEach(dir => {
                const item = document.createElement('div');
                item.innerHTML = `
                    <p>${dir.direccion} ${dir.preferida ? '<strong>(Preferida)</strong>' : ''}</p>
                    <button onclick="setPreferida(${dir.id})">Marcar como Preferida</button>
                    <button onclick="eliminarDireccion(${dir.id})">Eliminar</button>
                `;
                contenedor.appendChild(item);
            });
        });
}

function setPreferida(id) {
    fetch(`${BASE_URL}/ajax/direcciones.php`, {
        method: 'PATCH',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id })
    }).then(() => {
        Swal.fire('Actualizado', 'Dirección marcada como preferida', 'success');
        cargarDirecciones();
    });
}

function eliminarDireccion(id) {
    fetch(`${BASE_URL}/ajax/direcciones.php`, {
        method: 'DELETE',
        body: `id=${id}`
    }).then(() => {
        Swal.fire('Eliminada', 'Dirección borrada', 'info');
        cargarDirecciones();
    });
}
