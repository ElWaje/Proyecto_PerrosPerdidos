<?php
session_start();
define('INCLUDED', true);
require_once '../Config/BaseDeDatos.php';
require_once '../Config/helpers.php';

// Comprobar si existe un mensaje de error en la sesión
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    // Borra el mensaje de error de la sesión para no mostrarlo de nuevo inadvertidamente
    unset($_SESSION['error_message']);
} else {
    // Si no hay mensaje de error, redirige a la página principal
    handleErrors("No se encontró un mensaje de error.");
}
?>

<!DOCTYPE html>
<html lang = "es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>    
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css" />
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        .contenido {
            background-color: #f5f5f5;
        }
        .error-container {
            max-width: 500px;
            margin: 0 auto;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }        
        p {
            margin: 10px 0;
        }
        
        /* Estilos para mejorar la accesibilidad */
        [aria-hidden="true"] {
            display: none;
        }

        :focus {
            outline: 3px solid blue;
        }
        /* Media Queries */
        @media screen and (max-width: 768px) {
            .error-container {
                max-width: 90%;
                padding: 10px;
            }
        }
        @media screen and (max-width: 480px) {
            
        }        
    </style>    
    <script src="cabecera.js"></script>
</head>
<body>
    <header class="header">        
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="../../Api/perros.php">Perros</a></li>
                <li><a href="../../../Cliente/verPerrros.html">Ver Perros</a></li>
                <li><a href="../../../Cliente/perrosAdopcion.html">En Adopción</a></li>
                <li><a href="../../../Cliente/perrosEncontrados.html">Encontrados</a></li>
                <li><a href="../../../Cliente/perrosPerdidos.html">Perdidos</a></li>
                <li><a href="../../../Cliente/usuarios.html">Usuarios</a></li>
                <li><a href="../../Api/publicaciones.php">Publicaciones</a></li>
                <li><a href="../../Api/perfil.php">Perfil</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
            <h1 id="titulo">Error</h1>
            <div class="error-container">
                <h2 class="titulo-2">Error</h2>
                <p><?php echo htmlspecialchars($error_message); ?></p>
                <!-- Botón para volver a la página anterior -->
                <button onclick="goBack()">Volver</button>
            </div>
            <div>
                <p id="error-message"></p>
            </div>
    </div>
    <div id="contenedorPieDePagina"></div>
    <script src="../../../Cliente/js/api/cabecera.js"></script>  
    <script>
        // Función para volver a la página anterior
        function goBack() {
            window.history.back();
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("contenedorCabecera").innerHTML = cargarCabecera('../../../Cliente/img/logo.png', '../../Api/perfil.php');
            document.getElementById("contenedorPieDePagina").innerHTML = cargarPieDePagina('../../../Cliente/img/logo.jpg');
        });
    </script>
</body>
</html>