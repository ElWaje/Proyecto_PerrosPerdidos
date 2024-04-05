<?php
session_start();

define('INCLUDED', true);

require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

$userInfo = verifySession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$isAdmin = $_SESSION['isAdmin'] ?? false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleErrors("Acceso no permitido.");
}

// Obtener el ID del comentario a editar desde el formulario
$idComentario = $_POST['idComentario'] ?? null;
$textoComentario = $_POST['textoComentario'] ?? null;
$fotoComentario = $_FILES['fotoEditarComentario'] ?? null;

if (empty($idComentario) || empty($textoComentario)) {
    handleErrors("Error: Datos incompletos.");
}

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    // Verificar que el usuario activo es el autor del comentario o un administrador
    $stmt = $conn->prepare("SELECT idUsuario FROM Comentarios WHERE id = :idComentario");
    $stmt->bindValue(':idComentario', $idComentario, PDO::PARAM_INT);
    $stmt->execute();
    $comentario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comentario || ($comentario['idUsuario'] != $usuarioActivoId && !$isAdmin)) {
        handleErrors("Error: No tiene permisos para editar este comentario.");
    }

    // Continuar con la actualización del comentario...
    $stmt = $conn->prepare("UPDATE Comentarios SET texto = :textoComentario WHERE id = :idComentario");
    $stmt->bindValue(':textoComentario', $textoComentario, PDO::PARAM_STR);
    $stmt->bindValue(':idComentario', $idComentario, PDO::PARAM_INT);
    $stmt->execute();

    if ($fotoComentario && $fotoComentario['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = 'comentario_' . $idComentario . '.jpg';
        $rutaDestino = '../Publico/fotos_comentarios/' . $nombreArchivo;
        move_uploaded_file($fotoComentario['tmp_name'], $rutaDestino);

        $stmt = $conn->prepare("UPDATE Comentarios SET foto = :fotoComentario WHERE id = :idComentario");
        $stmt->bindValue(':fotoComentario', $nombreArchivo, PDO::PARAM_STR);
        $stmt->bindValue(':idComentario', $idComentario, PDO::PARAM_INT);
        $stmt->execute();
    }

    header('Location: publicaciones.php');
    exit();
} catch (PDOException $e) {
    handlePdoError($e);
}
?>
