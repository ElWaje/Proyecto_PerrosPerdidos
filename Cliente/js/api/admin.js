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

// Función para crear una nueva publicación
function crearPublicacion(publicacion) {
  axios.post('/api/publicaciones', publicacion)
    .then(function(response) {
      console.log('Publicación creada:', response.data);
    })
    .catch(function(error) {
      mostrarError('Error al crear la publicación');
      console.error('Error:', error);
    });
}

// Función para actualizar una publicación existente
function actualizarPublicacion(idPublicacion, publicacion) {
  axios.put(`/api/publicaciones/${idPublicacion}`, publicacion)
    .then(function(response) {
      console.log('Publicación actualizada:', response.data);
    })
    .catch(function(error) {
      mostrarError('Error al actualizar la publicación');
      console.error('Error:', error);
    });
}

// Función para eliminar una publicación
function eliminarPublicacion(idPublicacion) {
  axios.delete(`/api/publicaciones/${idPublicacion}`)
    .then(function() {
      console.log('Publicación eliminada');
    })
    .catch(function(error) {
      mostrarError('Error al eliminar la publicación');
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
  const listaPublicaciones = document.getElementById('listaPublicaciones');
  listaPublicaciones.innerHTML = '';

  publicaciones.forEach(function(publicacion) {
    const publicacionElement = document.createElement('li');
    publicacionElement.textContent = `${publicacion.titulo}: ${publicacion.contenido}`;
    listaPublicaciones.appendChild(publicacionElement);
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
verPerrosUsuario(idUsuario);
verPublicacionesUsuario(idUsuario);
verComentariosUsuario(idUsuario);