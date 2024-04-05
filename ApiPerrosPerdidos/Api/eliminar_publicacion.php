<?php
session_start();
define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

// Verificar sesión
$userInfo = verifySession(); 
// Obtener el ID de la publicación a eliminar desde el formulario
if (isset($_POST['idPublicacion'])) {
    $idPublicacion = trim($_POST['idPublicacion']);

    // Validar que el ID de la publicación no esté vacío
    if (empty($idPublicacion)) {
        // Manejar el error o mostrar un mensaje de error al usuario
        echo "Error: ID de publicación inválido";

        // Redireccionar a la página de publicaciones después de mostrar el mensaje de error
        header('Location: publicaciones.php');
        exit();
    }
} else {
    // Manejar el error o mostrar un mensaje de error al usuario
    echo "Error: No se ha proporcionado el ID de publicación";

    // Redireccionar a la página de publicaciones después de mostrar el mensaje de error
    header('Location: publicaciones.php');
    exit();
}

$isAdmin = $_SESSION['isAdmin'] ?? false;
$loggedInUserId = $_SESSION['id'];
$userIdToBeEdited = $_POST['idUsuario'] ?? null;
$idAutor = $_POST['idAutor'] ?? null;

if ($loggedInUserId === $idAutor || $isAdmin) {
    try {
            // Obtener la conexión a la base de datos
            $db = new BaseDeDatos();
            $conn = $db->getConnection();
        
            // Preparar la consulta SQL para obtener el nombre de la foto
            $queryGetPhoto = "SELECT foto FROM Publicaciones WHERE id = :id";
            $stmt = $conn->prepare($queryGetPhoto);
            $stmt->bindValue(':id', $idPublicacion);
        
            // Ejecutar la consulta
            $stmt->execute();
        
            // Obtener los resultados
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($result && $result['foto'] != "dog.jpg") {
                // Si la foto no es "dog.jpg", borrarla del directorio
                $file_path = "../Publico/fotos_publicaciones/" . $result['foto'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        
            // Preparar la consulta SQL para eliminar la publicación
            $queryDeletePost = "DELETE FROM Publicaciones WHERE id = :id";
            $stmt = $conn->prepare($queryDeletePost);
            $stmt->bindValue(':id', $idPublicacion);
        
            // Ejecutar la consulta para eliminar la publicación
            $stmt->execute();

            // Redireccionar a la página de publicaciones después de eliminar la publicación
            header('Location: publicaciones.php');
            exit();
        } catch (PDOException $e) {
            // Manejar el error o mostrar un mensaje de error al usuario
            handlePdoError($e);
        }
} else {
    handleErrors("No tienes permisos para eliminar esta publicación.");
}
?>