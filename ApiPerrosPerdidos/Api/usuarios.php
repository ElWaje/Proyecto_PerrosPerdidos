<?php
session_start();
define('INCLUDED', true);

require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    $query = "SELECT id, nombre, correoElectronico, telefono, rol, visible, fotoPerfil FROM usuarios";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $usuarios = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $usuarios[] = $row;
    }

    // Cerrar la conexión
    $conn = null; 

    // Devolver los datos como respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($usuarios);
} catch (PDOException $e) {
    handlePdoError($e);
}

?>
