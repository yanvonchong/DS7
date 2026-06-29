<?PHP
require_once "Modelo/conexion.php";
require_once "Modelo/ValidarForm.php";
require_once "Modelo/Productos.php";

/**
 * ProductoController
 * Controlador que recibe las peticiones HTTP y delega a ObjProductos.
 * Centraliza el CRUD mediante switch en index.php (Front Controller).
 */
class ProductoController {

    private $db;
    private $conn;
    private $misDatos;
    private $myProducto;

    public function __construct() {
        $this->db         = new mod_db();
        $this->conn       = $this->db->getConexion();
        $this->misDatos   = new FormValidator();
        $this->myProducto = new ObjProductos($this->db);
    }

    // ── POST: Crear producto ─────────────────────────────────────────
    public function crearProducto() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (is_null($data)) {
            http_response_code(400);
            echo json_encode(["success" => false,
                "message" => "JSON inválido o vacío. Verifica Content-Type: application/json."]);
            return;
        }

        $this->misDatos->enviarDatos($data);
        $this->misDatos->setRequiredFields(['codigo', 'producto', 'precio', 'cantidad']);
        $this->misDatos->validate();

        if ($this->misDatos->getError()) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Datos con errores de validación.",
                "errors"  => $this->misDatos->getErrorArray()
            ]);
            return;
        }

        $this->myProducto->DatosRequeridos($data);

        if ($this->myProducto->registrarProductos()) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Producto creado exitosamente."]);
        } else {
            http_response_code(503);
            echo json_encode(["success" => false, "message" => "No se pudo crear el producto."]);
        }
    }

    // ── GET: Listar productos ────────────────────────────────────────
    public function listarProductos() {
        $resultados = $this->myProducto->AllProducts();

        if (count($resultados) > 0) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "total"   => count($resultados),
                "data"    => $resultados
            ]);
        } else {
            http_response_code(200);
            echo json_encode(["success" => true, "total" => 0, "data" => []]);
        }
    }

    // ── PUT: Actualizar producto ─────────────────────────────────────
    public function actualizarProducto() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (is_null($data)) {
            http_response_code(400);
            echo json_encode(["success" => false,
                "message" => "JSON inválido o vacío."]);
            return;
        }

        // Se requiere el id del producto
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Se requiere el ID del producto."]);
            return;
        }

        $this->misDatos->enviarDatos($data);
        $this->misDatos->setRequiredFields(['codigo', 'producto', 'precio', 'cantidad']);
        $this->misDatos->validate();

        if ($this->misDatos->getError()) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Datos con errores de validación.",
                "errors"  => $this->misDatos->getErrorArray()
            ]);
            return;
        }

        $this->myProducto->DatosRequeridos($data);
        $this->myProducto->setId($data['id']);

        if ($this->myProducto->actualizarProducto()) {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Producto actualizado exitosamente."]);
        } else {
            http_response_code(503);
            echo json_encode(["success" => false, "message" => "No se pudo actualizar el producto."]);
        }
    }

    // ── DELETE: Eliminar producto ────────────────────────────────────
    public function eliminarProducto() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Se requiere el ID del producto."]);
            return;
        }

        $this->myProducto->setId($data['id']);

        if ($this->myProducto->eliminarProducto()) {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Producto eliminado exitosamente."]);
        } else {
            http_response_code(503);
            echo json_encode(["success" => false, "message" => "No se pudo eliminar el producto."]);
        }
    }

} // fin clase ProductoController
?>
