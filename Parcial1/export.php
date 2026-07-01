<?php
/**
 * Exportación a Excel con PhpSpreadsheet
 * Patrón igual al ejemplo del profesor (EjemploExcelIntegrado.php)
 *
 * Ruta: http://127.1.1.1/Parcial1/export.php
 *
 * Requiere: composer require phpoffice/phpspreadsheet
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/models/InscriptorModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;

// ── Obtener datos de la BD ──────────────────────────────────────────────────
$inscriptores = InscriptorModel::getAllForExport();

// ── Crear documento Spreadsheet ─────────────────────────────────────────────
$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("iTECH")
    ->setLastModifiedBy('Sistema iTECH')
    ->setTitle('Reporte de Inscriptores iTECH')
    ->setDescription('Inscriptores exportados desde la base de datos parcial1_itech');

// ── Hoja principal ──────────────────────────────────────────────────────────
$hoja = $documento->getActiveSheet();
$hoja->setTitle("Inscriptores");

// ── Encabezados ─────────────────────────────────────────────────────────────
$encabezado = [
    'N°', 'Integridad', 'Identidad', 'Nombre', 'Apellido',
    'Edad', 'Sexo', 'País de Residencia', 'Nacionalidad',
    'Correo', 'Celular', 'Áreas de Interés', 'Observaciones', 'Fecha Registro'
];
$hoja->fromArray($encabezado, null, 'A1');

// ── Estilo del encabezado ───────────────────────────────────────────────────
$rangoEncabezado = 'A1:N1';
$hoja->getStyle($rangoEncabezado)->applyFromArray([
    'font' => [
        'bold'  => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size'  => 11,
    ],
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '1A3C6E'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
]);

// ── Escribir datos fila por fila ────────────────────────────────────────────
$numeroDeFila = 2;

foreach ($inscriptores as $i => $reg) {
    $integro = $reg['integro'] ?? false;
    $badge   = $integro ? '✔ Íntegro' : '✘ Corrompido';

    $hoja->setCellValue('A' . $numeroDeFila, $i + 1);
    $hoja->setCellValue('B' . $numeroDeFila, $badge);
    $hoja->setCellValue('C' . $numeroDeFila, $reg['identidad']);
    $hoja->setCellValue('D' . $numeroDeFila, $reg['nombre']);
    $hoja->setCellValue('E' . $numeroDeFila, $reg['apellido']);
    $hoja->setCellValue('F' . $numeroDeFila, (int) $reg['edad']);
    $hoja->setCellValue('G' . $numeroDeFila, $reg['sexo']);
    $hoja->setCellValue('H' . $numeroDeFila, $reg['nombre_pais']);
    $hoja->setCellValue('I' . $numeroDeFila, $reg['nacionalidad']);
    $hoja->setCellValue('J' . $numeroDeFila, $reg['correo']);
    $hoja->setCellValue('K' . $numeroDeFila, $reg['celular']);
    $hoja->setCellValue('L' . $numeroDeFila, $reg['areas_str']);   // temas separados por comas
    $hoja->setCellValue('M' . $numeroDeFila, $reg['observaciones'] ?? '');
    $hoja->setCellValue('N' . $numeroDeFila, $reg['fecha_registro']);

    // Color de fila según integridad
    $colorFila = $integro ? 'E8F5E9' : 'FFEBEE';   // verde claro / rojo claro
    $hoja->getStyle('A' . $numeroDeFila . ':N' . $numeroDeFila)
         ->getFill()
         ->setFillType(Fill::FILL_SOLID)
         ->getStartColor()->setRGB($colorFila);

    $numeroDeFila++;
}

// ── Ancho automático de columnas ────────────────────────────────────────────
foreach (range('A', 'N') as $col) {
    $hoja->getColumnDimension($col)->setAutoSize(true);
}

// ── Bordes en toda la tabla ─────────────────────────────────────────────────
if ($numeroDeFila > 2) {
    $hoja->getStyle('A1:N' . ($numeroDeFila - 1))->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color'       => ['rgb' => 'B0BEC5'],
            ],
        ],
    ]);
}

// ── Guardar y enviar como descarga ──────────────────────────────────────────
$nombreArchivo = 'inscriptores_itech_' . date('Ymd_His') . '.xlsx';
$rutaTemporal  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nombreArchivo;

$writer = new Xlsx($documento);
$writer->save($rutaTemporal);

// Cabeceras HTTP para forzar descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Content-Length: ' . filesize($rutaTemporal));
header('Cache-Control: max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

readfile($rutaTemporal);
unlink($rutaTemporal);   // Eliminar archivo temporal
exit;
