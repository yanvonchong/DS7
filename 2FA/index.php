<?PHP
session_start();
include("clases/mysql.inc.php");
$db = new mod_db();

include("clases/SanitizarEntrada.php");
include("comunes/loginfunciones.php");
include("clases/objLoginAdmin.php");

$tolog = false;

if (isset($_POST["tolog"])) {
    $tolog = $_POST["tolog"];
}

// $tolog es el hidden del form de login
if (isset($tolog) && ($tolog == "true") && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    // ── Validar token Anti-CSRF ───────────────────────────────────────────────
    csrf_validar();

    $Usuario  = $_POST['usuario'];
    $ClaveKey = $_POST['contrasena'];
    $ipRemoto = $_SERVER['REMOTE_ADDR'];

    $Logearme = new ValidacionLogin($Usuario, $ClaveKey, $ipRemoto, $db);

    if ($Logearme->logger()) {
        $Logearme->autenticar();

        if ($Logearme->getIntentoLogin()) {
            // ── Credenciales válidas ────────────────────────────────────────
            $Logearme->registrarIntentos();

            // Verificar si el usuario tiene 2FA activado
            $usuarioDB = $db->log($Usuario);

            if ($usuarioDB && !empty($usuarioDB->secret_2fa)) {
                // ── Tiene 2FA: guardar datos en sesión temporal ─────────────
                // NO marcamos autenticado = SI todavía
                $_SESSION['pre_2fa_usuario'] = $Logearme->getUsuario();
                $_SESSION['pre_2fa_secret']  = $usuarioDB->secret_2fa;
                $_SESSION['autenticado']     = "PENDIENTE_2FA";

                $tolog = false;
                redireccionar("verificar2fa.php");

            } else {
                // ── Sin 2FA: acceso directo (usuario legacy) ────────────────
                $_SESSION['autenticado'] = "SI";
                $_SESSION['Usuario']     = $Logearme->getUsuario();

                $tolog = false;
                redireccionar("formularios/PanelControl.php");
            }

        } else {
            // Contraseña incorrecta
            $Logearme->registrarIntentos();
            $_SESSION["emsg"] = 1;
            redireccionar("login.php");
        }
    } else {
        // Usuario no encontrado
        $_SESSION["emsg"] = 1;
        redireccionar("login.php");
    }

} else {
    redireccionar("login.php");
}
?>
