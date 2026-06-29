<?PHP
/**
 * index.php — Front Controller de la API REST (Actividad 3)
 *
 * Punto de entrada único. Valida el JWT antes de derivar
 * la petición al controlador correspondiente.
 *
 * Endpoints:
 *   GET    /index.php  → listar productos
 *   POST   /index.php  → crear producto
 *   PUT    /index.php  → actualizar producto (requiere id en JSON)
 *   DELETE /index.php  → eliminar producto (requiere id en JSON)
 */

// 1. Cabeceras CORS y tipo de respuesta
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Preflight OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 2. Validar token JWT (Actividad 3 — Centralización)
//    Si el token es inválido o no existe, seguridad.php termina la ejecución con 401
require_once __DIR__ . '/seguridad.php';

// 3. Cargar el controlador de productos
require_once __DIR__ . '/Router/ProductosController.php';

$controller = new ProductoController();
$method     = $_SERVER['REQUEST_METHOD'];

// 4. Centralización con switch (criterio de rúbrica)
switch ($method) {

    case 'GET':
        // Listar todos los productos
        $controller->listarProductos();
        break;

    case 'POST':
        // Crear un nuevo producto
        $controller->crearProducto();
        break;

    case 'PUT':
        // Actualizar producto existente (enviar id + campos en el JSON)
        $controller->actualizarProducto();
        break;

    case 'DELETE':
        // Eliminar producto (enviar id en el JSON)
        $controller->eliminarProducto();
        break;

    default:
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "405 Método no permitido."
        ]);
        break;
}
?>
