<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

verifyAdminAndSession();

if (isset($_POST['idPublicacion'])) {
    $publicacionId = $_POST['idPublicacion'];

    // Definir la ruta de la foto
    $rutaFoto = '../../Publico/fotos_publicaciones/publicacion_' . $publicacionId . '.jpg';
    
    // Comprobar si la foto existe
    if (file_exists($rutaFoto)) {
        // Eliminar la foto
        unlink($rutaFoto);
    }

    try {
        $db = new BaseDeDatos();
        $conn = $db->getConnection();

        // Eliminar la publicación seleccionada
        $sqlEliminarPublicacion = "DELETE FROM Publicaciones WHERE id = :publicacionId";
        $stmtEliminarPublicacion = $conn->prepare($sqlEliminarPublicacion);
        $stmtEliminarPublicacion->bindParam(':publicacionId', $publicacionId, PDO::PARAM_INT);
        $stmtEliminarPublicacion->execute();

        // Redireccionar a la página de admin.html con un mensaje de éxito
        header('Location: ../admin.html?edit_success=1');
        exit;
    
    } catch (PDOException $e) {
        handlePdoError($e);
    }
}