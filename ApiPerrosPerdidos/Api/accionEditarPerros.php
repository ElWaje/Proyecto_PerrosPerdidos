<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php'; 

verifySession();  
$usuarioActivoId = $_SESSION['id'];
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $foto = isset($_FILES['photo']) ? $_FILES['photo'] : '';

        $errores = [];

        // Validaciones de los campos
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

        // Validación para el lugar
        if (empty($lugar) || strlen($lugar) > 100) {
            $errores[] = 'Lugar inválido. Debe tener menos de 100 caracteres y no puede estar vacío.';
        }

        // Validación para el color
        if (empty($color) || strlen($color) > 30) {
            $errores[] = 'Color inválido. Debe tener menos de 30 caracteres y no puede estar vacío.';
        }

        // Validación para la descripción
        if (strlen($descripcion) > 500) {
            $errores[] = 'Descripción demasiado larga. Debe tener menos de 500 caracteres.';
        }

        // Validación para el ID del dueño
        if (!is_numeric($idDueno) || $idDueno <= 0) {
            $errores[] = 'ID del dueño inválido. Debe ser un número positivo.';
        }

        // Procesar foto subida
        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            // Asignar nuevo nombre a la foto y mover a la carpeta destino
            $photoName = $nombre . '_' . $idDueno . '.jpg';
            $rutaDestino = '../Publico/fotos_perros/' . $photoName;

            if (!move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
                $errores[] = "Error al mover el archivo a la carpeta destino.";
            }
        } elseif ($foto['error'] === UPLOAD_ERR_NO_FILE) {
            // No se subió ninguna foto, obtener la actual
            $stmt = $conn->prepare("SELECT foto FROM Perros WHERE id = :idPerro");
            $stmt->bindValue(':idPerro', $perroId);
            $stmt->execute();
            $currentPhotoData = $stmt->fetch(PDO::FETCH_ASSOC);
            $photoName = $currentPhotoData ? $currentPhotoData['foto'] : null;
        } else {
            $errores[] = "Error al subir la foto.";
        }

        if (!empty($errores)) {
            foreach ($errores as $error) {
                handleErrors($error);
            }
        } else { 
            // Preparar la consulta SQL para actualizar el registro del perro
            $stmt = $conn->prepare("UPDATE Perros SET nombre = :nombre, raza = :raza, edad = :edad, collar = :collar, chip = :chip, lugarPerdido = :lugarPerdido, fechaPerdido = :fechaPerdido, lugarEncontrado = :lugarEncontrado, fechaEncontrado = :fechaEncontrado, estado = :estado, lugar = :lugar, color = :color, tamano = :tamano, descripcion = :descripcion, foto = :foto WHERE id = :idPerro");

            // Vincular los valores a la consulta
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':raza', $raza);
            $stmt->bindValue(':edad', $edad);
            $stmt->bindValue(':collar', $collar);
            $stmt->bindValue(':chip', $chip);
            $stmt->bindValue(':lugarPerdido', $lugarPerdido);
            $stmt->bindValue(':fechaPerdido', $fechaPerdido);
            $stmt->bindValue(':lugarEncontrado', $lugarEncontrado);
            $stmt->bindValue(':fechaEncontrado', $fechaEncontrado);
            $stmt->bindValue(':estado', $estado);
            $stmt->bindValue(':lugar', $lugar);
            $stmt->bindValue(':color', $color);
            $stmt->bindValue(':tamano', $tamano);
            $stmt->bindValue(':descripcion', $descripcion);
            $stmt->bindValue(':foto', $photoName);
            $stmt->bindValue(':idPerro', $perroId);

            // Ejecutar la consulta
            $stmt->execute();

            // Redireccionar a la página perros.php con mensaje de éxito
            header("Location: perros.php?mensaje=Perro actualizado con éxito");
            exit();
        }        
    }    
} catch (PDOException $e) {
    handlePdoError($e);
}
?>