// Función para obtener todas las publicaciones desde el servidor
function obtenerPublicaciones() {
  return fetch('./../../ApiPerrosPerdidos/api/publicaciones.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al obtener las publicaciones');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error al obtener las publicaciones:', error);
      throw error;
    });
}

// Función para obtener una publicación por su ID desde el servidor
function obtenerPublicacionPorId(id) {
  return fetch(`./../../ApiPerrosPerdidos/api/publicaciones.php/${id}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al obtener la publicación');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error al obtener la publicación por ID:', error);
      throw error;
    });
}

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