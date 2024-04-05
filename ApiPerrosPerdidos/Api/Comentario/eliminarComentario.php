<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


verifyAdminAndSession();

$usuarioActivoId = $_SESSION['id'];
$comentarioEliminado = false;

$db = new BaseDeDatos();
$conn = $db->getConnection();

if (isset($_POST['usuario'])) {
    $usuarioId = $_POST['usuario'];

    try {
        // Obtener los comentarios del usuario seleccionado
        $comentariosUsuario = obtenerComentariosUsuario($conn, $usuarioId);
    } catch (PDOException $e) {
        handlePdoError($e);
    }
}

if (isset($_POST['eliminarComentario'])) {
    $comentarioId = $_POST['eliminarComentario'];
    eliminarComentario($conn, $comentarioId, $usuarioId);
}

function obtenerComentariosUsuario($conn, $usuarioId) {
    $sqlComentariosUsuario = "SELECT id, texto FROM Comentarios WHERE idUsuario = :usuarioId";
    $stmtComentariosUsuario = $conn->prepare($sqlComentariosUsuario);
    $stmtComentariosUsuario->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmtComentariosUsuario->execute();
    return $stmtComentariosUsuario->fetchAll(PDO::FETCH_ASSOC);
}

function eliminarComentario($conn, $comentarioId, $usuarioId) {
    $rutaFoto = '../../Publico/fotos_comentarios/comentario_' . $comentarioId . '.jpg';
    if (file_exists($rutaFoto)) {
        unlink($rutaFoto);
    }

    try {
        $sqlEliminarComentario = "DELETE FROM Comentarios WHERE id = :comentarioId";
        $stmtEliminarComentario = $conn->prepare($sqlEliminarComentario);
        $stmtEliminarComentario->bindParam(':comentarioId', $comentarioId, PDO::PARAM_INT);
        $stmtEliminarComentario->execute();
        
        // Recargar los comentarios
        $comentariosUsuario = obtenerComentariosUsuario($conn, $usuarioId);
        return true; 
    } catch (PDOException $e) {
        handlePdoError($e);
    }
}
?>
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <title>Eliminar Comentario</title>    
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <style>
        .contenido {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
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
            background-color: #d32f2f;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .formulario-submit:hover {
            background-color: #B71C1C;
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
                margin-top: 30px;
                margin-bottom: 100px;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .formulario-container {
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
                <li><a href="borrarComentario.php">Volver</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
            <h1 id="titulo" >Panel de Administración</h1>
            <h2 class="titulo-2">Borrar Comentario</h2>
            <div class="formulario-container">
                <h1 class="formulario-title">Borrar Comentario</h1>
                
                <?php if (isset($comentariosUsuario) && count($comentariosUsuario) > 0): ?>
                    <form action="eliminarComentario.php" method="post">
                        <label for="eliminarComentario">Selecciona un comentario para eliminar:</label>
                        <select name="eliminarComentario" id="eliminarComentario" class="formulario-select">                    
                        <option value="" selected disabled>Seleccione un comentario</option>
                            <?php foreach ($comentariosUsuario as $comentario): ?>
                                <option value="<?php echo $comentario['id']; ?>"><?php echo $comentario['id'] . ' - ' . $comentario['texto']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="usuario" value="<?php echo $usuarioId; ?>">
                        <input type="submit" value="Borrar Comentario" class="formulario-submit">
                    </form>
                <?php else: ?>
                    <p>No hay comentarios para este usuario.</p>
                <?php endif; ?>
            </div>
    </div>  
    <div id="contenedorPieDePagina"></div>
    <script src="../../../Cliente/js/api/cabecera.js"></script>
    <script>
        window.onload = function() {
            <?php if ($comentarioEliminado): ?>
                alert("Comentario eliminado correctamente.");
            <?php endif; ?>
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