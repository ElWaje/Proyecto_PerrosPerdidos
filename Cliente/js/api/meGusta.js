// Función para dar/quitar me gusta a una publicación en el servidor
function gestionarMeGustaPublicacion(idPublicacion, accion) {
  fetch(`me_gusta_publicacion.php`, {
    method: 'POST',
    body: new URLSearchParams({
      'idPublicacion': idPublicacion
    }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  })
    .then(response => {
      if (response.redirected) {
        window.location.href = response.url;
      } else {
        throw new Error('La solicitud no resultó en una redirección.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Función para dar/quitar me gusta a un comentario en el servidor
function gestionarMeGustaComentario(idComentario) {
  fetch(`me_gusta_comentario.php`, {
    method: 'POST',
    body: new URLSearchParams({
      'idComentario': idComentario
    }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  })
  .then(response => {
    if (response.redirected) {
      window.location.href = response.url;
    } else {
      throw new Error('La solicitud no resultó en una redirección.');
    }
  })
  .catch(error => {
    console.error('Error:', error);
  });
}