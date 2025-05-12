document.addEventListener('DOMContentLoaded', () => {
    const formDireccion = document.getElementById('formDireccion');
    const togglePreferida = document.getElementById('preferida');

    if (formDireccion) {
        formDireccion.addEventListener('submit', async (e) => {
            e.preventDefault();

            const datos = {
                direccion: document.getElementById('direccion').value.trim(),
                ciudad: document.getElementById('ciudad').value.trim(),
                postal: document.getElementById('postal').value.trim(),
                preferida: togglePreferida.checked
            };

            const res = await fetch('acciones/guardar_direccion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const r = await res.json();
            if (r.status === 'ok') {
                Swal.fire('Guardado', 'Dirección registrada correctamente.', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', r.msg || 'Ocurrió un error al guardar', 'error');
            }
        });
    }

    // Marcar dirección como preferida
    document.querySelectorAll('.btn-preferida').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;

            const res = await fetch('acciones/marcar_preferida.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });

            const r = await res.json();
            if (r.status === 'ok') {
                Swal.fire('Actualizado', 'Dirección marcada como preferida.', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', r.msg || 'No se pudo actualizar.', 'error');
            }
        });
    });
});
