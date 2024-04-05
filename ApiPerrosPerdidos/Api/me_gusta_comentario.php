<?php
session_start();
define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

$usuarioActivo = verifySession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireccionar a la página de publicaciones si no se recibe una solicitud POST
    header('Location: publicaciones.php');
    exit();
}

// Obtener el ID del comentario al que se dio/quitó me gusta desde el formulario
$idComentario = $_POST['idComentario'];

// Validar el ID del comentario
if (!is_numeric($idComentario)) {
    // Redireccionar a la página de publicaciones si el ID no es válido
    header('Location: publicaciones.php');
    exit();
}

// Realizar las operaciones necesarias para agregar/quitar me gusta al comentario en la base de datos

try {
    // Conexión a la base de datos
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    // Verificar si el usuario ya dio me gusta al comentario
    $idUsuario = $_SESSION['id'];

    $query = "SELECT COUNT(*) AS meGusta FROM MeGustasComentario WHERE idUsuario = :idUsuario AND idComentario = :idComentario";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindValue(':idComentario', $idComentario, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $meGusta = ($result['meGusta'] > 0);

    // Si el usuario ya dio me gusta, quitar el me gusta
    if ($meGusta) {
        $query = "DELETE FROM MeGustasComentario WHERE idUsuario = :idUsuario AND idComentario = :idComentario";
    } else {
        // Si el usuario no dio me gusta, agregar el me gusta
        $query = "INSERT INTO MeGustasComentario (idUsuario, idComentario) VALUES (:idUsuario, :idComentario)";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindValue(':idComentario', $idComentario, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener el número actualizado de me gustas del comentario
    $queryMeGustasComentario = "SELECT COUNT(*) AS meGustas FROM MeGustasComentario WHERE idComentario = :idComentario";
    $stmtMeGustasComentario = $conn->prepare($queryMeGustasComentario);
    $stmtMeGustasComentario->bindValue(':idComentario', $idComentario, PDO::PARAM_INT);
    $stmtMeGustasComentario->execute();
    $resultMeGustasComentario = $stmtMeGustasComentario->fetch(PDO::FETCH_ASSOC);
    $meGustas = $resultMeGustasComentario['meGustas'];

    // Redirige a publicaciones.php una vez completada la operación
    header('Location: publicaciones.php');
    exit();

} catch (PDOException $e) {
    // Manejo de errores en caso de excepción
    handlePdoError($e);
}
?>