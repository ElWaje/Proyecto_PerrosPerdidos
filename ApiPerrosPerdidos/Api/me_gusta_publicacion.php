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

// Obtener el ID de la publicación a la que se dio/quitó me gusta desde el formulario
$idPublicacion = $_POST['idPublicacion'];

// Validar el ID de la publicación
if (!is_numeric($idPublicacion)) {
    // Redireccionar a la página de publicaciones si el ID no es válido
    header('Location: publicaciones.php');
    exit();
}

// Realizar las operaciones necesarias para agregar/quitar me gusta a la publicación en la base de datos

try {
    // Conexión a la base de datos
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    // Verificar si el usuario ya dio me gusta a la publicación
    $idUsuario = $_SESSION['id'];

    $query = "SELECT COUNT(*) AS meGusta FROM MeGustasPublicacion WHERE idUsuario = :idUsuario AND idPublicacion = :idPublicacion";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindValue(':idPublicacion', $idPublicacion, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $meGusta = ($result['meGusta'] > 0);

    // Si el usuario ya dio me gusta, quitar el me gusta
    if ($meGusta) {
        $query = "DELETE FROM MeGustasPublicacion WHERE idUsuario = :idUsuario AND idPublicacion = :idPublicacion";
    } else {
        // Si el usuario no dio me gusta, agregar el me gusta
        $query = "INSERT INTO MeGustasPublicacion (idUsuario, idPublicacion) VALUES (:idUsuario, :idPublicacion)";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindValue(':idPublicacion', $idPublicacion, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener el número actualizado de me gustas de la publicación
    $queryMeGustasPublicacion = "SELECT COUNT(*) AS meGustas FROM MeGustasPublicacion WHERE idPublicacion = :idPublicacion";
    $stmtMeGustasPublicacion = $conn->prepare($queryMeGustasPublicacion);
    $stmtMeGustasPublicacion->bindValue(':idPublicacion', $idPublicacion, PDO::PARAM_INT);
    $stmtMeGustasPublicacion->execute();
    $resultMeGustasPublicacion = $stmtMeGustasPublicacion->fetch(PDO::FETCH_ASSOC);
    $meGustas = $resultMeGustasPublicacion['meGustas'];

   // Redirige a publicaciones.php una vez completada la operación
   header('Location: publicaciones.php');
   exit();
} catch (PDOException $e) {
    // Manejo de errores en caso de excepción
    handlePdoError($e);
}
?>