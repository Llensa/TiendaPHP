document.addEventListener("DOMContentLoaded", () => {
  document.body.addEventListener("click", async (e) => {
    const btn = e.target.closest(".btn-agregar-deseo");
    if (!btn) return;

    const productoId = btn.dataset.id;

    const respuesta = await fetch(`${BASE_URL}/ajax/deseos_agregar.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ producto_id: productoId })
    });

    const data = await respuesta.json();

    if (data.status === "ok") {
      btn.textContent = data.accion === "agregado" ? "💔 Eliminar de Deseos" : "🤍 Agregar a Deseos";
    } else {
      alert(data.msg || "Error al actualizar deseos.");
    }
  });
});
