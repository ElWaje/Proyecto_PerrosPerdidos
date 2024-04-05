<?php
session_start();
define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';
verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";

// Obtener los datos del usuario desde la base de datos
$loggedInUserId = $_SESSION['id']; 
$userName = '';
$userEmail = '';
$userPassword = '';
$userRole = '';
$userDogNames = '';
$userPhoto = '';
$isAdmin = false;

try {
    // Crear una instancia de la clase BaseDeDatos
    $db = new BaseDeDatos();

    // Obtener la conexión a la base de datos
    $conn = $db->getConnection();

    // Realizar la consulta para obtener los datos del usuario
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE id = :id");
    $stmt->bindValue(':id', $loggedInUserId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Asignar los valores obtenidos a las variables correspondientes
    if ($user) {
        $userName = htmlspecialchars($user['nombre']);
        $userEmail = htmlspecialchars($user['correoElectronico']);
        $userPassword = htmlspecialchars($user['contrasena']);
        $userRole = htmlspecialchars($user['rol']); 
        $userPhoto = htmlspecialchars($user['fotoPerfil']);

        // Verificar si el usuario es administrador
        $isAdmin = ($userRole === 'admin');

        // Guardar el valor en la variable de sesión
        $_SESSION['isAdmin'] = $isAdmin;

        // Obtener los perros del usuario actual
        $stmtPerros = $conn->prepare("SELECT nombre FROM Perros WHERE idDueno = :idDueno");
        $stmtPerros->bindValue(':idDueno', $loggedInUserId);
        $stmtPerros->execute();
        $dogNamesArray = [];
        while ($row = $stmtPerros->fetch(PDO::FETCH_ASSOC)) {
            $dogNamesArray[] = htmlspecialchars($row['nombre']);
        }
        $userDogNames = implode(", ", $dogNamesArray);
    }
} catch (PDOException $e) {
    // Manejo de errores usando la función de helpers.php
    handlePdoError($e);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Publicaciones</title>
    <link rel="stylesheet" type="text/css" href="../../Cliente/css/estilos.css" />
    <link rel="stylesheet" type="text/css" href="../../Cliente/css/headerFooter.css" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        .contenido {
          background-color: #f5f5f5;
        }

        .posts-container {
            max-width: 800px;
            margin: 0 auto;
                margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        p {
            margin-bottom: 10px;
        }

        .publicacion {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .comentario {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-editar-comentario,
        .form-eliminar-comentario,
        .form-me-gusta-comentario,
        .form-comentario,
        .form-eliminar-publicacion,
        .form-editar-publicacion,
        .form-me-gusta-publicacion {
            margin-top: 10px;
        }
        .form-editar-comentario input[type="text"],
        .form-eliminar-comentario input[type="hidden"],
        .form-me-gusta-comentario input[type="hidden"],
        .form-comentario input[type="hidden"],
        .form-eliminar-publicacion input[type="hidden"],
        .form-editar-publicacion input[type="text"],
        .form-editar-publicacion input[type="hidden"],
        .form-me-gusta-publicacion input[type="hidden"] {
            display: block;
            margin-bottom: 5px;
            width: 98%;
            padding: 5px;
        }
        .form-comentario input[type="text"],
        .form-comentario textarea {
            display: block;
            margin-bottom: 5px;
            width: 98%;
            padding: 5px;
        }
        .form-comentario input[type="file"] {
            display: inline-block;
            margin-bottom: 20px;
            width: 100%;
        }
        .form-editar-comentario button,
        .form-eliminar-comentario button,
        .form-me-gusta-comentario button,
        .form-comentario button,
        .form-eliminar-publicacion button,
        .form-editar-publicacion button,
        .form-me-gusta-publicacion button {
            display: block;
            width: 98%;
            margin-top: 20px;
            padding: 5px;
            background-color: #337ab7;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-editar-comentario button:hover,
        .form-eliminar-comentario button:hover,
        .form-me-gusta-comentario button:hover,
        .form-comentario button:hover,
        .form-eliminar-publicacion button:hover,
        .form-editar-publicacion button:hover,
        .form-me-gusta-publicacion button:hover {
            background-color: #23527c;
        }
        .form-editar-comentario input[type="text"]:focus,
        .form-comentario input[type="text"]:focus,
        .form-editar-publicacion input[type="text"]:focus {
            outline: none;
            border-color: #337ab7;
        }        
        .crear-publicacion {
            margin-top: 20px;
            margin-bottom: 150px;
            margin-left: 50px;
            transition: transform 0.3s ease-in-out;
        }
        .crear-publicacion input[type="text"],
        .crear-publicacion textarea,
        .form-editar-publicacion textarea{
            display: block;
            margin-bottom: 5px;
            width: 98%;
            padding: 5px;
        }
        .crear-publicacion input[type="file"] {
            display: inline-block;
            margin-top: 20px;
            margin-bottom: 20px;
            width: 100%;
        }
        .crear-publicacion .photo-preview {
            margin-bottom: 20px;
        }
        .crear-publicacion .photo-preview:hover {
            transform: scale(2.5);
        }
        .crear-publicacion button {
            display: block;
            width: 98%;
            margin-top: 20px;
            padding: 5px;
            background-color: #337ab7;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .crear-publicacion button:hover {
            background-color: #23527c;
        }
        .crear-publicacion input[type="text"]:focus {
            outline: none;
            border-color: #337ab7;
        }                
        .foto-miniatura {
            width: 50px;
            height: 50px;
            transition: transform 0.3s ease;
        }
        .foto-miniatura:hover {
            width: 100px;
            height: 100px;
            transform: scale(2);
        }
        .foto-usuario {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
            object-fit: cover;
            object-position: center;
        }
        .nombre-usuario {
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }
        .publicacion-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .form-editar-publicacion input[type="file"] {
            display: inline-block;
            margin-bottom: 20px;
            width: 100%;
        }
        .form-group input[type="file"] {
            display: inline-block;
            margin-bottom: 20px;
            width: 100%;
        }
        .form-editar-publicacion .form-group .photo-preview {            
            margin-top: 20px;
            margin-bottom: 20px;
            width: 50px;
            height: 50px;
            transition: transform 0.3s ease-in-out;
        }
        .form-editar-publicacion .form-group .photo-preview {
            margin-bottom: 20px;
        }
        .form-editar-publicacion .form-group .photo-preview:hover {
            transform: scale(2.5);
        }
        .form-editar-comentario input[type="file"] {
            display: inline-block;
            margin-bottom: 20px;
            width: 100%;
        }
        .form-comentario input[type="file"] {
            display: inline-block;
            margin-bottom: 20px;
            width: 100%;
        }
        .form-editar-comentario .form-comentario .photo-preview {
            margin-top: 20px;
            margin-bottom: 20px;
            width: 50px;
            height: 50px;
            transition: transform 0.3s ease-in-out;
        }
        .form-editar-comentario .form-comentario .photo-preview:hover {
            transform: scale(2.5);
        }
        #error-message {
            display: none;
        }
        /* Estilos para mejorar la accesibilidad */
        [aria-hidden="true"] {
            display: none;
        }

        :focus {
            outline: 3px solid blue;
        }
        /* Media Queries para dispositivos móviles */
        @media screen and (max-width: 768px) {
            .posts-container, .crear-publicacion {
                max-width: 90%;
                padding: 10px;
            }
            .publicacion, .comentario {
                padding: 5px;
            }
            .form-editar-comentario input[type="text"],
            .form-comentario input[type="text"],
            .form-comentario textarea,
            .form-editar-publicacion input[type="text"],
            .form-editar-publicacion textarea {
                width: 95%;
            }
            .form-editar-comentario button,
            .form-comentario button,
            .form-editar-publicacion button {
                width: 95%;
            }
        }
        @media screen and (max-width: 480px) {
            .header h1, .header h2, .titulo-2 {
                font-size: 18px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <header class="header">        
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="perros.php">Perros</a></li>
                <li><a href="../../Cliente/verPerros.html">Ver Perros</a></li>
                <li><a href="../../Cliente/perrosPerdidos.html">Perdidos</a></li>
                <li><a href="../../Cliente/perrosEncontrados.html">Encontrados</a></li>
                <li><a href="../../Cliente/perrosAdopcion.html">En Adopción</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="../../Cliente/usuarios.html">Usuarios</a></li>
                <li><a href="../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>
    </header>  
    <div id="contenedorCabecera"></div>
    <div class="contenido"> 
            <h1 id="titulo">Publicaciones</h1>
            <div class="posts-container">
            <h1 class="titulo-2">Publicaciones</h1>
                <?php
                try {
                    // Verificar si el usuario tiene permisos de administrador o es el autor de la publicación o comentario
                    function checkPermissions($isAdmin, $authorId, $type)
                    {
                        $loggedInUserId = $_SESSION['id'];
                        if ($isAdmin) {
                            return true; 
                        }
                        if ($type == 'comment') {                    
                            return $loggedInUserId == $authorId;
                        } elseif ($type == 'post') {                    
                            return $loggedInUserId == $authorId;
                        }
                        return false;
                    }

                    // Obtener todas las publicaciones con la información de los usuarios
                    $query = "SELECT p.*, u.nombre AS nombreUsuario, u.fotoPerfil AS fotoUsuario, p.foto AS fotoPublicacion FROM Publicaciones p
                    JOIN Usuarios u ON p.idUsuario = u.id";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $publicaciones = [];
                    foreach ($result as $row) {
                        $publicacion = $row;

                        // Obtener el número de me gustas de la publicación
                        $queryMeGustasPublicacion = "SELECT COUNT(*) AS meGustas FROM MeGustasPublicacion WHERE idPublicacion = ?";
                        $stmtMeGustasPublicacion = $conn->prepare($queryMeGustasPublicacion);
                        $stmtMeGustasPublicacion->bindValue(1, $publicacion['id']);
                        $stmtMeGustasPublicacion->execute();
                        $resultMeGustasPublicacion = $stmtMeGustasPublicacion->fetch(PDO::FETCH_ASSOC);
                        $publicacion['meGusta'] = $resultMeGustasPublicacion['meGustas'];

                        // Obtener los comentarios de la publicación
                        $queryComentarios = "SELECT c.*, u.nombre AS nombreUsuario, u.fotoPerfil AS fotoUsuario, c.foto AS fotoComentario FROM Comentarios c
                                            JOIN Usuarios u ON c.idUsuario = u.id
                                            WHERE c.idPublicacion = ?";
                        $stmtComentarios = $conn->prepare($queryComentarios);
                        $stmtComentarios->bindValue(1, $publicacion['id']);
                        $stmtComentarios->execute();
                        $resultComentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);

                        $comentarios = [];
                        foreach ($resultComentarios as $rowComentario) {
                            $comentario = $rowComentario;

                            // Obtener el número de me gustas del comentario
                            $queryMeGustasComentario = "SELECT COUNT(*) AS meGustas FROM MeGustasComentario WHERE idComentario = ?";
                            $stmtMeGustasComentario = $conn->prepare($queryMeGustasComentario);
                            $stmtMeGustasComentario->bindValue(1, $comentario['id']);
                            $stmtMeGustasComentario->execute();
                            $resultMeGustasComentario = $stmtMeGustasComentario->fetch(PDO::FETCH_ASSOC);
                            $comentario['meGusta'] = $resultMeGustasComentario['meGustas'];

                            $comentarios[] = $comentario;
                        }

                        $publicacion['comentarios'] = $comentarios;
                        $publicaciones[] = $publicacion;
                    }

                    foreach ($publicaciones as $publicacion) {
                        $publicacionId = $publicacion['id'];
                        $publicacionTitulo = htmlspecialchars($publicacion['titulo']);
                        $publicacionFecha = $publicacion['fecha'];   
                        $publicacionTipo = $publicacion['tipo'];
                        $publicacionContenido = htmlspecialchars($publicacion['contenido']);
                        $postAuthorId = $publicacion['idAutor'];

                        // Verificar los permisos del usuario actual
                        $hasPermissionForPost = checkPermissions($isAdmin, $postAuthorId, 'post');

                        echo "<div class='publicacion'>";
                            echo "<h2>" . $publicacionTitulo . "</h2>";
                            echo "<p>" . $publicacionFecha . "</p>";
                            echo "<p>" . $publicacionTipo . "</p>";
                            echo "<div class='publicacion-info'>";
                            if (!empty($publicacion['fotoUsuario'])) {
                                echo "<img class='foto-usuario' src='../Publico/photos/" . htmlspecialchars($publicacion['fotoUsuario']) . "' alt='Foto del usuario'>";
                            }
                            echo "<p class='nombre-usuario'>" . htmlspecialchars($publicacion['nombreUsuario']) . "</p>";
                            echo "</div>";
                            echo '<p>' . $publicacionContenido . '</p>';
                            if (!empty($publicacion['fotoPublicacion'])) {
                                echo "<img class='foto-miniatura' src='../Publico/fotos_publicaciones/" . htmlspecialchars($publicacion['fotoPublicacion']) . "' alt='Foto de la publicación'>";
                            } else {
                                echo "<img class='foto-miniatura' src='../Publico/fotos_publicaciones/dog.jpg' alt='Foto por defecto'>";
                            }
                            echo "<p><span class='me-gusta-icono' id='iconoMeGusta_Publicacion_" . htmlspecialchars($publicacion['id']) . "'>👍</span> Me Gusta: <span id='numeroMeGustasPublicacion_" . htmlspecialchars($publicacion['id']) . "'>" . htmlspecialchars($publicacion['meGusta']) . "</span></p>";
                            foreach ($publicacion['comentarios'] as $comentario) {
                                $comentarioId = $comentario['id'];
                                $comentarioContenido = htmlspecialchars($comentario['texto']);
                                $comentarioFecha = $comentario['fecha'];
                                $commentAuthorId = $comentario['idAutor'];
                                // Verificar los permisos del usuario actual
                                $hasPermissionForComment = checkPermissions($isAdmin, $commentAuthorId, 'comment');

                                echo "<div class='comentario'>";                            
                                    echo "<p>" . $comentarioFecha . "</p>";
                                    echo "<div class='publicacion-info'>";
                                        if (!empty($comentario['fotoUsuario'])) {
                                            echo "<img class='foto-usuario' src='../Publico/photos/" . htmlspecialchars($comentario['fotoUsuario']) . "' alt='Foto del usuario'>";
                                        }
                                        echo "<p class='nombre-usuario'>" . htmlspecialchars($comentario['nombreUsuario']) . "</p>";
                                    echo "</div>";
                                    echo "<p>" . (isset($comentario['texto']) ? htmlspecialchars($comentario['texto']) : '') . "</p>";
                                    if (!empty($comentario['fotoComentario'])) {
                                        echo "<img class='foto-miniatura' src='../Publico/fotos_comentarios/" . htmlspecialchars($comentario['fotoComentario']) . "' alt='Foto del comentario'>";
                                    } else {
                                        echo "<img class='foto-miniatura' src='../Publico/fotos_comentarios/dog.jpg' alt='Foto por defecto'>";
                                    }                    
                                    echo "<p><span class='me-gusta-icono' id='iconoMeGusta_Comentario_" . htmlspecialchars($comentario['id']) . "'>👍</span> Me Gusta: <span id='numeroMeGustasComentario_" . htmlspecialchars($comentario['id']) . "'>" . htmlspecialchars($comentario['meGusta']) . "</span></p>";

                                    // Mostrar botones de edición y eliminación si el usuario tiene permisos
                                    if ($hasPermissionForComment) {
                                        echo '<form class="form-editar-comentario" action="editar_comentario.php" enctype="multipart/form-data" method="post" >';
                                        echo '<input type="hidden" name="idComentario" value="' . htmlspecialchars($comentario['id']) . '">';
                                        echo '<input type="hidden" name="idUsuario" value="' . $loggedInUserId . '">';
                                        echo '<input type="text" name="textoComentario" value="' . htmlspecialchars($comentario['texto']) . '" required>';
                                        echo '<div class="form-group">';
                                        echo '    <label for="fotoEditarComentario">Foto del comentario:</label>';
                                        echo '    <input type="file" id="fotoEditarComentario" name="fotoEditarComentario" accept="image/jpeg">';
                                        echo '    <label class="custom-file-upload" for="fotoEditarComentario">Seleccionar foto</label>';
                                        echo '    <div class="photo-preview">';
                                        echo '        <img id="photo-editar-comen-preview-img" src="#" alt="Foto del comentario">';
                                        echo '    </div>';
                                        echo '</div>';
                                        echo '<button type="submit">Editar Comentario</button>';
                                        echo '</form>';

                                        echo '<form class="form-eliminar-comentario" action="eliminar_comentario.php" enctype="multipart/form-data" method="post" onsubmit="return confirmarEliminacion();">';
                                        echo '<input type="hidden" name="idComentario" value="' . htmlspecialchars($comentario['id']) . "'>";
                                        echo '<input type="hidden" name="idUsuario" value="' . $loggedInUserId . '">';
                                        echo '<input type="hidden" name="idAutor" value="' . $commentAuthorId . '">';
                                        echo '<button type="submit">Eliminar Comentario</button>';
                                        echo '</form>';
                                    }                

                                    // Botón para dar/quitar me gusta al comentario
                                    echo "<form class='form-me-gusta-comentario' action='me_gusta_comentario.php' method='post'>";
                                    echo "<input type='hidden' name='idComentario' value='" . htmlspecialchars($comentario['id']) . "'>";
                                    echo "<input type='hidden' name='idUsuario' value='" . (isset($_SESSION['id']) ? htmlspecialchars($_SESSION['id']) : '') . "'>";
                                    echo "<button type='submit'>Me Gusta Comentario</button>";
                                    echo "</form>";

                                echo "</div>";
                            }
                            
                            // Formulario para agregar un nuevo comentario
                            echo "<form class='form-comentario' action='crear_comentario.php' method='post' enctype='multipart/form-data'>";
                            echo "<input type='hidden' name='idPublicacion' value='" . htmlspecialchars($publicacion['id']) . "'>";
                            echo "<input type='hidden' name='idUsuario' value='" . (isset($_SESSION['id']) ? htmlspecialchars($_SESSION['id']) : '') . "'>";
                            echo "<input type='hidden' name='idAutor' value='" . (isset($_SESSION['id']) ? htmlspecialchars($_SESSION['id']) : '') . "'>";
                            echo "<input type='text' name='textoComentario' placeholder='Escribe un comentario...' required>";
                            echo '<div class="form-group">';
                                    echo '<label for="fotoCrearComentario">Foto del comentario:</label>';
                                    echo '<input type="file" id="fotoCrearComentario" name="fotoCrearComentario" accept="image/jpeg">';
                                    echo '<label class="custom-file-upload" for="fotoCrearComentario">Seleccionar foto</label>';
                                    echo '<div class="photo-preview">';
                                        echo '<img id="photo-crear-comen-preview-img" src="#" alt="Foto del comentario">';
                                    echo '</div>';
                            echo '</div>';
                            echo "<button type='submit'>Crear Comentario</button>";
                            echo "</form>";
                            // Botones para editar y eliminar publicación
                            if ($hasPermissionForPost) {
                                echo '<form class="form-editar-publicacion" action="editar_publicacion.php" enctype="multipart/form-data" method="post" >';
                                echo '<input type="hidden" name="idPublicacion" value="' . $publicacionId . '">';
                                echo '<input type="hidden" name="idUsuario" value="' . $loggedInUserId . '">';
                                echo "<input type='text' name='tituloPublicacion' value='" . htmlspecialchars($publicacion['titulo']) . "' required>";
                                echo "<textarea name='contenidoPublicacion' required>" . htmlspecialchars($publicacion['contenido']) . "</textarea>";
                                echo '<div class="form-group">';
                                    echo '<label for="fotoEditarPublicacion">Foto de la publicación:</label>';
                                    echo '<input type="file" id="fotoEditarPublicacion" name="fotoEditarPublicacion" accept="image/jpeg">';
                                    echo '<label class="custom-file-upload" for="fotoEditarPublicacion">Seleccionar foto</label>';
                                    echo '<div class="photo-preview">';
                                        echo '<img id="photo-editar-publi-preview-img" src="#" alt="Foto de la publicacion">';
                                    echo '</div>';
                                echo '</div>';
                                echo '<button type="submit">Editar Publicación</button>';
                                echo '</form>';
                
                                echo '<form class="form-eliminar-publicacion" action="eliminar_publicacion.php" method="POST" onsubmit="return confirmarEliminacion();">';
                                echo '<input type="hidden" name="idPublicacion" value="' . htmlspecialchars($publicacion['id']) . "'>";
                                echo '<input type="hidden" name="idUsuario" value="' . $loggedInUserId . '">';
                                echo '<input type="hidden" name="idAutor" value="' . $postAuthorId . '">';
                                echo '<button type="submit">Eliminar Publicación</button>';
                                echo '</form>';
                            }

                            // Botón para dar/quitar me gusta a la publicación
                            echo "<form class='form-me-gusta-publicacion' action='me_gusta_publicacion.php' method='post'>";
                            echo "<input type='hidden' name='idPublicacion' value='" . htmlspecialchars($publicacion['id']) . "'>";
                            echo "<input type='hidden' name='idUsuario' value='" . (isset($_SESSION['id']) ? htmlspecialchars($_SESSION['id']) : '') . "'>";
                            echo "<button type='submit'>Me Gusta Publicación</button>";
                            echo "</form>";

                        echo "</div>";
                        
                        
                    }
                } catch (PDOException $e) {
                    handlePdoError($e);
                }
                ?>

            </div>
            <div class="crear-publicacion">
                <!-- Formulario para crear una nueva publicación -->
                <form action="crear_publicacion.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="idUsuario" value="<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : ''; ?>">            
                    <input type="hidden" name="idAutor" value= "<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : ''; ?>">
                    <input type="text" name="tituloPublicacion" placeholder="Título" required>
                    <textarea name="contenidoPublicacion" placeholder="Contenido" rows="3" required></textarea>
                    <select name="tipoPublicacion" required>
                        <option value="encontrado">Tipo Publicacón: Encontrado</option>
                        <option value="perdido">Tipo Publicacón: Perdido</option>
                        <option value="en adopcion">Tipo Publicacón: En Adopción</option>
                        <option value="otras">Tipo Publicacón: Otras</option>
                    </select>
                    <div class="form-group">
                        <label for="fotoCrearPublicacion">Foto de la publicación:</label>
                        <input type="file" id="fotoCrearPublicacion" name="fotoCrearPublicacion" accept="image/jpeg">
                        <label class="custom-file-upload" for="fotoCrearPublicacion">Seleccionar foto</label>
                        <div class="photo-preview">
                            <img id="photo-crear-publi-preview-img" src="#" alt="Foto de publicacion">
                        </div>
                    </div>      
                    <button type="submit">Crear Publicación</button>
                </form>
            </div>
            <div>
                <p id="error-message"></p>
            </div>
    </div>
    <div id="contenedorPieDePagina"></div>
    <script src="../../Cliente/js/api/cabecera.js"></script>   
    <script src="../../Cliente/js/api/perros.js"></script>
    <script src="../../Cliente/js/api/usuarios.js"></script>
    <script src="../../Cliente/js/api/admin.js"></script>    
    <script src="../../Cliente/js/api/error.js"></script>
    <script src="../../Cliente/js/api/comentarios.js"></script>
    <script src="../../Cliente/js/api/meGusta.js"></script>
    <script src="../../Cliente/js/api/publicaciones.js"></script>
    <script src="../../Cliente/js/main.js"></script>
    <script>
        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar?");
        }
        
        // Para Publicaciones
        function toggleMeGustaPublicacion(idPublicacion) {
            const botonMeGusta = document.getElementById(`botonMeGustaPublicacion_${idPublicacion}`);
            const numeroMeGustas = document.getElementById(`numeroMeGustasPublicacion_${idPublicacion}`);
            const meGusta = botonMeGusta.classList.contains('me-gusta-activo');

            // Realizar una petición al servidor para dar/quitar me gusta
            gestionarMeGustaPublicacion(idPublicacion, meGusta ? 'quitar-me-gusta' : 'me-gusta')
                .then(data => {
                    // Actualizar el estado del botón y el contador de me gustas
                    botonMeGusta.classList.toggle('me-gusta-activo', data.meGusta);
                    numeroMeGustas.textContent = data.meGustas;
                     
                })
                .catch(error => console.error(error));
        }

        // Para Comentarios
        function toggleMeGustaComentario(idComentario) {
            const botonMeGusta = document.getElementById(`botonMeGustaComentario_${idComentario}`);
            const numeroMeGustas = document.getElementById(`numeroMeGustasComentario_${idComentario}`);
            const meGusta = botonMeGusta.classList.contains('me-gusta-activo');

            // Realizar una petición al servidor para dar/quitar me gusta
            gestionarMeGustaComentario(idComentario, meGusta ? 'quitar-me-gusta' : 'me-gusta')
                .then(data => {
                    // Actualizar el estado del botón y el contador de me gustas
                    botonMeGusta.classList.toggle('me-gusta-activo', data.meGusta);
                    numeroMeGustas.textContent = data.meGustas;
                    
                })
                .catch(error => console.error(error));
        }

        // Mostrar vista previa de la foto de publicacion seleccionada
        document.getElementById('fotoCrearPublicacion').addEventListener('change', function(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var img = document.getElementById('photo-crear-publi-preview-img');
                img.src = reader.result;
            };
            reader.readAsDataURL(input.files[0]);
        });

        // Mostrar vista previa de la foto de publicacion seleccionada
        document.getElementById('fotoEditarPublicacion').addEventListener('change', function(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var img = document.getElementById('photo-editar-publi-preview-img');
                img.src = reader.result;
            };
            reader.readAsDataURL(input.files[0]);
        });

        // Mostrar vista previa de la foto de comentario seleccionada
        document.getElementById('fotoCrearComentario').addEventListener('change', function(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var img = document.getElementById('photo-crear-comen-preview-img');
                img.src = reader.result;
            };
            reader.readAsDataURL(input.files[0]);
        });
        // Mostrar vista previa de la foto de comentario seleccionada
        document.getElementById('fotoEditarComentario').addEventListener('change', function(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var img = document.getElementById('photo-editar-comen-preview-img');
                img.src = reader.result;
            };
            reader.readAsDataURL(input.files[0]);
        });    
        
    </script> 
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("contenedorCabecera").innerHTML =
          cargarCabecera(
            "../../Cliente/img/logo.png",
            "perfil.php"
          );
        document.getElementById("contenedorPieDePagina").innerHTML =
          cargarPieDePagina("../../Cliente/img/logo.jpg");
      });
    </script>   
</body>
</html>