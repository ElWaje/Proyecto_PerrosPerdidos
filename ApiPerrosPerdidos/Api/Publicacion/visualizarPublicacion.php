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

        // Consulta para obtener publicaciones
        $stmtPublicaciones = $conn->prepare("SELECT p.id, p.titulo, p.contenido, p.tipo, p.fecha, p.meGusta, p.foto, u.nombre AS nombreUsuario 
            FROM Publicaciones p 
            LEFT JOIN Usuarios u ON p.idAutor = u.id
            WHERE p.idAutor = :usuarioId
            ORDER BY p.fecha DESC");
        $stmtPublicaciones->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtPublicaciones->execute();
        $publicaciones = $stmtPublicaciones->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Visualizar Publicación</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">   
    <style>
         .contenido {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .publicaciones-container {
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px; 
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .publicaciones-container h3 {
            margin-bottom: 20px;
            font-size: 24px;            
            color: black;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .publicacion {
            border-bottom: 1px solid #ccc;
            padding: 20px 0;
            display: flex;
            align-items: flex-start;
        }
        .publicacion img {
            width: 50px;
            height: 50px;
            margin-right: 20px;
            border-radius: 5px;
            object-fit: cover;
        }
        .publicacion-contenido {
            flex: 1;
        }
        .publicacion .titulo {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }
        .publicacion .contenido {
            margin-bottom: 10px;
            margin-right: 20px;
            color: #333;
        }
        .publicacion .info {
            font-size: 14px;
            margin-right: 20px;
            color: #333;
        }
        .publicacion-id {
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
        .publicacion div {
            margin-bottom: 10px;
        }
        .publicacion {
            border-bottom: 1px solid #ccc;
            padding: 20px 0;
            display: flex;
            align-items: flex-start;
        }        
        .id-publicacion,
        .foto,
        .titulo-publicacion,
        .contenido-publicacion,
        .tipo-publicacion,
        .fecha-publicacion,
        .megusta-publicacion,
        .autor-publicacion {
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
            .publicaciones-container {
                max-width: 95%;
                margin-top: 20px;
                margin-bottom: 100px;
            }

            .publicacion {
                flex-direction: column;
            }

            .publicacion img {
                width: 100%;
                height: auto;
                margin-bottom: 10px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .publicaciones-container {
                max-width: 100%;
                padding: 10px;
            }

            .publicacion .titulo, .publicacion .contenido, .publicacion .info {
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
                <li><a href="mostrarPublicacion.php">Volver</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
        <h1 id="titulo" >Panel de Administración</h1>
        <h2 class="titulo-2">Mostrar Publicación</h2>               
        <div class="publicaciones-container">
            <h3>Publicaciones de <?php echo $nombreUsuario; ?></h3>        
            <?php if (empty($publicaciones)): ?>
                <!-- Mostrar el mensaje cuando no hay publicaciones -->
                <p>El usuario no tiene publicaciones</p>
            <?php else: ?>  
                <?php foreach ($publicaciones as $publicacion): ?>
                    <div class="publicacion">
                        <div class="id-publicacion">
                            <p class="tidpub">Id Publ: </p>
                            <p class="publicacion-id"><?php echo $publicacion['id']; ?></p>
                        </div>
                        <div class="foto">
                            <p class="tfoto">Foto: </p>
                            <?php if ($publicacion['foto']): ?>
                                <img src="../../Publico/fotos_publicaciones/<?php echo $publicacion['foto']; ?>" alt="Imagen de la publicación de <?php echo htmlspecialchars($publicacion['titulo']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="titulo-publicacion">
                            <p class="ttitulo">Título: </p>
                            <p class="titulo"><?php echo htmlspecialchars($publicacion['titulo']); ?></p>
                        </div>
                        <div class="contenido-publicacion">
                            <p class="tcontenido">Contenido: </p>
                            <p class="contenido"><?php echo htmlspecialchars($publicacion['contenido']); ?></p>
                        </div>
                        <div class="tipo-publicacion">                    
                            <p class="ttipo">Tipo: </p>
                            <p class="info"><?php echo htmlspecialchars($publicacion['tipo']); ?></p>
                        </div>
                        <div class="fecha-publicacion">
                            <p class="tfecha">Fecha: </p>
                            <p class="info">Fecha: <?php echo $publicacion['fecha']; ?></p>
                        </div>
                        <div class="megusta-publicacion">
                            <p class="tmegusta">Me gusta: </p>
                            <p class="info"><?php echo $publicacion['meGusta']; ?></p>
                        </div>
                        <div class="autor-publicacion">
                            <p class="tautor">Autor: </p>
                            <p class="info"><?php echo htmlspecialchars($publicacion['nombreUsuario']); ?></p>
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
