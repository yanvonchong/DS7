<?PHP
/**
 * login.php
 * Punto de entrada de autenticación.
 * Recibe usuario y clave por POST, valida con password_verify()
 * y devuelve un token JWT firmado.
 */

// Carga constantes de configuración (JWT_SECRET_KEY, JWT_USER_SECRET, JWT_CLAVE_HASH)
require_once __DIR__ . '/config/config.php';

// Carga el autoload de Composer (incluye firebase/php-jwt)
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;

header('Content-Type: application/json');

// Leer credenciales del cuerpo POST
$usuario = $_POST['usuario'] ?? '';
$clave   = $_POST['clave']   ?? '';

// Validación básica de campos vacíos
if (empty($usuario) || empty($clave)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Usuario y clave son requeridos.']);
    exit;
}

// Verificar usuario con password_verify() (Actividad 2 - hashing con BCRYPT)
$usuarioValido = ($usuario === JWT_USER_SECRET);
$claveValida   = password_verify($clave, JWT_CLAVE_HASH);

if ($usuarioValido && $claveValida) {

    $payload = [
        'iss'  => 'http://localhost',      // Emisor
        'iat'  => time(),                  // Emitido en
        'exp'  => time() + 3600,           // Expira en 1 hora
        'data' => [
            'id'      => 1,
            'usuario' => JWT_USER_SECRET,
            'rol'     => 'admin'
        ]
    ];

    // Codificar el token con la clave secreta desde config
    $jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'token'   => $jwt,
        'message' => 'Autenticación exitosa. Token válido por 1 hora.'
    ]);

} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => 'Credenciales inválidas.'
    ]);
}
?>
