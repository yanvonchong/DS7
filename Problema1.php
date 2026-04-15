<?php
if ($_POST) {
    $radio = $_POST["radio"];
    $area = pi() * pow($radio, 2);
    echo "El área del círculo es: " . $area;
}
?>

<form method="post">
    Ingrese el radio: <input type="number" name="radio" step="any">
    <input type="submit" value="Calcular">
</form>