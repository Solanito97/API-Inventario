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
        actualizarUsuario();
        break;

    case 'POST':            
        insertarUsuario();
        break;
                
    case 'DELETE':
        http_response_code(200);
        borrarUsuario();
        break;
                    
    case 'GET':
        if (!empty($_GET["idusuario"])) {
            $idusuario = intval($_GET["idusuario"]);
            obtenerUsuario($idusuario);
        } else {
            obtenerUsuarios();
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

function obtenerUsuarios() {
    global $db;

    $query = "SELECT `idusuario`, `nombre`, `email` FROM `usuarios`";
    $stm = $db->prepare($query);
    $stm->execute();

    $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultado);
}

function obtenerUsuario($idusuario) {
    global $db;

    $query = "SELECT `idusuario`, `nombre`, `email` FROM `usuarios` WHERE `idusuario` = ?";
    $stm = $db->prepare($query);            
    $stm->bindParam(1, $idusuario);
    $stm->execute();

    $resultado = $stm->fetch(PDO::FETCH_ASSOC);

    echo json_encode($resultado);
}

function insertarUsuario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "INSERT INTO `usuarios` (`nombre`, `email`, `password`) VALUES (:nombre, :email, :password)";
    $stm = $db->prepare($query);            
    $stm->bindParam(":nombre", $data->nombre);
    $stm->bindParam(":email", $data->email);
    $stm->bindParam(":password", $data->password);

    if ($stm->execute()) {
        echo json_encode(array("message" => "Usuario creado", "code" => "success"));
    } else {
        echo json_encode(array("message" => "Usuario no creado", "code" => "danger"));
    }
}

function actualizarUsuario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "UPDATE `usuarios` SET `nombre` = :nombre, `email` = :email, `password` = :password WHERE `idusuario` = :idusuario";
    $stm = $db->prepare($query);            
    $stm->bindParam(":idusuario", $data->idusuario);
    $stm->bindParam(":nombre", $data->nombre);
    $stm->bindParam(":email", $data->email);
    $stm->bindParam(":password", $data->password);

    if ($stm->execute()) {
        echo json_encode(array("message" => "Usuario actualizado", "code" => "success"));
    } else {
        echo json_encode(array("message" => "Usuario no actualizado", "code" => "danger"));
    }
}

function borrarUsuario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "DELETE FROM `usuarios` WHERE `idusuario` = :idusuario";
    $stm = $db->prepare($query);            
    $stm->bindParam(":idusuario", $data->idusuario);

    if ($stm->execute()) {
        echo json_encode(array("message" => "Usuario eliminado", "code" => "success"));
    } else {
        echo json_encode(array("message" => "Usuario no eliminado", "code" => "danger"));
    }
}
?>