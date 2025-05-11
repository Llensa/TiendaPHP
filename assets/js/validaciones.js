document.addEventListener("DOMContentLoaded", () => {

    function mostrarError(elementoInput, mensaje) {
        let errorSpan = elementoInput.nextElementSibling;
        if (!errorSpan || !errorSpan.classList.contains('error-js-mensaje')) {
            errorSpan = document.createElement('span');
            errorSpan.className = 'error-js-mensaje';
            // Insertar después del input o de su contenedor si es un grupo
            if(elementoInput.parentNode.classList.contains('form-grupo-input')) { // Si tienes un div contenedor para el input
                 elementoInput.parentNode.parentNode.insertBefore(errorSpan, elementoInput.parentNode.nextSibling);
            } else {
                 elementoInput.parentNode.insertBefore(errorSpan, elementoInput.nextSibling);
            }
        }
        errorSpan.textContent = mensaje;
        errorSpan.style.display = 'block'; // Asegurarse que sea visible
        elementoInput.classList.add('input-error'); // Opcional: clase para bordear en rojo
    }

    function limpiarError(elementoInput) {
        let errorSpan = elementoInput.nextElementSibling;
         if (errorSpan && errorSpan.classList.contains('error-js-mensaje')) {
            errorSpan.textContent = '';
            errorSpan.style.display = 'none';
        }
        elementoInput.classList.remove('input-error');
    }

    function limpiarTodosLosErrores(form) {
        const errores = form.querySelectorAll('.error-js-mensaje');
        errores.forEach(span => {
            span.textContent = '';
            span.style.display = 'none';
        });
        const inputsConError = form.querySelectorAll('.input-error');
        inputsConError.forEach(input => input.classList.remove('input-error'));
    }


    // Validación del registro (asumiendo IDs: formRegister, nombreRegister, emailRegister, passwordRegister)
    const formRegister = document.getElementById('formRegister'); // Necesitas añadir id="formRegister" a tu form
    if (formRegister) {
        formRegister.addEventListener("submit", (e) => {
            limpiarTodosLosErrores(formRegister);
            let valido = true;

            const nombre = formRegister.nombre; // Asume name="nombre"
            const email = formRegister.email;   // Asume name="email"
            const pass = formRegister.password; // Asume name="password"

            if (nombre.value.trim() === "") {
                mostrarError(nombre, "El nombre es obligatorio.");
                valido = false;
            }
            if (email.value.trim() === "") {
                mostrarError(email, "El correo electrónico es obligatorio.");
                valido = false;
            } else if (!email.value.includes("@") || !email.value.includes(".")) { // Validación simple
                mostrarError(email, "El correo electrónico no es válido.");
                valido = false;
            }
            if (pass.value.trim() === "") {
                mostrarError(pass, "La contraseña es obligatoria.");
                valido = false;
            } else if (pass.value.length < 6) { // Ejemplo: mínimo 6 caracteres
                mostrarError(pass, "La contraseña debe tener al menos 6 caracteres.");
                valido = false;
            }

            if (!valido) {
                e.preventDefault();
            }
        });
    }

    // Validación del login (asumiendo IDs: formLogin, emailLogin, passwordLogin)
    const formLogin = document.getElementById('formLogin'); // Necesitas añadir id="formLogin" a tu form
    if (formLogin) {
        formLogin.addEventListener("submit", (e) => {
            limpiarTodosLosErrores(formLogin);
            let valido = true;
            const email = formLogin.email;
            const pass = formLogin.password;

            if (email.value.trim() === "") {
                mostrarError(email, "El correo electrónico es obligatorio.");
                valido = false;
            }
            if (pass.value.trim() === "") {
                mostrarError(pass, "La contraseña es obligatoria.");
                valido = false;
            }
            if (!valido) {
                e.preventDefault();
            }
        });
    }

    // Validación de comentarios (asumiendo ID: form-comentario, y name="contenido" en textarea)
    const formComentario = document.getElementById('form-comentario');
    if (formComentario) {
        formComentario.addEventListener("submit", (e) => {
            limpiarTodosLosErrores(formComentario);
            let valido = true;
            const contenido = formComentario.contenido;

            if (contenido.value.trim() === "") {
                mostrarError(contenido, "El comentario no puede estar vacío.");
                valido = false;
            }
            if (!valido) {
                e.preventDefault();
            }
        });
    }

    // Validación admin productos (asumiendo ID: formAdminProducto)
    // y names: nombre, precio, imagenGuardada
    const formAdminProducto = document.getElementById('formAdminProducto'); // Necesitas añadir id="formAdminProducto" a tu form
    if (formAdminProducto && window.location.pathname.includes("/admin/")) { // Verifica que estemos en admin
        formAdminProducto.addEventListener("submit", (e) => {
            limpiarTodosLosErrores(formAdminProducto);
            let valido = true;

            const nombre = formAdminProducto.nombre;
            const precio = formAdminProducto.precio;
            const imagenGuardada = formAdminProducto.imagenGuardada; // Validar el hidden input
            const descripcion = formAdminProducto.descripcion;

            if (nombre.value.trim() === "") {
                mostrarError(nombre, "El nombre del producto es obligatorio.");
                valido = false;
            }
            if (descripcion.value.trim() === "") { // Añadida validación de descripción
                mostrarError(descripcion, "La descripción es obligatoria.");
                valido = false;
            }
            if (precio.value.trim() === "" || parseFloat(precio.value) <= 0) {
                mostrarError(precio, "El precio es obligatorio y debe ser mayor que cero.");
                valido = false;
            }
            if (imagenGuardada.value.trim() === "") {
                // El error para dropzone es más complicado, podría necesitar un span dedicado
                // Por ahora, podemos poner un error general o un span cerca del dropzone
                const dropZoneElement = document.getElementById('drop-zone');
                if(dropZoneElement) mostrarError(dropZoneElement, "Debe seleccionar o arrastrar una imagen.");
                else console.error("Drop zone no encontrado para mostrar error de imagen.")
                valido = false;
            }

            if (!valido) {
                e.preventDefault();
            }
        });
    }
});