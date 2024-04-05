<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

verifyAdminAndSession();

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    $usuarios = obtenerUsuarios($conn);
    $publicaciones = obtenerPublicaciones($conn);
} catch (PDOException $e) {
    handlePdoError($e);
}

function obtenerUsuarios($conn) {
    $sqlUsuarios = "SELECT id, nombre FROM Usuarios";
    $stmtUsuarios = $conn->prepare($sqlUsuarios);
    $stmtUsuarios->execute();
    return $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerPublicaciones($conn) {
    $sqlPublicaciones = "SELECT id, titulo FROM Publicaciones";
    $stmtPublicaciones = $conn->prepare($sqlPublicaciones);
    $stmtPublicaciones->execute();
    return $stmtPublicaciones->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>Crear Comentario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">   
    <style>
        .contenido {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .crear-comentario-container {
            max-width: 600px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .crear-comentario-container h2 {
            margin-bottom: 20px;
            font-size: 24px;            
            color: black;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .formulario-label {
            font-weight: bold;
            color: #333;
        }

        .formulario-input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            color: #333;
        }

        .selector-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .selector-label {
            margin-right: 10px;
            color: #333;
        }

        .formulario-botones {
            display: flex;
            justify-content: flex-end;
        }

        .formulario-botones button {
            margin-left: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .formulario-botones button:hover {
            background-color: #45a049;
        }

        .photo-preview {
            max-width: 200px;
            margin: 10px 0;
        }

        .photo-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .selecion{
            margin-bottom: 20px;
            margin-top: 20px;
        }
        
        .custom-file-upload {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .custom-file-upload:hover {
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
            .crear-comentario-container {
                max-width: 90%;
                padding: 15px;
            }
           
        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .crear-comentario-container {
                max-width: 100%;
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
            <h2 class="titulo-2">Crear Comentario</h2>               
            <div class="crear-comentario-container">
                <h2>Formulario de Comentario</h2>
                <form action="procesarCrearComentario.php" method="POST" enctype="multipart/form-data">
                    <div class="selector-container">
                        <label for="idUsuario" class="selector-label">Seleccionar Autor:</label>
                        <select name="idUsuario" id="idUsuario" class="formulario-input">
                            <option value=""  selected disabled>Seleccionar Autor</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="selector-container">
                        <label for="idPublicacion" class="selector-label">Seleccionar Publicación:</label>
                        <select name="idPublicacion" id="idPublicacion" class="formulario-input">
                            <option value="">Seleccionar Publicación</option>
                            <?php foreach ($publicaciones as $publicacion): ?>
                                <option value="<?php echo $publicacion['id']; ?>"><?php echo htmlspecialchars($publicacion['titulo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <label for="texto" class="formulario-label">Texto:</label>
                    <textarea name="texto" id="texto" class="formulario-input" rows="4" required></textarea>
                    <div class="form-group">
                        <label for="foto">Foto de comentario:</label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg">
                        <div class="selecion">
                            <label class="formulario-label" for="foto">Seleccionar foto</label>
                        </div>
                        <div class="photo-preview">
                            <img id="photo-preview-img" src="#" alt="Foto de comentario">
                        </div>
                    </div>
                    <div class="formulario-botones">
                        <button type="submit">Crear Comentario</button>
                        <button type="button" onclick="window.location.href='../admin.html'">Cancelar</button>
                    </div>
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