<?php
/**
 * api/products.php
 * Endpoint alternativo para la API de productos (con protección JWT).
 * Maneja GET, POST, PUT, DELETE delegando al ProductoController.
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Validar token JWT — si falla, seguridad.php responde 401 y termina
require_once dirname(__DIR__) . '/seguridad.php';

// Cargar controlador
require_once dirname(__DIR__) . '/Router/ProductosController.php';

$controller = new ProductoController();
$method     = $_SERVER['REQUEST_METHOD'];

// Centralización con switch
switch ($method) {

    case 'GET':
        $controller->listarProductos();
        break;

    case 'POST':
        $controller->crearProducto();
        break;

    case 'PUT':
        $controller->actualizarProducto();
        break;

    case 'DELETE':
        $controller->eliminarProducto();
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        break;
}
?>
