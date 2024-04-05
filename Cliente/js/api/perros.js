// Cargar las opciones del selector del formulario
function cargarOpcionesSelectorFormulario() {
    const perrosSelectorFormulario = document.getElementById('perros-selector-formulario');
    perrosSelectorFormulario.innerHTML = '';
  
    // Realizar la solicitud AJAX para obtener la lista de perros
    axios
      .get('../../../ApiPerrosPerdidos/Api/perros.php')
      .then(function (response) {
        const perros = response.data;
        perros.forEach(function (perro) {
          const option = document.createElement('option');
          option.value = perro.id;
          option.text = perro.nombre;
          perrosSelectorFormulario.appendChild(option);
        });
      })
      .catch(function (error) {
        console.error('Error al cargar la lista de perros:', error);
      });
  }
  
  // Cargar los datos del perro seleccionado en el formulario de edición
  function cargarDatosPerroSeleccionado() {
    const perroId = document.getElementById('perros-selector-formulario').value;
  
    // Realizar la solicitud AJAX para obtener los datos del perro seleccionado
    axios
      .get(`../../../ApiPerrosPerdidos/Api/perros.php?id=${perroId}`)
      .then(function (response) {
        const perro = response.data;
        document.getElementById('nombre-input').value = perro.nombre;
        document.getElementById('raza-input').value = perro.raza;
        document.getElementById('edad-input').value = perro.edad;
        document.getElementById('collar-input').value = perro.collar;
        document.getElementById('chip-input').value = perro.chip;
        document.getElementById('lugar-perdido-input').value = perro.lugarPerdido;
        document.getElementById('lugar-encontrado-input').value = perro.lugarEncontrado;
        document.getElementById('estado-input').value = perro.estado;
        document.getElementById('lugar-input').value = perro.lugar;
        document.getElementById('color-input2').value = perro.color;
        document.getElementById('tamano-input').value = perro.tamano;
        document.getElementById('descripcion-input').value = perro.descripcion;
      })
      .catch(function (error) {
        console.error('Error al cargar los datos del perro seleccionado:', error);
      });
  }
  
  // Función para agregar/editar un perro
  document.getElementById('agregar-perro-form').addEventListener('submit', function (event) {
    event.preventDefault();
  
    const accionPerro = document.getElementById('accion-perro').value;
  
    if (accionPerro === 'agregar') {
      const nombre = document.getElementById('nombre-input').value;
      const raza = document.getElementById('raza-input').value;
      const edad = document.getElementById('edad-input').value;
      const collar = document.getElementById('collar-input').value;
      const chip = document.getElementById('chip-input').value;
      const lugarPerdido = document.getElementById('lugar-perdido-input').value;
      const lugarEncontrado = document.getElementById('lugar-encontrado-input').value;
      const estado = document.getElementById('estado-input').value;
      const lugar = document.getElementById('lugar-input').value;
      const color = document.getElementById('color-input2').value;
      const tamano = document.getElementById('tamano-input').value;
      const descripcion = document.getElementById('descripcion-input').value;
  
      // Realizar la solicitud AJAX para crear un nuevo perro
      axios
        .post('../../../ApiPerrosPerdidos/Api/perros.php', {
          nombre,
          raza,
          edad,
          collar,
          chip,
          lugarPerdido,
          lugarEncontrado,
          estado,
          lugar,
          color,
          tamano,
          descripcion
        })
        .then(function (response) {
          alert('Perro agregado correctamente');
          location.reload();
        })
        .catch(function (error) {
          console.error('Error al agregar el perro:', error);
        });
    } else if (accionPerro === 'editar') {
      const perroId = document.getElementById('perros-selector-formulario').value;
      const nombre = document.getElementById('nombre-input').value;
      const raza = document.getElementById('raza-input').value;
      const edad = document.getElementById('edad-input').value;
      const collar = document.getElementById('collar-input').value;
      const chip = document.getElementById('chip-input').value;
      const lugarPerdido = document.getElementById('lugar-perdido-input').value;
      const lugarEncontrado = document.getElementById('lugar-encontrado-input').value;
      const estado = document.getElementById('estado-input').value;
      const lugar = document.getElementById('lugar-input').value;
      const color = document.getElementById('color-input2').value;
      const tamano = document.getElementById('tamano-input').value;
      const descripcion = document.getElementById('descripcion-input').value;
  
      // Realizar la solicitud AJAX para editar el perro
      axios
        .put(`../../../ApiPerrosPerdidos/Api/perros.php?id=${perroId}`, {
          nombre,
          raza,
          edad,
          collar,
          chip,
          lugarPerdido,
          lugarEncontrado,
          estado,
          lugar,
          color,
          tamano,
          descripcion
        })
        .then(function (response) {
          alert('Perro actualizado correctamente');
          location.reload();
        })
        .catch(function (error) {
          console.error('Error al actualizar el perro:', error);
        });
    }
  });
  
  // Función para eliminar un perro
  function eliminarPerro(perroId) {
    // Confirmar la eliminación del perro
    if (confirm('¿Estás seguro de eliminar este perro?')) {
      // Realizar la solicitud AJAX para eliminar el perro
      axios
        .delete(`../../../ApiPerrosPerdidos/Api/perros.php?id=${perroId}`)
        .then(function (response) {
          alert('Perro eliminado correctamente');
          location.reload();
        })
        .catch(function (error) {
          console.error('Error al eliminar el perro:', error);
        });
    }
  }