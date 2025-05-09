document.addEventListener("DOMContentLoaded", () => {
    // Validación del registro
    const formRegister = document.querySelector("form[action*='register']");
    if (formRegister) {
      formRegister.addEventListener("submit", (e) => {
        const nombre = formRegister.nombre.value.trim();
        const email = formRegister.email.value.trim();
        const pass = formRegister.password.value.trim();
  
        if (nombre === "" || email === "" || pass === "") {
          alert("Todos los campos son obligatorios");
          e.preventDefault();
        } else if (!email.includes("@")) {
          alert("El email no es válido");
          e.preventDefault();
        }
      });
    }
  
    // Validación del login
    const formLogin = document.querySelector("form[action*='login']");
    if (formLogin) {
      formLogin.addEventListener("submit", (e) => {
        const email = formLogin.email.value.trim();
        const pass = formLogin.password.value.trim();
  
        if (email === "" || pass === "") {
          alert("Email y contraseña obligatorios");
          e.preventDefault();
        }
      });
    }
  
    // Validación de comentarios
    const formComentario = document.querySelector("form[action*='comentarios']");
    if (formComentario) {
      formComentario.addEventListener("submit", (e) => {
        const contenido = formComentario.contenido.value.trim();
        if (contenido === "") {
          alert("El comentario no puede estar vacío");
          e.preventDefault();
        }
      });
    }
  
    // Validación admin productos
    const formProducto = document.querySelector("form[action='']");
    if (formProducto && location.pathname.includes("admin")) {
      formProducto.addEventListener("submit", (e) => {
        const nombre = formProducto.nombre.value.trim();
        const precio = formProducto.precio.value;
        const imagen = formProducto.imagen.value;
  
        if (nombre === "" || precio === "" || imagen === "") {
          alert("Todos los campos del producto son obligatorios");
          e.preventDefault();
        }
      });
    }
  });
  