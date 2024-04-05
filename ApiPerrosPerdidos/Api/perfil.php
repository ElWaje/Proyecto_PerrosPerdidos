<?php
session_start();

define('INCLUDED', true);

require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();  

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
        $userDogNames = htmlspecialchars($userDogNames);
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
    // Manejo de errores
    $errorMessage = handlePdoError($e);
}
?>

<!DOCTYPE html>
<html lang = "es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" type="text/css" href="../../Cliente/css/estilos.css" />
    <link rel="stylesheet" type="text/css" href="../../Cliente/css/headerFooter.css" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">   
    <style>
        .contenido {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }        
        p {
            margin: 10px 0;
        }
        .profile-info {
            margin-top: 20px;
        }
        .profile-info label {
            font-weight: bold;
        }
        .profile-info p {
            margin-bottom: 5px;
        }
        .show-password-container {
            margin-top: 10px;
        }
        .show-password-container input[type="checkbox"] {
            margin-right: 5px;
        }
        .profile-photo img {
            width: 100px;
            height: 100px;
            transition: transform 0.3s ease-in-out;
        }

        .profile-photo img:hover {
            transform: scale(2.5);
        }

        .buttons-container {
            display: flex;
            justify-content: space-between; 
            align-items: center;
            padding: 20px 0;
        }

        .button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, color 0.3s;
        }

        .edit-button {
            background-color: #4CAF50; 
            color: white;
        }

        .edit-button:hover {
            background-color: #45a049; 
        }

        .delete-button {
            background-color: #f44336; 
            color: white;
        }

        .delete-button:hover {
            background-color: #da190b; 
        }

        /* Estilos para mejorar la accesibilidad */
        [aria-hidden="true"] {
            display: none;
        }

        :focus {
            outline: 3px solid blue;
        }

        /* Media Queries para tablets */
        @media screen and (max-width: 768px) {
            .profile-container {
                width: 90%;
                padding: 10px;
            }
            
            .profile-photo img {
                width: 80px;
                height: 80px;
            }

            .button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }

        /* Media Queries para móviles */
        @media screen and (max-width: 480px) {
            .profile-container {
                width: 95%;
            }
            
            .profile-photo img {
                width: 60px;
                height: 60px;
            }

            .button {
                font-size: 12px;
                padding: 6px 12px;
            }
        }        

    </style>
</head>
<body>
    <header class="header">        
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="perros.php">Perros</a></li>
                <li><a href="../../Cliente/verPerros.html">Ver Perros</a></li>
                <li><a href="../../Cliente/perrosAdopcion.html">En Adopción</a></li>
                <li><a href="../../Cliente/perrosEncontrados.html">Encontrados</a></li>
                <li><a href="../../Cliente/perrosPerdidos.html">Perdidos</a></li>
                <li><a href="../../Cliente/usuarios.html">Usuarios</a></li>
                <li><a href="publicaciones.php">Publicaciones</a></li>
                <?php if ($isAdmin): ?>
                    <li class="admin"><a href="admin.html">Administración</a></li>
                <?php endif; ?>                                
                <li><a href="../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>  
    <div id="contenedorCabecera"></div>
    <div class="contenido"> 
        <h1 id=titulo>Perfil de Usuario</h1>                
        <div class="profile-container">
            <h2 class=titulo-2>Perfil de Usuario</h2>
            <div class="profile-info">
                <div class="profile-photo">
                    <?php if ($userPhoto): ?>
                        <?php if ($userPhoto=='default-profile-photo.png'): ?>
                            <img class="profile-photo" src="../Publico/photos/default-profile-photo.png" alt="Foto de perfil por defecto">
                        <?php else: ?>
                            <img class="profile-photo" src="../Publico/photos/<?php echo $userPhoto; ?>" alt="Foto de perfil">
                        <?php endif; ?>    
                    <?php else: ?>
                        <img class="profile-photo" src="../Publico/photos/default-profile-photo.png" alt="Foto de perfil por defecto">
                    <?php endif; ?>
                </div>
                <p><label>ID:</label> <?php echo $loggedInUserId; ?></p>
                <p><label>Nombre:</label> <?php echo $userName; ?></p>
                <p><label>Correo electrónico:</label> <?php echo $userEmail; ?></p>
                <div class="show-password-container">
                    <input type="checkbox" id="show-password-checkbox">
                    <label for="show-password-checkbox">Mostrar contraseña</label>
                </div>
                <p class="password-field"><label>Contraseña:</label> ********</p>
                <p><label>Rol:</label> <?php echo $userRole; ?></p>
                <p><label>Nombre de sus perros:</label> <?php echo $userDogNames; ?></p>
            </div>
            <div class="buttons-container">
                <form action="editarUsuario.php" method="post">
                    <input type="hidden" name="usuarioId" value="<?php echo $loggedInUserId; ?>">
                    <input type="submit" value="Editar datos" class="button edit-button">
                </form>

                <form action="borrarUsuario.php" method="post" onsubmit="return confirmDelete();">
                    <input type="hidden" name="loggedInUserId" value="<?php echo $loggedInUserId; ?>">
                    <input type="submit" value="Eliminar usuario" class="button delete-button">
                </form>
            </div>
        </div>  
        
        <?php if (isset($errorMessage)): ?>
            <div class="error-message">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

    </div>
    <div id="contenedorPieDePagina"></div>
    <script src="../../Cliente/js/api/cabecera.js"></script>
    <script>
        const showPasswordCheckbox = document.getElementById('show-password-checkbox');
        const passwordField = document.querySelector('.profile-info .password-field');

        showPasswordCheckbox.addEventListener('change', function () {
            if (showPasswordCheckbox.checked) {
                passwordField.innerHTML = '<label>Contraseña:</label> <?php echo htmlspecialchars($userPassword); ?>';
            } else {
                passwordField.innerHTML = '<label>Contraseña:</label> ********';
            }
        });
    </script>
    <script>
            function confirmDelete() {
                return confirm("¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.");
            }
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