<?php
session_start();
define('INCLUDED', true);
require_once '../../Src/Config/helpers.php';
require_once '../../Src/Config/BaseDeDatos.php';


// Utilizar la función de verificación de sesión y administrador
$userInfo = verifyAdminAndSession();
$usuarioActivoId = $userInfo['usuarioActivoId'];
$nombreUsuarioActivo = $userInfo['nombreUsuarioActivo'];

// Crear una instancia de la clase BaseDeDatos
$db = new BaseDeDatos();

try {
    // Obtener la conexión a la base de datos
    $conn = $db->getConnection();

        

    // Obtener la lista de usuarios desde la base de datos
    $sql = "SELECT id, nombre FROM usuarios";
    $result = $conn->query($sql);
    $usuarios = [];
    if ($result->rowCount() > 0) {
        $usuarios = $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar si se ha enviado el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los datos del formulario
        $nombre = $_POST['nombre'];
        $raza = $_POST['raza'];
        $edad = $_POST['edad'];
        $collar = $_POST['collar'];
        $chip = $_POST['chip'];
        $lugarPerdido = $_POST['lugarPerdido'];
        $fechaPerdido = $_POST['fechaPerdido'];
        $lugarEncontrado = $_POST['lugarEncontrado'];
        $fechaEncontrado = $_POST['fechaEncontrado'];
        $estado = $_POST['estado'];
        $lugar = $_POST['lugar'];
        $fechaUltimaActualizacion = date('Y-m-d H:i:s');
        $color = $_POST['color'];
        $tamano = $_POST['tamano'];
        $descripcion = $_POST['descripcion'];
        $idDueno = $_POST['idDueno'];
        $foto = isset($_FILES['foto']) ? $_FILES['foto'] : '';

        // Realizar validaciones de los datos
        $errores = [];

        if (empty($nombre) || empty($estado)) {
            $errores[] = 'El nombre y el estado son campos requeridos.';
        }

        if (empty($nombre) || strlen($nombre) > 50) {
            $errores[] = 'Nombre inválido. Debe tener menos de 50 caracteres.';
        }

        if (empty($raza) || strlen($raza) > 50) {
            $errores[] = 'Raza inválida. Debe tener menos de 50 caracteres.';
        }

        if ($edad < 0 || $edad > 30) {
            $errores[] = 'Edad inválida. Debe estar entre 0 y 30.';
        }

        if ($collar !== 'si' && $collar !== 'no') {
            $errores[] = 'El campo collar debe ser "si" o "no".';
        }

        if ($chip !== 'si' && $chip !== 'no') {
            $errores[] = 'El campo chip debe ser "si" o "no".';
        }

        if ($estado === 'perdido' && empty($lugarPerdido)) {
            $errores[] = 'El lugar perdido no puede estar vacío cuando el estado es "perdido".';
        }

        if ($estado === 'perdido' && !empty($lugarPerdido) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaPerdido)) {
            $errores[] = 'Fecha perdido inválida.';
        }

        if (empty($lugarPerdido)){
            $lugarPerdido= 'N/A';
            $fechaPerdido = NULL;
        }

        if ($estado === 'encontrado' && empty($lugarEncontrado)) {
            $errores[] = 'El lugar encontrado no puede estar vacío cuando el estado es "encontrado".';
        }

        if ($estado === 'encontrado' && !empty($lugarEncontrado) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEncontrado)) {
            $errores[] = 'Fecha encontrado inválida.';
        }

        if (empty($lugarEncontrado)){
            $lugarEncontrado= 'N/A';
            $fechaEncontrado = NULL;
        }

        // Validar el tamaño del perro
        if (!in_array($tamano, array('pequeño', 'mediano', 'grande'))) {
            $errores[] = 'Tamaño inválido. Debe ser "pequeño", "mediano" o "grande".';
        }

        // Verificar si hay errores
        if (!empty($errores)) {
            // Manejar los errores
            foreach ($errores as $error) {
                echo $error . '<br>';
            }
        } else {  
                    
                
        // Insertar el perro en la base de datos
        
            $stmt = $conn->prepare("INSERT INTO Perros (nombre, raza, edad, collar, chip, lugarPerdido, fechaPerdido, lugarEncontrado, fechaEncontrado, estado, lugar, fechaUltimaActualizacion, color, tamano, descripcion, idDueno) 
                                    VALUES (:nombre, :raza, :edad, :collar, :chip, :lugarPerdido, :fechaPerdido, :lugarEncontrado, :fechaEncontrado, :estado, :lugar, :fechaUltimaActualizacion, :color, :tamano, :descripcion, :idDueno)");

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':raza', $raza);
            $stmt->bindParam(':edad', $edad);
            $stmt->bindParam(':collar', $collar);
            $stmt->bindParam(':chip', $chip);
            $stmt->bindParam(':lugarPerdido', $lugarPerdido);
            $stmt->bindParam(':fechaPerdido', $fechaPerdido);
            $stmt->bindParam(':lugarEncontrado', $lugarEncontrado);
            $stmt->bindParam(':fechaEncontrado', $fechaEncontrado);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':lugar', $lugar);
            $stmt->bindParam(':fechaUltimaActualizacion', $fechaUltimaActualizacion);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':tamano', $tamano);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':idDueno', $idDueno);
            $stmt->execute();

            // Obtener el ID del perro recién creado
            $perroId = $conn->lastInsertId();

            if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
                
                // Obtener el nombre de archivo único para la foto
                $photoName = $nombre . $idDueno . '.jpg';

                // Definir la ruta de destino para guardar la foto
                $rutaDestino = '../../Publico/fotos_perros/' . $photoName;

                // Mover el archivo de la foto a la ruta de destino
                $result = move_uploaded_file($foto['tmp_name'], $rutaDestino);
                if ($result) {
                    echo "Archivo subido con éxito.";
                } else {
                    echo "Error al subir el archivo.";
                }
                // Actualizar la ruta de la foto del perro
                $query = "UPDATE Perros SET foto = :foto WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':foto', $photoName, PDO::PARAM_STR);
                $stmt->bindValue(':id', $perroId, PDO::PARAM_INT);
                $stmt->execute(); 
            } else {
                $photoName = 'dog.jpg';
                $sql = "UPDATE Perros SET foto = :foto WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':foto', $photoName, PDO::PARAM_STR);
                $stmt->bindValue(':id', $perroId, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Redirigir a la página de administración o mostrar un mensaje de éxito
            header('Location: ../admin.html');
            exit();
        }
    }
}catch (PDOException $e) {
    handlePdoError($e);
}
   
