<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

$userInfo = verifyAdminAndSession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$nombreUsuarioActivo = $userInfo['nombreUsuarioActivo'];

// Definir los formatos de archivo permitidos
$formatosPermitidos = array('jpg', 'jpeg', 'png');

// Crear una instancia de la clase BaseDeDatos
$db = new BaseDeDatos();

// Obtener la conexión a la base de datos
$conn = $db->getConnection();

// Obtener todos los usuarios y perros
try {
    $usuarios = obtenerUsuarios($conn);
    $perrosUsuarioActivo = obtenerPerrosPorUsuario($conn, $usuarioActivoId);
    $perroSeleccionado = obtenerPerroPorId($conn, $_POST['perroId'] ?? null);
} catch (PDOException $e) {
    handlePdoError($e);
}

// Función para obtener todos los usuarios
function obtenerUsuarios($conn) {
  try {
    $stmt = $conn->query("SELECT id, nombre FROM Usuarios");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    handlePdoError($e);
  }
}

// Función para obtener todos los perros de un usuario
function obtenerPerrosPorUsuario($conn, $idUsuario) {
  try {
    $stmt = $conn->prepare("SELECT id, nombre FROM Perros WHERE idDueno = :idUsuario");
    $stmt->bindValue(':idUsuario', $idUsuario);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    handlePdoError($e);
  }
}

// Función para obtener los datos de un perro por su ID
function obtenerPerroPorId($conn, $idPerro) {
  try {
    $stmt = $conn->prepare("SELECT * FROM Perros WHERE id = :idPerro");
    $stmt->bindValue(':idPerro', $idPerro);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    handlePdoError($e);
  }
}

