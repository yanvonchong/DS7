<?php
/**
 * registrar.php — Backend del Lab 1 (CRUD con Fetch API)
 * Estudiante: Yan
 *
 * Recibe peticiones POST desde script.js via fetch() + FormData.
 * Centraliza las operaciones con switch($_POST['Accion']).
 * Siempre retorna JSON con: success / message / accion / errors
 */

// ob_start() atrapa cualquier texto/error que PHP pueda imprimir
// antes del JSON, evitando que rompa la respuesta
ob_start();

// Ocultamos errores de pantalla — van al log del servidor, no al cliente
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Indicamos al navegador que la respuesta será JSON
header("Content-Type: application/json; charset=UTF-8");

// ── Cargar clases del modelo ─────────────────────────────────────────
// Si algún archivo falla (no existe, error de sintaxis) capturamos la excepción
try {
    require_once "Modelo/conexion.php";   // Clase mod_db — conexión PDO a MySQL
    require_once "Modelo/Productos.php";  // Clase ObjProductos — operaciones CRUD
    require_once "Modelo/ValidarForm.php"; // Clase FormValidator — validaciones
} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al cargar módulos: " . $e->getMessage(), "accion" => "", "errors" => []]);
    exit;
}

// ── Crear la conexión a la base de datos ────────────────────────────
// Si la BD no existe o la contraseña es incorrecta, capturamos el error
try {
    $db = new mod_db(); // Instancia la clase que abre la conexión PDO
} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error de conexión a la BD: " . $e->getMessage(), "accion" => "", "errors" => []]);
    exit;
}

// Instanciamos los objetos que vamos a usar en el switch
$producto  = new ObjProductos($db);   // Maneja las consultas de productos
$validator = new FormValidator();     // Valida los campos del formulario

// Leemos la acción que envió JavaScript (Guardar, Modificar, Buscar, Eliminar)
$accion = $_POST['Accion'] ?? ''; // ?? '' evita error si el campo no existe

// Estructura base de la respuesta JSON que siempre enviamos
$response = [
    "success" => false,      // true si la operación fue exitosa
    "message" => "",         // Mensaje descriptivo para el usuario
    "accion"  => $accion,    // Devolvemos la acción para que el switch de JS la use
    "errors"  => []          // Array de errores de validación por campo
];

// ════════════════════════════════════════════════════════════════════
// SWITCH CENTRALIZADO — criterio principal de la rúbrica
// Cada case maneja una operación distinta del CRUD
// ════════════════════════════════════════════════════════════════════
switch ($accion) {

    // ── GUARDAR: Insertar nuevo producto en la BD ────────────────────
    case 'Guardar':

        // Pasamos los datos del POST al validador
        $validator->enviarDatos($_POST);
        $validator->setRequiredFields(['codigo', 'producto', 'precio', 'cantidad']);
        $validator->validate(); // Ejecuta todas las reglas de validación

        // Si hay errores de validación, respondemos sin tocar la BD
        if ($validator->getError()) {
            $response["message"] = "Corrige los errores antes de guardar.";
            $response["errors"]  = $validator->getErrorArray();
            break;
        }

        // Regla de negocio: no se pueden registrar 0 unidades
        if (intval($_POST['cantidad']) < 1) {
            $response["message"]            = "La cantidad mínima al registrar un producto es 1.";
            $response["errors"]["cantidad"] = "Cantidad debe ser al menos 1.";
            break;
        }

        // Enviamos los datos al modelo para sanitizarlos y guardarlos
        $producto->DatosRequeridos([
            'codigo'   => $_POST['codigo'],
            'producto' => $_POST['producto'],
            'precio'   => $_POST['precio'],
            'cantidad' => $_POST['cantidad']
        ]);

        // registrarProductos() ejecuta el INSERT y devuelve true/false
        if ($producto->registrarProductos()) {
            $response["success"] = true;
            $response["message"] = "¡Producto guardado exitosamente!";
        } else {
            $response["message"] = "Error al guardar el producto en la base de datos.";
        }
        break;

    // ── MODIFICAR: Actualizar producto existente ─────────────────────
    case 'Modificar':

        // El ID es obligatorio para saber qué fila actualizar
        if (empty($_POST['id'])) {
            $response["message"] = "ID de producto no proporcionado.";
            break;
        }

        $validator->enviarDatos($_POST);
        $validator->setRequiredFields(['codigo', 'producto', 'precio', 'cantidad']);
        $validator->validate();

        if ($validator->getError()) {
            $response["message"] = "Corrige los errores antes de modificar.";
            $response["errors"]  = $validator->getErrorArray();
            break;
        }

        $producto->DatosRequeridos([
            'codigo'   => $_POST['codigo'],
            'producto' => $_POST['producto'],
            'precio'   => $_POST['precio'],
            'cantidad' => $_POST['cantidad']
        ]);
        $producto->setId($_POST['id']); // Indicamos qué producto actualizar por ID

        // actualizarProducto() ejecuta el UPDATE y devuelve true/false
        if ($producto->actualizarProducto()) {
            $response["success"] = true;
            $response["message"] = "¡Producto modificado exitosamente!";
        } else {
            $response["message"] = "Error al modificar el producto.";
        }
        break;

    // ── BUSCAR: Traer todos o filtrar por texto ──────────────────────
    case 'Buscar':

        $termino = $_POST['termino'] ?? '';

        if (empty(trim($termino))) {
            // Sin término = traer todos los productos (ORDER BY id DESC)
            $resultados = $producto->AllProducts();
        } else {
            // Con término = buscar por código o nombre usando LIKE
            $resultados = $producto->buscarProducto($termino);
        }

        $response["success"] = true;
        $response["message"] = count($resultados) . " producto(s) encontrado(s).";
        $response["data"]    = $resultados; // Array de productos para renderizar la tabla
        break;

    // ── ELIMINAR: Borrar producto por ID ────────────────────────────
    case 'Eliminar':

        // Sin ID no podemos saber qué eliminar
        if (empty($_POST['id'])) {
            $response["message"] = "ID de producto no proporcionado.";
            break;
        }

        $producto->setId($_POST['id']); // Seteamos el ID a eliminar

        // eliminarProducto() ejecuta el DELETE WHERE id = :id
        if ($producto->eliminarProducto()) {
            $response["success"] = true;
            $response["message"] = "Producto eliminado exitosamente.";
        } else {
            $response["message"] = "Error al eliminar el producto.";
        }
        break;

    // ── DEFAULT: Acción no reconocida ────────────────────────────────
    default:
        $response["message"] = "Acción no reconocida: '$accion'.";
        break;
}

// Limpiamos cualquier salida residual y enviamos SOLO el JSON
ob_end_clean();
echo json_encode($response);
exit;
?>
