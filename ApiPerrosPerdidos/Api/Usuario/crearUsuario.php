<?php
session_start();

define('INCLUDED', true);

require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

$userInfo = verifyAdminAndSession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$nombreUsuarioActivo = $userInfo['nombreUsuarioActivo'];

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validar los campos del formulario
  $nombre = $_POST['nombre'] ?? '';
  $correoElectronico = $_POST['correoElectronico'] ?? '';
  $contrasena = $_POST['contrasena'] ?? '';
  $rol = $_POST['rol'] ?? 'usuario';
  $telefono = $_POST['telefono'] ?? '';
  $direccion = $_POST['direccion'] ?? '';
  $visible = $_POST['visible'] ?? 'si';  
  $foto = isset($_FILES['foto']) ? $_FILES['foto'] : '';  
   
  // Crear una instancia de la clase BaseDeDatos
  $db = new BaseDeDatos();

  // Obtener la conexión a la base de datos
  $conn = $db->getConnection();
  try {
    // Verificar existencia de nombre, correo electrónico y contraseña en la base de datos
    $query = "SELECT * FROM usuarios WHERE nombre = :nombre OR correoElectronico = :correoElectronico";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correoElectronico', $correoElectronico);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        handleErrors("El nombre o correo electrónico ya están registrados");
    } else {
    // Crear el nuevo usuario en la base de datos
    
        $stmt = $conn->prepare("INSERT INTO Usuarios (nombre, correoElectronico, contrasena, rol, telefono, direccion, visible) VALUES (:nombre, :correoElectronico, :contrasena, :rol, :telefono, :direccion, :visible)");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':correoElectronico', $correoElectronico);
        $stmt->bindValue(':contrasena', $contrasena);
        $stmt->bindValue(':rol', $rol);
        $stmt->bindValue(':telefono', $telefono);
        $stmt->bindValue(':direccion', $direccion);
        $stmt->bindValue(':visible', $visible);
        $stmt->execute();
        // Obtener el ID del usuario recién creado
        $UsuarioId = $conn->lastInsertId();

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            
            // Obtener el nombre de archivo único para la foto
            $photoName = $nombre . '.jpg';

            // Definir la ruta de destino para guardar la foto
            $rutaDestino = '../../Publico/photos/' . $photoName;

            // Mover el archivo de la foto a la ruta de destino
            $result = move_uploaded_file($foto['tmp_name'], $rutaDestino);
            if ($result) {
                echo "Archivo subido con éxito.";
            } else {
                echo "Error al subir el archivo.";
            }
            // Actualizar la ruta de la foto de usuario
            $query = "UPDATE Usuarios SET fotoPerfil = :fotoPerfil WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':fotoPerfil', $photoName, PDO::PARAM_STR);
            $stmt->bindValue(':id', $UsuarioId, PDO::PARAM_INT);
            $stmt->execute(); 
        } else {
            $photoName = 'default-profile-photo.png';
            $sql = "UPDATE Usuarios SET fotoPerfil = :fotoPerfil WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':fotoPerfil', $photoName, PDO::PARAM_STR);
            $stmt->bindValue(':id', $UsuarioId, PDO::PARAM_INT);
            $stmt->execute();
        }
        header("Location: ../admin.html");
        exit();
    }    
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
    <title>Crear Usuario</title>
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
            width: 98%;
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
        .form-group {
            font-weight: bold;
        }
        /* Media Query para tablets (pantallas hasta 768px) */
        @media screen and (max-width: 768px) {
            .formulario-container {
                max-width: 90%;
                padding: 10px;
            }

            .header, .footer {
                text-align: center;
            }

            .nav-container ul {
                flex-direction: column;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .formulario-input, .formulario-select, .formulario-submit {
                font-size: 14px;
            }

            .header, .footer {
                display: block;
            }

            .nav-links li {
                margin: 5px 0;
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
        <h2 class="titulo-2">Crear usuario</h2>
        <div class="formulario-container">
            <h1 class="formulario-title">Crear Usuario</h1>
            <form action="crearUsuario.php" method="POST" enctype="multipart/form-data">
                <label for="nombre" class="formulario-label">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="formulario-input" required aria-label="Nombre">
                <label for="correoElectronico" class="formulario-label">Correo Electrónico:</label>
                <input type="email" name="correoElectronico" id="correoElectronico" class="formulario-input" required>
                <label for="contrasena" class="formulario-label">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" class="formulario-input" required>
                <label for="rol" class="formulario-label">Rol:</label>
                <select name="rol" id="rol" class="formulario-select">
                    <option value="usuario">Usuario</option>
                    <option value="admin">Admin</option>
                </select>
                <label for="telefono" class="formulario-label">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" class="formulario-input" required>
                <label for="direccion" class="formulario-label">Dirección:</label>
                <input type="text" name="direccion" id="direccion" class="formulario-input" required>
                <label for="visible" class="formulario-label">Visible:</label>
                <select name="visible" id="visible" class="formulario-select">
                    <option value="si">Sí</option>
                    <option value="no">No</option>
                </select>
                <div class="form-group">
                    <label for="foto">Foto de perfil:<br><br></label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg">
                    <label class="custom-file-upload" for="foto"><br><br>Seleccionar foto<br><br></label>
                    <div class="photo-preview">
                        <img id="photo-preview-img" src="#" alt="Foto de perfil">
                    </div>
                </div>
                <input type="submit" value="Crear Usuario" class="formulario-submit">
            </form>
        </div>
    </div>  
    <div id="contenedorPieDePagina"></div>
    <script src="../../../Cliente/js/api/cabecera.js"></script>
    <script>
        // Mostrar vista previa de la foto de perfil seleccionada
        document.getElementById('foto').addEventListener('change', function(event) {
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