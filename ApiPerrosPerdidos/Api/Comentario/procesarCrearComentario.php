<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


verifyAdminAndSession();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idUsuario = $_POST['idUsuario'] ?? '';
    $idPublicacion = $_POST['idPublicacion'] ?? '';
    $texto = $_POST['texto'] ?? '';
    $foto = $_FILES['foto'] ?? '';

    if (empty($idUsuario) || empty($idPublicacion) || empty($texto)) {
        handleErrors("Todos los campos son obligatorios.");
    }

    $idAutor = $idUsuario;

    try {
        $db = new BaseDeDatos();
        $conn = $db->getConnection();

        $sql = "INSERT INTO Comentarios (idUsuario, idPublicacion, idAutor, texto, fecha) VALUES (:idUsuario, :idPublicacion, :idAutor, :texto, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario);        
        $stmt->bindParam(':idPublicacion', $idPublicacion);
        $stmt->bindParam(':idAutor', $idAutor);
        $stmt->bindParam(':texto', $texto);
        $stmt->execute();

        $comentarioId = $conn->lastInsertId();

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = 'comentario_' . $comentarioId . '.jpg';
            $rutaDestino = '../../Publico/fotos_comentarios/' . $nombreArchivo;
            move_uploaded_file($foto['tmp_name'], $rutaDestino);

            $query = "UPDATE Comentarios SET foto = :foto WHERE id = :idComentario";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':foto', $nombreArchivo, PDO::PARAM_STR);
            $stmt->bindValue(':idComentario', $comentarioId, PDO::PARAM_INT);
            $stmt->execute(); 
        } else {
            $nombreArchivo = 'dog.jpg';
            $query = "UPDATE Comentarios SET foto = :foto WHERE id = :idComentario";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':foto', $nombreArchivo, PDO::PARAM_STR);
            $stmt->bindValue(':idComentario', $comentarioId, PDO::PARAM_INT);
            $stmt->execute();
        }
        echo "<script>
            alert('¡Comentario creado con éxito!');
            window.location.href = '../admin.html';
        </script>";
        exit();
    } catch (PDOException $e) {
        handlePdoError($e);
    }
} else {
    handleErrors("Acceso no válido.");
}
?>