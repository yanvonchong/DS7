<?php
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../helpers/Sanitizer.php';
require_once __DIR__ . '/../models/InscriptorModel.php';
require_once __DIR__ . '/../models/PaisModel.php';
require_once __DIR__ . '/../models/AreaInteresModel.php';

/**
 * Controlador: Inscriptor
 * Orquesta el flujo: recibe POST → valida → sanitiza → delega al modelo → selecciona vista.
 */
class InscriptorController {

    /**
     * Muestra el formulario de inscripción (GET).
     * También maneja el POST y redirige según resultado.
     */
    public static function showForm(): void {
        $errors   = [];
        $success  = false;
        $oldData  = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Validar datos crudos
            $errors = Validator::validateAll($_POST);

            if (empty($errors)) {
                // 2. Sanitizar y limpiar datos
                $clean = Sanitizer::sanitizeAll($_POST);

                try {
                    // 3. Guardar en BD (Modelo)
                    InscriptorModel::create($clean);
                    $success = true;
                } catch (Exception $e) {
                    // Error de BD (ej: cédula o correo duplicado)
                    $msg = $e->getMessage();
                    if (str_contains($msg, 'uq_identidad')) {
                        $errors[] = 'Ya existe un inscriptor con esa identidad.';
                    } elseif (str_contains($msg, 'uq_correo')) {
                        $errors[] = 'El correo ya está registrado.';
                    } else {
                        $errors[] = 'Error al guardar: ' . $msg;
                    }
                    $oldData = $_POST;
                }
            } else {
                $oldData = $_POST;
            }
        }

        $paises = PaisModel::getAll();
        $areas  = AreaInteresModel::getAll();

        require __DIR__ . '/../views/formulario.php';
    }

    /**
     * Muestra el reporte de inscriptores registrados.
     */
    public static function showReporte(): void {
        $inscriptores = InscriptorModel::getAllWithDetails();
        require __DIR__ . '/../views/reporte.php';
    }
}
