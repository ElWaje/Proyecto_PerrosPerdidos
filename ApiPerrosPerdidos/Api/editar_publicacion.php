<?php
session_start();
define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

// Verificar sesión y permisos del usuario
$userInfo = verifySession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$isAdmin = $_SESSION['isAdmin'] ?? false;

// Incluir el archivo BaseDeDatos.php
include_once '../Src/Config/BaseDeDatos.php';

// Obtener el ID de la publicación a editar desde el formulario
if (isset($_POST['idPublicacion'])) {
    $idPublicacion = trim($_POST['idPublicacion']);
    if (empty($idPublicacion)) {
        handleErrors("Error: ID de publicación inválido");
    }
} else {
    handleErrors("Error: No se ha proporcionado el ID de publicación");
}

// Validar que el usuario activo es el autor de la publicación o un administrador
$db = new BaseDeDatos();
$conn = $db->getConnection();
$query = "SELECT idUsuario FROM Publicaciones WHERE id = :idPublicacion";
$stmt = $conn->prepare($query);
$stmt->bindValue(':idPublicacion', $idPublicacion, PDO::PARAM_INT);
$stmt->execute();
$publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$publicacion || ($publicacion['idUsuario'] != $usuarioActivoId && !$isAdmin)) {
    handleErrors("Error: No tiene permisos para editar esta publicación.");
}

// Obtener los datos actualizados de la publicación desde el formulario
if (isset($_POST['tituloPublicacion']) && isset($_POST['contenidoPublicacion'])) {
    $titulo = trim($_POST['tituloPublicacion']);
    $contenido = trim($_POST['contenidoPublicacion']);
    $fotoPublicacion = isset($_FILES['fotoEditPublicacion']) ? $_FILES['fotoEditPublicacion'] : null;
    if (empty($titulo) || empty($contenido)) {
        handleErrors("Error: Los campos de título y contenido son obligatorios");
    }
} else {
    handleErrors("Error: No se han proporcionado los datos de la publicación");
}

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection(); 

    // Preparar la consulta SQL para actualizar la publicación
    $query = "UPDATE Publicaciones SET titulo = :tituloPublicacion, contenido = :contenidoPublicacion WHERE id = :idPublicacion";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':tituloPublicacion', $titulo);
    $stmt->bindValue(':contenidoPublicacion', $contenido);
    $stmt->bindValue(':idPublicacion', $idPublicacion);
    $stmt->execute();   
    
    // Manejo de la foto de la publicación
    if ($fotoPublicacion && $fotoPublicacion['error'] === UPLOAD_ERR_OK) {
        // Obtener el nombre de archivo único para la foto
        $photoName = 'publicacion_' . $idPublicacion . '.jpg';

        // Definir la ruta de destino para guardar la foto
        $rutaDestino = '../../Publico/fotos_publicaciones/' . $photoName;

        // Mover el archivo de la foto a la ruta de destino
        move_uploaded_file($fotoPublicacion['tmp_name'], $rutaDestino);
        
        // Actualizar la ruta de la foto de la publicación
        $query = "UPDATE Publicaciones SET foto = :foto WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':foto', $photoName, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idPublicacion, PDO::PARAM_INT);
        $stmt->execute();                         
    }   
      

    // Redirigir a la página de publicaciones después de editar la publicación
    header('Location: publicaciones.php');
    exit();
} catch (PDOException $e) {
    handlePdoError($e);
}
?>