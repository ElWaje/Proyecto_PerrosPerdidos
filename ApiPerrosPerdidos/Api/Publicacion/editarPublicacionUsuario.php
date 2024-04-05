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
        $usuarioSeleccionado = $_POST['idUsuario'] ?? '';
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
    <title>Editar Publicación Usuario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">   
    <style>
        .contenido {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .editar-publicacion-container {
            max-width: 600px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px;
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
            .editar-publicacion-container {
                max-width: 90%;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .editar-publicacion-container {
                max-width: 100%;
                padding: 10px;
            }

            .formulario-input, .formulario-submit, .custom-file-upload {
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
                <li><a href="editarPublicacion.php">Volver</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
        <h1 id="titulo" >Panel de Administración</h1>
        <h2 class="titulo-2">Editar Publicación</h2>
            <div class="editar-publicacion-container">
                <h2>Seleccionar Usuario y Publicación</h2>
                <?php if (empty($publicaciones)): ?>
                    <!-- Mostrar el mensaje cuando no hay publicaciones -->
                    <p>El usuario no tiene publicaciones</p>
                <?php else: ?>
                    <form action="editarPublicacionFormulario.php" method="post">
                        <div class="selector-container">
                            <label for="publicacion">Seleccionar Publicación:</label>
                            <select name="publicacion" id="publicacion" required>
                                <option value="" selected disabled>Seleccione una publicación</option>
                                <?php foreach ($publicaciones as $publicacion): ?>
                                    <option value="<?php echo $publicacion['id']; ?>"><?php echo htmlspecialchars($publicacion['titulo']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="usuarioSeleccionado" value="<?php echo htmlspecialchars($usuarioSeleccionado); ?>">
                        <div class="formulario-botones">
                            <button type="submit">Seleccionar Publicación</button>
                        </div>  
                    </form>
                <?php endif; ?> 
            </div>        
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