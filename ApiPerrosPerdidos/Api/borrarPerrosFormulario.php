<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();  

$usuarioActivoId = $_SESSION['id'];
$nombreUsuarioActivo = $_SESSION['nombre'] ?? '';
$perroSeleccionado = null;
$perroId = $_GET['perroId'] ?? null;

if (!$perroId) {
    handleErrors("No se especificó el ID del perro.");
}

$db = new BaseDeDatos();
$conn = $db->getConnection();

function obtenerPerroPorId($conn, $perroId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Perros WHERE id = :perroId");
        $stmt->bindValue(':perroId', $perroId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handlePdoError($e);
    }
}

$perroSeleccionado = obtenerPerroPorId($conn, $perroId);

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Borrar Perros Formulario</title>
  <link rel="stylesheet" type="text/css" href="../../Cliente/css/estilos.css" />
  <link rel="stylesheet" type="text/css" href="../../Cliente/css/headerFooter.css" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <style>
    div{
        margin-bottom: 10px;
    }

    .formulario-label {
        display: block;
        font-weight: bold;
    }
    
    .formulario-container {
        margin: 50px auto 150px auto;
        border-radius: 10px;
        width: 400px;
    }

    .formulario-container form {
        padding: 20px;
        background-color: #f7f7f7;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .formulario-container h2, .formulario-container h1 {
        text-align: center;
        margin-bottom: 20px;
    }

    .formulario-container label {
        margin-bottom: 5px;
    }

    .formulario-container input[type="text"], .formulario-container input[type="date"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .formulario-container input[type="submit"] {
        width: 100%;
        background-color: #d32f2f;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .formulario-container input[type="submit"] :hover {
            background-color: #B71C1C;
    }

    .formulario-container .photo-preview {
        margin: 10px 0 20px 0;
        text-align: center;
    }

    .formulario-container .photo-preview img {
        width: 100px;
        height: 100px;
        margin: 10px 0;
    }

    .estado {
        margin-top: 20px;
    }

    #descripcion {
        width: 98%;
    }

    /* Estilos para mejorar la accesibilidad */
    [aria-hidden="true"] {
            display: none;
    }

    :focus {
            outline: 3px solid blue;
    }

    /* Media Query para pantallas hasta 768px (Tablets) */
    @media screen and (max-width: 768px) {
        .formulario-container {
            width: 90%;
            padding: 15px;
        }

        /* Ajustes para los elementos del formulario */
        .formulario-container input[type="text"],
        .formulario-container input[type="date"],
        .formulario-container textarea,
        .formulario-container select {
            font-size: 14px;
        }

        .formulario-container label {
            margin-bottom: 5px;
        }
    }

    /* Media Query para pantallas hasta 480px (Móviles) */
    @media screen and (max-width: 480px) {
        .formulario-container {
            width: 100%;
            margin: 30px 10px;
            padding: 10px;
        }

        /* Ajustes para los elementos del formulario */
        .formulario-container input[type="text"],
        .formulario-container input[type="date"],
        .formulario-container textarea,
        .formulario-container select {
            font-size: 12px;
        }

        .formulario-container label {
            margin-bottom: 3px;
        }

    }

  </style>
</head>

