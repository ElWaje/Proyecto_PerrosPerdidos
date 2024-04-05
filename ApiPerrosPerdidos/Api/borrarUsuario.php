<?php
session_start();
define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();

$loggedInUserId = $_SESSION['id'];
$userIdToBeDeleted = $_POST['loggedInUserId'] ?? null;

if ($loggedInUserId !== $userIdToBeDeleted) {
    handleErrors("No tienes permisos para borrar este usuario.");
}

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT fotoPerfil FROM Usuarios WHERE id = :id");
    $stmt->bindValue(':id', $loggedInUserId);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && $user['fotoPerfil'] && $user['fotoPerfil'] != 'default-profile-photo.png') {
        unlink('../Publico/photos/' . $user['fotoPerfil']);
    }
    
    $stmt = $conn->prepare("DELETE FROM Usuarios WHERE id = :id");
    $stmt->bindValue(':id', $loggedInUserId);
    $stmt->execute();

    session_destroy();
    redirectToIndexWithError("Usuario eliminado exitosamente.");

} catch (PDOException $e) {
    handlePdoError($e);
}
?>