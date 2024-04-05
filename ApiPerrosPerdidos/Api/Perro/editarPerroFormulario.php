<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


// Utilizar la función de verificación de sesión y administrador
$userInfo = verifyAdminAndSession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$nombreUsuarioActivo = $userInfo['nombreUsuarioActivo'];

// Obtener el ID del perro seleccionado 
$idPerro = $_POST['idPerro'] ?? null;

// Crear una instancia de la clase BaseDeDatos
$db = new BaseDeDatos();

// Intentar obtener la conexión a la base de datos y realizar las operaciones
try {
    $conn = $db->getConnection();
    $perroSeleccionado = obtenerPerroPorId($conn, $idPerro);
    $nombreUsuarioDueno = obtenerNombreUsuarioPorId($conn, $perroSeleccionado['idDueno']);
} catch (PDOException $e) {
    handlePdoError($e);
}

// Función para obtener todos los usuarios
function obtenerUsuarios($conn) {
  try {
    $stmt = $conn->query("SELECT id, nombre FROM Usuarios");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "<script>alert('Error en la conexión a la base de datos: " . $e->getMessage() . "');</script>";
  }
}

// Función para obtener el nombre de un usuario por su ID
function obtenerNombreUsuarioPorId($conn, $idUsuario) {
    try {
      $stmt = $conn->prepare("SELECT nombre FROM Usuarios WHERE id = :idUsuario");
      $stmt->bindValue(':idUsuario', $idUsuario);
      $stmt->execute();
      return $stmt->fetchColumn();
    } catch (PDOException $e) {
      echo "<script>alert('Error en la conexión a la base de datos: " . $e->getMessage() . "');</script>";
    }
  }

// Función para obtener todos los perros de un usuario
function obtenerPerrosPorUsuario($conn, $idUsuario) {
  try {
    $stmt = $conn->prepare("SELECT id, nombre FROM Perros WHERE idDueno = :idUsuario");
    $stmt->bindValue(':idUsuario', $idUsuario);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "<script>alert('Error en la conexión a la base de datos: " . $e->getMessage() . "');</script>";
  }
}

// Función para obtener los datos de un perro por su ID
function obtenerPerroPorId($conn, $idPerro) {
  try {
    $stmt = $conn->prepare("SELECT * FROM Perros WHERE id = :idPerro");
    $stmt->bindValue(':idPerro', $idPerro);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "<script>alert('Error en la conexión a la base de datos: " . $e->getMessage() . "');</script>";
  }
}

// Obtener los datos del perro seleccionado
$perroSeleccionado = obtenerPerroPorId($conn, $idPerro);

