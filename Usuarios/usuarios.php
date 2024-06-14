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
        if (!empty($_GET["idUsuarios"])) {
            $idUsuarios = intval($_GET["idUsuarios"]);
            obtenerUsuario($idUsuarios);
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

    try {
        $query = "SELECT `idUsuarios`, `nombre`, `email` FROM `Usuarios_Byron`";
        $stm = $db->prepare($query);
        $stm->execute();

        $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultado);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al obtener usuarios", "error" => $e->getMessage()));
    }
}

function obtenerUsuario($idUsuarios) {
    global $db;

    try {
        $query = "SELECT `idUsuarios`, `nombre`, `email` FROM `Usuarios_Byron` WHERE `idUsuarios` = ?";
        $stm = $db->prepare($query);
        $stm->bindParam(1, $idUsuarios);
        $stm->execute();

        $resultado = $stm->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            echo json_encode($resultado);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Usuario no encontrado"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al obtener el usuario", "error" => $e->getMessage()));
    }
}

function insertarUsuario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->nombre) || empty($data->email) || empty($data->password)) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos"));
        return;
    }

    try {
        $query = "INSERT INTO `Usuarios_Byron` (`idUsuarios`, `nombre`, `email`, `password`) VALUES (:idUsuarios, :nombre, :email, :password)";
        $stm = $db->prepare($query);
        $stm->bindParam(":nombre", $data->nombre);
        $stm->bindParam(":email", $data->email);
        $stm->bindParam(":password", $data->password);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Usuario creado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Usuario no creado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear el usuario", "error" => $e->getMessage()));
    }
}

function actualizarUsuario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->idUsuarios) || empty($data->nombre) || empty($data->email) || empty($data->password)) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos"));
        return;
    }

    try {
        $query = "UPDATE `Usuarios_Byron` SET `nombre` = :nombre, `email` = :email, `password` = :password WHERE `idUsuarios` = :idUsuarios";
        $stm = $db->prepare($query);
        $stm->bindParam(":idUsuarios", $data->idUsuarios);
        $stm->bindParam(":nombre", $data->nombre);
        $stm->bindParam(":email", $data->email);
        $stm->bindParam(":password", $data->password);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Usuario actualizado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Usuario no actualizado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar el usuario", "error" => $e->getMessage()));
    }
}

function borrarUsuario() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->idUsuarios)) {
        http_response_code(400);
        echo json_encode(array("message" => "ID de usuario no proporcionado"));
        return;
    }

    try {
        $query = "DELETE FROM `Usuarios_Byron` WHERE `idUsuarios` = :idUsuarios";
        $stm = $db->prepare($query);
        $stm->bindParam(":idUsuarios", $data->idUsuarios);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Usuario eliminado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Usuario no eliminado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al eliminar el usuario", "error" => $e->getMessage()));
    }
}
?>
