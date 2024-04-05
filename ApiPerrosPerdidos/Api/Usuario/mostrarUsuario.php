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
        $stmt = $conn->query("SELECT * FROM Usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleErrors("Error en la conexión a la base de datos: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <title>Mostrar Usuario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <style>
         .tabla-container {
            max-width: 800px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .tabla-title {            
            color: black;
            text-align: center;
            margin-bottom: 20px;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla th,
        .tabla td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .tabla th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .tabla td img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease-in-out;
        }

        .tabla td img:hover {
            transform: scale(2.5);
        }

        .tabla td img,
        .tabla td {
            vertical-align: middle;
        }

        /* Estilos para mejorar la accesibilidad */
        [aria-hidden="true"] {
            display: none;
        }

        :focus {
            outline: 3px solid blue;
        }
        
        /* Media Query para pantallas hasta 768px (Tablets) */
        @media screen and (max-width: 768px) {
            .tabla-container {
                max-width: 90%;
                padding: 15px;
            }

            .tabla {
                font-size: 14px;
            }

            .tabla td img {
                width: 40px;
                height: 40px
            }

        }

        /* Media Query para pantallas hasta 480px (Móviles) */
        @media screen and (max-width: 480px) {
            .tabla-container {
                max-width: 100%;
                margin: 20px 10px;
                padding: 10px;
            }

            .tabla {
                font-size: 12px;
            }

            .tabla td img {
                width: 30px;
                height: 30px;
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
        <h2 class="titulo-2">Mostrar usuarios</h2>
        <div class="tabla-container">
            <h1 class="tabla-title">Usuarios</h1>
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Visible</th>
                        <th>Foto de Perfil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario) { ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo $usuario['nombre']; ?></td>
                        <td><?php echo $usuario['correoElectronico']; ?></td>
                        <td><?php echo $usuario['rol']; ?></td>
                        <td><?php echo $usuario['telefono']; ?></td>
                        <td><?php echo $usuario['direccion']; ?></td>
                        <td><?php echo $usuario['visible']; ?></td>
                        <td>
                            <?php if (!empty($usuario['fotoPerfil'])) { ?>
                                <img src="../../Publico/photos/<?php echo $usuario['fotoPerfil']; ?>" alt="Foto de perfil" width="50" height="50">                          
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
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
 