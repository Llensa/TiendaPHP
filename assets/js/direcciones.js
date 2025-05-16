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

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-direccion');
    const lista = document.getElementById('lista-direcciones');

    document.addEventListener('click', async (e) => {
        if (e.target.classList.contains('btn-eliminar-direccion')) {
            const id = e.target.dataset.id;
            if (!confirm('¿Seguro que deseas eliminar esta dirección?')) return;
            await fetch(`eliminar_direccion.php?id=${id}`, { method: 'GET' });
            document.querySelector(`.direccion-card[data-id="${id}"]`).remove();
        }

        if (e.target.classList.contains('btn-editar-direccion')) {
            const id = e.target.dataset.id;
            const res = await fetch(`obtener_direccion.php?id=${id}`);
            const d = await res.json();

            document.getElementById('direccion-id').value = d.id;
            document.getElementById('calle').value = d.calle;
            document.getElementById('num').value = d.numero;
            document.getElementById('apar').value = d.apartamento || '';
            document.getElementById('ci').value = d.ciudad;
            document.getElementById('prov').value = d.provincia;
            document.getElementById('codigo_postal').value = d.codigo_postal;
            document.getElementById('pais_form').value = d.pais;
            document.getElementById('es_predeterminada').checked = d.es_predeterminada == 1;
        }
    });

    
});
document.addEventListener('DOMContentLoaded', () => {
    const direccionGuardada = document.getElementById('direccionGuardada');
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

