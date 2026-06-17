<?PHP
session_start();
include("comunes/loginfunciones.php");
include("clases/GoogleAuthenticator.php");

// Solo permitir acceso si viene de un login válido pendiente de 2FA
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== "PENDIENTE_2FA") {
    redireccionar("login.php");
    exit;
}

$mensaje = "";

// ── Procesar formulario de código 2FA ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_2fa'])) {

    // ── Validar token Anti-CSRF ───────────────────────────────────────────────
    csrf_validar();

    $codigo_usuario = trim($_POST['codigo_2fa']);
    $secret         = $_SESSION['pre_2fa_secret'];

    $g = new GoogleAuthenticator();

    if ($g->checkCode($secret, $codigo_usuario)) {
        // ── Código válido: completar la sesión ──────────────────────────────
        $_SESSION['autenticado'] = "SI";
        $_SESSION['Usuario']     = $_SESSION['pre_2fa_usuario'];

        // Limpiar datos temporales
        unset($_SESSION['pre_2fa_usuario']);
        unset($_SESSION['pre_2fa_secret']);

        redireccionar("formularios/PanelControl.php");
        exit;

    } else {
        $mensaje = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA | Sistema</title>

    <link rel="shortcut icon" href="patria/5564844.png">
    <script src="jquery/jquery-latest.js" type="text/javascript"></script>
    <script src="jquery/jquery.validate.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#form2fa").validate({
                rules: {
                    codigo_2fa: {
                        required: true,
                        minlength: 6,
                        maxlength: 6,
                        digits: true
                    }
                },
                messages: {
                    codigo_2fa: {
                        required:  "Ingresa el código de 6 dígitos",
                        minlength: "El código debe tener exactamente 6 dígitos",
                        maxlength: "El código debe tener exactamente 6 dígitos",
                        digits:    "Solo se permiten números"
                    }
                },
                errorElement: "span",
                errorClass: "field-error"
            });

            // Auto-submit cuando se completan 6 dígitos
            $("#codigo_2fa").on("input", function () {
                if ($(this).val().length === 6) {
                    $("#form2fa").submit();
                }
            });
        });
    </script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            text-align: center;
        }

        /* ── Header ── */
        .card-header {
            background: linear-gradient(135deg, #0f3460, #533483);
            padding: 30px 32px 24px;
            color: #fff;
        }
        .card-header .lock-icon { font-size: 2.8em; display: block; margin-bottom: 10px; }
        .card-header h1 { font-size: 1.4em; font-weight: 700; letter-spacing: 0.5px; }
        .card-header p  { font-size: 0.82em; opacity: 0.75; margin-top: 4px; }

        /* ── Body ── */
        .card-body { padding: 28px 32px 24px; }

        .info-texto {
            font-size: 0.88em;
            color: #555;
            line-height: 1.55;
            margin-bottom: 22px;
        }
        .info-texto strong { color: #333; }

        /* ── Input OTP ── */
        .otp-wrap { position: relative; margin-bottom: 6px; }
        .otp-wrap input[type=text] {
            width: 100%;
            padding: 14px 12px;
            font-size: 2em;
            letter-spacing: 10px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            background: #fafafa;
            color: #0f3460;
            font-weight: 700;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .otp-wrap input[type=text]:focus {
            border-color: #533483;
            box-shadow: 0 0 0 3px rgba(83,52,131,0.12);
            background: #fff;
        }

        span.field-error {
            color: #e53935;
            font-size: 0.76em;
            display: block;
            margin-bottom: 10px;
        }

        /* ── Error banner ── */
        .error-banner {
            background: #fdecea;
            border-left: 4px solid #e53935;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 0.83em;
            color: #b71c1c;
            margin-bottom: 18px;
            text-align: left;
        }

        /* ── Indicador de dígitos ── */
        .digits-hint {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-bottom: 18px;
        }
        .digits-hint span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ddd;
            transition: background 0.15s;
        }

        /* ── Botón ── */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0f3460, #533483);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.4px;
            transition: opacity 0.2s, transform 0.1s;
        }
        .btn-submit:hover  { opacity: 0.9; }
        .btn-submit:active { transform: scale(0.99); }

        /* ── Tip ── */
        .tip-banner {
            background: linear-gradient(90deg, #e8f4fd, #f0e8fd);
            border-left: 4px solid #533483;
            border-radius: 6px;
            padding: 9px 14px;
            font-size: 0.79em;
            color: #444;
            margin-top: 16px;
            text-align: left;
            line-height: 1.5;
        }

        /* ── Footer ── */
        .card-footer {
            background: #f8f8f8;
            border-top: 1px solid #eee;
            padding: 13px 32px;
            text-align: center;
            font-size: 0.85em;
            color: #666;
        }
        .card-footer a { color: #533483; font-weight: 600; text-decoration: none; }
        .card-footer a:hover { text-decoration: underline; }

        .page-footer {
            margin-top: 22px;
            font-size: 0.76em;
            color: rgba(255,255,255,0.45);
            text-align: center;
        }
    </style>
</head>
<body>

<div class="card">

    <!-- Header -->
    <div class="card-header">
        <span class="lock-icon">🔐</span>
        <h1>Verificación en 2 pasos</h1>
        <p>Desarrollo de Software VII &nbsp;|&nbsp; UTP</p>
    </div>

    <!-- Body -->
    <div class="card-body">

        <?PHP if ($mensaje === "error") { ?>
        <div class="error-banner">
            ❌ <strong>Código incorrecto o expirado.</strong><br>
            Asegúrate de que la hora de tu celular esté sincronizada.
        </div>
        <?PHP } ?>

        <p class="info-texto">
            Abre <strong>Google Authenticator</strong> en tu celular e ingresa el código
            de 6 dígitos para
            <strong><?php echo htmlspecialchars($_SESSION['pre_2fa_usuario'] ?? ''); ?></strong>.
        </p>

        <!-- Indicador de progreso visual -->
        <div class="digits-hint" id="dots">
            <span></span><span></span><span></span>
            <span></span><span></span><span></span>
        </div>

        <form id="form2fa" method="POST" action="verificar2fa.php">
            <?PHP csrf_campo(); ?>
            <div class="otp-wrap">
                <input
                    type="text"
                    id="codigo_2fa"
                    name="codigo_2fa"
                    maxlength="6"
                    autocomplete="one-time-code"
                    inputmode="numeric"
                    placeholder="000000"
                    autofocus
                />
            </div>

            <button type="submit" class="btn-submit">Verificar código →</button>

            <div class="tip-banner">
                💡 El código cambia cada <strong>30 segundos</strong>. Si expiró, espera el siguiente.
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="card-footer">
        <a href="login.php">⬅ Volver al inicio de sesión</a>
    </div>

</div>

<p class="page-footer">© Universidad Tecnológica de Panamá &nbsp;|&nbsp; Desarrollo de Software VII</p>

<!-- Puntos de progreso reactivos -->
<script>
    document.getElementById('codigo_2fa').addEventListener('input', function () {
        var len  = this.value.length;
        var dots = document.querySelectorAll('#dots span');
        dots.forEach(function (d, i) {
            d.style.background = i < len ? '#533483' : '#ddd';
        });
    });
</script>

</body>
</html>
