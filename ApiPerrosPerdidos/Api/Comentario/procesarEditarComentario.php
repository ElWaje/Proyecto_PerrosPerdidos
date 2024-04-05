<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


verifyAdminAndSession();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idUsuario = $_POST['usuario'];
    $idComentario = $_POST['comentario'];
    $texto = $_POST['texto']; 
    $fotoActual = $_POST['foto_actual'];       
    $foto = isset($_FILES['foto']) ? $_FILES['foto'] : '';

    if (empty($idUsuario) || empty($idComentario) || empty($texto)) {
        handleErrors("Todos los campos son obligatorios.");
    }

    $idAutor = $idUsuario;

    try {
        $db = new BaseDeDatos();
        $conn = $db->getConnection();

        if ($foto) {           
            $nombreArchivo = 'comentario_' . $idComentario . '.jpg';
            $rutaDestino = '../../Publico/fotos_comentarios/' . $nombreArchivo;
            move_uploaded_file($foto['tmp_name'], $rutaDestino);
            $query = "UPDATE Comentarios SET idUsuario = :idUsuario, idAutor = :idAutor, texto = :texto, foto = :foto WHERE id = :idComentario";
        } else {
            $query = "UPDATE Comentarios SET idUsuario = :idUsuario, idAutor = :idAutor, texto = :texto, foto = :foto WHERE id = :idComentario";
        }
        
        $fotoParaGuardar = ($foto) ? $nombreArchivo : $fotoActual;

        $stmt = $conn->prepare($query);    
        $stmt->bindParam(':idUsuario', $idUsuario);
        $stmt->bindParam(':idAutor', $idAutor);
        $stmt->bindParam(':texto', $texto);        
        $stmt->bindParam(':foto', $fotoParaGuardar);
        $stmt->bindParam(':idComentario', $idComentario);
        $stmt->execute();

        echo "<script>
            alert('¡Comentario editado con éxito!');
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