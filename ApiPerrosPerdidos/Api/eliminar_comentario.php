<?php
session_start();
define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

// Verificar sesión
$userInfo = verifySession(); 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: publicaciones.php');
    exit();
}

$idComentario = $_POST['idComentario'];
$isAdmin = $_SESSION['isAdmin'] ?? false;
$loggedInUserId = $_SESSION['id'];
$userIdToBeEdited = $_POST['idUsuario'] ?? null;
$idAutor = $_POST['idAutor'] ?? null;

if ($loggedInUserId === $idAutor || $isAdmin) {
    try {
        $db = new BaseDeDatos();
        $conn = $db->getConnection();
        // Preparar la consulta SQL para obtener el nombre de la foto
        $queryGetPhoto = "SELECT foto FROM Comentarios WHERE id = :id";
        $stmt = $conn->prepare($queryGetPhoto);
        $stmt->bindValue(':id', $idComentario);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['foto'] != "dog.jpg") {
            // Si la foto no es "dog.jpg", borrarla del directorio
            $file_path = "../Publico/fotos_comentarios/" . $result['foto'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $query = "DELETE FROM Comentarios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $idComentario, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: publicaciones.php');
        exit();
    } catch (PDOException $e) {
        handlePdoError($e);
    }
} else {
    handleErrors("No tienes permisos para eliminar este comentario.");
}
?>
