<?php
session_start();

define('INCLUDED', true);

require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession(); 

$idUsuario = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleErrors("Solicitud no válida.");
}

// Obtener el ID de la publicación a la cual se agregará el comentario desde el formulario
$idPublicacion = $_POST['idPublicacion'];

// Validar el ID de la publicación
if (!is_numeric($idPublicacion)) {
    // Redireccionar a la página de publicaciones si el ID no es válido
    handleErrors("ID no válido.");
}

$foto = isset($_FILES['fotoCrearComentario']) ? $_FILES['fotoCrearComentario'] : '';

// Obtener el texto del comentario desde el formulario
$textoComentario = $_POST['textoComentario'];

// Validar el texto del comentario
if (empty($textoComentario)) {
    handleErrors("Texto de comentario vacío.");
}

// Realizar las operaciones necesarias para crear el comentario en la base de datos

try {
    // Conexión a la base de datos
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    // Obtener el ID del usuario actualmente en sesión
    $idUsuario = $_SESSION['id'];
    $comentarioAutorId = $_POST['idAutor'];
    // Insertar el comentario en la base de datos sin la foto
    $query = "INSERT INTO Comentarios (idUsuario, idPublicacion, idAutor, texto, fecha, meGusta, foto) VALUES (:idUsuario, :idPublicacion, :idAutor, :textoComentario, NOW(), 0, 'dog.jpg')";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindValue(':idPublicacion', $idPublicacion, PDO::PARAM_INT);        
    $stmt->bindValue(':idAutor', $comentarioAutorId, PDO::PARAM_INT);
    $stmt->bindValue(':textoComentario', $textoComentario, PDO::PARAM_STR); 
    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el ID del comentario recién creado
    $idComentario = $conn->lastInsertId();

    // Verificar si se ha subido una imagen
    $nombreArchivo = ($foto && $foto['error'] === UPLOAD_ERR_OK) ? 'comentario_' . $idComentario . '.jpg' : 'dog.jpg';
    

    // Definir la ruta de destino para guardar la foto
    $rutaDestino = '../Publico/fotos_comentarios/' . $nombreArchivo;

    if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
        move_uploaded_file($foto['tmp_name'], $rutaDestino);
    }
       
    // Insertar el comentario en la base de datos con la foto
        
    $query = "UPDATE Comentarios SET foto = :fotoComentario WHERE id = :idComentario";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':fotoComentario', $nombreArchivo, PDO::PARAM_STR);
    $stmt->bindValue(':idComentario', $idComentario, PDO::PARAM_STR);
    $stmt->execute();
    
    
    // Redireccionar a la página de publicaciones después de crear el comentario
    header('Location: publicaciones.php');
    exit();
} catch (PDOException $e) {
    // Manejo de errores en caso de excepción
    handlePdoError($e);
}
?>