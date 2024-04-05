// Obtener el contenedor de mensajes de error
const errorMessageContainer = document.getElementById('error-message');

// Función para mostrar un mensaje de error
function mostrarError(mensaje) {
  // Crear un elemento de párrafo para el mensaje de error
  const errorElement = document.createElement('p');
  errorElement.textContent = mensaje;

  // Agregar el mensaje de error al contenedor
  errorMessageContainer.appendChild(errorElement);
}

// Función para borrar todos los mensajes de error
function borrarErrores() {
  // Vaciar el contenedor de mensajes de error
  errorMessageContainer.innerHTML = '';
}