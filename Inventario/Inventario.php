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

    $query = "SELECT `idInventarios`, `producto_id`, `cantidad`, `ubicacion`, `creado_por`, `modificado_por` FROM `Inventarios`";
    $stm = $db->prepare($query);
    $stm->execute();

    $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultado);
}

function obtenerInventario($idInventarios) {
    global $db;

    $query = "SELECT `idInventarios`, `producto_id`, `cantidad`, `ubicacion`, `creado_por`, `modificado_por` FROM `Inventarios` WHERE `idInventarios` = ?";
    $stm = $db->prepare($query);
    $stm->bindParam(1, $idInventarios);
    $stm->execute();

    $resultado = $stm->fetch(PDO::FETCH_ASSOC);

    echo json_encode($resultado);
}

function insertarInventario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "INSERT INTO `Inventarios` (`producto_id`, `cantidad`, `ubicacion`, `creado_por`, `modificado_por`) VALUES (:producto_id, :cantidad, :ubicacion, :creado_por, :modificado_por)";
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
}

function actualizarInventario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "UPDATE `Inventarios` SET `producto_id` = :producto_id, `cantidad` = :cantidad, `ubicacion` = :ubicacion, `modificado_por` = :modificado_por WHERE `idInventarios` = :idInventarios";
    $stm = $db->prepare($query);
    $stm->bindParam(":idInventarios", $data->idInventarios);
    $stm->bindParam(":producto_id", $data->producto_id);
    $stm->bindParam(":cantidad", $data->cantidad);
    $stm->bindParam(":ubicacion", $data->ubicacion);
    $stm->bindParam(":modificado_por", $data->modificado_por);

    if ($stm->execute()) {
        echo json_encode(array("message" => "Inventario actualizado", "code" => "success"));
    } else {
        echo json_encode(array("message" => "Inventario no actualizado", "code" => "danger"));
    }
}

function borrarInventario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "DELETE FROM `Inventarios` WHERE `idInventarios` = :idInventarios";
    $stm = $db->prepare($query);
    $stm->bindParam(":idInventarios", $data->idInventarios);

    if ($stm->execute()) {
        echo json_encode(array("message" => "Inventario eliminado", "code" => "success"));
    } else {
        echo json_encode(array("message" => "Inventario no eliminado", "code" => "danger"));
    }
}

?>
