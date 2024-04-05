<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

verifyAdminAndSession();

$formatosPermitidos = array('jpg', 'jpeg', 'png'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $autorId = $_POST['autor'] ?? '';
    $foto = $_FILES['foto'] ?? null;

    if (empty($titulo) || empty($contenido) || empty($tipo) || empty($autorId)) {
        handleErrors("Todos los campos son obligatorios.");
    }

    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    try {
        $sql = "INSERT INTO Publicaciones (idUsuario, idAutor, titulo, contenido, tipo) 
                VALUES (:idUsuario, :idAutor, :titulo, :contenido, :tipo)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $autorId);
        $stmt->bindParam(':idAutor', $autorId);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':tipo', $tipo);  
        $stmt->execute();

        $publicacionId = $conn->lastInsertId();

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = 'publicacion_' . $publicacionId . '.jpg';
            $rutaDestino = '../../Publico/fotos_publicaciones/' . $nombreArchivo;
            if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
                $query = "UPDATE Publicaciones SET foto = :fotoPublicacion WHERE id = :idPublicacion";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':fotoPublicacion', $nombreArchivo, PDO::PARAM_STR);
                $stmt->bindValue(':idPublicacion', $publicacionId, PDO::PARAM_INT);
                $stmt->execute();
            }
        } else {
            $query = "UPDATE Publicaciones SET foto = 'dog.jpg' WHERE id = :idPublicacion";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':idPublicacion', $publicacionId, PDO::PARAM_INT);
            $stmt->execute();
        }

        header('Location: ../admin.html?success=1');
        exit;
    } catch (PDOException $e) {
        handlePdoError($e);
    }
} else {
    handleErrors("Acceso no válido.");
}