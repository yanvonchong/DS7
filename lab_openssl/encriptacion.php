<?php

/*
|--------------------------------------------------------------------------
| VARIABLES INICIALES
|--------------------------------------------------------------------------
| Estas variables almacenarán:
| - El mensaje ingresado
| - La clave secreta
| - El IV en hexadecimal
| - El texto cifrado
| - El texto descifrado
*/

$mensaje = "";
$clave = "";
$ivHex = "";
$cifrado = "";
$descifrado = "";

/*
|--------------------------------------------------------------------------
| VALIDAR SI EL FORMULARIO FUE ENVIADO
|--------------------------------------------------------------------------
| Se verifica si el método es POST para procesar los datos.
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /*
    |--------------------------------------------------------------------------
    | OBTENER LOS DATOS DEL FORMULARIO
    |--------------------------------------------------------------------------
    | trim() elimina espacios vacíos al inicio y final.
    */

    $mensaje = trim($_POST["mensaje"]);
    $clave = trim($_POST["clave"]);

    /*
    |--------------------------------------------------------------------------
    | VALIDAR QUE LOS CAMPOS NO ESTÉN VACÍOS
    |--------------------------------------------------------------------------
    */

    if (!empty($mensaje) && !empty($clave)) {

        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR LA CLAVE A 16 CARACTERES
        |--------------------------------------------------------------------------
        | AES-128 necesita una clave exactamente de 16 caracteres.
        | substr() corta la cadena.
        | str_pad() rellena con ceros si faltan caracteres.
        */

        $clave = str_pad(substr($clave, 0, 16), 16, "0");

        /*
        |--------------------------------------------------------------------------
        | GENERAR VECTOR DE INICIALIZACIÓN (IV)
        |--------------------------------------------------------------------------
        | openssl_random_pseudo_bytes() genera bytes aleatorios.
        | openssl_cipher_iv_length() obtiene el tamaño correcto del IV.
        */

        $iv = openssl_random_pseudo_bytes(
            openssl_cipher_iv_length("AES-128-CBC")
        );

        /*
        |--------------------------------------------------------------------------
        | CIFRAR EL MENSAJE
        |--------------------------------------------------------------------------
        | openssl_encrypt():
        | - Cifra el mensaje usando AES-128-CBC.
        */

        $cifrado = openssl_encrypt(
            $mensaje,
            "AES-128-CBC",
            $clave,
            0,
            $iv
        );

        /*
        |--------------------------------------------------------------------------
        | DESCIFRAR EL MENSAJE
        |--------------------------------------------------------------------------
        | openssl_decrypt():
        | - Recupera el mensaje original.
        */

        $descifrado = openssl_decrypt(
            $cifrado,
            "AES-128-CBC",
            $clave,
            0,
            $iv
        );

        /*
        |--------------------------------------------------------------------------
        | CONVERTIR EL IV A HEXADECIMAL
        |--------------------------------------------------------------------------
        | bin2hex() transforma bytes binarios en texto hexadecimal.
        */

        $ivHex = bin2hex($iv);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Laboratorio OpenSSL</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background: linear-gradient(to right, #141e30, #243b55);
            min-height: 100vh;
            color: white;
        }

        .card-custom{
            border-radius: 20px;
            border: none;
        }

        .title{
            font-weight: bold;
            text-align: center;
        }

        .result-box{
            background: #f8f9fa;
            color: black;
            padding: 15px;
            border-radius: 10px;
            word-wrap: break-word;
        }

        textarea{
            resize: none;
        }

    </style>

</head>

<body>

<div class="container py-5">

    <!-- TARJETA PRINCIPAL -->
    <div class="card card-custom shadow-lg p-4">

        <h1 class="title text-dark mb-4">
            🔐 Cifrado con OpenSSL
        </h1>

        <!-- FORMULARIO -->
        <form method="POST">

            <!-- MENSAJE -->
            <div class="mb-3">

                <label class="form-label text-dark">
                    Mensaje en Claro
                </label>

                <textarea
                    name="mensaje"
                    class="form-control"
                    rows="5"
                    required
                ><?= htmlspecialchars($mensaje) ?></textarea>

            </div>

            <!-- CLAVE -->
            <div class="mb-3">

                <label class="form-label text-dark">
                    Clave Secreta
                </label>

                <input
                    type="text"
                    name="clave"
                    class="form-control"
                    value="<?= htmlspecialchars($clave) ?>"
                    required
                >

            </div>

            <!-- BOTÓN -->
            <button class="btn btn-primary w-100">
                🔒 Cifrar Mensaje
            </button>

        </form>

    </div>

    <!-- RESULTADOS -->
    <?php if (!empty($cifrado)) : ?>

        <div class="card card-custom shadow-lg p-4 mt-4">

            <h2 class="text-dark mb-4">
                ✅ Resultados del Proceso
            </h2>

            <!-- IV -->
            <div class="mb-3">

                <h5 class="text-dark">
                    Vector de Inicialización (IV)
                </h5>

                <div class="result-box">
                    <?= $ivHex ?>
                </div>

            </div>

            <!-- TEXTO CIFRADO -->
            <div class="mb-3">

                <h5 class="text-dark">
                    Texto Cifrado
                </h5>

                <div class="result-box">
                    <?= $cifrado ?>
                </div>

            </div>

            <!-- TEXTO DESCIFRADO -->
            <div class="mb-3">

                <h5 class="text-dark">
                    Texto Descifrado
                </h5>

                <div class="result-box">
                    <?= $descifrado ?>
                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>