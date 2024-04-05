<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/helpers.php'; 
require_once '../Src/Config/BaseDeDatos.php';

verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";
  
try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    // Verificar si se solicitan las razas
    if (isset($_GET['getRazas'])) {
        // Realizar la consulta para obtener las razas
        $query = "SELECT DISTINCT raza FROM perros";
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Obtener las razas como un array
        $razas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Agregar la opción "Todos" al array de razas
        array_unshift($razas, 'todos');

        echo json_encode($razas);
        exit(); 
    }

    // Verificar si se realiza la búsqueda de perros por raza
    if (isset($_GET['raza'])) {
        $raza = $_GET['raza'];

        // Realizar la consulta para obtener los perros por raza
        $query = "SELECT perros.*, usuarios.nombre AS nombreDueno 
        FROM perros 
        JOIN usuarios ON perros.idDueno = usuarios.id";

        if ($raza !== 'todos') { 
            $query .= " WHERE perros.raza = :raza";
        }

        $stmt = $conn->prepare($query);
        
        if ($raza !== 'todos') { 
            $stmt->bindParam(':raza', $raza);
        }

        $stmt->execute();

        // Obtener los perros por raza como un array
        $perros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($perros);
        exit(); 
    }
} catch (PDOException $e) {
    handlePdoError($e);
}
?>