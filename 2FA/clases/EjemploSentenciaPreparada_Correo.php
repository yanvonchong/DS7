<?PHP
/**
 * EjemploSentenciaPreparada_Correo.php
 * Verifica vía AJAX si el correo ya está registrado en la BD
 */
ini_set('display_errors', 1);
ini_set('log_errors', 1);

include("mysql.inc.php");
$clasePDO = new mod_db();
$conn     = $clasePDO->getConexion();

try {
    $email = $_POST['email'];

    $query = $conn->prepare("SELECT * FROM usuarios WHERE Correo = :email");
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->execute();

    $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultado) >= 1) {
        echo "existe";
    } else {
        echo "libre";
    }
} catch (PDOException $e) {
    echo "❌ Ya existe este Artículo con esta descripción: " . $e->getMessage();
}
?>
