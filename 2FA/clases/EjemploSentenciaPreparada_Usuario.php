<?PHP
/**
 * EjemploSentenciaPreparada_Usuario.php
 * Verifica vía AJAX si el nombre de usuario ya está registrado en la BD
 */
include("mysql.inc.php");
$clasePDO = new mod_db();
$conn     = $clasePDO->getConexion();

try {
    $usuario = trim($_POST['usuario'] ?? '');

    $query = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE Usuario = :usuario");
    $query->bindParam(":usuario", $usuario, PDO::PARAM_STR);
    $query->execute();

    $total = $query->fetchColumn();

    echo ($total >= 1) ? "existe" : "libre";

} catch (PDOException $e) {
    echo "libre"; // en caso de error, no bloquear al usuario
}
?>
