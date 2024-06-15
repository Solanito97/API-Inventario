<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: PUT, POST, DELETE, GET, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin, Authorization, X-Requested-With');

include_once '../config/database.php';

$database = new DatabasesConexion();
$db = $database->obtenerConn();

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'PUT':
        http_response_code(200);
        actualizarProveedor();
        break;

    case 'POST':
        insertarProveedor();
        break;

    case 'DELETE':
        http_response_code(200);
        borrarProveedor();
        break;

    case 'GET':
        if (!empty($_GET["idProveedores"])) {
            $idProveedores = intval($_GET["idProveedores"]);
            obtenerProveedor($idProveedores);
        } else {
            obtenerProveedores();
        }
        break;

    case 'OPTIONS':
        http_response_code(200);
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function obtenerProveedores() {
    global $db;

    try {
        $query = "SELECT `idProveedores`, `nombre`, `contacto`, `direccion`, `creado_por`, `modificado_por` FROM `Proveedores_Byron`";
        $stm = $db->prepare($query);
        $stm->execute();

        $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultado);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al obtener proveedores", "error" => $e->getMessage()));
    }
}

function obtenerProveedor($idProveedores) {
    global $db;

    try {
        $query = "SELECT `idProveedores`, `nombre`, `contacto`, `direccion`, `creado_por`, `modificado_por` FROM `Proveedores_Byron` WHERE `idProveedores` = ?";
        $stm = $db->prepare($query);
        $stm->bindParam(1, $idProveedores);
        $stm->execute();

        $resultado = $stm->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            echo json_encode($resultado);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Proveedor no encontrado"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al obtener el proveedor", "error" => $e->getMessage()));
    }
}

function insertarProveedor() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->nombre)) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos"));
        return;
    }

    try {
        $query = "INSERT INTO `Proveedores_Byron` (`nombre`, `contacto`, `direccion`, `creado_por`, `modificado_por`) VALUES (:nombre, :contacto, :direccion, :creado_por, :modificado_por)";
        $stm = $db->prepare($query);
        $stm->bindParam(":nombre", $data->nombre);
        $stm->bindParam(":contacto", $data->contacto);
        $stm->bindParam(":direccion", $data->direccion);
        $stm->bindParam(":creado_por", $data->creado_por);
        $stm->bindParam(":modificado_por", $data->modificado_por);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Proveedor creado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Proveedor no creado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear el proveedor", "error" => $e->getMessage()));
    }
}

function actualizarProveedor() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->idProveedores) || empty($data->nombre)) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos"));
        return;
    }

    try {
        $query = "UPDATE `Proveedores_Byron` SET `nombre` = :nombre, `contacto` = :contacto, `direccion` = :direccion, `creado_por` = :creado_por, `modificado_por` = :modificado_por WHERE `idProveedores` = :idProveedores";
        $stm = $db->prepare($query);
        $stm->bindParam(":idProveedores", $data->idProveedores);
        $stm->bindParam(":nombre", $data->nombre);
        $stm->bindParam(":contacto", $data->contacto);
        $stm->bindParam(":direccion", $data->direccion);
        $stm->bindParam(":creado_por", $data->creado_por);
        $stm->bindParam(":modificado_por", $data->modificado_por);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Proveedor actualizado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Proveedor no actualizado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar el proveedor", "error" => $e->getMessage()));
    }
}

function borrarProveedor() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->idProveedores)) {
        http_response_code(400);
        echo json_encode(array("message" => "ID de proveedor no proporcionado"));
        return;
    }

    try {
        $query = "DELETE FROM `Proveedores_Byron` WHERE `idProveedores` = :idProveedores";
        $stm = $db->prepare($query);
        $stm->bindParam(":idProveedores", $data->idProveedores);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Proveedor eliminado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Proveedor no eliminado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al eliminar el proveedor", "error" => $e->getMessage()));
    }
}
?>
