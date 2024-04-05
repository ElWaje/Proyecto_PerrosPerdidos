<?php
session_start();
define('INCLUDED', true);
require_once 'Src/Config/BaseDeDatos.php';
require_once 'Src/Config/helpers.php';

// Verificar si se envió el formulario de autenticación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validar los datos recibidos
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        // Los datos son válidos, realizar la autenticación con la base de datos
        try {
            $database = new BaseDeDatos();
            $conn = $database->getConnection();

            // Consultar la base de datos para verificar el correo electrónico
            $query = "SELECT id, nombre, contrasena, rol FROM usuarios WHERE correoElectronico = :correoElectronico";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':correoElectronico', $email);
            $stmt->execute();

            // Verificar si se encontró un registro en la base de datos
            if ($stmt->rowCount() === 1) {
                // Obtiene la contraseña hasheada y verifica
                $row = $stmt->fetch();
                $hashedPassword = $row['contrasena'];

                if (password_verify($password, $hashedPassword)) {
                    // Autenticación exitosa
                    $idUsuario = $row['id'];
                    $nombreUsuario = $row['nombre'];
                    $rolUsuario = $row['rol'];

                    // Guardar los datos del usuario en la sesión
                    $_SESSION['id'] = $idUsuario;
                    $_SESSION['nombre'] = $nombreUsuario;
                    $_SESSION['isAdmin'] = ($rolUsuario === 'admin');

                    // Redirigir a la página de perfil
                    header("Location: Api/perfil.php");
                    exit();
                } else {
                    redirectToIndexWithError('Contraseña incorrecta. Por favor intenta de nuevo.');
                }
            } else {
                redirectToIndexWithError('No se encontró un registro coincidente en la base de datos');
            }
        } catch (PDOException $exception) {
            redirectToIndexWithError('Ocurrió un error durante el inicio de sesión. Por favor intenta de nuevo.');
        }
    } else {
        redirectToIndexWithError('Datos de inicio de sesión inválidos. Por favor intenta de nuevo.');
    }
} else {
    redirectToIndexWithError('El formulario no fue enviado correctamente.');
}
?>