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

        $sqlPublicacionesUsuario = "SELECT id, titulo FROM Publicaciones WHERE idAutor = :usuarioId";
        $stmtPublicacionesUsuario = $conn->prepare($sqlPublicacionesUsuario);
        $stmtPublicacionesUsuario->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtPublicacionesUsuario->execute();
        $publicacionesUsuario = $stmtPublicacionesUsuario->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Quitar Publicación</title>    
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <style>
        .contenido {
            font-family: Arial, sans-serif;
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
                max-width: 90%;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .formulario-container {
                max-width: 100%;
                margin-top: 20px;
                margin-bottom: 100px;
            }

            .formulario-select, .formulario-submit {
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
                <li><a href="borrarPublicacion.php">Volver</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
        <h1 id="titulo" >Panel de Administración</h1>
        <h2 class="titulo-2">Borrar Publicación</h2>

        <div class="formulario-container">
            <h1 class="formulario-title">Borrar Publicación</h1>
            
            <?php if (isset($publicacionesUsuario) && count($publicacionesUsuario) > 0): ?>
                <form action="eliminarPublicacion.php" method="post">
                    <label for="eliminarPublicacion">Selecciona una publicación para eliminar:</label>
                    <select name="idPublicacion" id="eliminarPublicacion" class="formulario-select">
                        <option value="" selected disabled>Seleccione una publicación</option>
                        <?php foreach ($publicacionesUsuario as $publicacion): ?>
                            <option value="<?php echo $publicacion['id']; ?>"><?php echo $publicacion['id'] . ' - ' . $publicacion['titulo']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="usuario" value="<?php echo $usuarioId; ?>">
                    <input type="submit" value="Borrar Publicación" class="formulario-submit">
                </form>
            <?php else: ?>
                <p>No hay publicaciones para este usuario.</p>
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