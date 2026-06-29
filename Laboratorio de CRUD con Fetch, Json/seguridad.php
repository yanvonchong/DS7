<?php
/**
 * seguridad.php
 * Guardián de la API: valida el token JWT en cada petición protegida.
 * Se incluye al inicio de cualquier endpoint que requiera autenticación.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// ── Capturar el encabezado Authorization de forma robusta ────────────
// (Apache, Nginx e IIS lo exponen con nombres distintos)
$encabezado_auth = null;

if (isset($_SERVER['Authorization'])) {
    $encabezado_auth = trim($_SERVER['Authorization']);
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $encabezado_auth = trim($_SERVER['HTTP_AUTHORIZATION']);
} elseif (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $encabezado_auth = trim($headers['Authorization']);
    }
}

// ── Extraer el token Bearer ──────────────────────────────────────────
$token = null;
if (!empty($encabezado_auth) && preg_match('/Bearer\s(\S+)/', $encabezado_auth, $matches)) {
    $token = $matches[1];
}

// Escenario Negativo: sin token → 401
if (!$token) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'Acceso denegado. Token no suministrado.'
    ]);
    exit();
}

// ── Validar el token con la clave de config.php ──────────────────────
try {
    $datos_decodificados = JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
    $usuario_autenticado = $datos_decodificados->data;

} catch (\Firebase\JWT\ExpiredException $e) {
    _responderError('El token ha expirado. Inicia sesión nuevamente.', 401);
} catch (\Firebase\JWT\SignatureInvalidException $e) {
    _responderError('Firma de token inválida.', 401);
} catch (\Exception $e) {
    _responderError('Token inválido o corrupto: ' . $e->getMessage(), 401);
}

// ── Función auxiliar de error ────────────────────────────────────────
function _responderError($mensaje, $codigo) {
    http_response_code($codigo);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $mensaje]);
    exit();
}
?>
