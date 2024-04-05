<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';

verifyAdminAndSession();

try {
    // Crear una instancia de la clase BaseDeDatos
    $db = new BaseDeDatos();

    // Obtener la conexión a la base de datos
    $conn = $db->getConnection();

    // Realizar la consulta para obtener los nombres y IDs de los usuarios
    $sql = "SELECT id, nombre FROM Usuarios";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    handlePdoError($e); 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>Crear Publicación</title>
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

        .formulario-input#titulo {
            width: 100%; 
            height: 30px;
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

            .formulario-input, .formulario-botones button, .custom-file-upload {
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
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
        <h1 id="titulo" >Panel de Administración</h1>
        <h2 class="titulo-2">Crear Publicación</h2>           
            <div class="crear-comentario-container">
            <h2>Formulario de Publicación</h2>
            <form action="procesarCrearPublicacion.php" method="POST" enctype="multipart/form-data">
                <div class ="form-titulo">    
                    <label for="titulo" class="formulario-label">Título:</label>
                    <input type="text" name="titulo" id="form-titulo" class="formulario-input" required>
                </div>
                <label for="contenido" class="formulario-label">Contenido:</label>
                <textarea name="contenido" id="contenido" class="formulario-input" rows="5" required></textarea>
                <label for="tipo" class="formulario-label">Tipo:</label>
                <select name="tipo" id="tipo" class="formulario-input" required>
                    <option value="encontrado">Encontrado</option>
                    <option value="perdido">Perdido</option>
                    <option value="en adopción">En Adopción</option>
                    <option value="otras">Otras</option>
                </select>
                <div class="form-group">
                    <label for="foto">Foto de la publicación:</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg">
                    <div class="selecion">
                        <label class="formulario-label" for="foto">Seleccionar foto</label>
                    </div>
                    <div class="photo-preview">
                        <img id="photo-preview-img" src="#" alt="Foto de la publicacion">
                    </div>
                </div>
                <label for="autor" class="formulario-label">Autor:</label>
                <select name="autor" id="autor" class="formulario-input" required>
                    <option value="">Seleccionar Autor</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="formulario-botones">
                    <button type="submit">Crear Publicación</button>
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