<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Hash | Sistema 2FA</title>
    <link rel="shortcut icon" href="patria/5564844.png">
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
            padding: 24px;
        }

        h1 { color: #fff; font-size: 1.5em; margin-bottom: 4px; text-align: center; }
        .subtitle { color: rgba(255,255,255,0.55); font-size: 0.82em; margin-bottom: 24px; text-align: center; }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            width: 100%;
            max-width: 860px;
        }

        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 16px 50px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #0f3460, #533483);
            padding: 18px 24px;
            color: #fff;
        }
        .card-header h2 { font-size: 1.05em; font-weight: 700; }
        .card-header p  { font-size: 0.78em; opacity: 0.75; margin-top: 3px; }

        .card-body { padding: 22px 24px; }

        label {
            display: block;
            font-size: 0.76em;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 5px;
        }

        input[type=text], input[type=password], textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 0.92em;
            color: #333;
            background: #fafafa;
            margin-bottom: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus, textarea:focus {
            border-color: #533483;
            box-shadow: 0 0 0 3px rgba(83,52,131,0.1);
            background: #fff;
        }
        textarea { resize: vertical; font-family: monospace; font-size: 0.83em; min-height: 70px; }

        .btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #0f3460, #533483);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.9; }

        .resultado {
            margin-top: 14px;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 0.86em;
            line-height: 1.5;
        }
        .resultado.ok      { background: #e8f5e9; border-left: 4px solid #43a047; color: #2e7d32; }
        .resultado.error   { background: #fdecea; border-left: 4px solid #e53935; color: #b71c1c; }
        .resultado.info    { background: linear-gradient(90deg,#e8f4fd,#f0e8fd); border-left: 4px solid #533483; color: #333; }

        .hash-output {
            font-family: monospace;
            font-size: 0.8em;
            word-break: break-all;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px 10px;
            margin-top: 6px;
            cursor: pointer;
            title: "Clic para copiar";
        }
        .hash-output:hover { background: #ede7f6; }

        .cost-badge {
            display: inline-block;
            background: #533483;
            color: #fff;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 0.75em;
            margin-left: 6px;
        }

        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 14px 0;
        }

        .info-list {
            font-size: 0.82em;
            color: #555;
            padding-left: 18px;
            line-height: 1.8;
        }

        .page-footer {
            margin-top: 20px;
            font-size: 0.75em;
            color: rgba(255,255,255,0.4);
            text-align: center;
        }
        .page-footer a { color: rgba(255,255,255,0.6); text-decoration: none; }
        .page-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h1>🔐 Interfaz de Hash &amp; Verificación</h1>
<p class="subtitle">Desarrollo de Software VII &nbsp;|&nbsp; UTP &nbsp;|&nbsp; Demostración de password_hash() y password_verify()</p>

<div class="grid">

    <!-- ══ CARD 1: Generar Hash ══ -->
    <div class="card">
        <div class="card-header">
            <h2>Generar Hash <span class="cost-badge">bcrypt cost 13</span></h2>
            <p>Ingresa una contraseña y genera su hash seguro con password_hash()</p>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="accion" value="generar">
                <label for="pass_gen">Contraseña en texto plano</label>
                <input type="text" id="pass_gen" name="pass_gen"
                       placeholder="Ej: MiClave123!"
                       value="<?php echo htmlspecialchars($_POST['pass_gen'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="btn">Generar Hash →</button>
            </form>

            <?PHP if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'generar') {
                $passInput = $_POST['pass_gen'] ?? '';
                if ($passInput !== '') {
                    $hash = password_hash($passInput, PASSWORD_BCRYPT, ['cost' => 13]);
                    $info = password_get_info($hash);
            ?>
            <div class="resultado info">
                <strong>✅ Hash generado:</strong>
                <div class="hash-output" onclick="navigator.clipboard.writeText(this.innerText)" title="Clic para copiar">
                    <?php echo htmlspecialchars($hash); ?>
                </div>
                <hr class="divider">
                <ul class="info-list">
                    <li><strong>Algoritmo:</strong> <?php echo $info['algoName']; ?></li>
                    <li><strong>Cost (factor de trabajo):</strong> <?php echo $info['options']['cost']; ?></li>
                    <li><strong>Longitud del hash:</strong> <?php echo strlen($hash); ?> caracteres</li>
                    <li><strong>Función PHP:</strong> <code>password_hash($pass, PASSWORD_BCRYPT, ['cost' => 13])</code></li>
                </ul>
                <small style="color:#888; font-size:0.78em;">💡 Clic en el hash para copiarlo</small>
            </div>
            <?PHP } else { ?>
            <div class="resultado error">⚠️ Ingresa una contraseña para generar el hash.</div>
            <?PHP } } ?>
        </div>
    </div>

    <!-- ══ CARD 2: Verificar Hash ══ -->
    <div class="card">
        <div class="card-header">
            <h2>Verificar Hash</h2>
            <p>Comprueba si una contraseña coincide con un hash usando password_verify()</p>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="accion" value="verificar">
                <label for="pass_ver">Contraseña en texto plano</label>
                <input type="text" id="pass_ver" name="pass_ver"
                       placeholder="Ej: MiClave123!"
                       value="<?php echo htmlspecialchars($_POST['pass_ver'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <label for="hash_ver">Hash a verificar</label>
                <textarea id="hash_ver" name="hash_ver"
                          placeholder="Pega aquí el hash generado (empieza con $2y$...)"><?php echo htmlspecialchars($_POST['hash_ver'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <button type="submit" class="btn">Verificar →</button>
            </form>

            <?PHP if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'verificar') {
                $passVer  = $_POST['pass_ver'] ?? '';
                $hashVer  = trim($_POST['hash_ver'] ?? '');
                if ($passVer !== '' && $hashVer !== '') {
                    $valido = password_verify($passVer, $hashVer);
            ?>
            <?php if ($valido): ?>
            <div class="resultado ok">
                ✅ <strong>¡Contraseña VÁLIDA!</strong><br>
                La contraseña ingresada coincide con el hash.
                <ul class="info-list" style="margin-top:8px;">
                    <li><strong>password_verify()</strong> retornó: <code>true</code></li>
                    <li>Función PHP: <code>password_verify($password, $hash)</code></li>
                </ul>
            </div>
            <?php else: ?>
            <div class="resultado error">
                ❌ <strong>Contraseña INCORRECTA.</strong><br>
                La contraseña NO coincide con el hash proporcionado.
                <ul class="info-list" style="margin-top:8px;">
                    <li><strong>password_verify()</strong> retornó: <code>false</code></li>
                </ul>
            </div>
            <?php endif; ?>
            <?PHP } else { ?>
            <div class="resultado error">⚠️ Ingresa la contraseña y el hash para verificar.</div>
            <?PHP } } ?>
        </div>
    </div>

</div>

<!-- ══ CARD 3: Explicación ══ -->
<div class="card" style="max-width:860px; width:100%; margin-top:20px;">
    <div class="card-header">
        <h2>¿Cómo funciona bcrypt?</h2>
        <p>Referencia rápida sobre el algoritmo de hash seguro para contraseñas</p>
    </div>
    <div class="card-body" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        <div>
            <label>password_hash() — Genera el hash</label>
            <div style="background:#1a1a2e; color:#a5d6a7; font-family:monospace; font-size:0.82em; padding:14px; border-radius:8px; line-height:1.7;">
                <span style="color:#90caf9;">$hash</span> = password_hash(<br>
                &nbsp;&nbsp;<span style="color:#fff176;">$password</span>,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#888">// texto plano</span><br>
                &nbsp;&nbsp;<span style="color:#ce93d8;">PASSWORD_BCRYPT</span>,<span style="color:#888"> // algoritmo</span><br>
                &nbsp;&nbsp;[<span style="color:#ffab91">'cost'</span> =&gt; <span style="color:#80cbc4;">13</span>]&nbsp;&nbsp;<span style="color:#888"> // factor</span><br>
                );
            </div>
        </div>
        <div>
            <label>password_verify() — Valida el hash</label>
            <div style="background:#1a1a2e; color:#a5d6a7; font-family:monospace; font-size:0.82em; padding:14px; border-radius:8px; line-height:1.7;">
                <span style="color:#90caf9;">$ok</span> = password_verify(<br>
                &nbsp;&nbsp;<span style="color:#fff176;">$password</span>,&nbsp;<span style="color:#888">// texto plano</span><br>
                &nbsp;&nbsp;<span style="color:#90caf9;">$hash</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#888"> // hash guardado</span><br>
                );<br>
                <span style="color:#888">// retorna true o false</span>
            </div>
        </div>
        <div style="grid-column:1/-1;">
            <ul class="info-list" style="color:#555;">
                <li>Cada vez que se llama <code>password_hash()</code> genera un <strong>salt aleatorio</strong> distinto — nunca dos hashes iguales para la misma contraseña.</li>
                <li>El <strong>cost 13</strong> significa 2¹³ = 8192 rondas de hashing, haciendo ataques de fuerza bruta muy lentos.</li>
                <li><code>password_verify()</code> extrae el salt del hash y reaplica el proceso para comparar — nunca almacena la contraseña en texto plano.</li>
                <li>El hash resultante tiene siempre <strong>60 caracteres</strong> con formato: <code>$2y$13$[salt22chars][hash31chars]</code></li>
            </ul>
        </div>
    </div>
</div>

<p class="page-footer">
    © Universidad Tecnológica de Panamá &nbsp;|&nbsp; Desarrollo de Software VII &nbsp;|&nbsp;
    <a href="login.php">← Volver al login</a>
</p>

</body>
</html>
