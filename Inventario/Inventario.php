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
        actualizarInventario();
        break;

    case 'POST':
        insertarInventario();
        break;

    case 'DELETE':
        http_response_code(200);
        borrarInventario();
        break;

    case 'GET':
        if (!empty($_GET["idInventarios"])) {
            $idInventarios = intval($_GET["idInventarios"]);
            obtenerInventario($idInventarios);
        } else {
            obtenerInventarios();
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

function obtenerInventarios() {
    global $db;

    try {
        $query = "SELECT `idInventarios`, `producto_id`, `cantidad`, `ubicacion`, `creado_por`, `modificado_por` FROM `Inventarios_Byron`";
        $stm = $db->prepare($query);
        $stm->execute();

        $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultado);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al obtener inventarios", "error" => $e->getMessage()));
    }
}

function obtenerInventario($idInventarios) {
    global $db;

    try {
        $query = "SELECT `idInventarios`, `producto_id`, `cantidad`, `ubicacion`, `creado_por`, `modificado_por` FROM `Inventarios_Byron` WHERE `idInventarios` = ?";
        $stm = $db->prepare($query);
        $stm->bindParam(1, $idInventarios);
        $stm->execute();

        $resultado = $stm->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            echo json_encode($resultado);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Inventario no encontrado"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al obtener el inventario", "error" => $e->getMessage()));
    }
}

function insertarInventario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->producto_id) || empty($data->cantidad)) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos"));
        return;
    }

    try {
        $query = "INSERT INTO `Inventarios_Byron` (`producto_id`, `cantidad`, `ubicacion`, `creado_por`, `modificado_por`) VALUES (:producto_id, :cantidad, :ubicacion, :creado_por, :modificado_por)";
        $stm = $db->prepare($query);
        $stm->bindParam(":producto_id", $data->producto_id);
        $stm->bindParam(":cantidad", $data->cantidad);
        $stm->bindParam(":ubicacion", $data->ubicacion);
        $stm->bindParam(":creado_por", $data->creado_por);
        $stm->bindParam(":modificado_por", $data->modificado_por);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Inventario creado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Inventario no creado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear el inventario", "error" => $e->getMessage()));
    }
}

function actualizarInventario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->idInventarios) || empty($data->producto_id) || empty($data->cantidad)) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos"));
        return;
    }

    try {
        $query = "UPDATE `Inventarios_Byron` SET `producto_id` = :producto_id, `cantidad` = :cantidad, `ubicacion` = :ubicacion, `creado_por` = :creado_por, `modificado_por` = :modificado_por WHERE `idInventarios` = :idInventarios";
        $stm = $db->prepare($query);
        $stm->bindParam(":idInventarios", $data->idInventarios);
        $stm->bindParam(":producto_id", $data->producto_id);
        $stm->bindParam(":cantidad", $data->cantidad);
        $stm->bindParam(":ubicacion", $data->ubicacion);
        $stm->bindParam(":creado_por", $data->creado_por);
        $stm->bindParam(":modificado_por", $data->modificado_por);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Inventario actualizado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Inventario no actualizado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar el inventario", "error" => $e->getMessage()));
    }
}

function borrarInventario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->idInventarios)) {
        http_response_code(400);
        echo json_encode(array("message" => "ID de inventario no proporcionado"));
        return;
    }

    try {
        $query = "DELETE FROM `Inventarios_Byron` WHERE `idInventarios` = :idInventarios";
        $stm = $db->prepare($query);
        $stm->bindParam(":idInventarios", $data->idInventarios);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Inventario eliminado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Inventario no eliminado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al eliminar el inventario", "error" => $e->getMessage()));
    }
}
?>

