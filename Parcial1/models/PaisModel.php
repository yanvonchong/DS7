<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Modelo: Pais
 * Maneja operaciones CRUD sobre la tabla `paises`.
 */
class PaisModel {

    /**
     * Retorna todos los países ordenados alfabéticamente.
     */
    public static function getAll(): array {
        $stmt = Database::query("SELECT id_pais, nombre_pais FROM paises ORDER BY nombre_pais");
        return $stmt->fetchAll();
    }

    /**
     * Busca un país por su ID.
     */
    public static function getById(int $id): array|false {
        $stmt = Database::query(
            "SELECT id_pais, nombre_pais FROM paises WHERE id_pais = ?",
            [$id]
        );
        return $stmt->fetch();
    }
}
