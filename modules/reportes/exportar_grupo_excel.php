<?php
require '../../includes/conexion.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Crear un nuevo documento de Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Título del documento
$sheet->setCellValue('A1', 'Reporte de Socios por Asociación');
$sheet->mergeCells('A1:E1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Encabezados de la tabla
$headers = ['N°', 'Asociación', 'Cantidad de Socios'];
$sheet->fromArray($headers, NULL, 'A3');

// Aplicar estilos a los encabezados
$headerStyle = $sheet->getStyle('A3:C3');
$headerStyle->getFont()->setBold(true);
$headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('D9D9D9');
$headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Consulta para obtener la cantidad de socios por grupo
$query = "
    SELECT g.nombre_grupo AS asociacion, COUNT(sa.socio_idsocio) AS cantidad_socios
    FROM socio_asociacion sa
    JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
    GROUP BY sa.grupo_idgrupo
    ORDER BY g.nombre_grupo ASC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $rowNumber = 4;
    $contador = 1;

    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A{$rowNumber}", $contador);
        $sheet->setCellValue("B{$rowNumber}", $row['asociacion']);
        $sheet->setCellValue("C{$rowNumber}", $row['cantidad_socios']);

        $contador++;
        $rowNumber++;
    }

    // Aplicar bordes a la tabla
    $lastRow = $rowNumber - 1;
    $tableStyle = $sheet->getStyle("A3:C{$lastRow}");
    $tableStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new Color('000000'));

    // Autoajustar el tamaño de las columnas
    foreach (range('A', 'C') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}

// Nombre del archivo y configuración para descarga
$filename = 'Reporte_Socios_Asociaciones.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
