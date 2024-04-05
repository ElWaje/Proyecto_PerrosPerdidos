<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


verifyAdminAndSession();

$db = new BaseDeDatos();
$conn = $db->getConnection();

$selectedUserId = isset($_POST['idUsuario']) ? filter_var($_POST['idUsuario'], FILTER_SANITIZE_NUMBER_INT) : null;
$selectedComentarioId = isset($_GET['comentario']) ? filter_var($_GET['comentario'], FILTER_SANITIZE_NUMBER_INT) : null;

try {
    $comentarios = obtenerComentariosDeUsuario($conn, $selectedUserId);
    $comentarioSeleccionado = obtenerComentarioSeleccionado($conn, $selectedComentarioId);
} catch (PDOException $e) {
    handlePdoError($e);
}

function obtenerComentariosDeUsuario($conn, $userId) {
    $sqlComentarios = "SELECT id, texto FROM Comentarios WHERE idUsuario = :idUsuario";
    $stmtComentarios = $conn->prepare($sqlComentarios);
    $stmtComentarios->bindParam(':idUsuario', $userId);
    $stmtComentarios->execute();
    return $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerComentarioSeleccionado($conn, $comentarioId) {
    if ($comentarioId) {
        $sqlComentarioSeleccionado = "SELECT * FROM Comentarios WHERE id = :idComentario";
        $stmtComentarioSeleccionado = $conn->prepare($sqlComentarioSeleccionado);
        $stmtComentarioSeleccionado->bindParam(':idComentario', $comentarioId);
        $stmtComentarioSeleccionado->execute();
        return $stmtComentarioSeleccionado->fetch(PDO::FETCH_ASSOC);
    }
    return [];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>Editar Comentario Usuario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">   
    <style>
        .contenido {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .editar-comentario-container {
            max-width: 600px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .editar-comentario-container h2 {
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
            margin-bottom: 10px;
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
            .editar-comentario-container {
                max-width: 90%;
                margin-top: 30px;
                margin-bottom: 100px;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .editar-comentario-container {
                max-width: 100%;
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
                <li><a href="editarComentario.php">Volver</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
            <h1 id="titulo" >Panel de Administración</h1>
            <h2 class="titulo-2">Editar Comentario</h2>         
            <div class="editar-comentario-container">
                <h2>Editar Comentario</h2>
                <?php if (empty($comentarios)): ?>
                    <!-- Mostrar el mensaje cuando no hay comentarios -->
                    <p>El usuario no tiene comentarios</p>
                <?php else: ?> 
                    <form action="editarComentarioTexto.php" method="POST" enctype="multipart/form-data"> 
                        <input type="hidden" name="usuario" value="<?php echo $selectedUserId; ?>">               
                        <div class="selector-container">
                            <label for="comentario" class="selector-label">Seleccionar Comentario:</label>
                            <select name="comentario" id="comentario" class="formulario-input">
                                <option value="" selected disabled>Seleccionar Comentario</option>
                                <?php foreach ($comentarios as $comentario): ?>
                                    <option value="<?php echo $comentario['id']; ?>" <?php if ($comentario['id'] == $selectedComentarioId) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($comentario['texto']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="formulario-botones">
                            <button type="button" onclick="redirectToEditarComentario()">Pulse aquí para cambiar el Autor del comentario</button>
                        </div>
                        <div class="formulario-botones">
                        <button type="button" onclick="redirectToEditarPublicacion()">Pulse aquí para cambiar de Publicación el Comentario</button>
                        </div>                                
                        <div class="formulario-botones">
                            <button type="submit">Guardar Cambios</button>
                        </div>
                    </form>
                <?php endif; ?>            
            </div>
    </div>  
    <div id="contenedorPieDePagina"></div>
    <script src="../../../Cliente/js/api/cabecera.js"></script>    
    <script>
        function redirectToEditarComentario() {
            var selectedComentarioId = document.getElementById('comentario').value;

            if (selectedComentarioId === "") {
                alert("No se ha seleccionado un comentario y es obligatorio seleccionar uno.");
            } else {
                window.location.href = 'editarComentarioFormulario.php?comentario=' + selectedComentarioId;
            }
        }
    </script>

    <script>
        function redirectToEditarPublicacion() {
            var selectedComentarioId = document.getElementById('comentario').value;

            if (selectedComentarioId === "") {
                alert("No se ha seleccionado un comentario y es obligatorio seleccionar uno.");
            } else {
                window.location.href = 'editarComentarioPublicacion.php?comentario=' + selectedComentarioId;
            }
        }
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