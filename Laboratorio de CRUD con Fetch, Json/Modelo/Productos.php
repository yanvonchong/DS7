<?PHP
/**
 * Modelo/Productos.php
 * Clase ObjProductos — representa un producto y sus operaciones en BD.
 * Trabaja con la clase mod_db para ejecutar consultas de forma segura.
 */

require_once "Modelo/SanitizarEntrada.php"; // Clase para limpiar datos de entrada

class ObjProductos {

    // ── Atributos privados del producto ─────────────────────────────
    private $Codigo;    // Código alfanumérico del producto (ej: A001)
    private $Producto;  // Nombre/descripción del producto
    private $Precio;    // Precio decimal
    private $Cantidad;  // Unidades disponibles
    private $idp;       // ID de la fila en la BD (usado para UPDATE y DELETE)

    private $pdo;       // Referencia al objeto mod_db (conexión a MySQL)
    private $controlError = array(); // Array para almacenar errores internos

    /**
     * Constructor: recibe el objeto de conexión mod_db
     * @param mod_db $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo; // Guardamos la referencia a la BD
    }

    // ── Setters: cargan y sanitizan los datos del producto ───────────

    /**
     * DatosRequeridos() — recibe el array de datos y los sanitiza
     * antes de asignarlos a las propiedades de la clase
     */
    public function DatosRequeridos(array $datos) {
        // limpiarXSS elimina caracteres peligrosos (previene ataques XSS)
        $this->Codigo   = SanitizarEntrada::limpiarXSS($datos["codigo"]);

        // TipoTitulo pone la primera letra en mayúscula
        $datos["producto"] = SanitizarEntrada::limpiarXSS($datos["producto"]);
        $this->Producto = SanitizarEntrada::TipoTitulo($datos["producto"]);

        $this->Precio   = $datos["precio"];
        $this->Cantidad = $datos["cantidad"];
    }

    /**
     * setId() — asigna el ID del producto para operaciones UPDATE/DELETE
     */
    public function setId($id) {
        $this->idp = intval($id); // intval() asegura que sea un entero
    }

    // ── Getters ──────────────────────────────────────────────────────
    public function getCodigo()   { return $this->Codigo; }
    public function getCantidad() { return $this->Cantidad; }
    public function getPrecio()   { return $this->Precio; }
    public function getProducto() { return $this->Producto; }
    public function getId()       { return $this->idp; }

    // ════════════════════════════════════════════════════════════════
    // OPERACIONES CRUD
    // ════════════════════════════════════════════════════════════════

    /**
     * registrarProductos() — INSERT: inserta un nuevo producto en la BD
     * Usa insertSeguro() de mod_db que usa parámetros preparados (evita SQL Injection)
     * @return bool true si el INSERT fue exitoso
     */
    public function registrarProductos() {
        // Construimos el array asociativo campo => valor
        $data = [
            "codigo"   => $this->Codigo,
            "producto" => $this->Producto,
            "precio"   => $this->Precio,
            "cantidad" => $this->Cantidad
        ];
        return $this->pdo->insertSeguro("productos", $data);
    }

    /**
     * AllProducts() — SELECT: devuelve todos los productos ordenados
     * por ID descendente (los más recientes primero)
     * @return array
     */
    public function AllProducts() {
        return $this->pdo->Arreglos("SELECT * FROM productos ORDER BY id DESC");
    }

    /**
     * actualizarProducto() — UPDATE: modifica un producto existente
     * Usa updateSeguro() que construye el SET y WHERE con parámetros preparados
     * @return bool
     */
    public function actualizarProducto() {
        // Campos a actualizar
        $dataActualizar = [
            "codigo"   => $this->Codigo,
            "producto" => $this->Producto,
            "precio"   => $this->Precio,
            "cantidad" => $this->Cantidad
        ];
        // Condición WHERE id = ?
        $condicion = ["id" => $this->idp];

        return $this->pdo->updateSeguro("productos", $dataActualizar, $condicion);
    }

    /**
     * buscarProducto() — SELECT con LIKE: filtra por código o nombre
     * @param string $termino texto a buscar
     * @return array productos que coincidan
     */
    public function buscarProducto($termino) {
        $terminoSanitizado = SanitizarEntrada::limpiarXSS($termino);

        // Usamos la conexión PDO directamente para poder usar bindValue con LIKE
        $conn = $this->pdo->getConexion();
        $sql  = "SELECT * FROM productos
                 WHERE codigo LIKE :termino
                    OR producto LIKE :termino
                 ORDER BY id DESC";

        $stmt = $conn->prepare($sql);
        $like = "%" . $terminoSanitizado . "%"; // % actúa como comodín en SQL
        $stmt->bindValue(":termino", $like);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna array asociativo
    }

    /**
     * eliminarProducto() — DELETE: borra el producto con el ID asignado
     * @return bool
     */
    public function eliminarProducto() {
        $conn = $this->pdo->getConexion();
        $sql  = "DELETE FROM productos WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $this->idp, PDO::PARAM_INT); // PDO_PARAM_INT = entero
        return $stmt->execute();
    }

} // fin clase ObjProductos
?>
