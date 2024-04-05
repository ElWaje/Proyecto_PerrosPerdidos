<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

verifyAdminAndSession();

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuarioSeleccionado = $_POST['usuarioSeleccionado'] ?? '';

        $sqlPublicaciones = "SELECT id, titulo, contenido, tipo, foto FROM Publicaciones WHERE idUsuario = :idUsuario";
        $stmtPublicaciones = $conn->prepare($sqlPublicaciones);
        $stmtPublicaciones->bindParam(':idUsuario', $usuarioSeleccionado);
        $stmtPublicaciones->execute();
        $publicaciones = $stmtPublicaciones->fetchAll(PDO::FETCH_ASSOC);

        $publicacionIdSeleccionada = $_POST['publicacion'] ?? '';

        $sqlPublicacionSeleccionada = "SELECT idAutor, titulo, contenido, tipo, foto FROM Publicaciones WHERE id = :id";
        $stmtPublicacionSeleccionada = $conn->prepare($sqlPublicacionSeleccionada);
        $stmtPublicacionSeleccionada->bindParam(':id', $publicacionIdSeleccionada);
        $stmtPublicacionSeleccionada->execute();
        $publicacionSeleccionada = $stmtPublicacionSeleccionada->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    handlePdoError($e);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>Editar Publicación Formulario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
     <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">   
    <style>
        .contenido {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .editarpublicacion-container {
            max-width: 600px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .editar-publicacion-container {
            max-width: 600px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 50px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .editar-publicacion-container h2 {
            margin-bottom: 20px;
            font-size: 24px;            
            color: black;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .formulario-label {
            font-weight: bold;
            margin-bottom: 10px;
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

        .textarea{
            width: 100%;
            margin-bottom: 10px;
        }

        .selector-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .selector-label {
            margin-right: 10px;
            margin-bottom: 10px;
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
            .editarpublicacion-container, .editar-publicacion-container {
                max-width: 90%;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .editarpublicacion-container, .editar-publicacion-container {
                max-width: 100%;
                padding: 10px;
            }

            .formulario-input, .textarea, .formulario-botones button, .custom-file-upload {
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
                <li><a href="../admin.html">Administración<noscript></noscript></a></li>
                <li><a href="editarPublicacionUsuario.php">Volver<noscript></noscript></a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
        <h1 id="titulo" >Panel de Administración</h1>
        <h2 class="titulo-2">Editar Publicación</h2>                
        <div class="editarpublicacion-container">        
                <div class="editar-publicacion-container">
                    <h2>Editar Publicación</h2>
                    <form action="procesarEditarPublicacion.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="publicacionId" name="publicacionIdSeleccionada" value="<?php echo htmlspecialchars($publicacionIdSeleccionada); ?>">
                        <input type="hidden" id="idAutor" name="idAutor" value="<?php echo htmlspecialchars($publicacionSeleccionada['idAutor']); ?>">
                        <div class="formulario-botones">
                            <button type="button" id ="btnCambiarAutor" onclick="redirectToEditarPublicacion()">Pulse aquí para cambiar el Autor de la publicación</button>
                        </div>
                        <div class ="form-titulo">
                            <label for="titulo" class="formulario-label">Título:</label>
                            <input type="text" name="titulo" class="formulario-input" value="<?php echo htmlspecialchars($publicacionSeleccionada['titulo']); ?>" required>
                        </div>
                        <div>
                            <label for="contenido" class="formulario-label">Contenido:</label>
                            <textarea name="contenido" class="textarea" id="contenido" rows="5" required><?php echo htmlspecialchars($publicacionSeleccionada['contenido']); ?></textarea>
                        </div>
                        <div>
                            <label for="tipo" class="formulario-label">Tipo:</label>
                            <select name="tipo" id="tipo" required>
                                <option value="encontrado" <?php if ($publicacionSeleccionada['tipo'] === 'encontrado') echo 'selected'; ?>>Encontrado</option>
                                <option value="perdido" <?php if ($publicacionSeleccionada['tipo'] === 'perdido') echo 'selected'; ?>>Perdido</option>
                                <option value="en adopción" <?php if ($publicacionSeleccionada['tipo'] === 'en adopción') echo 'selected'; ?>>En Adopción</option>
                                <option value="otras" <?php if ($publicacionSeleccionada['tipo'] === 'otras') echo 'selected'; ?>>Otras</option>
                            </select>
                        </div>
                        <input type="hidden" name="foto_actual" id="foto" value="<?php echo isset($publicacionSeleccionada['foto']) ? $publicacionSeleccionada['foto'] : ''; ?>">            
                        <div class="form-group">
                                <label for="foto">Foto de la publicacion:<br><br></label>
                                <input type="file" id="photo" name="foto" accept="image/jpeg">
                                <label class="formulario-label" for="foto"><br><br>Seleccionar foto<br><br></label>
                                <div class="photo-preview">
                                    <img id="photo-preview-img" src="#" alt="Foto de la publicación">
                                </div>
                        </div>                  
                        <div class="formulario-botones">
                            <button type="submit">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
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
        function redirectToEditarPublicacion() {
            var publicacionIdSeleccionada = document.getElementById('publicacionId').value;
            window.location.href = 'editarPublicacionCambio.php?publicacion=' + publicacionIdSeleccionada;
        }

        // Cargar el evento onclick después de que se haya cargado el DOM
        document.addEventListener("DOMContentLoaded", function () {
            var btnCambiarAutor = document.getElementById("btnCambiarAutor");
            btnCambiarAutor.addEventListener("click", redirectToEditarPublicacion);
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