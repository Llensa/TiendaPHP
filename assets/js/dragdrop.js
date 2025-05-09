document.addEventListener("DOMContentLoaded", () => {
    const dropZone = document.getElementById('drop-zone');
    const inputImagen = document.getElementById('imagen');
    const imagenGuardada = document.getElementById('imagenGuardada');
  
    if (!dropZone || !inputImagen || !imagenGuardada) return;
  
    dropZone.addEventListener('click', () => inputImagen.click());
  
    dropZone.addEventListener('dragover', e => {
      e.preventDefault();
      dropZone.classList.add('dragover');
    });
  
    dropZone.addEventListener('dragleave', () => {
      dropZone.classList.remove('dragover');
    });
  
    dropZone.addEventListener('drop', e => {
      e.preventDefault();
      dropZone.classList.remove('dragover');
      const file = e.dataTransfer.files[0];
      subirImagen(file);
    });
  
    inputImagen.addEventListener('change', () => {
      const file = inputImagen.files[0];
      subirImagen(file);
    });
  
    function subirImagen(file) {
      const formData = new FormData();
      formData.append('imagen', file);
  
      fetch('subir.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'ok') {
          imagenGuardada.value = data.nombre;
          dropZone.innerText = 'Imagen subida: ' + data.nombre;
        } else {
          alert('Error al subir la imagen');
        }
      });
    }
  });
  