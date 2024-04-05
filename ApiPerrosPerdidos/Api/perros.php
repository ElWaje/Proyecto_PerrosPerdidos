<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";

// Crear una instancia de la clase BaseDeDatos
$db = new BaseDeDatos();

// Obtener la conexión a la base de datos
try {
  $conn = $db->getConnection();
} catch (PDOException $e) {
  handlePdoError($e);
}

function obtenerPerrosPorUsuario($conn, $idUsuario)
{
  try {
    $stmt = $conn->prepare("SELECT id, nombre, raza, edad FROM Perros WHERE idDueno = :idUsuario");
    $stmt->bindValue(':idUsuario', $idUsuario);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    handlePdoError($e); 
  }
}

$perros = obtenerPerrosPorUsuario($conn, $usuarioActivoId);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Perros</title>
  <link rel="stylesheet" type="text/css" href="../../Cliente/css/estilos.css" />
  <link rel="stylesheet" type="text/css" href="../../Cliente/css/headerFooter.css" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <style>
    .tabla-container {
      margin-top: 40px;
      width: 80%; 
      margin-left: auto;
      margin-right: auto;
      margin-bottom: 50px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      overflow: hidden;
      background: #fff;
      transform-style: preserve-3d;
    }

    th,
    td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #dee2e6;
    }

    th {
      background: #f9fafb;
      font-weight: 600;
    }

    td {
      font-weight: 500;
    }

    tr:hover {
      transform: scale(1.02);
      transition: transform 0.2s ease-in-out;
    }

    .btn-ver-ficha {
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      background-color: #4caf50;
      color: #fff;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
      transform-style: preserve-3d;
      perspective: 1000px;
      transform: translateZ(0);
    }

    .btn-ver-ficha:hover {
      background-color: #45a049;
      transform: translateZ(30px);
    }

    .formulario-container {
      width: 15%; 
      margin-left: auto;
      margin-right: auto;
      margin-bottom: 150px;
      transform-style: preserve-3d;
      perspective: 1000px;
      transform: translateZ(0);
    }

    form {
      transform-style: preserve-3d;
      perspective: 1000px;
      transform: translateZ(0);
    }

    .formulario-container h2 {
      transform: translateZ(30px);
    }

    .formulario-container div {
      transform: translateZ(30px);
      margin-bottom: 10px;
    }

    .formulario-container button {
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      margin-top: 15px;
      background-color: #4caf50;
      color: #fff;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
      transform: translateZ(0);
    }

    .formulario-container button:hover {
      background-color: #45a049;
      transform: translateZ(30px);
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
        .tabla-container {
            width: 95%;
            margin-left: auto;
            margin-right: auto;
        }

        .formulario-container {
            width: 90%;
        }

        table {
            box-shadow: none;
            border-radius: 0;
        }

        th, td {
            padding: 10px 5px;
        }

        .btn-ver-ficha {
            padding: 5px 10px;
            font-size: 14px;
        }

        .formulario-container button {
            padding: 5px 10px;
            font-size: 14px;
        }
    }

    /* Media Queries para dispositivos móviles */
    @media screen and (max-width: 480px) {
        .tabla-container {
            width: 100%;
        }

        .formulario-container {
            width: 100%;
        }

        th, td {
            padding: 8px;
            font-size: 12px;
        }

        .btn-ver-ficha {
            font-size: 12px;
        }

        .formulario-container button {
            font-size: 12px;
        }
    }

  </style>
</head>

<body>
<header class="header">    
    <div class="nav-container">
      <ul class="nav-links">
        <li><a href="../../Cliente/perrosPerdidos.html">Perdidos</a></li>
        <li><a href="../../Cliente/verPerros.html">Ver Perros</a></li>
        <li><a href="../../Cliente/perrosEncontrados.html">Encontrados</a></li>
        <li><a href="../../Cliente/perrosAdopcion.html">En Adopción</a></li>
        <li><a href="perfil.php">Perfil</a></li>
        <li><a href="../../Cliente/usuarios.html">Usuarios</a></li>
        <li><a href="publicaciones.php">Publicaciones</a></li>
        <li><a href="../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
      </ul>
    </div>
  </header> 
  <div id="contenedorCabecera"></div>
  <div class="contenido">
    <main class="main">
    <h1 id="titulo">Gestión de PErros</h1>
      <div class="posts-container">
      <h1 class="titulo-2">Perros de <?php echo $nombreUsuarioActivo; ?></h1>
      <div class="tabla-container">
        <table>
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Raza</th>
              <th>Edad</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($perros as $perro) : ?>
              <tr>
                <td><?php echo $perro['nombre']; ?></td>
                <td><?php echo $perro['raza']; ?></td>
                <td><?php echo $perro['edad']; ?></td>
                <td>
                  <a class="btn-ver-ficha" href="fichaPerro.php?perroId=<?php echo $perro['id']; ?>">Ver Ficha</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="formulario-container">
        <h2>Opciones de Perros</h2>
        <form id="formulario-perros" method="post" action="perro.php">
          <div>
            <label for="perro-selector">Perros:</label>
            <select id="perro-selector" name="perroId">
              <option value="">Seleccionar perro</option>
              <?php foreach ($perros as $perro) : ?>
                <option value="<?php echo $perro['id']; ?>"><?php echo $perro['nombre']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <input type="radio" id="crear-perro" name="accionPerro" value="crear" checked>
            <label for="crear-perro">Crear Nuevo Perro</label>
          </div>
          <div>
            <input type="radio" id="editar-perro" name="accionPerro" value="editar">
            <label for="editar-perro">Editar Perro Existente</label>
          </div>
          <div>
            <input type="radio" id="borrar-perro" name="accionPerro" value="borrar">
            <label for="borrar-perro">Borrar Perro Existente</label>
          </div>
          <div>
            <button type="submit">Ejecutar Acción</button>
          </div>
        </form>
      </div>
    </main>
  </div>
  <div id="contenedorPieDePagina"></div>
  <script src="../../Cliente/js/api/cabecera.js"></script> 
  <script>
    var perroSelector = document.getElementById('perro-selector');
    var editarPerroRadio = document.getElementById('editar-perro');
    var borrarPerroRadio = document.getElementById('borrar-perro');
    var crearPerroRadio = document.getElementById('crear-perro');
    
    // Establecer valor inicial y desactivar opciones de edición y eliminación
    perroSelector.value = '';
    editarPerroRadio.disabled = true;
    borrarPerroRadio.disabled = true;
    crearPerroRadio.disabled = false;  

    perroSelector.addEventListener('change', function() {
      if (this.value === '') {
        editarPerroRadio.disabled = true;
        borrarPerroRadio.disabled = true;        
        crearPerroRadio.disabled = false;
      } else {
        editarPerroRadio.disabled = false;
        borrarPerroRadio.disabled = false;
        crearPerroRadio.disabled = true;
      }
    });

    editarPerroRadio.addEventListener('change', function() {
      if (this.checked) {
        borrarPerroRadio.disabled = true;
        crearPerroRadio.disabled = true;
      } else {
        borrarPerroRadio.disabled = false;
      }
    });

    borrarPerroRadio.addEventListener('change', function() {
      if (this.checked) {
        editarPerroRadio.disabled = true;
        crearPerroRadio.disabled = true;
      } else {
        editarPerroRadio.disabled = false;
      }
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