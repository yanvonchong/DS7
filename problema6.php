<!DOCTYPE html>
<html>
<head>
    <title>Calculadora</title>

    <style>
        body {
            text-align: center;
            margin-top: 100px;
            font-family: Arial;
        }

        form {
            display: inline-block;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<h2>Calculadora básica</h2>

<form method="post">
    Número 1:<br>
    <input type="number" name="num1" required><br><br>

    Número 2:<br>
    <input type="number" name="num2" required><br><br>

    Operación:<br>
    <select name="operacion">
        <option value="suma">Suma</option>
        <option value="resta">Resta</option>
        <option value="multiplicacion">Multiplicación</option>
    </select><br><br>

    <input type="submit" value="Calcular">
</form>

<?php
if ($_POST) {
    $num1 = $_POST["num1"];
    $num2 = $_POST["num2"];
    $operacion = $_POST["operacion"];

    if ($operacion == "suma") {
        $resultado = $num1 + $num2;
    } elseif ($operacion == "resta") {
        $resultado = $num1 - $num2;
    } elseif ($operacion == "multiplicacion") {
        $resultado = $num1 * $num2;
    }

    echo "<h3>Resultado: $resultado</h3>";
}
?>

</body>
</html>