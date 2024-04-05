// Función para obtener la lista de usuarios desde la API
function obtenerUsuarios() {
  axios.get('/api/usuarios')
    .then(function(response) {
      if (response.status === 200) {
        const usuarios = response.data;
        renderizarUsuarios(usuarios);
      } else {
        console.error('Error al obtener la lista de usuarios');
      }
    })
    .catch(function(error) {
      console.error('Error al realizar la solicitud:', error);
    });
}

// Función para crear un usuario
function crearUsuario(nombre, email, contraseña) {
  const datos = {
    nombre: nombre,
    email: email,
    contraseña: contraseña
  };

  fetch('/api/usuarios', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(datos)
  })
    .then(function(response) {
      if (response.ok) {
        console.log('El usuario se creó correctamente');
        obtenerUsuarios();
      } else {
        console.error('Error al crear el usuario');
      }
    })
    .catch(function(error) {
      console.error('Error al realizar la solicitud:', error);
    });
}

// Función para actualizar un usuario
function actualizarUsuario(id, nombre, email, contraseña) {
  const datos = {
    nombre: nombre,
    email: email,
    contraseña: contraseña
  };

  fetch(`/api/usuarios/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(datos)
  })
    .then(function(response) {
      if (response.ok) {
        console.log('El usuario se actualizó correctamente');
        obtenerUsuarios();
      } else {
        console.error('Error al actualizar el usuario');
      }
    })
    .catch(function(error) {
      console.error('Error al realizar la solicitud:', error);
    });
}

// Función para borrar un usuario
function borrarUsuario(id) {
  fetch(`/api/usuarios/${id}`, {
    method: 'DELETE'
  })
    .then(function(response) {
      if (response.ok) {
        console.log('El usuario se borró correctamente');
        obtenerUsuarios();
      } else {
        console.error('Error al borrar el usuario');
      }
    })
    .catch(function(error) {
      console.error('Error al realizar la solicitud:', error);
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

obtenerUsuarios();