<?php
session_start();

define('INCLUDED', true);

require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


// Utilizar la función de verificación de sesión y administrador
$userInfo = verifyAdminAndSession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$nombreUsuarioActivo = $userInfo['nombreUsuarioActivo'];

// Crear una instancia de la clase BaseDeDatos
$db = new BaseDeDatos();

// Intentar obtener la conexión a la base de datos y realizar las operaciones
try {
    $conn = $db->getConnection();
    $usuarios = obtenerUsuarios($conn);
    $usuarioSeleccionadoId = $_POST['usuario'] ?? null;
    $nombreUsuarioSeleccionado = obtenerNombreUsuarioPorId($conn, $usuarioSeleccionadoId);
    $perrosUsuarioSeleccionado = obtenerPerrosPorUsuario($conn, $usuarioSeleccionadoId);
} catch (PDOException $e) {
    handlePdoError($e);
}

// Función para obtener todos los usuarios
function obtenerUsuarios($conn) {
    try {
        $stmt = $conn->query("SELECT id, nombre FROM Usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<script>alert('Error en la conexión a la base de datos: " . $e->getMessage() . "');</script>";
    }
}

// Función para obtener el nombre de un usuario por su ID
function obtenerNombreUsuarioPorId($conn, $idUsuario) {
    try {
        $stmt = $conn->prepare("SELECT nombre FROM Usuarios WHERE id = :idUsuario");
        $stmt->bindValue(':idUsuario', $idUsuario);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        echo "<script>alert('Error en la conexión a la base de datos: " . $e->getMessage() . "');</script>";
    }
}

// Función para obtener todos los perros de un usuario
function obtenerPerrosPorUsuario($conn, $idUsuario) {
    try {
        $stmt = $conn->prepare("SELECT id, nombre FROM Perros WHERE idDueno = :idUsuario");
        $stmt->bindValue(':idUsuario', $idUsuario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<script>alert('Error en la conexión a la base de datos: " . $e->getMessage() . "');</script>";
    }
}

// Función para mostrar los perros de un usuario en un selector
function mostrarPerros($perros) {
    foreach ($perros as $perro) {
        echo '<option value="' . $perro['id'] . '">' . $perro['id'] . ' - ' . $perro['nombre'] . '</option>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <title>Editar Perro</title>
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
                max-width: 90%;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .formulario-container {
                max-width: 100%;
                padding: 10px;
            }

            .formulario-select, .formulario-submit {
                font-size: 14px;
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
            <li><a href="editarPerro.php">Volver</a></li>
            <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
        </ul>
    </div>        
</header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
        <h1 id="titulo" >Panel de Administración</h1>
        <h2 class="titulo-2">Editar perro</h2>
        <div class="formulario-container">
            <h1 class="formulario-title">Editar Perro</h1>
            <?php if (empty($perrosUsuarioSeleccionado)): ?>
                        <!-- Mostrar el mensaje cuando no hay perros -->
                        <p>El usuario no tiene perros</p>
            <?php else: ?>
                        <form action="editarPerroFormulario.php" method="POST">
                            <input type="hidden" name="usuario" value="<?php echo $usuarioSeleccionadoId; ?>">
                            <label for="perro">Selecciona un perro de <?php echo $nombreUsuarioSeleccionado; ?>:</label>
                            <select name="idPerro" id="perro" class="formulario-select">
                                    <option value="" selected disabled>Seleccione un perro del usuario</option>
                                    <?php mostrarPerros($perrosUsuarioSeleccionado); ?>
                            </select>
                            <input type="submit" value="Guardar cambios" class="formulario-submit">
                        </form>
            <?php endif; ?> 
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