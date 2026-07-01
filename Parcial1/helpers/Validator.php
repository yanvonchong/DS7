<?php
/**
 * Clase Validator
 * Validación del lado del servidor con métodos estáticos.
 * (Programación Orientada a Objetos - Métodos Estáticos)
 */
class Validator {

    /**
     * Valida un número de identidad (cédula o pasaporte).
     * Acepta formato panameño: 8-8-8888 o solo dígitos/letras.
     */
    public static function validateIdentidad(string $value): bool {
        $v = trim($value);
        return $v !== '' && strlen($v) <= 20 && preg_match('/^[A-Za-z0-9\-]+$/', $v) === 1;
    }

    /**
     * Valida que el nombre solo contenga letras y espacios.
     */
    public static function validateNombre(string $value): bool {
        $v = trim($value);
        return $v !== '' && mb_strlen($v) <= 100
            && preg_match('/^[\p{L}\s\-]+$/u', $v) === 1;
    }

    /**
     * Valida que el apellido solo contenga letras y espacios.
     */
    public static function validateApellido(string $value): bool {
        return self::validateNombre($value); // misma lógica
    }

    /**
     * Valida que la edad sea un número entre 1 y 120.
     */
    public static function validateEdad(mixed $value): bool {
        return is_numeric($value) && (int)$value >= 1 && (int)$value <= 120;
    }

    /**
     * Valida el campo sexo.
     */
    public static function validateSexo(string $value): bool {
        return in_array($value, ['M', 'F', 'Otro'], true);
    }

    /**
     * Valida un correo electrónico.
     */
    public static function validateCorreo(string $value): bool {
        return filter_var(trim($value), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida un número de celular (7-15 dígitos, puede incluir +, -, espacios).
     */
    public static function validateCelular(string $value): bool {
        $digits = preg_replace('/[\s\-\+]/', '', trim($value));
        return $digits !== '' && preg_match('/^\d{7,15}$/', $digits) === 1;
    }

    /**
     * Valida que se haya seleccionado un país.
     */
    public static function validatePais(mixed $value): bool {
        return is_numeric($value) && (int)$value > 0;
    }

    /**
     * Valida que se haya seleccionado al menos un área de interés.
     */
    public static function validateAreas(mixed $value): bool {
        return is_array($value) && count($value) > 0;
    }

    /**
     * Valida todos los campos del formulario.
     * Retorna un array de mensajes de error (vacío = sin errores).
     *
     * @param array $data Datos del POST
     * @return string[]
     */
    public static function validateAll(array $data): array {
        $errors = [];

        if (!self::validateIdentidad($data['identidad'] ?? ''))
            $errors[] = 'La identidad es requerida y solo puede contener letras, números y guiones (máx. 20 caracteres).';

        if (!self::validateNombre($data['nombre'] ?? ''))
            $errors[] = 'El nombre es requerido y solo puede contener letras (máx. 100 caracteres).';

        if (!self::validateApellido($data['apellido'] ?? ''))
            $errors[] = 'El apellido es requerido y solo puede contener letras (máx. 100 caracteres).';

        if (!self::validateEdad($data['edad'] ?? ''))
            $errors[] = 'La edad debe ser un número entero entre 1 y 120.';

        if (!self::validateSexo($data['sexo'] ?? ''))
            $errors[] = 'Debe seleccionar un sexo válido (M, F u Otro).';

        if (!self::validatePais($data['id_pais'] ?? 0))
            $errors[] = 'Debe seleccionar un país de residencia.';

        if (!self::validateNombre($data['nacionalidad'] ?? ''))
            $errors[] = 'La nacionalidad es requerida y solo puede contener letras.';

        if (!self::validateCorreo($data['correo'] ?? ''))
            $errors[] = 'El correo electrónico no tiene un formato válido.';

        if (!self::validateCelular($data['celular'] ?? ''))
            $errors[] = 'El número de celular no es válido (7–15 dígitos).';

        if (!self::validateAreas($data['areas'] ?? null))
            $errors[] = 'Debe seleccionar al menos un área tecnológica de interés.';

        return $errors;
    }
}
