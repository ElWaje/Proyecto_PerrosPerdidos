// Función para obtener todos los comentarios de una publicación desde el servidor
function obtenerComentariosPorPublicacion(idPublicacion) {
  return fetch(`../../../ApiPerrosPerdidos/api/publicaciones/${idPublicacion}/comentarios`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al obtener los comentarios');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para obtener un comentario por su ID desde el servidor
function obtenerComentarioPorId(id) {
  return fetch(`../../../ApiPerrosPerdidos/api/comentarios/${id}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al obtener el comentario');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para crear un nuevo comentario en el servidor
function crearComentario(comentario) {
  return fetch('../../../ApiPerrosPerdidos/api/comentarios', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(comentario)
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al crear el comentario');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para actualizar un comentario en el servidor
function actualizarComentario(id, comentario) {
  return fetch(`../../../ApiPerrosPerdidos/api/comentarios/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(comentario)
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al actualizar el comentario');
      }
      return response.json();
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para eliminar un comentario en el servidor
function eliminarComentario(id) {
  return fetch(`../../../ApiPerrosPerdidos/api/comentarios/${id}`, {
    method: 'DELETE'
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error al eliminar el comentario');
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
}