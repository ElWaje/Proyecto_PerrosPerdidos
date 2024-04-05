<?php
// Asegúrate de que la sesión está iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!defined('INCLUDED')) {
    http_response_code(403);
    die("Acceso no permitido.");
}

$config = require 'config.php';
$base_url = $config['base_url'];

// Ruta a la página de error personalizada
$error_page = $base_url . "ApiPerrosPerdidos/Src/Error/mostrarError.php";

function redirectToIndexWithError($message) {
    $_SESSION['error_message'] = $message;
    global $base_url;
    header("Location: " . $base_url . "Cliente/inicioSesion.html");
    exit();
}

function handleErrors($message) {
    $_SESSION['error_message'] = $message;
    global $error_page;
    header("Location: $error_page");
    exit();
}

function handleNewErrors($message, $redirectURL = null) {
  // Almacenar el mensaje de error en la sesión para poder mostrarlo en la página de redirección.
  $_SESSION['error_message'] = $message;
  
  // Acceder a la variable global que contiene la ruta a la página de error personalizada.
  global $error_page;
  
  // Comprobar si se proporcionó una URL específica a la que redirigir.
  if ($redirectURL) {
      // Si se proporcionó una URL de redirección, redirigir a esa URL.
      header("Location: $redirectURL");
  } else {
      // Si no se proporcionó una URL de redirección, redirigir a la página de error predeterminada.
      header("Location: $error_page");
  }
  
  // Terminar la ejecución del script para evitar que se ejecute código adicional después de la redirección.
  exit();
}

function handlePdoError(PDOException $e) {
    error_log("Error de base de datos: " . $e->getMessage());
    $_SESSION['error_message'] = "Ocurrió un error al procesar tu solicitud. Por favor, intenta más tarde.";
    global $error_page;
    header("Location: $error_page");
    exit();
}

function verifyAdminAndSession() {
    
    $isAdmin = $_SESSION['isAdmin'] ?? false;
    if (!$isAdmin) {
        redirectToIndexWithError('Acceso denegado: no es administrador.');
    }

    
    if (!isset($_SESSION['id'])) {
        redirectToIndexWithError('Es necesario iniciar sesión.');
    }

    
    $usuarioActivoId = $_SESSION['id'];
    $nombreUsuarioActivo = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "";

    return ['usuarioActivoId' => $usuarioActivoId, 'nombreUsuarioActivo' => $nombreUsuarioActivo];
}

function verifySession() {
    $usuarioActivoId = $_SESSION[''] ?? null;

    
    if (!isset($_SESSION['id'])) {
        redirectToIndexWithError('Es necesario iniciar sesión.');
    }

    
    $usuarioActivoId = $_SESSION['id'];
    $nombreUsuarioActivo = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "";

    return ['usuarioActivoId' => $usuarioActivoId, 'nombreUsuarioActivo' => $nombreUsuarioActivo];
}
