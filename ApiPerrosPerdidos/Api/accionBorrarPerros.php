<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php'; 

verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";  

if (!isset($_POST['perroId']) || empty($_POST['perroId'])) {
    handleErrors("Id de perro inválido");
}

$perroId = $_POST['perroId'];

$db = new BaseDeDatos();
$conn = $db->getConnection();

function obtenerPerroPorId($conn, $perroId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Perros WHERE id = :perroId");
        $stmt->bindValue(':perroId', $perroId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handlePdoError($e);
    }
}

$perro = obtenerPerroPorId($conn, $perroId);

if (!$perro) {
    handleErrors("El perro seleccionado no existe");
}

if ($perro['foto'] && $perro['foto'] != 'dog.jpg') {
    $path = '../Publico/fotos_perros/' . $perro['foto'];
    if (file_exists($path)) {
        try {
            unlink($path);
        } catch (Exception $e) {
            handleErrors("Error al eliminar la foto del perro: " . $e->getMessage());
        }
    }
}

try {
    $stmt = $conn->prepare("DELETE FROM Perros WHERE id = :perroId");
    $stmt->bindValue(':perroId', $perroId);
    $stmt->execute();
    header("Location: perros.php");
    exit();
} catch (PDOException $e) {
    handlePdoError($e);
}
?>