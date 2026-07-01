<?php
/**
 * Clase Database
 * Maneja la conexión a la base de datos mediante PDO.
 * Patrón Singleton para reutilizar la conexión.
 */
class Database {

    private static string $host   = 'localhost';
    private static string $dbname = 'parcial1';
    private static string $user   = 'root';
    private static string $pass   = '';
    private static ?PDO  $conn   = null;

    // Evitar instanciación directa
    private function __construct() {}

    /**
     * Obtiene (o crea) la conexión PDO.
     */
    public static function getConnection(): PDO {
        if (self::$conn === null) {
            try {
                $dsn = "mysql:host=" . self::$host
                     . ";dbname=" . self::$dbname
                     . ";charset=utf8mb4";

                self::$conn = new PDO($dsn, self::$user, self::$pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die(json_encode([
                    'error' => 'Error de conexión a la base de datos: ' . $e->getMessage()
                ]));
            }
        }
        return self::$conn;
    }

    /**
     * Prepara y ejecuta una consulta con parámetros.
     *
     * @param string $sql    Consulta SQL con placeholders
     * @param array  $params Parámetros para los placeholders
     * @return PDOStatement
     */
    public static function query(string $sql, array $params = []): PDOStatement {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Inserta un registro y devuelve el último ID insertado.
     *
     * @param string $sql
     * @param array  $params
     * @return string Último ID insertado
     */
    public static function insert(string $sql, array $params = []): string {
        self::query($sql, $params);
        return self::getConnection()->lastInsertId();
    }

    /**
     * Cierra la conexión.
     */
    public static function disconnect(): void {
        self::$conn = null;
    }
}
