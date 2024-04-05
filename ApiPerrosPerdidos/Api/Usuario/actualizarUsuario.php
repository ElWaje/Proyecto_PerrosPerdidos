<?php
session_start();

define('INCLUDED', true);

require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php'; 

$userInfo = verifyAdminAndSession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$nombreUsuarioActivo = $userInfo['nombreUsuarioActivo'];

// Definir los formatos de archivo permitidos
$formatosPermitidos = array('jpg', 'jpeg', 'png');

// Obtener el ID del usuario seleccionado
$usuarioSeleccionadoId = $_POST['usuarioId'] ?? null;

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $correoElectronico = $_POST['correoElectronico'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $visible = $_POST['visible'] ?? '';
    $fotoPerfil = $_POST['fotoPerfil'] ?? '';
    $foto = $_FILES['photo'] ?? null;

    if (empty($nombre) || empty($correoElectronico) || empty($contrasena) || empty($rol) || empty($telefono) || empty($direccion) || empty($visible)) {
        handleNewErrors("Todos los campos son obligatorios.", "editarUsuarioFormulario.php?usuarioId=$usuarioSeleccionadoId&error=empty");
    }

    // Crear una instancia de la clase BaseDeDatos
    $db = new BaseDeDatos();

    // Obtener la conexión a la base de datos
    $conn = $db->getConnection();

    try {
        actualizarUsuario($conn, $usuarioSeleccionadoId, $nombre, $correoElectronico, $contrasena, $rol, $telefono, $direccion, $visible, $fotoPerfil, $foto, $formatosPermitidos);
        header("Location: ../admin.html?success=1");
        exit();
    } catch (Exception $e) {
        handleNewErrors($e->getMessage(), "editarUsuarioFormulario.php?usuarioId=$usuarioSeleccionadoId&error");
    }
} else {
    handleNewErrors("Formulario no enviado correctamente.", "../admin.html");
}

function actualizarUsuario($conn, $usuarioId, $nombre, $correoElectronico, $contrasena, $rol, $telefono, $direccion, $visible, $fotoPerfil, $foto, $formatosPermitidos) {
    try {
        $stmt = $conn->prepare("UPDATE Usuarios SET nombre = :nombre, correoElectronico = :correoElectronico, contrasena = :contrasena, rol = :rol, telefono = :telefono, direccion = :direccion, visible = :visible, fotoPerfil = :fotoPerfil WHERE id = :usuarioId");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':correoElectronico', $correoElectronico);
        $stmt->bindValue(':contrasena', $contrasena);
        $stmt->bindValue(':rol', $rol);
        $stmt->bindValue(':telefono', $telefono);
        $stmt->bindValue(':direccion', $direccion);
        $stmt->bindValue(':visible', $visible);

        if ($foto && $foto['error'] === 0) {
            $nombreTmpUsuario = $foto['name'];
            $extension = pathinfo($nombreTmpUsuario, PATHINFO_EXTENSION);

            if (!in_array(strtolower($extension), $formatosPermitidos)) {
                handleErrors('Formato de imagen inválido. Solo se permiten archivos JPG, JPEG y PNG.');
            }

            $photoName = $nombre . '.jpg';
            $photoRuta = '../../Publico/photos/' . $photoName;
            $result = move_uploaded_file($foto['tmp_name'], $photoRuta);
            if (!$result) {
                handleErrors('Error al subir el archivo.');
            }

            $stmt->bindParam(':fotoPerfil', $photoName);
        } else {
            $stmt->bindValue(':fotoPerfil', $fotoPerfil);
        }

        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->execute();
    } catch (PDOException $e) {
        $message = handlePdoError($e);
        handleErrors($message);
    }
}
?>