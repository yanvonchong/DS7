<?php
/**
 * Clase Sanitizer — Data Cleaning / Data Sanitization
 * Limpia y normaliza los datos de entrada con métodos estáticos.
 * (Programación Orientada a Objetos - Métodos Estáticos)
 */
class Sanitizer {

    /**
     * Elimina etiquetas HTML, escapa caracteres especiales y recorta espacios.
     */
    public static function sanitizeString(string $value): string {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Convierte a entero seguro.
     */
    public static function sanitizeInt(mixed $value): int {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitiza un correo electrónico.
     */
    public static function sanitizeEmail(string $value): string {
        return strtolower(trim((string) filter_var($value, FILTER_SANITIZE_EMAIL)));
    }

    /**
     * Sanitiza un número de celular: solo dígitos, +, -, espacios.
     */
    public static function sanitizeCelular(string $value): string {
        return preg_replace('/[^0-9\+\-\s]/', '', trim($value));
    }

    /**
     * DATA CLEANING: convierte texto a formato Título (primera letra en mayúscula).
     * Funciona correctamente con caracteres UTF-8 (tildes, ñ, etc.).
     *
     * Ejemplo: "JUAN carlos"  → "Juan Carlos"
     */
    public static function toTitleCase(string $value): string {
        $value = mb_strtolower(trim($value), 'UTF-8');
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Sanitiza un array de IDs (áreas de interés).
     * @param mixed $areas
     * @return int[]
     */
    public static function sanitizeAreas(mixed $areas): array {
        if (!is_array($areas)) return [];
        return array_map('intval', $areas);
    }

    /**
     * Sanitiza todos los campos del formulario de una vez.
     *
     * @param array $data  Datos crudos del POST
     * @return array       Datos limpios
     */
    public static function sanitizeAll(array $data): array {
        return [
            'identidad'     => self::sanitizeString($data['identidad']    ?? ''),
            'nombre'        => self::toTitleCase($data['nombre']           ?? ''),
            'apellido'      => self::toTitleCase($data['apellido']         ?? ''),
            'edad'          => self::sanitizeInt($data['edad']             ?? 0),
            'sexo'          => self::sanitizeString($data['sexo']          ?? ''),
            'id_pais'       => self::sanitizeInt($data['id_pais']          ?? 0),
            'nacionalidad'  => self::toTitleCase($data['nacionalidad']     ?? ''),
            'correo'        => self::sanitizeEmail($data['correo']         ?? ''),
            'celular'       => self::sanitizeCelular($data['celular']      ?? ''),
            'observaciones' => self::sanitizeString($data['observaciones'] ?? ''),
            'areas'         => self::sanitizeAreas($data['areas']          ?? []),
        ];
    }
}
