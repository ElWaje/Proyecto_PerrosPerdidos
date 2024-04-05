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

// Obtener el ID del usuario seleccionado
$usuarioSeleccionadoId = $_POST['usuarioId'] ?? null;

// Obtener los datos del usuario seleccionado
$usuarioSeleccionado = obtenerUsuarioPorId($conn, $usuarioSeleccionadoId);

// Función para obtener todos los usuarios
function obtenerUsuarios($conn) {
    try {
        $stmt = $conn->query("SELECT id, nombre FROM Usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleErrors(handlePdoError($e));
    }
}

// Función para obtener los datos de un usuario por su ID
function obtenerUsuarioPorId($conn, $idUsuario) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE id = :idUsuario");
        $stmt->bindValue(':idUsuario', $idUsuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleErrors(handlePdoError($e));
    }
}

// Función para mostrar los usuarios en un selector
function mostrarUsuarios($usuarios, $usuarioSeleccionadoId) {
    foreach ($usuarios as $usuario) {
        $selected = ($usuario['id'] == $usuarioSeleccionadoId) ? 'selected' : '';
        echo '<option value="' . $usuario['id'] . '" ' . $selected . '>' . $usuario['id'] . ' - ' . $usuario['nombre'] . '</option>';
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <title>Editar Usuario Formulario</title>
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

        .formulario-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 16px;
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
            margin-top: 20px;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .formulario-submit:hover {
            background-color: #45a049;
        }
        .form-group{
            font-weight: bold;
        }
        /* Estilos para mejorar la accesibilidad */
        [aria-hidden="true"] {
            display: none;
        }

        :focus {
            outline: 3px solid blue;
        }
        @media screen and (max-width: 768px) {
            .formulario-container {
                max-width: 90%;
                padding: 15px;
            }

            .formulario-input, .formulario-select {
                font-size: 14px;
            }
        }

        @media screen and (max-width: 480px) {
            .formulario-container {
                max-width: 100%;
                margin: 20px 10px;
                padding: 10px;
            }

            .formulario-input, .formulario-select {
                font-size: 12px;
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
        <h2 class="titulo-2">Formulario de edición de <?php echo $usuarioSeleccionado['nombre']; ?></h2>
        <div class="formulario-container">
            <h1 class="formulario-title">Editar Usuario</h1>
            <form action="actualizarUsuario.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="usuarioId" value="<?php echo $usuarioSeleccionado['id']; ?>">
                <label for="nombre" class="formulario-label">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="formulario-input" value="<?php echo $usuarioSeleccionado['nombre']; ?>">
                <label for="correoElectronico" class="formulario-label">Correo Electrónico:</label>
                <input type="email" name="correoElectronico" id="correoElectronico" class="formulario-input" value="<?php echo $usuarioSeleccionado['correoElectronico']; ?>">
                <label for="contrasena" class="formulario-label">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" class="formulario-input" value="<?php echo $usuarioSeleccionado['contrasena']; ?>">
                <label for="rol" class="formulario-label">Rol:</label>
                <select name="rol" id="rol" class="formulario-select">
                    <option value="usuario" <?php if ($usuarioSeleccionado['rol'] === 'usuario') echo 'selected'; ?>>Usuario</option>
                    <option value="admin" <?php if ($usuarioSeleccionado['rol'] === 'admin') echo 'selected'; ?>>Admin</option>
                </select>
                <label for="telefono" class="formulario-label">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" class="formulario-input" value="<?php echo $usuarioSeleccionado['telefono']; ?>">
                <label for="direccion" class="formulario-label">Dirección:</label>
                <input type="text" name="direccion" id="direccion" class="formulario-input" value="<?php echo $usuarioSeleccionado['direccion']; ?>">
                <label for="visible" class="formulario-label">Visible:</label>
                <select name="visible" id="visible" class="formulario-select">
                    <option value="si" <?php if ($usuarioSeleccionado['visible'] === 'si') echo 'selected'; ?>>Sí</option>
                    <option value="no" <?php if ($usuarioSeleccionado['visible'] === 'no') echo 'selected'; ?>>No</option>
                </select>
                <input type="hidden" name="fotoPerfil" id="fotoPerfil" value="<?php echo $usuarioSeleccionado['fotoPerfil']; ?>">
                <div class="form-group">
                    <label for="photo">Foto de perfil:<br><br></label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg">
                    <label class="formulario-label" for="photo"><br><br>Seleccionar foto<br><br></label>
                    <div class="photo-preview">
                        <img id="photo-preview-img" src="#" alt="Foto de perfil">
                    </div>
                </div>
                <input type="submit" value="Guardar cambios" class="formulario-submit">                       
            </form>
        </div>
    </div>  
    <div id="contenedorPieDePagina"></div>
    <script src="../../../Cliente/js/api/cabecera.js"></script>
    <script>
        // Mostrar vista previa de la foto de perfil seleccionada
        document.getElementById('photo').addEventListener('change', function(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var img = document.getElementById('photo-preview-img');
                img.src = reader.result;
            };
            reader.readAsDataURL(input.files[0]);
        });
    </script>
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