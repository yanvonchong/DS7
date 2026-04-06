<!DOCTYPE html>
<html>
<head>
    <title>Validación de datos</title>

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

<h2>Formulario de usuario</h2>

<form method="post">
    Nombre:<br>
    <input type="text" name="nombre" required><br><br>

    Edad:<br>
    <input type="number" name="edad" required><br><br>

    <input type="submit" value="Enviar">
</form>

<?php
if ($_POST) {

    // TRIMMING (quitar espacios)
    $nombre = trim($_POST["nombre"]);

    // NORMALIZACIÓN (todo en minúsculas)
    $nombre = strtolower($nombre);

    // SANITIZACIÓN (evitar código malicioso)
    $nombre = htmlspecialchars($nombre);

    $edad = $_POST["edad"];

    echo "<h3>Nombre: $nombre</h3>";

    // Validar edad
    if ($edad >= 18) {
        echo "<h3>Eres mayor de edad</h3>";
    } else {
        echo "<h3>No eres mayor de edad</h3>";
    }
}
?>

</body>
</html>