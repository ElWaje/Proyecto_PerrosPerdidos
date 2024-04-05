<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";
  
try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    $query = "SELECT p.nombre, p.raza, p.edad, p.collar, p.chip, p.lugar, p.fechaUltimaActualizacion, p.foto, p.color, p.tamano, p.descripcion, u.nombre AS nombreDueno 
              FROM perros p
              INNER JOIN usuarios u ON p.idDueno = u.id
              WHERE p.estado = 'en adopcion'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $perros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    handlePdoError($e);
}

header('Content-Type: application/json');
echo json_encode($perros);
?>
