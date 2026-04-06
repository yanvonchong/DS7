<!DOCTYPE html>
<html>
<head>
    <title>Conversión pulgadas a cm</title>

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

<h2>Convertir pulgadas a centímetros</h2>

<form method="post">
    Ingrese pulgadas:<br><br>
    <input type="number" name="pulgadas" step="any" required><br><br>
    <input type="submit" value="Convertir">
</form>

<?php
if ($_POST) {
    $pulgadas = $_POST["pulgadas"];
    $cm = $pulgadas * 2.54;

    echo "<h3>Resultado: " . number_format($cm, 2) . " cm</h3>";
}
?>

</body>
</html>