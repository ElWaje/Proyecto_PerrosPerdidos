<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php'; 

verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";

try {
    // Crear una instancia de la clase BaseDeDatos
    $db = new BaseDeDatos();

    // Obtener la conexión a la base de datos
    $conn = $db->getConnection();

    // Validar y procesar el formulario
 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
   
        // Crear un perro nuevo
        $perroId = htmlspecialchars($_POST['perroId']);
        $nombre = htmlspecialchars($_POST['nombre']);
        $raza = htmlspecialchars($_POST['raza']);
        $edad = htmlspecialchars($_POST['edad']);
        $collar = isset($_POST['collar']) ? 'si' : 'no';
        $chip = isset($_POST['chip']) ? 'si' : 'no';
        $lugarPerdido = htmlspecialchars($_POST['lugar-perdido']);
        $fechaPerdido = htmlspecialchars($_POST['fecha-perdido']);
        $lugarEncontrado = htmlspecialchars($_POST['lugar-encontrado']);
        $fechaEncontrado = htmlspecialchars($_POST['fecha-encontrado']);
        $estado = htmlspecialchars($_POST['estado']);
        $lugar = htmlspecialchars($_POST['lugar']);
        $color = htmlspecialchars($_POST['color']);
        $tamano = htmlspecialchars($_POST['tamano']);
        $descripcion = htmlspecialchars($_POST['descripcion']);
        $idDueno = htmlspecialchars($_POST['idDueno']);
        $foto = isset($_FILES['foto']) ? $_FILES['foto'] : '';
        
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

        if (empty($lugarPerdido)){
            $lugarPerdido= 'N/A';
            $fechaPerdido = NULL;
        }

        if ($estado === 'encontrado' && empty($lugarEncontrado)) {
            $errores[] = 'El lugar encontrado no puede estar vacío cuando el estado es "encontrado".';
        }

        if ($estado === 'encontrado' && !empty($lugarEncontrado) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEncontrado)) {
            $errores[] = 'Fecha encontrado inválida.';
        }

        if (empty($lugarEncontrado)){
            $lugarEncontrado= 'N/A';
            $fechaEncontrado = NULL;
        }

        // Validar el tamaño del perro
        if (!in_array($tamano, array('pequeño', 'mediano', 'grande'))) {
            $errores[] = 'Tamaño inválido. Debe ser "pequeño", "mediano" o "grande".';
        }

        // Verificar si hay errores
        if (!empty($errores)) {
            // Manejar los errores
            foreach ($errores as $error) {
                handleErrors($error);
            }
        } else {  
                    
                  
          // Insertar el perro en la base de datos
              $stmt = $conn->prepare("INSERT INTO Perros (nombre, raza, edad, collar, chip, lugarPerdido, fechaPerdido, lugarEncontrado, fechaEncontrado, estado, lugar, fechaUltimaActualizacion, color, tamano, descripcion, idDueno) 
                                      VALUES (:nombre, :raza, :edad, :collar, :chip, :lugarPerdido, :fechaPerdido, :lugarEncontrado, :fechaEncontrado, :estado, :lugar, NOW(), :color, :tamano, :descripcion, :idDueno)");

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
              $stmt->bindParam(':color', $color);
              $stmt->bindParam(':tamano', $tamano);
              $stmt->bindParam(':descripcion', $descripcion);
              $stmt->bindParam(':idDueno', $idDueno);
              $stmt->execute();

              // Obtener el ID del usuario recién creado
              $idPerro = $conn->lastInsertId();

              if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
                  
                  // Obtener el nombre de archivo único para la foto
                  $photoName = $nombre . $idDueno . '.jpg';

                  // Definir la ruta de destino para guardar la foto
                  $rutaDestino = '../Publico/fotos_perros/' . $photoName;

                  // Mover el archivo de la foto a la ruta de destino
                  $result = move_uploaded_file($foto['tmp_name'], $rutaDestino);
                  if (!$result) {                
                    handleErrors("Error al subir el archivo.");
                  }
                  // Actualizar la ruta de la foto del perro
                  $query = "UPDATE Perros SET foto = :foto WHERE id = :id";
                  $stmt = $conn->prepare($query);
                  $stmt->bindValue(':foto', $photoName, PDO::PARAM_STR);
                  $stmt->bindValue(':id', $idPerro, PDO::PARAM_INT);
                  $stmt->execute(); 
              } else {
                  $photoName = 'dog.jpg';
                  $sql = "UPDATE Perros SET foto = :foto WHERE id = :id";
                  $stmt = $conn->prepare($sql);
                  $stmt->bindValue(':foto', $photoName, PDO::PARAM_STR);
                  $stmt->bindValue(':id', $idPerro, PDO::PARAM_INT);
                  $stmt->execute();
              }
              // Redirigir a la página de perros con un mensaje de éxito
              header("Location: perros.php?mensaje=Perro creado con éxito");
              exit();          
        }      
    }  
} catch (PDOException $e) {
  handlePdoError($e);
}
?>