// Obtener el nombre del usuario dueño del perro
$nombreUsuarioDueno = obtenerNombreUsuarioPorId($conn, $perroSeleccionado['idDueno']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <title>Editar Perro Formulario</title>
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../Cliente/css/headerFooter.css" />    
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <style>
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
            width: 98%;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .formulario-input {
            width: 98%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 16px;
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
            background-color: #4caf50;
            color: white;
            margin-top: 20px;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .formulario-submit:hover {
            background-color: #45a049;
        }

        .form-group {
            font-weight: bold;            
            margin-bottom: 20px;
        }

        input[type="text"], textarea {
          width: 98%;
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
            .formulario-container, .tabla-container, .tabla-title-container {
                max-width: 90%;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .formulario-container, .tabla-container, .tabla-title-container {
                max-width: 100%;
                padding: 10px;
            }

            .formulario-input, .formulario-select, .formulario-submit {
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
                <li><a href="editarPerro.php">Volver</a></li>
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>    
    <div id="contenedorCabecera"></div>
    <div class="contenido">
            <h1 id="titulo" >Panel de Administración</h1>
            <h2 class="titulo-2">Editar perro</h2>
            <div class="formulario-container">
                <h1 class="formulario-title">Editar Perro</h1>
                <form action="actualizarPerro.php" method="POST" enctype=multipart/form-data>
                <input type="hidden" name="perroId" value="<?php echo $perroSeleccionado['id']; ?>">        
                <div class="form-group">
                    <label for="nombre" class="formulario-label">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="formulario-input" value="<?php echo $perroSeleccionado['nombre']; ?>">
                </div>
                <div class="form-group">  
                    <label for="raza" class="formulario-label">Raza:</label>
                    <input type="text" name="raza" id="raza" class="formulario-input" value="<?php echo $perroSeleccionado['raza']; ?>">
                </div>        
                <div class="form-group">    
                    <label for="edad" class="formulario-label">Edad:</label>
                    <input type="number" name="edad" id="edad" class="formulario-input" value="<?php echo $perroSeleccionado['edad']; ?>">
                </div>
                <div class="form-group">
                    <label for="collar" class="formulario-label">Collar:</label>
                    <select name="collar" id="collar" class="formulario-select">
                        <option value="si" <?php if ($perroSeleccionado['collar'] == 'si') echo 'selected'; ?>>Sí</option>
                        <option value="no" <?php if ($perroSeleccionado['collar'] == 'no') echo 'selected'; ?>>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="chip" class="formulario-label">Chip:</label>
                    <select name="chip" id="chip" class="formulario-select">
                        <option value="si" <?php if ($perroSeleccionado['chip'] == 'si') echo 'selected'; ?>>Sí</option>
                        <option value="no" <?php if ($perroSeleccionado['chip'] == 'no') echo 'selected'; ?>>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="lugarPerdido" class="formulario-label">Lugar Perdido:</label>
                    <input type="text" name="lugarPerdido" id="lugarPerdido" class="formulario-input" value="<?php echo $perroSeleccionado['lugarPerdido']; ?>">
                </div>
                <div class="form-group">
                    <label for="fechaPerdido" class="formulario-label">Fecha Perdido:</label>
                    <input type="date" name="fechaPerdido" id="fechaPerdido" class="formulario-input" value="<?php echo $perroSeleccionado['fechaPerdido']; ?>">
                </div>
                <div class="form-group">
                    <label for="lugarEncontrado" class="formulario-label">Lugar Encontrado:</label>
                    <input type="text" name="lugarEncontrado" id="lugarEncontrado" class="formulario-input" value="<?php echo $perroSeleccionado['lugarEncontrado']; ?>">
                </div>
                <div class="form-group">
                    <label for="fechaEncontrado" class="formulario-label">Fecha Encontrado:</label>
                    <input type="date" name="fechaEncontrado" id="fechaEncontrado" class="formulario-input" value="<?php echo $perroSeleccionado['fechaEncontrado']; ?>">
                </div>
                <div class="form-group">
                    <label for="estado" class="formulario-label">Estado:</label>
                    <select name="estado" id="estado" class="formulario-select">
                        <option value="perdido" <?php if ($perroSeleccionado['estado'] == 'perdido') echo 'selected'; ?>>Perdido</option>
                        <option value="encontrado" <?php if ($perroSeleccionado['estado'] == 'encontrado') echo 'selected'; ?>>Encontrado</option>
                        <option value="en adopción" <?php if ($perroSeleccionado['estado'] == 'en adopción') echo 'selected'; ?>>En adopción</option>
                        <option value="con dueño" <?php if ($perroSeleccionado['estado'] == 'con dueño') echo 'selected'; ?>>Con dueño</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="lugar" class="formulario-label">Lugar:</label>
                    <input type="text" name="lugar" id="lugar" class="formulario-input" value="<?php echo $perroSeleccionado['lugar']; ?>">
                </div>
                <div class="form-group">
                    <label for="fechaUltimaActualizacion" class="formulario-label">Fecha Última Actualización:</label>
                    <input type="datetime-local" name="fechaUltimaActualizacion" id="fechaUltimaActualizacion" class="formulario-input" value="<?php echo date('Y-m-d\TH:i', strtotime($perroSeleccionado['fechaUltimaActualizacion'])); ?>">
                </div>           
                <div class="form-group">
                        <label for="foto">Foto del perro:<br><br></label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg">
                        <label class="formulario-label" for="foto"><br><br>Seleccionar foto<br><br></label>
                        <div class="photo-preview">
                            <img id="photo-preview-img" src="#" alt="Foto de perro">
                        </div>
                </div>
                <div class="form-group">
                    <label for="color" class="formulario-label">Color:</label>
                    <input type="text" name="color" id="color" class="formulario-input" value="<?php echo $perroSeleccionado['color']; ?>">
                </div>
                <div class="form-group">
                    <label for="tamano" class="formulario-label">Tamaño:</label>
                    <select name="tamano" id="tamano" class="formulario-select">
                        <option value="pequeño" <?php if ($perroSeleccionado['tamano'] == 'pequeño') echo 'selected'; ?>>Pequeño</option>
                        <option value="mediano" <?php if ($perroSeleccionado['tamano'] == 'mediano') echo 'selected'; ?>>Mediano</option>
                        <option value="grande" <?php if ($perroSeleccionado['tamano'] == 'grande') echo 'selected'; ?>>Grande</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="descripcion" class="formulario-label">Descripción:</label>
                    <textarea name="descripcion" id="descripcion" class="formulario-input"><?php echo $perroSeleccionado['descripcion']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="idDueno" class="formulario-label">ID Dueño:</label>
                    <input type="number" name="idDueno" id="idDueno" class="formulario-input" value="<?php echo $perroSeleccionado['idDueno']; ?>">
                </div>
                <div class="form-group">
                    <label for="nombreDueno" class="formulario-label">Nombre Dueño: <?php echo $nombreUsuarioDueno; ?></label>
                </div>
                <input type="submit" value="Guardar cambios" class="formulario-submit">
            </form>
            </div>
    </div>  
    <div id="contenedorPieDePagina"></div>
    <script src="../../../Cliente/js/api/cabecera.js"></script>   
    <script>
      // Mostrar vista previa de la foto de perfil seleccionada
      document.getElementById('foto').addEventListener('change', function(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var img = document.getElementById('photo-preview-img');
                img.src = reader.result;
            };
            reader.readAsDataURL(input.files[0]);
        });
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