<body>
  <header class="header">
    <div class="nav-container">
      <ul class="nav-links">
        <li><a href="perros.php">Perros</a></li>
        <li><a href="../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
      </ul>
    </div>
  </header> 
  <div id="contenedorCabecera"></div>
  <div class="contenido">
        <main class="main"> 
            <h1 id="titulo">Gestión de Perros</h1>
            <div class="formulario-container">
            <h1 class="titulo-2">Formulario de borrado  de Perros de <?php echo htmlspecialchars($nombreUsuarioActivo); ?></h1>
            <form id="formulario-perros" enctype="multipart/form-data" method="post" action="accionBorrarPerros.php">
                <?php if ($perroSeleccionado): ?>
                    <input type="hidden" name="perroId" value="<?php echo htmlspecialchars($perroSeleccionado['id']); ?>">            
                    <div>
                        <label for="nombre" class="formulario-label">Nombre:</label>
                        <input type="text" id="nombre" value="<?php echo htmlspecialchars($perroSeleccionado['nombre']); ?>" readonly>
                    </div>
                    <div>
                        <label for="raza" class="formulario-label">Raza:</label>
                        <input type="text" id="raza" value="<?php echo htmlspecialchars($perroSeleccionado['raza'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>
                    <div>
                        <label for="edad" class="formulario-label">Edad:</label>
                        <input type="text" id="edad" value="<?php echo htmlspecialchars($perroSeleccionado['edad'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="collar" class="formulario-label">Collar:</label>
                        <select id="collar" disabled>
                            <option value="si" <?php echo $perroSeleccionado['collar'] === 'si' ? 'selected' : ''; ?>>Sí</option>
                            <option value="no" <?php echo $perroSeleccionado['collar'] === 'no' ? 'selected' : ''; ?>>No</option>
                        </select><br>
                    </div>
                    <div class="form-group">
                        <label for="chip" class="formulario-label">Chip:</label>
                        <select id="chip" disabled>
                            <option value="si" <?php echo ($perroSeleccionado['chip'] === 'si') ? 'selected' : ''; ?>>Sí</option>
                            <option value="no" <?php echo ($perroSeleccionado['chip'] === 'no') ? 'selected' : ''; ?>>No</option>
                        </select><br>
                    </div>
                    <div>
                        <label for="lugar-perdido" class="formulario-label">Lugar Perdido:</label>
                        <input type="text" id="lugar-perdido" value="<?php echo htmlspecialchars($perroSeleccionado['lugarPerdido'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>
                    <div>
                        <label for="fecha-perdido" class="formulario-label">Fecha Perdido:</label>
                        <input type="date" id="fecha-perdido" value="<?php echo htmlspecialchars($perroSeleccionado['fechaPerdido'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>
                    <div>
                        <label for="lugar-encontrado">Lugar Encontrado:</label>
                        <input type="text" id="lugar-encontrado" value="<?php echo htmlspecialchars($perroSeleccionado['lugarEncontrado'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>

                    <div>
                        <label for="fecha-encontrado" class="formulario-label">Fecha Encontrado:</label>
                        <input type="date" id="fecha-encontrado" value="<?php echo htmlspecialchars($perroSeleccionado['fechaEncontrado'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="estado" class="formulario-label">Estado:</label>
                        <select id="estado" disabled>
                            <option value="perdido" <?php echo ($perroSeleccionado['estado'] == 'perdido') ? 'selected' : ''; ?>>Perdido</option>
                            <option value="encontrado" <?php echo ($perroSeleccionado['estado'] == 'encontrado') ? 'selected' : ''; ?>>Encontrado</option>
                            <option value="en adopción" <?php echo ($perroSeleccionado['estado'] == 'en adopción') ? 'selected' : ''; ?>>En adopción</option>
                            <option value="con dueño" <?php echo ($perroSeleccionado['estado'] == 'con dueño') ? 'selected' : ''; ?>>Con dueño</option>
                        </select><br>
                    </div>

                    <div>
                        <label for="lugar">Lugar:</label>
                        <input type="text" id="lugar" value="<?php echo htmlspecialchars($perroSeleccionado['lugar'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>           

                    <div>
                        <label for="color" class="formulario-label">Color:</label>
                        <input type="text" id="color" value="<?php echo htmlspecialchars($perroSeleccionado['color'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="tamano" class="formulario-label">Tamaño:</label>
                        <select id="tamano" disabled>
                            <option value="pequeño" <?php echo ($perroSeleccionado['tamano'] == 'pequeño') ? 'selected' : ''; ?>>Pequeño</option>
                            <option value="mediano" <?php echo ($perroSeleccionado['tamano'] == 'mediano') ? 'selected' : ''; ?>>Mediano</option>
                            <option value="grande" <?php echo ($perroSeleccionado['tamano'] == 'grande') ? 'selected' : ''; ?>>Grande</option>
                        </select>
                    </div>

                    <div>
                        <label for="descripcion" class="formulario-label">Descripción:</label>
                        <textarea id="descripcion" rows="4" readonly><?php echo htmlspecialchars($perroSeleccionado['descripcion'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div>                
                    <input type="submit" name="accionBorrarPerro" value="Eliminar Perro" onclick="return confirmarBorrado();">
                    </div>
                    
                <?php else: ?>
                    <p>El perro seleccionado no existe.</p>
                <?php endif; ?>                               
            </form>
            </div>
        </main>

  </div>  
  <div id="contenedorPieDePagina"></div>
  <script src="../../Cliente/js/api/cabecera.js"></script>
  <script>
      function confirmarBorrado() {
          return window.confirm("¿Estás seguro de que deseas eliminar este perro?");
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