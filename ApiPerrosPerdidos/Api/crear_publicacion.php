<?php
session_start();
define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession(); 

$idUsuario = $_SESSION['id'];
$idAutor = $idUsuario;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleErrors("Acceso no permitido.");
}

$titulo = $_POST['tituloPublicacion'] ?? null;
$contenido = $_POST['contenidoPublicacion'] ?? null;
$foto = $_FILES['fotoCrearPublicacion'] ?? null;
$tipo = $_POST['tipoPublicacion'] ?? null;

if (empty($titulo) || empty($contenido)) {
    handleErrors("Error: Debes completar todos los campos.");
}

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    $query = "INSERT INTO Publicaciones (idUsuario, idAutor, titulo, contenido, tipo, fecha) 
              VALUES (:idUsuario, :idAutor, :titulo, :contenido, :tipo, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindValue(':idAutor', $idAutor, PDO::PARAM_INT);
    $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
    $stmt->bindValue(':contenido', $contenido, PDO::PARAM_STR);
    $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->execute();

    $idPublicacion = $conn->lastInsertId();

    $nombreArchivo = ($foto && $foto['error'] === UPLOAD_ERR_OK) ? 'publicacion_' . $idPublicacion . '.jpg' : 'dog.jpg';
    $rutaDestino = '../Publico/fotos_publicaciones/' . $nombreArchivo;

    if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
        move_uploaded_file($foto['tmp_name'], $rutaDestino);
    }

    $query = "UPDATE Publicaciones SET foto = :foto WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':foto', $nombreArchivo, PDO::PARAM_STR);
    $stmt->bindValue(':id', $idPublicacion, PDO::PARAM_INT);
    $stmt->execute(); 

    header('Location: publicaciones.php');
    exit();
} catch (PDOException $e) {
    handlePdoError($e);
}
?>
