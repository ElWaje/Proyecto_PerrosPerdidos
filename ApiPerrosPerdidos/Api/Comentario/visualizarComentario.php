<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


verifyAdminAndSession();

$db = new BaseDeDatos();
$conn = $db->getConnection();

try {
    if (isset($_POST['usuario'])) {
        $usuarioId = $_POST['usuario'];

        // Obtener el nombre del usuario
        $stmtNombreUsuario = $conn->prepare("SELECT nombre FROM Usuarios WHERE id = :usuarioId");
        $stmtNombreUsuario->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtNombreUsuario->execute();
        $usuario = $stmtNombreUsuario->fetch(PDO::FETCH_ASSOC);
        $nombreUsuario = $usuario['nombre'];

        // Consulta para obtener comentarios
        $stmtComentarios = $conn->prepare("SELECT c.id, c.texto, c.fecha, c.meGusta, c.foto, p.titulo AS tituloPublicacion, u.nombre AS nombreAutor 
            FROM Comentarios c 
            LEFT JOIN Publicaciones p ON c.idPublicacion = p.id
            LEFT JOIN Usuarios u ON c.idAutor = u.id
            WHERE c.idUsuario = :usuarioId
            ORDER BY c.fecha DESC");
        $stmtComentarios->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtComentarios->execute();
        $comentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);

    } else {
        redirectToIndexWithError("No se ha seleccionado un usuario.");
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
    <title>Visualizar Comentario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">   
    <style>
        .contenido {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .comentarios-container {
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px; 
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .comentarios-container h3 {
            margin-bottom: 20px;
            font-size: 24px;            
            color: black;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .comentario {
            border-bottom: 1px solid #ccc;
            padding: 20px 0;
            display: flex;
            align-items: flex-start;
        }

        .comentario img {
            width: 50px;
            height: 50px;
            margin-right: 20px;
            border-radius: 5px;
            object-fit: cover;
        }

        .comentario-contenido {
            flex: 1;
        }

        .comentario .titulo {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        .comentario .contenido {
            margin-bottom: 10px;
            margin-right: 20px;
            color: #333;
        }

        .comentario .info {
            font-size: 14px;
            margin-right: 20px;
            color: #333;
        }

        .comentario-id {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
            margin-right: 20px;
            padding: 5px 10px;
            border-radius: 8px;
            background-color: #f2f2f2;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        
        .comentario div {
            margin-bottom: 10px;
        }

        .comentario {
            border-bottom: 1px solid #ccc;
            padding: 20px 0;
            display: flex;
            align-items: flex-start;
        }

        .id-comentario,
        .foto,
        .titulo-publicacion,
        .contenido-comentario,
        .fecha-comentario,
        .megusta-comentario,
        .autor-comentario {
            margin-right: 20px;
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
            .comentarios-container {
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .comentarios-container {
                padding: 10px;
            }

            .comentario img {
                width: 40px;
                height: 40px;
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
                <li><a href="mostrarComentario.php">Volver</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
            <h1 id="titulo" >Panel de Administración</h1>
            <h2 class="titulo-2">Mostrar Comentario</h2> 
            <div class="comentarios-container">
                <h3>Comentarios de <?php echo $nombreUsuario; ?></h3>  
                <?php if (empty($comentarios)): ?>
                    <!-- Mostrar el mensaje cuando no hay comentarios -->
                    <p>El usuario no tiene comentarios</p>
                <?php else: ?>      
                    <?php foreach ($comentarios as $comentario): ?>
                        <div class="comentario">
                            <div class="id-comentario">
                                <p class="tidcom">Id Comentario: </p>
                                <p class="comentario-id"><?php echo $comentario['id']; ?></p>
                            </div>
                            <div class="foto">
                                <p class="tfoto">Foto: </p>
                                <?php if ($comentario['foto']): ?>
                                    <img src="../../Publico/fotos_comentarios/<?php echo $comentario['foto']; ?>" alt="Imagen de la publicación">
                                <?php endif; ?>
                            </div>
                            <div class="contenido-comentario">
                                <p class="tcontenido">Texto: </p>
                                <p class="contenido"><?php echo htmlspecialchars($comentario['texto']); ?></p>
                            </div>
                            <div class="fecha-comentario">
                                <p class="tfecha">Fecha: </p>
                                <p class="info">Fecha: <?php echo $comentario['fecha']; ?></p>
                            </div>
                            <div class="megusta-comentario">
                                <p class="tmegusta">Me gusta: </p>
                                <p class="info"><?php echo $comentario['meGusta']; ?></p>
                            </div>
                            <div class="titulo-publicacion">
                                <p class="ttitulo">Título de la publicación: </p>
                                <p class="titulo"><?php echo htmlspecialchars($comentario['tituloPublicacion']); ?></p>
                            </div>
                            <div class="autor-comentario">
                                <p class="tautor">Autor: </p>
                                <p class="info"><?php echo htmlspecialchars($comentario['nombreAutor']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
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