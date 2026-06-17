<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión | Sistema 2FA</title>
    <link rel="shortcut icon" href="patria/5564844.png">
    <script src="jquery/jquery-latest.js" type="text/javascript"></script>
    <script src="jquery/jquery.validate.js" type="text/javascript"></script>

    <?PHP
    session_start();
    include("comunes/loginfunciones.php");
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#deteccionUser").validate({
                rules: {
                    usuario:    "required",
                    contrasena: "required"
                },
                messages: {
                    usuario:    "⚠ El usuario es obligatorio",
                    contrasena: "⚠ La contraseña es obligatoria"
                },
                errorElement: "span",
                errorClass: "field-error"
            });

            $(".toggle-pass").on("click", function () {
                var input = $("#contrasena");
                var type  = input.attr("type") === "password" ? "text" : "password";
                input.attr("type", type);
                $(this).text(type === "password" ? "👁" : "🙈");
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
            max-width: 400px;
            overflow: hidden;
        }

        /* ── Header ── */
        .card-header {
            background: linear-gradient(135deg, #0f3460, #533483);
            padding: 30px 32px 24px;
            text-align: center;
            color: #fff;
        }
        .card-header .lock-icon { font-size: 2.6em; display: block; margin-bottom: 10px; }
        .card-header h1 { font-size: 1.45em; font-weight: 700; letter-spacing: 0.5px; }
        .card-header p  { font-size: 0.82em; opacity: 0.75; margin-top: 4px; }

        /* ── Body ── */
        .card-body { padding: 28px 32px 24px; }

        .field { margin-bottom: 16px; }
        .field label {
            display: block;
            font-size: 0.78em;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .input-wrap { position: relative; }
        .input-wrap .icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1em;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 0.95em;
            color: #333;
            background: #fafafa;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .input-wrap input:focus {
            border-color: #533483;
            box-shadow: 0 0 0 3px rgba(83,52,131,0.1);
            background: #fff;
        }
        .input-wrap .toggle-pass {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }

        span.field-error {
            color: #e53935;
            font-size: 0.76em;
            display: block;
            margin-top: 3px;
        }

        .login-error-banner {
            background: #fdecea;
            border-left: 4px solid #e53935;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 0.83em;
            color: #b71c1c;
            margin-bottom: 18px;
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
            margin-top: 6px;
        }
        .btn-submit:hover  { opacity: 0.9; }
        .btn-submit:active { transform: scale(0.99); }

        .badge-2fa {
            display: flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(90deg, #e8f4fd, #f0e8fd);
            border-left: 4px solid #533483;
            border-radius: 6px;
            padding: 9px 14px;
            font-size: 0.8em;
            color: #444;
            margin-top: 16px;
        }

        /* ── Footer ── */
        .card-footer {
            background: #f8f8f8;
            border-top: 1px solid #eee;
            padding: 14px 32px;
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

    <div class="card-header">
        <span class="lock-icon">🔒</span>
        <h1>Iniciar sesión</h1>
        <p>Desarrollo de Software VII &nbsp;|&nbsp; UTP</p>
    </div>

    <div class="card-body">

        <?PHP
        if (isset($_SESSION["emsg"]) && $_SESSION["emsg"] == 1) {
            echo '<div class="login-error-banner">⚠️ Usuario o contraseña incorrectos. Vuelve a intentarlo.</div>';
            unset($_SESSION["emsg"]);
        }
        ?>

        <form id="deteccionUser" name="deteccionUser" method="post" action="index.php">
            <input type="hidden" name="tolog" id="tolog" value="true" />
            <?PHP csrf_campo(); ?>

            <div class="field">
                <label for="usuario">Usuario</label>
                <div class="input-wrap">
                    <span class="icon">🪪</span>
                    <input type="text" id="usuario" name="usuario" placeholder="Tu nombre de usuario" minlength="4" autofocus />
                </div>
            </div>

            <div class="field">
                <label for="contrasena">Contraseña</label>
                <div class="input-wrap">
                    <span class="icon">🔑</span>
                    <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" />
                    <button type="button" class="toggle-pass">👁</button>
                </div>
            </div>

            <button type="submit" class="btn-submit">Entrar &nbsp;→</button>

            <div class="badge-2fa">
                🛡️ Este sistema usa <strong>&nbsp;autenticación de dos factores (2FA)</strong>
            </div>
        </form>
    </div>

    <div class="card-footer">
        ¿No tienes cuenta? <a href="FormularioRegistrese.php">Regístrate aquí</a>
    </div>

</div>

<p class="page-footer">© Universidad Tecnológica de Panamá &nbsp;|&nbsp; Desarrollo de Software VII</p>

</body>
</html>
