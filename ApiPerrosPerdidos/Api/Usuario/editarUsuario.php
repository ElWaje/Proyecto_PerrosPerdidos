<?php
session_start();

define('INCLUDED', true);

require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php'; 

$userInfo = verifyAdminAndSession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$nombreUsuarioActivo = $userInfo['nombreUsuarioActivo'];

// Crear una instancia de la clase BaseDeDatos
$db = new BaseDeDatos();

// Obtener la conexión a la base de datos
$conn = $db->getConnection();

// Obtener todos los usuarios
$usuarios = obtenerUsuarios($conn);

// Función para obtener todos los usuarios
function obtenerUsuarios($conn) {
    try {
        $stmt = $conn->query("SELECT id, nombre FROM Usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleErrors(handlePdoError($e)); 
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <title>Editar Usuario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <style>
        .selector-container {
            max-width: 500px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .selector-title {            
            color: black;
            text-align: center;
            margin-bottom: 20px;
        }

        .selector-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .selector-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 16px;
        }

        .selector-submit {
            width: 100%;
            background-color: #4caf50;
            color: white;
            margin-top: 20px;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .selector-submit:hover {
            background-color: #45a049;
        }

        /* Estilos para mejorar la accesibilidad */
        [aria-hidden="true"] {
            display: none;
        }

        :focus {
            outline: 3px solid blue;
        }

        @media screen and (max-width: 768px) {
            .selector-container {
                max-width: 90%;
                padding: 15px;
            }
        }

        @media screen and (max-width: 480px) {
            .selector-container {
                max-width: 100%;
                margin: 20px 10px;
                padding: 10px;
            }
        }

        /* Estilos para mejorar la accesibilidad */
        [aria-hidden="true"] {
            display: none;
        }

        :focus {
            outline: 3px solid blue;
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
        <h2 class="titulo-2">Selección de usuario a editar</h2>
        <div class="selector-container">
            <h1 class="selector-title">Seleccionar Usuario</h1>
            <form action="editarUsuarioFormulario.php" method="POST">
                <label for="usuario">Selecciona un usuario:</label>
                <select name="usuarioId" id="usuario" class="selector-select">
                    <option value="" selected disabled>Seleccione un usuario</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['id']; ?>"><?php echo $usuario['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Editar Usuario" class="selector-submit">
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