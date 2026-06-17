<?PHP
ini_set('display_errors', 1);
ini_set('log_errors', 1);

session_start();
include("clases/mysql.inc.php");
$pdo = new mod_db();

include("clases/SanitizarEntrada.php");
include("clases/ClaseRegistrese.php");
include("comunes/loginfunciones.php");

// ── Validar token Anti-CSRF ───────────────────────────────────────────────────
csrf_validar();

// Librería TOTP propia (sin Composer)
include("clases/GoogleAuthenticator.php");

$arrMensaje = array();

try {
    $ip = $_SERVER['REMOTE_ADDR'];

    $MyRegistro = new RegistroUsuario($_POST, $pdo, $arrMensaje);

    if (count($arrMensaje) == 0) {
        $Accion = $_POST['Accion'];

        if ($Accion == "Guardar") {
            /* ── 1. Guardar datos personales ─────────────────────────────── */
            $MyRegistro->Guardar_RegistroUsuario();
            $mensaje = 1;

            /* ── 2. Generar el secreto 2FA ───────────────────────────────── */
            $g      = new GoogleAuthenticator();
            $secret = $g->generateSecret();

            /* ── 3. Guardar el secreto en la BD ─────────────────────────── */
            if ($MyRegistro->GuardarMySecreto($secret)) {

                /* ── 4. Generar URL para el QR ──────────────────────────── */
                $nombre_usuario   = $MyRegistro->getUsuario();
                $nombre_aplicacion = 'MiSistemaLogin';
                $url    = $g->getQRCodeUrl($nombre_usuario, $secret, $nombre_aplicacion);
                $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($url);

                /* ── 5. Mostrar el QR al usuario ────────────────────────── */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activar Google Authenticator | 2FA</title>
    <link rel="shortcut icon" href="patria/5564844.png">
    <link rel="stylesheet" href="css/cmxform.css" type="text/css" />
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f4f6f8; }
        .qr-container {
            max-width: 480px;
            margin: 50px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            padding: 32px 36px;
            text-align: center;
        }
        h2 { color: #222; margin-bottom: 6px; }
        .subtitulo { color: #666; font-size: 0.9em; margin-bottom: 20px; }
        .qr-img { border: 3px solid #0077cc; border-radius: 6px; padding: 6px; }
        .qr-label {
            background: #e8f4fd;
            border-radius: 6px;
            padding: 12px 16px;
            margin: 18px 0;
            font-size: 0.9em;
            color: #333;
            text-align: left;
        }
        .secret-box {
            font-family: monospace;
            font-size: 1.1em;
            background: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px 14px;
            letter-spacing: 3px;
            display: inline-block;
            margin: 8px 0;
        }
        .btn-login {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 28px;
            background: #0077cc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 1em;
        }
        .btn-login:hover { background: #005fa3; }
        .pasos { text-align: left; font-size: 0.9em; color: #444; }
        .pasos li { margin-bottom: 6px; }
    </style>
</head>
<body>
<div class="qr-container">
    <h2>✅ Registro exitoso</h2>
    <p class="subtitulo">Desarrollo de Software VII | UTP</p>

    <p><strong>¡Un paso más!</strong> Escanea el código QR con
        <strong>Google Authenticator</strong> o <strong>Authy</strong>.</p>

    <img class="qr-img"
         src="<?php echo $qr_url; ?>"
         alt="Código QR para Google Authenticator" />

    <div class="qr-label">
        <strong>Si no puedes escanear el QR</strong>, ingresa el secreto manualmente en la app:<br>
        <span class="secret-box"><?php echo htmlspecialchars($secret); ?></span>
    </div>

    <ol class="pasos">
        <li>Abre <strong>Google Authenticator</strong> en tu celular.</li>
        <li>Toca el botón <strong>"+"</strong> → <em>Escanear código QR</em>.</li>
        <li>Apunta la cámara a este código.</li>
        <li>La app mostrará un código de 6 dígitos cada 30 segundos.</li>
        <li>Úsalo en el siguiente inicio de sesión cuando se te pida.</li>
    </ol>

    <a href="login.php" class="btn-login">
        🔐 Ir al inicio de sesión
    </a>
</div>
</body>
</html>
<?PHP
            } // fin GuardarMySecreto
        } // fin if Accion == Guardar

    } else {
        // Hay errores de validación — regresar con mensajes
        foreach ($arrMensaje as $val) {
            echo $val . '<br />';
        }
        echo '<br><a href="FormularioRegistrese.php">⬅ Volver al formulario</a>';
    }

} catch (ErrorException $e) {
    echo "❌ Ha ocurrido un error al procesar la solicitud. Intente más tarde.";
} finally {
    $pdo = null;
    $MyRegistro = null;
}
?>
