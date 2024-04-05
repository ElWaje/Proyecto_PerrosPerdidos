<?php
session_start();

define('INCLUDED', true);

require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php'; 


// Verificar si el usuario ha iniciado sesión
verifySession(); 

// Obtener el ID del usuario activo 
$loggedInUserId = $_SESSION['id'];

// Obtener el ID del usuario a editar 
$userIdToBeEdited = $_POST['userId'] ?? null;

// Verificar si el usuario activo es el dueño del perfil que se desea editar 
if ($loggedInUserId !== $userIdToBeEdited) {
    handleErrors("No tienes permisos para editar este usuario.");
}

try {
    // Crear una instancia de la clase BaseDeDatos
    $db = new BaseDeDatos();
    // Obtener la conexión a la base de datos
    $conn = $db->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Validaciones
        if (empty($_POST['nombre']) || strlen($_POST['nombre']) > 50) {
            handleErrors("El nombre es obligatorio y no debe superar los 50 caracteres.");
        }

        if (!filter_var($_POST['correoElectronico'], FILTER_VALIDATE_EMAIL)) {
            handleErrors("Por favor, introduce una dirección de correo electrónico válida.");
        }

        if (empty($_POST['contrasena'])) {
            handleErrors("La contraseña es obligatoria.");
        }

        if (empty($_POST['telefono'])) {
            handleErrors("El teléfono es obligatorio.");
        }

        if (empty($_POST['direccion'])) {
            handleErrors("La dirección es obligatoria.");
        }

        if (!in_array($_POST['visible'], ['si', 'no'])) { 
            handleErrors("Valor no permitido para 'visible'.");
        }
        
        // Actualizar datos del usuario
        $stmtUpdate = $conn->prepare("UPDATE Usuarios SET nombre = :nombre, correoElectronico = :correoElectronico, contrasena = :contrasena, telefono = :telefono, direccion = :direccion, visible = :visible WHERE id = :id");
        $stmtUpdate->bindValue(':nombre', $_POST['nombre']);
        $stmtUpdate->bindValue(':correoElectronico', $_POST['correoElectronico']);
        $stmtUpdate->bindValue(':contrasena', $_POST['contrasena']);
        $stmtUpdate->bindValue(':telefono', $_POST['telefono']);
        $stmtUpdate->bindValue(':direccion', $_POST['direccion']);
        $stmtUpdate->bindValue(':visible', $_POST['visible']);
        $stmtUpdate->bindValue(':id', $userIdToBeEdited);
        $stmtUpdate->execute();

        // Verificar si se envió una foto
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $photoName = $_POST['nombre'] . '.jpg';
            $photoRuta = '../Publico/photos/' . $photoName;
            $result = move_uploaded_file($_FILES['photo']['tmp_name'], $photoRuta);

            if ($result) {
                // Actualizar el nombre de la foto en la BD
                $stmtUpdatePhoto = $conn->prepare("UPDATE Usuarios SET fotoPerfil = :fotoPerfil WHERE id = :id");
                $stmtUpdatePhoto->bindValue(':fotoPerfil', $photoName);
                $stmtUpdatePhoto->bindValue(':id', $userIdToBeEdited);
                $stmtUpdatePhoto->execute();
            } else {
                handleErrors("Error al subir el archivo.");
            }
        }

        header("Location: ../../Cliente/usuarios.html");
        exit();
    }
    
} catch (PDOException $e) {
    handlePdoError($e);
}
?>