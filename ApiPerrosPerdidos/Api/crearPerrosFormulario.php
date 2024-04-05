<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear Perros Formulario</title>
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
        background-color: #4caf50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
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
        <h1 class="titulo-2">Formulario de creación de Perros de <?php echo $nombreUsuarioActivo; ?></h1>
          <form id="formulario-perros" enctype="multipart/form-data" method="post" action="accionCrearPerros.php">
              <div>
                <label for="nombre" class="formulario-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
              </div>
              <div>
                <label for="raza" class="formulario-label">Raza:</label>
                <input type="text" id="raza" name="raza" required>
              </div>
              <div>
                <label for="edad" class="formulario-label">Edad:</label>
                <input type="text" id="edad" name="edad" required>
              </div>
              <div>
                <label for="collar" class="formulario-label">Collar:</label>
                <input type="checkbox" id="collar" name="collar" value="si">Marcar para si
              </div>
              <div>
                <label for="chip" class="formulario-label">Chip:</label>
                <input type="checkbox" id="chip" name="chip" value="si">Marcar para si
              </div>
              <div>
                <label for="lugar-perdido" class="formulario-label">Lugar Perdido:</label>
                <input type="text" id="lugar-perdido" name="lugar-perdido">
              </div>
              <div>
                <label for="fecha-perdido" class="formulario-label">Fecha Perdido:</label>
                <input type="date" id="fecha-perdido" name="fecha-perdido">
              </div>
              <div>
                <label for="lugar-encontrado" class="formulario-label">Lugar Encontrado:</label>
                <input type="text" id="lugar-encontrado" name="lugar-encontrado">
              </div>
              <div>
                <label for="fecha-encontrado" class="formulario-label">Fecha Encontrado:</label>
                <input type="date" id="fecha-encontrado" name="fecha-encontrado">
              </div>          
              <div class="form-group">
                    <label for="photo" class="formulario-label">Foto de perfil:</label>
                    <input type="file" id="photo" name="foto" accept="image/jpeg">
                    <label class="formulario-label" for="photo">Seleccionar foto</label>
                    <div class="photo-preview">
                        <img id="photo-preview-img" src="#" alt="Foto de perfil">
                    </div>
              </div>
              <div class="form-group">
                  <label for="estado" class="formulario-label">Estado:</label>
                  <select id="estado" name="estado">
                      <option value="perdido">Perdido</option>
                      <option value="encontrado">Encontrado</option>
                      <option value="en adopción">En adopción</option>
                      <option value="con dueño">Con dueño</option>
                  </select><br>
                </div>
              <div>
                <label for="lugar" class="formulario-label">Lugar:</label>
                <input type="text" id="lugar" name="lugar" required>
              </div>
              <div>
                <label for="color" class="formulario-label">Color:</label>
                <input type="text" id="color" name="color" required>
              </div>
              <div class="form-group">
                <label for="tamano" class="formulario-label">Tamaño:</label>
                <select id="tamano" name="tamano">
                    <option value="pequeño">Pequeño</option>
                    <option value="mediano">Mediano</option>
                    <option value="grande">Grande</option>
                </select>
              </div>
              <div>
                <label for="descripcion" class="formulario-label">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
              </div>
              <input type="hidden" name="idDueno" value="<?php echo $usuarioActivoId; ?>">                 
              <div>
                <input type="submit" value="Crear Perro">
              </div>
          </form>
        </div>
      </main>
  </div>  
  <div id="contenedorPieDePagina"></div>
  <script src="../../Cliente/js/api/cabecera.js"></script>
  <script>
      // Mostrar vista previa de la foto de perfil seleccionada
      document.getElementById('photo').addEventListener('change', function(event) {
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
            "../../Cliente/img/logo.png",
            "perfil.php"
          );
        document.getElementById("contenedorPieDePagina").innerHTML =
          cargarPieDePagina("../../Cliente/img/logo.jpg");
      });
  </script> 
</body>

</html>