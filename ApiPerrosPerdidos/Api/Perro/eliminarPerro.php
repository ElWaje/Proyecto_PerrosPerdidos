<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

$userInfo = verifyAdminAndSession();

// Obtener el ID del perro a borrar
if (isset($_POST['idPerro'])) {
    $datosPerro = explode(',', $_POST['idPerro']);
    $perroId = $datosPerro[0];
    $nombrePerro = $datosPerro[1];
    $idDueno = $datosPerro[2];

    // Definir la ruta de la foto
    $rutaFoto = '../../Publico/fotos_perros/' . $nombrePerro . $idDueno . '.jpg';

    // Comprobar si la foto existe
    if (file_exists($rutaFoto)) {
        // Intentar eliminar la foto
        if (!unlink($rutaFoto)) {
            handleErrors('Error al eliminar la foto del perro.');
        }
    }
} else {
    // Si no se seleccionó un perro, redirigir al formulario con un mensaje de error
    handleErrors('Perro no seleccionado.');
}

// Crear una instancia de la clase BaseDeDatos y obtener la conexión a la base de datos
$db = new BaseDeDatos();
$conn = $db->getConnection();

// Intentar eliminar el perro de la base de datos
try {
    $stmt = $conn->prepare("DELETE FROM Perros WHERE id = :idPerro");
    $stmt->bindValue(':idPerro', $perroId);
    $stmt->execute();

    // Redireccionar a admin.html con éxito
    header("Location: ../admin.html");
    exit();
} catch (PDOException $e) {
    handlePdoError($e);
}
?>