// Cerrar la conexión a la base de datos
$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Perro</title>
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
            text-align: center;
            margin-bottom: 30px;
        }

        .formulario-label {
            display: block;
            font-weight: bold;
        }

        .formulario-input {
            width: 100%;            
            margin-bottom: 30px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .formulario-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .formulario-input,
        .formulario-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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
            .formulario-container {
                max-width: 90%;
                margin: 10px auto;
                padding: 15px;
            }

        }

        /* Media Query para móviles (pantallas hasta 480px) */
        @media screen and (max-width: 480px) {
            .formulario-container {
                max-width: 100%;
                margin: 5px auto;
                padding: 10px;
            }

            .formulario-label, .formulario-select, .formulario-submit {
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
                <li><a href="../../../Cliente/cerrarSesion.html">Cerrar Sesión</a></li>
            </ul>
        </div>        
    </header>
    <div id="contenedorCabecera"></div>
    <div class="contenido">
            <h1 id="titulo" >Panel de Administración</h1>
            <h2 class="titulo-2">Crear perro</h2>
            <div class="formulario-container">
            <h1 class="formulario-title">Crear Perro</h1>
            <form action="crearPerro.php" method="POST" enctype=multipart/form-data>
                <div class="form-group">
                <label for="nombre" class="formulario-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required><br>
                </div>
                <div class="form-group">
                <label for="raza" class="formulario-label">Raza:</label>
                <input type="text" id="raza" name="raza"><br>
                </div>
                <div class="form-group">
                <label for="edad" class="formulario-label">Edad:</label>
                <input type="number" id="edad" name="edad" required><br>
                </div>
                <div class="form-group">
                <label for="collar" class="formulario-label">Collar:</label>
                <select id="collar" name="collar">
                    <option value="si">Sí</option>
                    <option value="no">No</option>
                </select><br>
                </div>
                <div class="form-group">
                <label for="chip" class="formulario-label">Chip:</label>
                <select id="chip" name="chip">
                    <option value="si">Sí</option>
                    <option value="no">No</option>
                </select><br>
                </div>  
                <div class="form-group">
                <label for="lugarPerdido" class="formulario-label">Lugar Perdido:</label>
                <input type="text" id="lugarPerdido" name="lugarPerdido"><br>
                </div>
                <div class="form-group">
                <label for="fechaPerdido" class="formulario-label">Fecha Perdido:</label>
                <input type="date" id="fechaPerdido" name="fechaPerdido"><br>
                </div>
                <div class="form-group">
                <label for="lugarEncontrado" class="formulario-label">Lugar Encontrado:</label>
                <input type="text" id="lugarEncontrado" name="lugarEncontrado"><br>
                </div>
                <div class="form-group">
                <label for="fechaEncontrado" class="formulario-label">Fecha Encontrado:</label>
                <input type="date" id="fechaEncontrado" name="fechaEncontrado"><br>
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
                <div class="form-group">
                <label for="lugar" class="formulario-label">Lugar:</label>
                <input type="text" id="lugar" name="lugar"><br>
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
                <input type="text" id="color" name="color"><br>
                </div>
                <div class="form-group">
                <label for="tamano" class="formulario-label">Tamaño:</label>
                <select id="tamano" name="tamano">
                    <option value="pequeño">Pequeño</option>
                    <option value="mediano">Mediano</option>
                    <option value="grande">Grande</option>
                </select>
                </div>
                <div class="form-group">
                <label for="descripcion" class="formulario-label">Descripción:</label>
                <textarea id="descripcion" name="descripcion"></textarea><br>
                </div>
                <div class="form-group">
                <label for="idDueno" class="formulario-label">Dueño:</label>
                <select id="idDueno" name="idDueno">
                    <option value="" selected disabled>Seleccione un usuario</option>
                    <?php foreach ($usuarios as $usuario) : ?>
                        <option value="<?php echo $usuario['id']; ?>"><?php echo $usuario['nombre']; ?></option>
                    <?php endforeach; ?>
                </select><br>
                </div>
                <input type="submit" value="Crear Perro" class="formulario-submit">
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

