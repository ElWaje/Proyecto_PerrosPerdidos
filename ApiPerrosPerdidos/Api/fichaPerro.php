<?php
session_start();

define('INCLUDED', true);
require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";

$perroId = isset($_GET['perroId']) ? $_GET['perroId'] : '';

try {
  $db = new BaseDeDatos();
  $conn = $db->getConnection();
  $stmt = $conn->prepare("SELECT * FROM Perros WHERE id = :perroId");
  $stmt->bindValue(':perroId', $perroId);
  $stmt->execute();
  $perroElegido = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  handlePdoError($e);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ficha Perro</title>
  <link rel="stylesheet" type="text/css" href="../../Cliente/css/estilos.css" />
  <link rel="stylesheet" type="text/css" href="../../Cliente/css/headerFooter.css" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <style>
    .ficha-container {
      display: flex;
      margin: auto;
      margin-top: 50px;
      margin-bottom: 150px;
      border: 5px solid #000;
      border-radius: 10px;
      width: 70%;
      padding: 20px;
    }

    .btn-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    }

    .volver-btn {
        padding: 10px 20px;
        font-size: 18px;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        margin-top: 20px;
        background-color: coral;
        color: white;
        transition: background-color 0.3s;        
    }

    .volver-btn:hover {
          background-color: #ff7f50;
    }

    .ficha-foto {
      flex: 1;
    }

    .ficha-foto img {
      width: 100%;
      height: auto;
      max-height: 300px;
      object-fit: cover;
      object-position: center;
    }

    .ficha-datos {
      flex: 2;
      padding-left: 20px;
    }

    .ficha-nombre {
      font-size: 24px;
      font-weight: bold;
      color:coral;
      margin-bottom: 10px;
    }

    .ficha-linea {
      margin-bottom: 10px;
    }

    /* Estilos para mejorar la accesibilidad */
    [aria-hidden="true"] {
            display: none;
    }

    :focus {
            outline: 3px solid blue;
    }
    /* Media query para pantallas de hasta 768px */
    @media screen and (max-width: 768px) {
        .ficha-container {
            flex-direction: column;
            width: 90%;
        }

        .ficha-foto, .ficha-datos {
            flex: 1;
        }

        .ficha-foto img {
            max-height: 250px;
        }

        .ficha-nombre {
            font-size: 22px;
        }

        .volver-btn {
            font-size: 16px;
        }

    }

    /* Media query para pantallas de hasta 480px */
    @media screen and (max-width: 480px) {
        .ficha-container {
            flex-direction: column;
            width: 100%;
        }

        .ficha-foto img {
            max-height: 200px;
        }

        .ficha-nombre {
            font-size: 20px;
        }

        .volver-btn {
            font-size: 14px;
        }

        .btn-container {
            flex-direction: column;
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
          <?php
          $perroId = isset($_GET['perroId']) ? $_GET['perroId'] : '';
          try {
            // Crear una instancia de la clase BaseDeDatos
            $db = new BaseDeDatos();

            // Obtener la conexión a la base de datos
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM Perros WHERE id = :perroId");
            $stmt->bindValue(':perroId', $perroId);
            $stmt->execute();
            $perroElegido = $stmt->fetch(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            echo "<script>alert('Error en la conexión a la base de datos: " . $e->getMessage() . "');</script>";
          }
          ?>
          <h1 id="titulo">Ficha de Perro</h1>
          <h1 class="titulo-2">Ficha de <?php echo $perroElegido['nombre']; ?></h1>
          <div class="ficha-container">
            <div class="ficha-foto">
              <img src="../Publico/fotos_perros/<?php echo $perroElegido['foto']; ?>" alt="Foto del Perro">
            </div>
            <div class="ficha-datos">
              <div class="ficha-nombre"><?php echo $perroElegido['nombre']; ?></div>
              <div class="ficha-linea">
                <strong>Raza:</strong> <?php echo $perroElegido['raza']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Edad:</strong> <?php echo $perroElegido['edad']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Collar:</strong> <?php echo $perroElegido['collar']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Chip:</strong> <?php echo $perroElegido['chip']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Lugar Perdido:</strong> <?php echo $perroElegido['lugarPerdido']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Fecha Perdido:</strong> <?php echo $perroElegido['fechaPerdido']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Lugar Encontrado:</strong> <?php echo $perroElegido['lugarEncontrado']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Fecha Encontrado:</strong> <?php echo $perroElegido['fechaEncontrado']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Estado:</strong> <?php echo $perroElegido['estado']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Lugar:</strong> <?php echo $perroElegido['lugar']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Fecha Última Actualización:</strong> <?php echo $perroElegido['fechaUltimaActualizacion']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Color:</strong> <?php echo $perroElegido['color']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Tamaño:</strong> <?php echo $perroElegido['tamano']; ?>
              </div>
              <div class="ficha-linea">
                <strong>Descripción:</strong> <?php echo $perroElegido['descripcion']; ?>
              </div>
              <div class="ficha-linea">
                <strong>ID Dueño:</strong> <?php echo $perroElegido['idDueno']; ?>
              </div>
              <div class="ficha-linea">
                <strong>INombre Dueño:</strong> <?php echo $nombreUsuarioActivo; ?>
              </div>
              <div class="btn-container">
                  <button onclick="window.location.href='perros.php'" class="volver-btn">Volver a Perros</button>
              </div>
            </div>
          </div> 
    </main>
  </div>  
  <div id="contenedorPieDePagina"></div>
  <script src="../../Cliente/js/api/cabecera.js"></script>
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