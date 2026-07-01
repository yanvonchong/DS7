<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Modelo: AreaInteres
 * Maneja operaciones sobre la tabla `areas_interes`.
 */
class AreaInteresModel {

    /**
     * Retorna todas las áreas de interés.
     */
    public static function getAll(): array {
        $stmt = Database::query("SELECT id_area, nombre_area FROM areas_interes ORDER BY id_area");
        return $stmt->fetchAll();
    }

    /**
     * Retorna las áreas de un inscriptor como array de nombres.
     */
    public static function getByInscriptor(int $idInscriptor): array {
        $stmt = Database::query(
            "SELECT a.nombre_area
             FROM areas_interes a
             INNER JOIN inscriptor_areas ia ON ia.id_area = a.id_area
             WHERE ia.id_inscriptor = ?
             ORDER BY a.id_area",
            [$idInscriptor]
        );
        return array_column($stmt->fetchAll(), 'nombre_area');
    }

    /**
     * Inserta la relación inscriptor ↔ área.
     */
    public static function linkToInscriptor(int $idInscriptor, int $idArea): void {
        Database::query(
            "INSERT IGNORE INTO inscriptor_areas (id_inscriptor, id_area) VALUES (?, ?)",
            [$idInscriptor, $idArea]
        );
    }
}
