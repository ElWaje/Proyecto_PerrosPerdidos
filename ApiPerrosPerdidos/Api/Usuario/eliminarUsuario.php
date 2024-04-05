<?php
session_start();

define('INCLUDED', true);

require_once '../../Src/Config/BaseDeDatos.php';
require_once '../../Src/Config/helpers.php';

// Verificar permisos y sesión
$userInfo = verifyAdminAndSession();

// Obtener la conexión a la base de datos (si BaseDeDatos.php lo proporciona)
$db = new BaseDeDatos();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['usuarioId']) && $_POST['usuarioId'] != '') {
        $datosUsuario = explode(',', $_POST['usuarioId']);
        $idUsuario = $datosUsuario[0];
        $nombreUsuario = $datosUsuario[1];

        // Definir la ruta de la foto
        $rutaFoto = '../../Publico/photos/' . $nombreUsuario . '.jpg';

        // Comprobar y eliminar la foto
        if (file_exists($rutaFoto)) {
            unlink($rutaFoto);
        }

        // Realizar la eliminación del usuario en la base de datos
        eliminarUsuario($conn, $idUsuario);

        echo "<script>
            alert('¡Usuario eliminado con éxito!');
            window.location.href = '../admin.html';
        </script>";
        exit();
    } else {
        handleNewErrors('Usuario no seleccionado', 'borrarUsuario.php');
    }
}

function eliminarUsuario($conn, $usuarioId) {
    try {
        $stmt = $conn->prepare("DELETE FROM Usuarios WHERE id = :usuarioId");
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();
    } catch (PDOException $e) {
        handlePdoError($e);
    }
}
?>