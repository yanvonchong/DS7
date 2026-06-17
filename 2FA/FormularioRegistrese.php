<?PHP
session_start();
include("comunes/loginfunciones.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario | Sistema 2FA</title>
    <link rel="shortcut icon" href="patria/5564844.png">
    <script src="jquery/jquery-latest.js" type="text/javascript"></script>
    <script src="jquery/jquery.validate.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // Validación AJAX: usuario duplicado
            $("#usuario").on("blur", function () {
                var usuarioVal = $(this).val().trim();
                if (usuarioVal.length < 3) return;
                $.ajax({
                    type: "POST",
                    url: "clases/EjemploSentenciaPreparada_Usuario.php",
                    data: { usuario: usuarioVal },
                    dataType: "html",
                    success: function (resp) {
                        resp = jQuery.trim(resp);
                        if (resp === "existe") {
                            $("#msg-usuario").html("<span class='msg-error'>❌ Este nombre de usuario ya está en uso.</span>");
                        } else {
                            $("#msg-usuario").html("<span class='msg-ok'>✔ Usuario disponible</span>");
                        }
                    }
                });
            });

            $("#form1").validate({
                submitHandler: function (form1) {
                    // Bloquear envío si el usuario está duplicado
                    if ($("#msg-usuario").find(".msg-error").length > 0) {
                        $("#msg-usuario").html("<span class='msg-error'>❌ Elige otro nombre de usuario.</span>");
                        return false;
                    }
                    var email1 = $("#email1").val();
                    $.ajax({
                        type: "POST",
                        url: "clases/EjemploSentenciaPreparada_Correo.php",
                        data: { email: email1 },
                        dataType: "html",
                        beforeSend: function () {
                            $("#mensaje-estado").html("<span class='msg-checking'>⏳ Verificando correo...</span>");
                        },
                        success: function (datos) {
                            datos = jQuery.trim(datos);
                            if (datos == "libre") {
                                $("#mensaje-estado").html("");
                                form1.submit();
                            } else {
                                $("#mensaje-estado").html("<span class='msg-error'>❌ Este correo ya está en uso.</span>");
                            }
                        },
                        error: function () {
                            alert("El proceso ha fallado!");
                        }
                    });
                },
                rules: {
                    nombre:      "required",
                    apellido:    "required",
                    usuario:     "required",
                    clave:       "required",
                    clave_again: { equalTo: "#clave" },
                    email1:      { required: true, email: true },
                    sexo:        "required"
                },
                messages: {
                    nombre:      "⚠ El nombre es obligatorio",
                    apellido:    "⚠ El apellido es obligatorio",
                    usuario:     "⚠ El usuario es obligatorio",
                    clave:       "⚠ La contraseña es obligatoria",
                    clave_again: "⚠ Las contraseñas no coinciden",
                    email1: {
                        required: "⚠ El correo es obligatorio",
                        email:    "⚠ Ingresa un correo válido"
                    },
                    sexo: "⚠ Selecciona el sexo"
                },
                errorElement: "span",
                errorClass: "field-error"
            });

            // Mostrar/ocultar contraseña
            $(".toggle-pass").on("click", function () {
                var input = $($(this).data("target"));
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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
        }

        /* ── Cabecera ── */
        .card-header {
            background: linear-gradient(135deg, #0f3460, #533483);
            padding: 28px 32px 22px;
            text-align: center;
            color: #fff;
        }
        .card-header .lock-icon {
            font-size: 2.4em;
            display: block;
            margin-bottom: 8px;
        }
        .card-header h1 {
            font-size: 1.4em;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .card-header p {
            font-size: 0.82em;
            opacity: 0.8;
            margin-top: 4px;
        }

        /* ── Cuerpo ── */
        .card-body { padding: 28px 32px 24px; }

        .info-banner {
            background: linear-gradient(90deg, #e8f4fd, #f0e8fd);
            border-left: 4px solid #533483;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 0.83em;
            color: #333;
            margin-bottom: 22px;
            line-height: 1.5;
        }

        /* ── Filas de campos ── */
        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field {
            margin-bottom: 14px;
        }
        .field label {
            display: block;
            font-size: 0.78em;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap .icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.95em;
            pointer-events: none;
        }
        .input-wrap input,
        .input-wrap select {
            width: 100%;
            padding: 9px 12px 9px 34px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 0.92em;
            color: #333;
            background: #fafafa;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .input-wrap input:focus,
        .input-wrap select:focus {
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
        .msg-error    { color: #e53935; font-size: 0.82em; }
        .msg-ok       { color: #2e7d32; font-size: 0.82em; }
        .msg-checking { color: #888;   font-size: 0.82em; }
        #mensaje-estado, #msg-usuario { display: block; margin-top: 4px; }

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

        /* ── Footer del card ── */
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

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 16px 0 14px;
            color: #bbb;
            font-size: 0.78em;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #eee;
        }
    </style>
</head>
<body>

<div class="card">

    <!-- Cabecera -->
    <div class="card-header">
        <span class="lock-icon">🔐</span>
        <h1>Crear cuenta</h1>
        <p>Desarrollo de Software VII &nbsp;|&nbsp; UTP</p>
    </div>

    <!-- Cuerpo -->
    <div class="card-body">

        <div class="info-banner">
            🛡️ Al registrarte se generará un <strong>código QR</strong> para activar
            la autenticación de dos factores (<strong>2FA</strong>) con Google Authenticator.
        </div>

        <span id="mensaje-estado"></span>

        <form id="form1" method="post" action="ProcesarRegistro.php">
            <input type="hidden" name="Accion" value="Guardar" />
            <?PHP csrf_campo(); ?>

            <!-- Nombre y Apellido -->
            <div class="row-2">
                <div class="field">
                    <label for="nombre">Nombre</label>
                    <div class="input-wrap">
                        <span class="icon">👤</span>
                        <input type="text" id="nombre" name="nombre" placeholder="Juan" />
                    </div>
                </div>
                <div class="field">
                    <label for="apellido">Apellido</label>
                    <div class="input-wrap">
                        <span class="icon">👤</span>
                        <input type="text" id="apellido" name="apellido" placeholder="Pérez" />
                    </div>
                </div>
            </div>

            <!-- Usuario -->
            <div class="field">
                <label for="usuario">Nombre de usuario</label>
                <div class="input-wrap">
                    <span class="icon">🪪</span>
                    <input type="text" id="usuario" name="usuario" placeholder="jperez" />
                </div>
                <span id="msg-usuario"></span>
            </div>

            <!-- Email -->
            <div class="field">
                <label for="email1">Correo electrónico</label>
                <div class="input-wrap">
                    <span class="icon">✉️</span>
                    <input type="email" id="email1" name="email1" placeholder="juan@correo.com" />
                </div>
            </div>

            <div class="divider">Seguridad</div>

            <!-- Contraseñas -->
            <div class="row-2">
                <div class="field">
                    <label for="clave">Contraseña</label>
                    <div class="input-wrap">
                        <span class="icon">🔑</span>
                        <input type="password" id="clave" name="clave" placeholder="••••••••" />
                        <button type="button" class="toggle-pass" data-target="#clave">👁</button>
                    </div>
                </div>
                <div class="field">
                    <label for="clave_again">Repetir contraseña</label>
                    <div class="input-wrap">
                        <span class="icon">🔑</span>
                        <input type="password" id="clave_again" name="clave_again" placeholder="••••••••" />
                        <button type="button" class="toggle-pass" data-target="#clave_again">👁</button>
                    </div>
                </div>
            </div>

            <!-- Sexo -->
            <div class="field">
                <label for="sexo">Sexo</label>
                <div class="input-wrap">
                    <span class="icon">⚥</span>
                    <select id="sexo" name="sexo">
                        <option value="">-- Seleccione --</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Registrarse y activar 2FA &nbsp;→
            </button>
        </form>
    </div>

    <!-- Footer -->
    <div class="card-footer">
        ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
    </div>

</div>

</body>
</html>
