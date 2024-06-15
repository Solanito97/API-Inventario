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
        actualizarProducto();
        break;

    case 'POST':
        insertarProducto();
        break;

    case 'DELETE':
        http_response_code(200);
        borrarProducto();
        break;

    case 'GET':
        if (!empty($_GET["idProductos"])) {
            $idProductos = intval($_GET["idProductos"]);
            obtenerProducto($idProductos);
        } else {
            obtenerProductos();
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

function obtenerProductos() {
    global $db;

    try {
        $query = "SELECT `idProductos`, `nombre`, `descripcion`, `precio`, `proveedor_id`, `estado`, `creado_por`, `modificado_por` FROM `Productos_Byron`";
        $stm = $db->prepare($query);
        $stm->execute();

        $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultado);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al obtener productos", "error" => $e->getMessage()));
    }
}

function obtenerProducto($idProductos) {
    global $db;

    try {
        $query = "SELECT `idProductos`, `nombre`, `descripcion`, `precio`, `proveedor_id`, `estado`, `creado_por`, `modificado_por` FROM `Productos_Byron` WHERE `idProductos` = ?";
        $stm = $db->prepare($query);
        $stm->bindParam(1, $idProductos);
        $stm->execute();

        $resultado = $stm->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            echo json_encode($resultado);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Producto no encontrado"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al obtener el producto", "error" => $e->getMessage()));
    }
}

function insertarProducto() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->nombre) || empty($data->precio) || empty($data->proveedor_id)) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos"));
        return;
    }

    try {
        $query = "INSERT INTO `Productos_Byron` (`nombre`, `descripcion`, `precio`, `proveedor_id`, `estado`, `creado_por`, `modificado_por`) VALUES (:nombre, :descripcion, :precio, :proveedor_id, :estado, :creado_por, :modificado_por)";
        $stm = $db->prepare($query);
        $stm->bindParam(":nombre", $data->nombre);
        $stm->bindParam(":descripcion", $data->descripcion);
        $stm->bindParam(":precio", $data->precio);
        $stm->bindParam(":proveedor_id", $data->proveedor_id);
        $stm->bindParam(":estado", $data->estado);
        $stm->bindParam(":creado_por", $data->creado_por);
        $stm->bindParam(":modificado_por", $data->modificado_por);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Producto creado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Producto no creado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear el producto", "error" => $e->getMessage()));
    }
}

function actualizarProducto() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->idProductos) || empty($data->nombre) || empty($data->precio) || empty($data->proveedor_id)) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos"));
        return;
    }

    try {
        $query = "UPDATE `Productos_Byron` SET `nombre` = :nombre, `descripcion` = :descripcion, `precio` = :precio, `proveedor_id` = :proveedor_id, `estado` = :estado, `creado_por` = :creado_por, `modificado_por` = :modificado_por WHERE `idProductos` = :idProductos";
        $stm = $db->prepare($query);
        $stm->bindParam(":idProductos", $data->idProductos);
        $stm->bindParam(":nombre", $data->nombre);
        $stm->bindParam(":descripcion", $data->descripcion);
        $stm->bindParam(":precio", $data->precio);
        $stm->bindParam(":proveedor_id", $data->proveedor_id);
        $stm->bindParam(":estado", $data->estado);
        $stm->bindParam(":creado_por", $data->creado_por);
        $stm->bindParam(":modificado_por", $data->modificado_por);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Producto actualizado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Producto no actualizado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar el producto", "error" => $e->getMessage()));
    }
}

function borrarProducto() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->idProductos)) {
        http_response_code(400);
        echo json_encode(array("message" => "ID de producto no proporcionado"));
        return;
    }

    try {
        $query = "DELETE FROM `Productos_Byron` WHERE `idProductos` = :idProductos";
        $stm = $db->prepare($query);
        $stm->bindParam(":idProductos", $data->idProductos);

        if ($stm->execute()) {
            echo json_encode(array("message" => "Producto eliminado", "code" => "success"));
        } else {
            echo json_encode(array("message" => "Producto no eliminado", "code" => "danger"));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error al eliminar el producto", "error" => $e->getMessage()));
    }
}
?>

