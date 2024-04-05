// Función para crear una nueva publicación en el servidor
function crearPublicacion(publicacion) {
  return fetch('../../../ApiPerrosPerdidos/api/publicaciones.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(publicacion)
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al crear la publicación');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para actualizar una publicación en el servidor
function actualizarPublicacion(id, publicacion) {
  return fetch(`../../../ApiPerrosPerdidos/api/publicaciones.php/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(publicacion)
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al actualizar la publicación');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para eliminar una publicación en el servidor
function eliminarPublicacion(id) {
  return fetch(`../../../ApiPerrosPerdidos/api/publicaciones.php/${id}`, {
    method: 'DELETE'
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al eliminar la publicación');
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para obtener la lista de publicaciones desde la API
function obtenerPublicaciones() {
  return fetch('../../../ApiPerrosPerdidos/api/publicaciones.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al obtener las publicaciones');
      }
      return response.json();
    })
    .catch(error => {
      mostrarError('Error al obtener la lista de publicaciones');
      console.error('Error:', error);
    });
}

obtenerPublicaciones()
  .then(publicaciones => {
    // Renderizar las publicaciones en el DOM
    renderizarPublicaciones(publicaciones);
  })
  .catch(error => {
    mostrarError('Error al obtener las publicaciones');
    console.error('Error:', error);
  });

// Función para renderizar las publicaciones en el DOM
function renderizarPublicaciones(publicaciones) {
  const contenedorPublicaciones = document.getElementById('contenedorPublicaciones');

  // Limpiar el contenedor antes de agregar las publicaciones
  contenedorPublicaciones.innerHTML = '';

  publicaciones.forEach(publicacion => {
    // Crear el elemento de la publicación
    const publicacionElement = document.createElement('div');
    publicacionElement.classList.add('publicacion');

    // Agregar el título y contenido de la publicación
    const tituloElement = document.createElement('h2');
    tituloElement.textContent = publicacion.titulo;
    publicacionElement.appendChild(tituloElement);

    const contenidoElement = document.createElement('p');
    contenidoElement.textContent = publicacion.contenido;
    publicacionElement.appendChild(contenidoElement);

    // Agregar el autor de la publicación
    const autorElement = document.createElement('p');
    autorElement.textContent = 'Autor: ' + publicacion.autor;
    publicacionElement.appendChild(autorElement);

    // Agregar el número de me gustas de la publicación
    const meGustasElement = document.createElement('p');
    meGustasElement.textContent = 'Me Gustas: ' + publicacion.meGustas;
    publicacionElement.appendChild(meGustasElement);

    // Crear el menú de acciones para la publicación
    const accionesElement = document.createElement('div');
    accionesElement.classList.add('acciones');

    // Botón de me gusta para la publicación
    const meGustaBtn = document.createElement('button');
    meGustaBtn.textContent = 'Me Gusta';
    meGustaBtn.addEventListener('click', () => {
      darMeGustaPublicacion(publicacion.id)
        .then(() => {
          // Actualizar el número de me gustas en el DOM
          publicacion.meGustas++;
          meGustasElement.textContent = 'Me Gustas: ' + publicacion.meGustas;
        })
        .catch(error => {
          mostrarError('Error al dar me gusta');
          console.error('Error:', error);
        });
    });
    accionesElement.appendChild(meGustaBtn);

    // Botón de eliminar me gusta para la publicación
    const eliminarMeGustaBtn = document.createElement('button');
    eliminarMeGustaBtn.textContent = 'Eliminar Me Gusta';
    eliminarMeGustaBtn.addEventListener('click', () => {
      quitarMeGustaPublicacion(publicacion.id)
        .then(() => {
          // Actualizar el número de me gustas en el DOM
          publicacion.meGustas--;
          meGustasElement.textContent = 'Me Gustas: ' + publicacion.meGustas;
        })
        .catch(error => {
          mostrarError('Error al eliminar me gusta');
          console.error('Error:', error);
        });
    });
    accionesElement.appendChild(eliminarMeGustaBtn);

    // Agregar el botón de editar publicación
    const editarPublicacionBtn = document.createElement('button');
    editarPublicacionBtn.textContent = 'Editar Publicación';
    editarPublicacionBtn.addEventListener('click', () => {
      // Obtener la publicación actual
      const idPublicacion = publicacion.id; 
      const tituloActual = publicacion.titulo;
      const contenidoActual = publicacion.contenido;

      // Crear un formulario para editar la publicación
      const formularioEditar = document.createElement('form');

      // Campo de título
      const inputTitulo = document.createElement('input');
      inputTitulo.type = 'text';
      inputTitulo.value = tituloActual;
      formularioEditar.appendChild(inputTitulo);

      // Campo de contenido
      const inputContenido = document.createElement('textarea');
      inputContenido.value = contenidoActual;
      formularioEditar.appendChild(inputContenido);

      // Botón de guardar cambios
      const guardarCambiosBtn = document.createElement('button');
      guardarCambiosBtn.textContent = 'Guardar Cambios';
      guardarCambiosBtn.addEventListener('click', () => {
        const tituloActualizado = inputTitulo.value;
        const contenidoActualizado = inputContenido.value;

        const publicacionActualizada = {
          id: idPublicacion,
          titulo: tituloActualizado,
          contenido: contenidoActualizado
          // Agrega otros campos de la publicación si es necesario
        };

        // Llamar a la función para actualizar la publicación en el servidor
        actualizarPublicacion(idPublicacion, publicacionActualizada)
          .then(() => {
            // Actualizar la publicación en el DOM con los datos actualizados
            publicacion.titulo = tituloActualizado;
            publicacion.contenido = contenidoActualizado;
            renderizarPublicaciones([publicacion]); // Vuelve a renderizar la publicación actualizada
          })
          .catch(error => {
            mostrarError('Error al actualizar la publicación');
            console.error('Error:', error);
          });

        // Eliminar el formulario de edición y volver a mostrar los botones de acciones
        formularioEditar.remove();
        accionesElement.style.display = 'block';
      });

      // Botón de cancelar edición
      const cancelarEdicionBtn = document.createElement('button');
      cancelarEdicionBtn.textContent = 'Cancelar Edición';
      cancelarEdicionBtn.addEventListener('click', () => {
        // Eliminar el formulario de edición y volver a mostrar los botones de acciones
        formularioEditar.remove();
        accionesElement.style.display = 'block';
      });

      // Agregar los botones de guardar cambios y cancelar edición al formulario
      formularioEditar.appendChild(guardarCambiosBtn);
      formularioEditar.appendChild(cancelarEdicionBtn);

      // Ocultar los botones de acciones mientras se edita la publicación
      accionesElement.style.display = 'none';

      // Agregar el formulario de edición al elemento de la publicación
      publicacionElement.appendChild(formularioEditar);
    });
    accionesElement.appendChild(editarPublicacionBtn);

    // Agregar el botón de eliminar publicación
    const eliminarPublicacionBtn = document.createElement('button');
    eliminarPublicacionBtn.textContent = 'Eliminar Publicación';
    eliminarPublicacionBtn.addEventListener('click', () => {
      eliminarUnaPublicacion(publicacion.id)
        .then(() => {
          // Eliminar la publicación del DOM
          publicacionElement.remove();
        })
        .catch(error => {
          mostrarError('Error al eliminar la publicación');
          console.error('Error:', error);
        });
    });
    accionesElement.appendChild(eliminarPublicacionBtn);

    // Agregar el menú de acciones al elemento de la publicación
    publicacionElement.appendChild(accionesElement);

    // Agregar la publicación al contenedor
    contenedorPublicaciones.appendChild(publicacionElement);
  });
}

// Función para obtener todas las publicaciones desde el servidor
function obtenerPublicaciones() {
  return fetch('../../../ApiPerrosPerdidos/api/publicaciones.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al obtener las publicaciones');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para obtener una publicación por su ID desde el servidor
function obtenerPublicacionPorId(id) {
  return fetch(`../../../ApiPerrosPerdidos/api/publicaciones.php/${id}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al obtener la publicación');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para crear una nueva publicación en el servidor
function crearPublicacion(publicacion) {
  return fetch('../../../ApiPerrosPerdidos/api/publicaciones', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(publicacion)
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al crear la publicación');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para actualizar una publicación en el servidor
function actualizarPublicacion(id, publicacion) {
  return fetch(`../../../ApiPerrosPerdidos/api/publicaciones/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(publicacion)
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al actualizar la publicación');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para eliminar una publicación en el servidor
function eliminarPublicacion(id) {
  return fetch(`../../../ApiPerrosPerdidos/api/publicaciones/${id}`, {
    method: 'DELETE'
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al eliminar la publicación');
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para obtener la lista de usuarios desde la API
function obtenerUsuarios() {
  axios.get('/api/usuarios')
    .then(function(response) {
      const usuarios = response.data;
      renderizarUsuarios(usuarios);
    })
    .catch(function(error) {
      mostrarError('Error al obtener la lista de usuarios');
      console.error('Error:', error);
    });
}

// Función para obtener la lista de perros desde la API
function obtenerPerros() {
  axios.get('/api/perros')
    .then(function(response) {
      const perros = response.data;
      renderizarPerros(perros);
    })
    .catch(function(error) {
      mostrarError('Error al obtener la lista de perros');
      console.error('Error:', error);
    });
}

// Función para obtener la lista de comentarios desde la API
function obtenerComentarios() {
  axios.get('/api/comentarios')
    .then(function(response) {
      const comentarios = response.data;
      renderizarComentarios(comentarios);
    })
    .catch(function(error) {
      mostrarError('Error al obtener la lista de comentarios');
      console.error('Error:', error);
    });
}

// Función para obtener la lista de publicaciones desde la API
function obtenerPublicaciones() {
  axios.get('/api/publicaciones')
    .then(function(response) {
      const publicaciones = response.data;
      renderizarPublicaciones(publicaciones);
    })
    .catch(function(error) {
      mostrarError('Error al obtener la lista de publicaciones');
      console.error('Error:', error);
    });
}

// Función para ver los perros de un usuario
function verPerrosUsuario(idUsuario) {
  axios.get(`/api/usuarios/${idUsuario}/perros`)
    .then(function(response) {
      const perros = response.data;
      renderizarPerrosUsuario(perros);
    })
    .catch(function(error) {
      mostrarError('Error al obtener los perros del usuario');
      console.error('Error:', error);
    });
}

// Función para ver las publicaciones de un usuario
function verPublicacionesUsuario(idUsuario) {
  axios.get(`/api/usuarios/${idUsuario}/publicaciones`)
    .then(function(response) {
      const publicaciones = response.data;
      renderizarPublicacionesUsuario(publicaciones);
    })
    .catch(function(error) {
      mostrarError('Error al obtener las publicaciones del usuario');
      console.error('Error:', error);
    });
}

// Función para ver los comentarios de un usuario
function verComentariosUsuario(idUsuario) {
  axios.get(`/api/usuarios/${idUsuario}/comentarios`)
    .then(function(response) {
      const comentarios = response.data;
      renderizarComentariosUsuario(comentarios);
    })
    .catch(function(error) {
      mostrarError('Error al obtener los comentarios del usuario');
      console.error('Error:', error);
    });
}

// Función para renderizar la lista de usuarios en el DOM
function renderizarUsuarios(usuarios) {
  const listaUsuarios = document.getElementById('listaUsuarios');
  listaUsuarios.innerHTML = '';

  usuarios.forEach(function(usuario) {
    const usuarioElement = document.createElement('li');
    usuarioElement.textContent = `${usuario.nombre} - ${usuario.email}`;
    listaUsuarios.appendChild(usuarioElement);
  });
}

// Función para renderizar la lista de perros en el DOM
function renderizarPerros(perros) {
  const listaPerros = document.getElementById('listaPerros');
  listaPerros.innerHTML = '';

  perros.forEach(function(perro) {
    const perroElement = document.createElement('li');
    perroElement.textContent = `${perro.nombre} - Raza: ${perro.raza}`;
    listaPerros.appendChild(perroElement);
  });
}

// Función para renderizar la lista de comentarios en el DOM
function renderizarComentarios(comentarios) {
  const listaComentarios = document.getElementById('listaComentarios');
  listaComentarios.innerHTML = '';

  comentarios.forEach(function(comentario) {
    const comentarioElement = document.createElement('li');
    comentarioElement.textContent = `${comentario.autor}: ${comentario.contenido}`;
    listaComentarios.appendChild(comentarioElement);
  });
}

// Función para renderizar la lista de publicaciones en el DOM
function renderizarPublicaciones(publicaciones) {
  const contenedorPublicaciones = document.getElementById('contenedorPublicaciones');

  // Limpiar el contenedor antes de agregar las publicaciones
  contenedorPublicaciones.innerHTML = '';

  publicaciones.forEach(publicacion => {
    // Crear el elemento de la publicación
    const publicacionElement = document.createElement('div');
    publicacionElement.classList.add('publicacion');

    // Agregar el título y contenido de la publicación
    const tituloElement = document.createElement('h2');
    tituloElement.textContent = publicacion.titulo;
    publicacionElement.appendChild(tituloElement);

    const contenidoElement = document.createElement('p');
    contenidoElement.textContent = publicacion.contenido;
    publicacionElement.appendChild(contenidoElement);

    // Agregar el autor de la publicación
    const autorElement = document.createElement('p');
    autorElement.textContent = 'Autor: ' + publicacion.autor;
    publicacionElement.appendChild(autorElement);

    // Agregar el número de me gustas de la publicación
    const meGustasElement = document.createElement('p');
    meGustasElement.textContent = 'Me Gustas: ' + publicacion.meGustas;
    publicacionElement.appendChild(meGustasElement);

    // Crear el menú de acciones para la publicación
    const accionesElement = document.createElement('div');
    accionesElement.classList.add('acciones');

    // Botón de me gusta para la publicación
    const meGustaBtn = document.createElement('button');
    meGustaBtn.textContent = 'Me Gusta';
    meGustaBtn.addEventListener('click', () => {
      darMeGustaPublicacion(publicacion.id)
        .then(() => {
          // Actualizar el número de me gustas en el DOM
          publicacion.meGustas++;
          meGustasElement.textContent = 'Me Gustas: ' + publicacion.meGustas;
        })
        .catch(error => {
          mostrarError('Error al dar me gusta');
          console.error('Error:', error);
        });
    });
    accionesElement.appendChild(meGustaBtn);

    // Botón de eliminar me gusta para la publicación
    const eliminarMeGustaBtn = document.createElement('button');
    eliminarMeGustaBtn.textContent = 'Eliminar Me Gusta';
    eliminarMeGustaBtn.addEventListener('click', () => {
      quitarMeGustaPublicacion(publicacion.id)
        .then(() => {
          // Actualizar el número de me gustas en el DOM
          publicacion.meGustas--;
          meGustasElement.textContent = 'Me Gustas: ' + publicacion.meGustas;
        })
        .catch(error => {
          mostrarError('Error al eliminar me gusta');
          console.error('Error:', error);
        });
    });
    accionesElement.appendChild(eliminarMeGustaBtn);

    // Agregar el botón de editar publicación
    const editarPublicacionBtn = document.createElement('button');
    editarPublicacionBtn.textContent = 'Editar Publicación';
    editarPublicacionBtn.addEventListener('click', () => {
      
    });
    accionesElement.appendChild(editarPublicacionBtn);

    // Agregar el botón de eliminar publicación
    const eliminarPublicacionBtn = document.createElement('button');
    eliminarPublicacionBtn.textContent = 'Eliminar Publicación';
    eliminarPublicacionBtn.addEventListener('click', () => {
      eliminarUnaPublicacion(publicacion.id)
        .then(() => {
          // Eliminar la publicación del DOM
          publicacionElement.remove();
        })
        .catch(error => {
          mostrarError('Error al eliminar la publicación');
          console.error('Error:', error);
        });
    });
    accionesElement.appendChild(eliminarPublicacionBtn);

    // Agregar el menú de acciones al elemento de la publicación
    publicacionElement.appendChild(accionesElement);

    // Agregar la publicación al contenedor
    contenedorPublicaciones.appendChild(publicacionElement);
  });
}

// Función para renderizar los perros de un usuario en el DOM
function renderizarPerrosUsuario(perros) {
  const listaPerrosUsuario = document.getElementById('listaPerrosUsuario');
  listaPerrosUsuario.innerHTML = '';

  perros.forEach(function(perro) {
    const perroElement = document.createElement('li');
    perroElement.textContent = `${perro.nombre} - Raza: ${perro.raza}`;
    listaPerrosUsuario.appendChild(perroElement);
  });
}

// Función para renderizar las publicaciones de un usuario en el DOM
function renderizarPublicacionesUsuario(publicaciones) {
  const listaPublicacionesUsuario = document.getElementById('listaPublicacionesUsuario');
  listaPublicacionesUsuario.innerHTML = '';

  publicaciones.forEach(function(publicacion) {
    const publicacionElement = document.createElement('li');
    publicacionElement.textContent = `${publicacion.titulo}: ${publicacion.contenido}`;
    listaPublicacionesUsuario.appendChild(publicacionElement);
  });
}

// Función para renderizar los comentarios de un usuario en el DOM
function renderizarComentariosUsuario(comentarios) {
  const listaComentariosUsuario = document.getElementById('listaComentariosUsuario');
  listaComentariosUsuario.innerHTML = '';

  comentarios.forEach(function(comentario) {
    const comentarioElement = document.createElement('li');
    comentarioElement.textContent = `${comentario.autor}: ${comentario.contenido}`;
    listaComentariosUsuario.appendChild(comentarioElement);
  });
}

// Llamar a las funciones para obtener y renderizar los datos necesarios
obtenerUsuarios();
obtenerPerros();
obtenerComentarios();
obtenerPublicaciones();
verAmistadesSeguidores(obtenerIdUsuarioActual());
verPerrosUsuario(obtenerIdUsuarioActual());
verPublicacionesUsuario(obtenerIdUsuarioActual());
verComentariosUsuario(obtenerIdUsuarioActual());

// Obtener los datos del usuario actual desde localStorage
const usuarioActualJSON = localStorage.getItem('usuarioActual');
let usuarioActual = null;

if (usuarioActualJSON) {
  try {
    usuarioActual = JSON.parse(usuarioActualJSON);
  } catch (error) {
    console.error('Error al parsear los datos del usuario actual:', error);
  }
}

// Función auxiliar para obtener el ID del usuario actual
function obtenerIdUsuarioActual() {
  if (usuarioActual && usuarioActual.id) {
    return usuarioActual.id;
  }

  // Si no se encuentra el ID del usuario, retorna null o algún valor que indique que no se pudo obtener
  return null;
}
