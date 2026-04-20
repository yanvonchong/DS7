<!DOCTYPE html>
<html>
<head>
    <title>Operaciones matemáticas</title>
</head>
<body style="text-align:center; margin-top:100px; font-family:Arial;">

<h2>Operaciones Matemáticas</h2>

<form method="post">
    Número 1:<br>
    <input type="number" name="num1" required><br><br>

    Número 2:<br>
    <input type="number" name="num2" required><br><br>

    <input type="submit" value="Calcular">
</form>

<?php
if ($_POST) {
    $num1 = $_POST["num1"];
    $num2 = $_POST["num2"];

    $suma = $num1 + $num2;
    $resta = $num1 - $num2;
    $multiplicacion = $num1 * $num2;

    echo "<h3>Suma: $suma</h3>";
    echo "<h3>Resta: $resta</h3>";
    echo "<h3>Multiplicación: $multiplicacion</h3>";
}
?>

</body>
</html>