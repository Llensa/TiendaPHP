document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-agregar-deseo').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;

            fetch(`${BASE_URL}/ajax/deseos_agregar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ producto_id: id })
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'ok') {
                    Swal.fire('🎉', res.msg, 'success');

                    // Actualizar el texto del botón según acción
                    if (res.accion === 'agregado') {
                        this.textContent = '💔 Eliminar de Deseos';
                    } else if (res.accion === 'eliminado') {
                        this.textContent = '🤍 Agregar a Deseos';
                    }
                } else {
                    Swal.fire('Ups', res.msg, 'warning');
                }
            }).catch(err => {
                console.error('Error de red', err);
                Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
            });
        });
    });
});
