<!DOCTYPE html>
<html>
<head>
    <title>Área del círculo</title>
</head>
<body>

<h2>Calcular área de un círculo</h2>

<form method="post">
    Ingrese el radio: 
    <input type="number" name="radio" step="any" required>
    <input type="submit" value="Calcular">
</form>

<?php
if ($_POST) {
    $radio = $_POST["radio"];

    // Cálculo del área
    $area = pi() * pow($radio, 2);

    echo "<h3>El área es: " . number_format($area, 2) . "</h3>";
}
?>

</body>
</html>