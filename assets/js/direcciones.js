document.addEventListener('DOMContentLoaded', () => {
    const BASE_URL = typeof GLOBAL_BASE_URL !== 'undefined' ? GLOBAL_BASE_URL : '';
    if (BASE_URL === '' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
        // Solo advertir si no es un entorno local, donde las rutas relativas podrían funcionar.
        console.warn('GLOBAL_BASE_URL no está definida. Las llamadas AJAX podrían fallar si la página no está en la raíz.');
    }
    const AJAX_DIRECCIONES_URL = `${BASE_URL}/ajax/direcciones.php`;

    const listaDireccionesContenedor = document.getElementById('lista-direcciones');
    const formDireccion = document.getElementById('form-direccion');
    const formDireccionTitulo = document.getElementById('form-direccion-titulo');
    const btnCancelarEdicion = document.getElementById('btn-cancelar-edicion');


    const inputDireccionId = document.getElementById('direccion-id');
    const inputCalle = document.getElementById('calle');
    const inputNum = document.getElementById('num');
    const inputApar = document.getElementById('apar');
    const inputCi = document.getElementById('ci');
    const inputProv = document.getElementById('prov');
    const inputCodigoPostal = document.getElementById('codigo_postal_form'); // ID actualizado
    const inputPaisForm = document.getElementById('pais_form');
    const inputEsPredeterminada = document.getElementById('es_predeterminada'); // El checkbox

    function escapeHTML(str) {
        if (str === null || typeof str === 'undefined') return '';
        if (typeof str !== 'string') str = String(str);
        return str.replace(/[&<>"']/g, match => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[match]);
    }

    function mostrarMensaje(tipo, texto) {
        // Implementa una forma más elegante de mostrar mensajes si lo deseas
        alert(`${tipo.toUpperCase()}: ${texto}`);
    }

    function cargarDirecciones() {
        if (!listaDireccionesContenedor) {
            console.error("Contenedor #lista-direcciones no encontrado.");
            return;
        }
        listaDireccionesContenedor.innerHTML = '<p>Cargando direcciones...</p>';

        fetch(AJAX_DIRECCIONES_URL)
            .then(res => {
                if (!res.ok) throw new Error(`Error HTTP ${res.status} al cargar direcciones.`);
                return res.json();
            })
            .then(data => {
                listaDireccionesContenedor.innerHTML = '';
                if (data.status === 'ok' && data.direcciones && Array.isArray(data.direcciones)) {
                    if (data.direcciones.length === 0) {
                        listaDireccionesContenedor.innerHTML = '<p>No tienes direcciones guardadas.</p>';
                        return;
                    }
                    data.direcciones.forEach(dir => {
                        const item = document.createElement('div');
                        item.classList.add('direccion-card');
                        item.setAttribute('data-id', dir.id);

                        let direccionCompletaHTML = `<p>${escapeHTML(dir.calle)} ${escapeHTML(dir.numero)}`;
                        if (dir.apartamento) direccionCompletaHTML += `, ${escapeHTML(dir.apartamento)}`;
                        direccionCompletaHTML += `</p><p>${escapeHTML(dir.ciudad)}, ${escapeHTML(dir.provincia)}, ${escapeHTML(dir.pais)} - CP: ${escapeHTML(dir.codigo_postal)}</p>`;

                        item.innerHTML = `
                            <h4>Dirección Registrada:</h4> ${direccionCompletaHTML}
                            ${dir.es_predeterminada == 1 ? '<small><strong>✔ Dirección Predeterminada</strong></small>' : ''}
                            <div class="acciones">
                                <button class="btn-direccion editar btn-editar-direccion" data-id="${dir.id}">Editar</button>
                                <button class="btn-direccion eliminar btn-eliminar-direccion" data-id="${dir.id}">Eliminar</button>
                                ${dir.es_predeterminada != 1 ? `<button class="btn-direccion btn-predeterminada-direccion" data-id="${dir.id}">Hacer Predeterminada</button>` : ''}
                            </div>
                        `;
                        listaDireccionesContenedor.appendChild(item);
                    });
                } else if (data.status === 'unauthorized') {
                    listaDireccionesContenedor.innerHTML = `<p>${escapeHTML(data.message || 'No autorizado. Por favor, inicia sesión.')}</p>`;
                } else {
                    listaDireccionesContenedor.innerHTML = '<p>Error: No se pudieron cargar las direcciones.</p>';
                    console.error('Respuesta de carga de direcciones:', data);
                }
            })
            .catch(err => {
                console.error('Error en fetch cargarDirecciones:', err);
                listaDireccionesContenedor.innerHTML = '<p>Ocurrió un error al cargar tus direcciones. Intenta de nuevo más tarde.</p>';
            });
    }

    if (formDireccion) {
        formDireccion.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitButton = formDireccion.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Guardando...';

            const datosParaEnviar = {
                id: inputDireccionId.value || null,
                calle: inputCalle.value,
                numero: inputNum.value,
                apartamento: inputApar.value,
                ciudad: inputCi.value,
                provincia: inputProv.value,
                codigo_postal: inputCodigoPostal.value,
                pais: inputPaisForm.value,
                es_predeterminada_check: inputEsPredeterminada.checked // El backend espera esto
            };

            try {
                const res = await fetch(AJAX_DIRECCIONES_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(datosParaEnviar)
                });
                const result = await res.json();

                if (!res.ok) { // Manejar errores HTTP como 4xx, 5xx
                     throw new Error(result.message || `Error HTTP ${res.status}`);
                }

                if (result.status === 'ok') {
                    resetearFormulario();
                    cargarDirecciones();
                    mostrarMensaje('Éxito', result.message || 'Dirección guardada correctamente.');
                } else {
                    mostrarMensaje('Error', result.message || 'Error al guardar la dirección.');
                }
            } catch (err) {
                console.error('Error al guardar dirección:', err);
                mostrarMensaje('Error', err.message || 'Ocurrió un error al intentar guardar la dirección.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Guardar Dirección';
            }
        });
    }

    function resetearFormulario() {
        if (formDireccion) formDireccion.reset();
        if (inputDireccionId) inputDireccionId.value = '';
        if (formDireccionTitulo) formDireccionTitulo.textContent = 'Agregar Nueva Dirección';
        if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'none';
         if (formDireccion) { // Ensure formDireccion exists
            const submitButton = formDireccion.querySelector('button[type="submit"]');
            if (submitButton) submitButton.textContent = 'Guardar Dirección';
        }
    }

    if (btnCancelarEdicion) {
        btnCancelarEdicion.addEventListener('click', resetearFormulario);
    }

    document.addEventListener('click', async (e) => {
        const target = e.target;

        if (target.classList.contains('btn-eliminar-direccion')) {
            const id = target.dataset.id;
            if (!confirm('¿Seguro que deseas eliminar esta dirección?')) return;

            try {
                const res = await fetch(AJAX_DIRECCIONES_URL, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json'},
                    body: `id=${encodeURIComponent(id)}`
                });
                const result = await res.json();
                 if (!res.ok) throw new Error(result.message || `Error HTTP ${res.status}`);

                if (result.status === 'ok') {
                    cargarDirecciones();
                    resetearFormulario(); // Si la dirección eliminada estaba en el form
                    mostrarMensaje('Éxito', result.message || 'Dirección eliminada.');
                } else {
                    mostrarMensaje('Error', result.message || 'Error al eliminar la dirección.');
                }
            } catch (err) {
                console.error('Error al eliminar dirección:', err);
                mostrarMensaje('Error', err.message || 'Ocurrió un error al eliminar la dirección.');
            }
        }

        if (target.classList.contains('btn-editar-direccion')) {
            const id = target.dataset.id;
            try {
                const res = await fetch(`${AJAX_DIRECCIONES_URL}?id=${id}`);
                const result = await res.json();
                if (!res.ok) throw new Error(result.message || `Error HTTP ${res.status}`);

                if (result.status === 'ok' && result.direccion) {
                    const d = result.direccion;
                    inputDireccionId.value = d.id;
                    inputCalle.value = d.calle || '';
                    inputNum.value = d.numero || '';
                    inputApar.value = d.apartamento || '';
                    inputCi.value = d.ciudad || '';
                    inputProv.value = d.provincia || '';
                    inputCodigoPostal.value = d.codigo_postal || '';
                    inputPaisForm.value = d.pais || 'Argentina';
                    inputEsPredeterminada.checked = (d.es_predeterminada == 1);

                    if (formDireccionTitulo) formDireccionTitulo.textContent = 'Editar Dirección';
                    if (btnCancelarEdicion) btnCancelarEdicion.style.display = 'block';
                     const submitButton = formDireccion.querySelector('button[type="submit"]');
                    if (submitButton) submitButton.textContent = 'Actualizar Dirección';

                    formDireccion.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    mostrarMensaje('Error', result.message || 'No se pudieron obtener los datos de la dirección para editar.');
                }
            } catch (err) {
                console.error('Error al obtener dirección para editar:', err);
                mostrarMensaje('Error', err.message || 'Error al cargar datos de la dirección para editar.');
            }
        }

        if (target.classList.contains('btn-predeterminada-direccion')) {
            const id = target.dataset.id;
            if (!confirm('¿Establecer esta dirección como predeterminada?')) return;

            try {
                const res = await fetch(AJAX_DIRECCIONES_URL, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const result = await res.json();
                if (!res.ok) throw new Error(result.message || `Error HTTP ${res.status}`);

                if (result.status === 'ok') {
                    cargarDirecciones();
                    mostrarMensaje('Éxito', result.message || 'Dirección establecida como predeterminada.');
                } else {
                    mostrarMensaje('Error', result.message || 'Error al establecer como predeterminada.');
                }
            } catch (err) {
                console.error('Error al hacer predeterminada:', err);
                mostrarMensaje('Error', err.message || 'Ocurrió un error al establecer la dirección como predeterminada.');
            }
        }
    });

    if (listaDireccionesContenedor) {
        cargarDirecciones();
    } else {
        console.warn("Contenedor #lista-direcciones no encontrado. La lista inicial de PHP debería mostrarse si existe, pero las funciones AJAX no actualizarán la lista dinámicamente sin este contenedor.");
    }
});