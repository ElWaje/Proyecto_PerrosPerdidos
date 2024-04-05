<?php
session_start();

define('INCLUDED', true);

require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';
 

$userInfo = verifyAdminAndSession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$nombreUsuarioActivo = $userInfo['nombreUsuarioActivo'];

// Crear una instancia de la clase BaseDeDatos
$db = new BaseDeDatos();
try {
    $conn = $db->getConnection();
    // Función para obtener todos los usuarios
    function obtenerUsuarios($conn) {
        try {
            $stmt = $conn->query("SELECT id, nombre FROM Usuarios");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            handlePdoError($e);
        }
    }
    $usuarios = obtenerUsuarios($conn);  

    // Función para obtener todos los perros con sus datos y el nombre del dueño
    function obtenerPerrosConDueños($conn, $idUsuario = null) {
            $sql = "SELECT p.id, p.nombre AS nombre_perro, p.raza, p.edad, p.collar, p.chip, p.lugarPerdido, p.fechaPerdido, p.lugarEncontrado, p.fechaEncontrado, p.estado, p.lugar, p.fechaUltimaActualizacion, p.foto, p.color, p.tamano, p.descripcion, p.idDueno, u.nombre AS nombre_dueño
                    FROM Perros p
                    INNER JOIN Usuarios u ON p.idDueno = u.id";
            if ($idUsuario) {
                $sql .= " WHERE p.idDueno = :idUsuario";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            } else {
                $stmt = $conn->query($sql);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
    // Obtener el ID del usuario seleccionado
    $idUsuarioSeleccionado = $_POST['usuario'] ?? null;
    $perros = obtenerPerrosConDueños($conn, $idUsuarioSeleccionado);
    $sinPerros = empty($perros);

} catch (PDOException $e) {
    handlePdoError($e);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <title>Mostrar Perro</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <style>

        /* Nuevo contenedor para el título */
        .tabla-title-container {
            width: 100%;
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding: 10px 0;
        }

        /* Estilos mejorados para el título de la tabla */
        .tabla-title {
            margin-bottom: 0;
            font-size: 28px;
            color: #4CAF50;
            font-weight: bold; 
        }

        .tabla-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 50px;
            margin-bottom: 150px;
            padding: 20px 0; 
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        
        /* Estilos para la tabla */
        .tabla {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
        }

        /* Estilos para las filas de la tabla (zebra striping) */
        .tabla tr:nth-child(odd) {
            background-color: #f2f2f2; 
        }

        .tabla tr:nth-child(even) {
            background-color: #ffffff; 
        }

        /* Estilos mejorados para la cabecera de la tabla */
        .tabla th {
            background-color: #4CAF50; 
            color: white; 
            padding: 12px; 
            font-weight: bold;
            text-align: left;
            transition: transform 0.2s ease-in-out;
        }

        /* Efecto de transformación al pasar el mouse por encima */
        .tabla th:hover {
            transform: scale(1.1);
        }

        /* Estilos para las celdas de la tabla */
        .tabla td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
            transition: transform 0.2s ease-in-out;
            vertical-align: middle;
        }

        /* Estilos para las imágenes en las celdas */
        .tabla td img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease-in-out;
        }

        /* Efecto de transformación para las imágenes */
        .tabla td img:hover {
            transform: scale(2.5);
        }

        /* Estilos para el formulario */
        form {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 5px;
            text-align: center;
        }

        /* Estilos para los elementos del formulario */
        form label, form select, form input[type="submit"] {
            margin: 10px;
            padding: 8px;
            border-radius: 4px;
        }

        /* Estilos para el label del formulario */
        form label {
            display: block;
            margin-bottom: 4px;
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }

        form select {
             margin-top: 4px;
        }

        /* Estilos para el botón del formulario */
        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        /* Efecto al pasar el mouse por encima del botón */
        form input[type="submit"]:hover {
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
            .formulario-container, .tabla-container {
                max-width: 90%;
                padding: 15px;
            }

            .tabla {
                font-size: 14px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .formulario-container, .tabla-container {
                max-width: 100%;
                padding: 10px;
            }

            .tabla {
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
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
        <h1 id="titulo" >Panel de Administración</h1>
        <h2 class="titulo-2">Mostrar perros</h2>
        <form action="mostrarPerro.php" method="post">
            <label for="usuario">Seleccionar Usuario:</label>
            <select name="usuario" id="usuario">
                <option value="">Todos los Usuarios</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?php echo $usuario['id']; ?>" <?php echo $idUsuarioSeleccionado == $usuario['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($usuario['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Mostrar Perros">
        </form>
        <div class="tabla-title-container">
            <h1 class="tabla-title">Lista de Perros</h1>
        </div>
        <div class="tabla-container">
            <?php if ($sinPerros): ?>
                <p>No hay perros registrados para el usuario seleccionado.</p>
            <?php else: ?>
                <table class="tabla">
                    <thead>
                        <tr>                    
                            <th>Foto</th>    
                            <th>ID</th>
                            <th>Nombre del Perro</th>
                            <th>Raza</th>
                            <th>Edad</th>
                            <th>Collar</th>
                            <th>Chip</th>
                            <th>Lugar Perdido</th>
                            <th>Fecha Perdido</th>
                            <th>Lugar Encontrado</th>
                            <th>Fecha Encontrado</th>
                            <th>Estado</th>
                            <th>Lugar</th>
                            <th>Fecha Última Actualización</th>
                            <th>Color</th>
                            <th>Tamaño</th>
                            <th>Descripción</th>
                            <th>ID Dueño</th>
                            <th>Nombre del Dueño</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($perros as $perro): ?>
                        <tr>
                            
                            <td>
                                <?php if (!empty($perro['foto'])) { ?>
                                    <img src="../../Publico/fotos_perros/<?php echo $perro['foto']; ?>" alt="Foto del perro" width="50" height="50">                          
                                <?php } ?>
                            </td>
                            <td><?php echo $perro['id']; ?></td>
                            <td><?php echo $perro['nombre_perro']; ?></td>
                            <td><?php echo $perro['raza']; ?></td>
                            <td><?php echo $perro['edad']; ?></td>
                            <td><?php echo $perro['collar']; ?></td>
                            <td><?php echo $perro['chip']; ?></td>
                            <td><?php echo $perro['lugarPerdido']; ?></td>
                            <td><?php echo $perro['fechaPerdido']; ?></td>
                            <td><?php echo $perro['lugarEncontrado']; ?></td>
                            <td><?php echo $perro['fechaEncontrado']; ?></td>
                            <td><?php echo $perro['estado']; ?></td>
                            <td><?php echo $perro['lugar']; ?></td>
                            <td><?php echo $perro['fechaUltimaActualizacion']; ?></td>                    
                            <td><?php echo $perro['color']; ?></td>
                            <td><?php echo $perro['tamano']; ?></td>
                            <td><?php echo $perro['descripcion']; ?></td>
                            <td><?php echo $perro['idDueno']; ?></td>
                            <td><?php echo $perro['nombre_dueño']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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