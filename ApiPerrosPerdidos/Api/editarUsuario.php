<?php
session_start();

define('INCLUDED', true);

require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

// Verificar si el usuario ha iniciado sesión
verifySession(); 
$isAdmin = $_SESSION['isAdmin'] ?? false;
$loggedInUserId = $_SESSION['id'];
$userIdToBeEdited = $_POST['usuarioId'] ?? null;

if ($loggedInUserId === $userIdToBeEdited || $isAdmin) {
    try {
        $db = new BaseDeDatos();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE id = :id");
        $stmt->bindValue(':id', $userIdToBeEdited);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            handleErrors("Usuario no encontrado.");
        }

        $userName = htmlspecialchars($user['nombre']);
        $userEmail = htmlspecialchars($user['correoElectronico']);
        $userPassword = htmlspecialchars($user['contrasena']);
        $userPhone = htmlspecialchars($user['telefono']);
        $userAddress = htmlspecialchars($user['direccion']);
        $userVisible = htmlspecialchars($user['visible']);
        $userRole = htmlspecialchars($user['rol']);
                        
    } catch (PDOException $e) {
        handlePdoError($e);
    }
} else {
    handleErrors("No tienes permisos para editar este usuario.");
}
?>

<!DOCTYPE html>
<html lang="es">

<html lang = "es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>Editar Usuario</title>
    <link rel="stylesheet" type="text/css" href="../../Cliente/css/estilos.css" />
    <link rel="stylesheet" type="text/css" href="../../Cliente/css/headerFooter.css" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
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

        .formulario-label-rol {
            display: block;
            margin-bottom: 8px;
            margin-top: 20px;
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
            margin-bottom: 20px;
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
        /* Media Query para pantallas hasta 768px (Tablets) */
        @media screen and (max-width: 768px) {
            .formulario-container {
                max-width: 90%;
                padding: 15px;
            }

            .formulario-input, .formulario-select, .formulario-submit {
                font-size: 14px;
            }

            .formulario-label, .formulario-label-rol {
                margin-bottom: 5px;
            }
        }

        /* Media Query para pantallas hasta 480px (Móviles) */
        @media screen and (max-width: 480px) {
            .formulario-container {
                max-width: 100%;
                margin: 20px 10px;
                padding: 10px;
            }

            .formulario-input, .formulario-select, .formulario-submit {
                font-size: 12px;
            }

            .formulario-label, .formulario-label-rol {
                margin-bottom: 3px;
            }

        }
    </style>
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="perros.php">Perros</a></li>
                <li><a href="../../Cliente/verPerros.html">Ver Perros</a></li>
                <li><a href="../../Cliente/perrosAdopcion.html">En Adopción</a></li>
                <li><a href="../../Cliente/perrosEncontrados.html">Encontrados</a></li>
                <li><a href="../../Cliente/perrosPerdidos.html">Perdidos</a></li>
                <li><a href="../../Cliente/usuarios.html">Usuarios</a></li>
                <li><a href="publicaciones.php">Publicaciones</a></li>
                <li><a href="perfil.php">Volver</a></li>
                <?php if ($isAdmin): ?>
                    <li class="admin"><a href="admin.html">Administración</a></li>
                <?php endif; ?>                
                <li><a href="../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>  
    <div id="contenedorCabecera"></div>
    <div class="contenido"> 
            <h1 id=titulo>Perfil de Usuario</h1> 
            <h2 class="titulo-2">Formulario de edición de <?php echo $userName; ?></h2>
            <div class="formulario-container">
                <h1 class="formulario-title">Editar Usuario</h1>
                <form action="actualizarUsuario.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="userId" value="<?php echo $userIdToBeEdited; ?>">

                    <label for="nombre" class="formulario-label">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="formulario-input" value="<?php echo $userName; ?>"><br>

                    <label for="correoElectronico" class="formulario-label">Correo Electrónico:</label>
                    <input type="email" name="correoElectronico" class="formulario-input" id="correoElectronico" value="<?php echo $userEmail; ?>"><br>

                    <label for="contrasena" class="formulario-label">Contraseña:</label>
                    <input type="password" name="contrasena" id="contrasena" class="formulario-input" value="<?php echo $userPassword; ?>"><br>

                    <label for="telefono" class="formulario-label">Teléfono:</label>
                    <input type="tel" name="telefono" id="telefono" class="formulario-input" value="<?php echo $userPhone; ?>"><br>

                    <label for="direccion" class="formulario-label">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="formulario-input" value="<?php echo $userAddress; ?>"><br>

                    <label for="visible" class="formulario-label">Visible:</label>
                    <select name="visible" id="visible" class="formulario-select">
                        <option value="si" <?php if ($user['visible'] === 'si') echo 'selected'; ?>>Sí</option>
                        <option value="no" <?php if ($user['visible'] === 'no') echo 'selected'; ?>>No</option>
                    </select>
                    <div class="form-group">
                        <label for="photo">Foto de perfil:<br><br></label>
                        <input type="file" id="photo" name="photo" accept="image/jpeg">
                        <label class="formulario-label" for="photo"><br><br>Seleccionar foto<br><br></label>
                        <div class="photo-preview">
                            <img id="photo-preview-img" src="#" alt="Foto de perfil">
                        </div>
                    </div>
                    <?php if($isAdmin): ?>
                    <label for="rol" class="formulario-label-rol">Rol:</label>
                    <select name="rol" id="rol" class="formulario-select">
                        <option value="usuario" <?php echo $userRole === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                        <option value="admin" <?php echo $userRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select><br>
                    <?php endif; ?>
                    <input type="submit" value="Guardar cambios" class="formulario-submit">
                </form>
            </div>    
    </div>
    <div id="contenedorPieDePagina"></div>
    <script src="../../Cliente/js/api/cabecera.js"></script>
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
            "../../Cliente/img/logo.png",
            "perfil.php"
          );
        document.getElementById("contenedorPieDePagina").innerHTML =
          cargarPieDePagina("../../Cliente/img/logo.jpg");
      });
    </script> 
</body>
</html>