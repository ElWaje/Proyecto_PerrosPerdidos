<?php
session_start();


define('INCLUDED', true);


require_once '../Src/Config/BaseDeDatos.php';
require_once '../Src/Config/helpers.php';

verifySession();  
$usuarioActivoId = $_SESSION['id'];
// Obtener el nombre del usuario activo
$nombreUsuarioActivo = $_SESSION['nombre'] ?? "";
  
if (!isset($_POST['accionPerro'])) {
    redirectToIndexWithError("Acción no definida.");
}

$accion = $_POST['accionPerro'];
$perroId = $_POST['perroId'] ?? '';

switch ($accion) {
    case 'crear':
        header("Location: crearPerrosFormulario.php");
        exit();
        break;

    case 'editar':
        if (!empty($perroId)) {
            header("Location: editarPerrosFormulario.php?perroId=$perroId");
            exit();
        } else {
            handleErrors("Por favor, seleccione un perro para editar.");
        }
        break;

    case 'borrar':
        if (!empty($perroId)) {
            header("Location: borrarPerrosFormulario.php?perroId=$perroId");
            exit();
        } else {
            handleErrors("Por favor, seleccione un perro para borrar.");
        }
        break;

    default:
        handleErrors("Acción no reconocida.");
        break;
}
?>