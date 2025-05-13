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
                    Swal.fire('💖 Agregado', res.msg, 'success');
                } else {
                    Swal.fire('Ups', res.msg, 'warning');
                }
            });
        });
    });
});
