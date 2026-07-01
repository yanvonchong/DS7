<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/AreaInteresModel.php';

/**
 * Modelo: Inscriptor
 * Maneja CRUD sobre `inscriptores` y la firma digital con OpenSSL.
 */
class InscriptorModel {

    // Rutas a las claves RSA
    private static string $privateKeyPath = __DIR__ . '/../keys/private.pem';
    private static string $publicKeyPath  = __DIR__ . '/../keys/public.pem';

    // ---------------------------------------------------------------
    // Firma digital (OpenSSL)
    // ---------------------------------------------------------------

    /**
     * Genera la cadena canónica de datos que se va a firmar.
     * Los campos se concatenan en orden fijo para que la verificación sea reproducible.
     */
    private static function buildSignaturePayload(array $data): string {
        return implode('|', [
            $data['identidad'],
            $data['nombre'],
            $data['apellido'],
            $data['correo'],
            $data['celular'],
            $data['sexo'],
        ]);
    }

    /**
     * Busca el archivo openssl.cnf en rutas comunes de WAMP.
     */
    private static function getOpensslConf(): ?string {
        $rutas = array_merge(
            glob('C:/wamp64/bin/php/*/extras/ssl/openssl.cnf') ?: [],
            glob('C:/wamp64/bin/apache/*/conf/openssl.cnf') ?: [],
            [
                'C:/Program Files/OpenSSL-Win64/bin/openssl.cfg',
                'C:/OpenSSL-Win64/bin/openssl.cfg',
            ]
        );
        foreach ($rutas as $r) { if (file_exists($r)) return $r; }
        return null;
    }

    /**
     * Firma los datos con la clave privada RSA.
     * Retorna la firma en base64 o cadena vacía si no hay clave.
     */
    public static function firmar(array $data): string {
        if (!file_exists(self::$privateKeyPath)) return '';
        if ($conf = self::getOpensslConf()) putenv('OPENSSL_CONF=' . $conf);
        // Leer el PEM directamente (evita problema con file:// en Windows)
        $privateKeyPem = file_get_contents(self::$privateKeyPath);
        $privateKey    = openssl_pkey_get_private($privateKeyPem);
        if (!$privateKey) return '';

        $payload   = self::buildSignaturePayload($data);
        $signature = '';
        openssl_sign($payload, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    /**
     * Verifica la firma de un inscriptor.
     * Retorna true si los datos no han sido alterados, false si están corrompidos.
     */
    public static function verificarFirma(array $inscriptor): bool {
        if (empty($inscriptor['firma'])) return false;
        if (!file_exists(self::$publicKeyPath)) return false;

        if ($conf = self::getOpensslConf()) putenv('OPENSSL_CONF=' . $conf);
        // Leer el PEM directamente (evita problema con file:// en Windows)
        $publicKeyPem = file_get_contents(self::$publicKeyPath);
        $publicKey    = openssl_pkey_get_public($publicKeyPem);
        if (!$publicKey) return false;

        $payload   = self::buildSignaturePayload($inscriptor);
        $signature = base64_decode($inscriptor['firma']);
        $result    = openssl_verify($payload, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        return $result === 1;
    }

    // ---------------------------------------------------------------
    // CRUD
    // ---------------------------------------------------------------

    /**
     * Guarda un nuevo inscriptor (con sus áreas) y retorna el ID insertado.
     * Usa transacción para garantizar integridad.
     */
    public static function create(array $data): int {
        $firma = self::firmar($data);

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $id = (int) Database::insert(
                "INSERT INTO inscriptores
                    (identidad, nombre, apellido, edad, sexo, id_pais, nacionalidad,
                     correo, celular, observaciones, firma)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $data['identidad'],
                    $data['nombre'],
                    $data['apellido'],
                    $data['edad'],
                    $data['sexo'],
                    $data['id_pais'],
                    $data['nacionalidad'],
                    $data['correo'],
                    $data['celular'],
                    $data['observaciones'],
                    $firma,
                ]
            );

            // Relación con áreas de interés
            foreach ($data['areas'] as $idArea) {
                AreaInteresModel::linkToInscriptor($id, (int)$idArea);
            }

            $pdo->commit();
            return $id;

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Retorna todos los inscriptores con su país y áreas (para el reporte).
     */
    public static function getAllWithDetails(): array {
        $stmt = Database::query(
            "SELECT i.*, p.nombre_pais
             FROM inscriptores i
             INNER JOIN paises p ON p.id_pais = i.id_pais
             ORDER BY i.fecha_registro DESC"
        );
        $rows = $stmt->fetchAll();

        // Añadir áreas y estado de firma a cada fila
        foreach ($rows as &$row) {
            $row['areas']   = AreaInteresModel::getByInscriptor((int)$row['id_inscriptor']);
            $row['integro'] = self::verificarFirma($row);
        }
        return $rows;
    }

    /**
     * Retorna todos los inscriptores (datos planos, para exportar a Excel).
     */
    public static function getAllForExport(): array {
        $rows = self::getAllWithDetails();
        // Aplanar áreas a string separado por comas
        foreach ($rows as &$row) {
            $row['areas_str'] = implode(', ', $row['areas']);
        }
        return $rows;
    }
}
