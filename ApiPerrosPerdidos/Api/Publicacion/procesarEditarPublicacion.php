<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

verifyAdminAndSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $publicacionId = $_POST['publicacionIdSeleccionada'] ?? null;
    $titulo = $_POST['titulo'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $idAutor = $_POST['idAutor'] ?? '';
    $foto = $_FILES['foto'] ?? null;

    if (empty($titulo) || empty($contenido) || empty($tipo)) {
        handleErrors("Todos los campos son obligatorios.");
    }

    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    try {
        $stmt = $conn->prepare("UPDATE Publicaciones SET idUsuario = :idUsuario, idAutor = :idAutor, titulo = :titulo, contenido = :contenido, tipo = :tipo WHERE id = :id");
        $stmt->bindParam(':idUsuario', $idAutor);
        $stmt->bindParam(':idAutor', $idAutor);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':id', $publicacionId);   
        $stmt->execute();

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $photoName = 'publicacion_' . $publicacionId . '.jpg';
            $rutaDestino = '../../Publico/fotos_publicaciones/' . $photoName;

            if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
                $query = "UPDATE Publicaciones SET foto = :foto WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':foto', $photoName);
                $stmt->bindParam(':id', $publicacionId);
                $stmt->execute();                         
            } else {
                handleErrors("Error al subir el archivo.");
            }
        } 

        header('Location: ../admin.html?edit_success=1');
        exit;
    } catch (PDOException $e) {
        handlePdoError($e);
    }
} else {
    handleErrors("Acceso no válido.");
}
?>