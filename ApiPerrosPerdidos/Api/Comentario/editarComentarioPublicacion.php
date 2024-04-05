<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


verifyAdminAndSession();

$selectedComentarioId = $_GET['comentario'] ?? null;
if (!$selectedComentarioId) {
    redirectToIndexWithError("Comentario no especificado.");
}

try {
    $db = new BaseDeDatos();
    $conn = $db->getConnection();

    list($publicacionActual, $publicaciones) = obtenerDatosComentario($conn, $selectedComentarioId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        procesarCambioPublicacion($conn, $_POST, $selectedComentarioId);
    }
} catch (PDOException $e) {
    handlePdoError($e);
}

function obtenerDatosComentario($conn, $comentarioId) {
    // Obtener publicación actual del comentario
    $sqlPublicacionActual = "SELECT idPublicacion FROM Comentarios WHERE id = :idComentario";
    $stmtPublicacionActual = $conn->prepare($sqlPublicacionActual);
    $stmtPublicacionActual->bindParam(':idComentario', $comentarioId);
    $stmtPublicacionActual->execute();
    $publicacionActual = $stmtPublicacionActual->fetch(PDO::FETCH_ASSOC);

    // Obtener todas las publicaciones
    $sqlPublicaciones = "SELECT id, titulo FROM Publicaciones";
    $stmtPublicaciones = $conn->prepare($sqlPublicaciones);
    $stmtPublicaciones->execute();
    $publicaciones = $stmtPublicaciones->fetchAll(PDO::FETCH_ASSOC);

    return [$publicacionActual, $publicaciones];
}

function procesarCambioPublicacion($conn, $postData, $comentarioId) {
    $selectedIdPublicacion = $postData['publicacion'] ?? null;

    if (!$selectedIdPublicacion) {
        redirectToIndexWithError("No se ha seleccionado una publicación.");
    }

    $sqlActualizarIdPublicacion = "UPDATE Comentarios SET idPublicacion = :idPublicacion WHERE id = :idComentario";
    $stmtActualizarIdPublicacion = $conn->prepare($sqlActualizarIdPublicacion);      
    $stmtActualizarIdPublicacion->bindParam(':idPublicacion', $selectedIdPublicacion);
    $stmtActualizarIdPublicacion->bindParam(':idComentario', $comentarioId);
    $stmtActualizarIdPublicacion->execute();

    echo "<script>
            alert('¡Comentario editado con éxito!');
            window.location.href = '../admin.html';
        </script>";
        exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>Editar Comentario Publicación</title>
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
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .editar-comentario-container {
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
                <li><a href="editarComentarioUsuario.php">Volver</a></li>
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
                <form action="editarComentarioPublicacion.php?comentario=<?php echo htmlspecialchars($selectedComentarioId); ?>" method="POST">
                    <?php if (is_array($publicacionActual) && !empty($publicacionActual['idPublicacion'])): ?>
                        <p>El comentario actualmente está en la publicación: </p>
                        <p><strong><?php echo htmlspecialchars($publicacionActual['idPublicacion']); ?></strong></p>
                    <?php else: ?>
                        <p>No se encontró la publicación asociada al comentario.</p>
                    <?php endif; ?>            
                    <div class="selector-container">
                            <label for="publicacion">Seleccionar Publicación:</label>
                            <select name="publicacion" id="publicacion" required>
                                <option value="" selected disabled>Seleccione una publicación</option>
                                <?php foreach ($publicaciones as $publicacion): ?>
                                    <option value="<?php echo $publicacion['id']; ?>"><?php echo htmlspecialchars($publicacion['id'] . ' - ' . $publicacion['titulo']); ?></option>
                                <?php endforeach; ?>
                            </select>
                    </div>            
                    <div class="formulario-botones">
                        <button type="submit">Guardar Cambios</button>
                    </div>
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