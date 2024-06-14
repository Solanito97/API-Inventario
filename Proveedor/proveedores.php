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

    $query = "SELECT `idProveedores`, `nombre`, `contacto`, `direccion`, `creado_por`, `modificado_por` FROM `Proveedores`";
    $stm = $db->prepare($query);
    $stm->execute();

    $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultado);
}

function obtenerProveedor($idProveedores) {
    global $db;

    $query = "SELECT `idProveedores`, `nombre`, `contacto`, `direccion`, `creado_por`, `modificado_por` FROM `Proveedores` WHERE `idProveedores` = ?";
    $stm = $db->prepare($query);
    $stm->bindParam(1, $idProveedores);
    $stm->execute();

    $resultado = $stm->fetch(PDO::FETCH_ASSOC);

    echo json_encode($resultado);
}

function insertarProveedor() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "INSERT INTO `Proveedores` (`nombre`, `contacto`, `direccion`, `creado_por`, `modificado_por`) VALUES (:nombre, :contacto, :direccion, :creado_por, :modificado_por)";
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
}

function actualizarProveedor() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "UPDATE `Proveedores` SET `nombre`= :nombre, `contacto`= :contacto, `direccion`= :direccion, `modificado_por`= :modificado_por WHERE `idProveedores`= :idProveedores";
    $stm = $db->prepare($query);
    $stm->bindParam(":idProveedores", $data->idProveedores);
    $stm->bindParam(":nombre", $data->nombre);
    $stm->bindParam(":contacto", $data->contacto);
    $stm->bindParam(":direccion", $data->direccion);
    $stm->bindParam(":modificado_por", $data->modificado_por);

    if ($stm->execute()) {
        echo json_encode(array("message" => "Proveedor actualizado", "code" => "success"));
    } else {
        echo json_encode(array("message" => "Proveedor no actualizado", "code" => "danger"));
    }
}

function borrarProveedor() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "DELETE FROM `Proveedores` WHERE `idProveedores`= :idProveedores";
    $stm = $db->prepare($query);
    $stm->bindParam(":idProveedores", $data->idProveedores);

    if ($stm->execute()) {
        echo json_encode(array("message" => "Proveedor eliminado", "code" => "success"));
    } else {
        echo json_encode(array("message" => "Proveedor no eliminado", "code" => "danger"));
    }
}

?>