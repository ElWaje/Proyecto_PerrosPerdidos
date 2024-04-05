<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


verifyAdminAndSession();

$usuarioActivoId = $_SESSION['id'];

$nombreUsuarioActivo = $_SESSION['nombre'] ?? '';

$db = new BaseDeDatos();
$conn = $db->getConnection();

$usuarios = obtenerUsuarios($conn);

function obtenerUsuarios($conn) {
    try {
        $stmt = $conn->query("SELECT id, nombre FROM Usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handlePdoError($e);
    }
}

function mostrarUsuarios($usuarios) {
    foreach ($usuarios as $usuario) {
        echo '<option value="' . $usuario['id'] . '">' . htmlspecialchars($usuario['id'] . ' - ' . $usuario['nombre']) . '</option>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <title>Mostrar Comentario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <style>
            
        .formulario-container {
            max-width: 500px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .formulario-title {            
            color: black;
            text-align: center;
            margin-bottom: 20px;
        }

        .formulario-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .formulario-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 16px;
        }

        .formulario-submit {
            width: 100%;
            background-color: #4caf50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .formulario-submit:hover {
            background-color: #45a049;
        }
        
        /* Estilos para mejorar la accesibilidad */
        [aria-hidden="true"] {
            display: none;
        }

        :focus {
            outline: 3px solid blue;
        } 

        /* Media Query para tablets (pantallas hasta 768px) */
        @media screen and (max-width: 768px) {
            .formulario-container {
                margin-top: 30px;
                margin-bottom: 100px;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .formulario-container {
                margin-top: 20px;
                margin-bottom: 80px;
                padding: 10px;
            }

        }

    </style>
</head>
<body>
<header class="header">
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="../perfil.php">Perfil</a></li>
                <li><a href="../admin.html">Administración</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
        <h1 id="titulo" >Panel de Administración</h1>
        <h2 class="titulo-2">Mostrar Comentario</h2>
        <div class="formulario-container">
            <h1 class="formulario-title">Mostrar Comentario</h1>
            <form action="visualizarComentario.php" method="post">
                <label for="usuario">Selecciona un usuario:</label>
                <select name="usuario" id="usuario" class="formulario-select">
                    <option value="">Seleccionar un usuario</option>
                    <?php mostrarUsuarios($usuarios); ?>
                </select>
                <input type="submit" value="Ver Comentarios" class="formulario-submit">
            </form>
        </div>
    </div>  
    <div id="contenedorPieDePagina"></div>
    <script src="../../../Cliente/js/api/cabecera.js"></script>
    <script>
       document.addEventListener("DOMContentLoaded", function () {
           document.getElementById("contenedorCabecera").innerHTML =
           cargarCabecera(
               "../../../Cliente/img/logo.png",
               "../perfil.php"
           );
           document.getElementById("contenedorPieDePagina").innerHTML =
           cargarPieDePagina("../../../Cliente/img/logo.jpg");
       });
    </script>
</body>
</html>