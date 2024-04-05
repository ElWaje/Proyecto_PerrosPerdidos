<?php
define('INCLUDED', true);
require_once 'Src/Config/BaseDeDatos.php';
require_once 'Src/Config/helpers.php';

function validateUserData($name, $email, $password, $phone, $address, $userVisible) {
    if (empty($name)) {
        return "Por favor, ingresa un nombre.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Por favor, ingresa un correo electrónico válido.";
    }
    if (strlen($password) < 6) {
        return "La contraseña debe tener al menos 6 caracteres.";
    }
    if (empty($phone)) {
        return "Por favor, ingresa un teléfono.";
    }
    if (empty($address)) {
        return "Por favor, ingresa una dirección.";
    }
    if ($userVisible !== "si" && $userVisible !== "no") {
        return "Por favor, selecciona una opción de visibilidad válida.";
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errorMessage = validateUserData($_POST['name'], $_POST['email'], $_POST['password'], $_POST['phone'], $_POST['address'], $_POST['visible']);
    
    if ($errorMessage) {
        handleErrors($errorMessage);
    }
    
    // Hashing the password
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $db = new BaseDeDatos();
        $conn = $db->getConnection();

        $query = "SELECT * FROM usuarios WHERE nombre = :nombre OR correoElectronico = :correoElectronico";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $_POST['name']);
        $stmt->bindParam(':correoElectronico', $_POST['email']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            handleErrors("El nombre o correo electrónico ya están registrados.");
        } else {
            $photoName = isset($_FILES['photo']) && $_FILES['photo']['error'] === 0 ? $_POST['name'] . '.jpg' : 'default-profile-photo.png';
            
            if ($photoName !== 'default-profile-photo.png') {
                $photoRuta = 'Publico/photos/' . $photoName;
                if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoRuta)) {
                    handleErrors("Error al subir el archivo.");
                }
            }

            $query = "INSERT INTO usuarios(nombre, correoElectronico, contrasena, rol, telefono, direccion, visible, fotoPerfil) VALUES (:nombre, :correoElectronico, :contrasena, 'usuario', :telefono, :direccion, :visible, :fotoPerfil)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre', $_POST['name']);
            $stmt->bindParam(':correoElectronico', $_POST['email']);
            $stmt->bindParam(':contrasena', $hashedPassword);
            $stmt->bindParam(':telefono', $_POST['phone']);
            $stmt->bindParam(':direccion', $_POST['address']);
            $stmt->bindParam(':visible', $_POST['visible']);
            $stmt->bindParam(':fotoPerfil', $photoName);
            
            $stmt->execute();

            header("Location: ../Cliente/inicioSesion.html");
            exit();
        }

    } catch (PDOException $exception) {
        handleErrors("Error en la base de datos: " . $exception->getMessage());
    }
}

?>