// Validar y procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los datos del formulario
  $perroId = $_POST['perroId'];
  $nombre = $_POST['nombre'];
  $raza = $_POST['raza'];
  $edad = $_POST['edad'];
  $collar = $_POST['collar'];
  $chip = $_POST['chip'];
  $lugarPerdido = $_POST['lugarPerdido'];
  $fechaPerdido = $_POST['fechaPerdido'];
  $lugarEncontrado = $_POST['lugarEncontrado'];
  $fechaEncontrado = $_POST['fechaEncontrado'];  
  $estado = $_POST['estado'];
  $lugar = $_POST['lugar'];
  $fechaUltimaActualizacion = $_POST['fechaUltimaActualizacion'];
  $color = $_POST['color'];
  $tamano = $_POST['tamano'];
  $descripcion = $_POST['descripcion'];
  $idDueno = $_POST['idDueno'];
  $foto = isset($_FILES['foto']) ? $_FILES['foto'] : '';

  // Realizar validaciones de los datos
  $errores = [];

  if (empty($nombre) || empty($estado)) {
      $errores[] = 'El nombre y el estado son campos requeridos.';
  }

  if (empty($nombre) || strlen($nombre) > 50) {
      $errores[] = 'Nombre inválido. Debe tener menos de 50 caracteres.';
  }

  if (empty($raza) || strlen($raza) > 50) {
      $errores[] = 'Raza inválida. Debe tener menos de 50 caracteres.';
  }

  if ($edad < 0 || $edad > 30) {
      $errores[] = 'Edad inválida. Debe estar entre 0 y 30.';
  }

  if ($collar !== 'si' && $collar !== 'no') {
      $errores[] = 'El campo collar debe ser "si" o "no".';
  }

  if ($chip !== 'si' && $chip !== 'no') {
      $errores[] = 'El campo chip debe ser "si" o "no".';
  }

  if ($estado === 'perdido' && empty($lugarPerdido)) {
      $errores[] = 'El lugar perdido no puede estar vacío cuando el estado es "perdido".';
  }

  if ($estado === 'perdido' && !empty($lugarPerdido) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaPerdido)) {
      $errores[] = 'Fecha perdido inválida.';
  }

  if ($estado === 'encontrado' && empty($lugarEncontrado)) {
      $errores[] = 'El lugar encontrado no puede estar vacío cuando el estado es "encontrado".';
  }

  if ($estado === 'encontrado' && !empty($lugarEncontrado) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEncontrado)) {
      $errores[] = 'Fecha encontrado inválida.';
  }  

  // Validar el tamaño del perro
  if (!in_array($tamano, array('pequeño', 'mediano', 'grande'))) {
      $errores[] = 'Tamaño inválido. Debe ser "pequeño", "mediano" o "grande".';
  }

  // Verificar si hay errores
  if (!empty($errores)) {
      // AManejar los errores
      foreach ($errores as $error) {
        handleNewErrors($mensajeError, '../../Src/Error/mostrarError.php');
      }
  } else {  
    if (empty($lugarPerdido)){
      $lugarPerdido= 'N/A';
    } 
    if (empty($lugarEncontrado)){
      $lugarEncontrado = 'N/A';
    } 
    if ($lugarPerdido == 'N/A'){
      $fechaPerdido = NULL;
    } 
    if ($lugarEncontrado == 'N/A'){
      $fechaEncontrado = NULL;
    }        

    // Realizar la actualización del perro en la base de datos
    try {
      $stmt = $conn->prepare("UPDATE Perros SET nombre = :nombre, raza = :raza, edad = :edad, collar = :collar, chip = :chip, lugarPerdido = :lugarPerdido, fechaPerdido = :fechaPerdido, lugarEncontrado = :lugarEncontrado, fechaEncontrado = :fechaEncontrado, estado = :estado, lugar = :lugar, fechaUltimaActualizacion = :fechaUltimaActualizacion, color = :color, tamano = :tamano, descripcion = :descripcion, idDueno = :idDueno WHERE id = :perroId");

      $stmt->bindParam(':nombre', $nombre);
      $stmt->bindParam(':raza', $raza);
      $stmt->bindParam(':edad', $edad);
      $stmt->bindParam(':collar', $collar);
      $stmt->bindParam(':chip', $chip);
      $stmt->bindParam(':lugarPerdido', $lugarPerdido);
      $stmt->bindParam(':fechaPerdido', $fechaPerdido);
      $stmt->bindParam(':lugarEncontrado', $lugarEncontrado);
      $stmt->bindParam(':fechaEncontrado', $fechaEncontrado);
      $stmt->bindParam(':estado', $estado);
      $stmt->bindParam(':lugar', $lugar);
      $stmt->bindParam(':fechaUltimaActualizacion', $fechaUltimaActualizacion);
      $stmt->bindParam(':color', $color);
      $stmt->bindParam(':tamano', $tamano);
      $stmt->bindParam(':descripcion', $descripcion);
      $stmt->bindParam(':idDueno', $idDueno);
      $stmt->bindParam(':perroId', $perroId);
      $stmt->execute();

      // Obtener el ID del usuario recién actualizado
      $stmt = $conn->prepare("SELECT id FROM Perros ORDER BY fechaUltimaActualizacion DESC LIMIT 1");
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $ultimoIdActualizado = $result['id'];

      if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
          
          // Obtener el nombre de archivo único para la foto
          $photoName = $nombre . $idDueno . '.jpg';
         
          // Definir la ruta de destino para guardar la foto
          $rutaDestino = '../../Publico/fotos_perros/' . $photoName;

          // Mover el archivo de la foto a la ruta de destino
          $result = move_uploaded_file($foto['tmp_name'], $rutaDestino);
          if ($result) {
              echo "Archivo subido con éxito.";
          } else {
              echo "Error al subir el archivo.";
          }
          
          // Actualizar la ruta de la foto de usuario
          $query = "UPDATE Perros SET foto = :foto WHERE id = :id";
          $stmt = $conn->prepare($query);
          $stmt->bindParam(':foto', $photoName);
          $stmt->bindParam(':id', $ultimoIdActualizado);
          $stmt->execute(); 
      } 

      // Redirigir a la página de administración
      header("Location: ../admin.html");
      exit();
    } catch (PDOException $e) {
      handlePdoError($e);
    }
  }
}
// Cerrar la conexión a la base de datos
$conn = null;
?>