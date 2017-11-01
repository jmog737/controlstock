<?php

/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

//define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once('..\..\excel\PHPExcel.php');
//require_once ('..\..\excel\PHPxcel\Shared\String.php');

function generarExcelStock($registros, $total) {
  // Create new PHPExcel object
$objPHPExcel = new PHPExcel();

//PHPExcel_Shared_String::setDecimalSeparator(',');
//PHPExcel_Shared_String::setThousandsSeparator('.');

// Set document properties
$objPHPExcel->getProperties()->setCreator("Juan Martín Ortega")
							 ->setLastModifiedBy("Juan Martín Ortega")
							 ->setTitle("Stock")
							 ->setSubject("Datos exportados")
							 ->setDescription("Archivo excel con el resultado de la consulta realizada.")
							 ->setKeywords("stock excel php")
							 ->setCategory("Resultado");

/// Declaro hoja activa:
$hoja = $objPHPExcel->getSheet(0);

// Agrego los títulos:
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Id')
            ->setCellValue('B1', 'Entidad')
            ->setCellValue('C1', 'Nombre')
            ->setCellValue('D1', 'BIN')
            ->setCellValue('E1', 'Stock');
/// Formato de los títulos:
$header = 'A1:E1';
$hoja->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('AEE2FA');
$style = array(
    'font' => array('bold' => true,),
    'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
    );
$hoja->getStyle($header)->applyFromArray($style);
$hoja->setTitle('Stock');
$hoja->getTabColor()->setRGB('023184');

/// Datos de los campos:
foreach ($registros as $i => $dato) {
  /// Acomodo el índice pues empieza en 0, y en el 1 están los nombres de los campos:
  $i = $i + 2;
  $celda = 'A'.$i;
  $hoja->fromArray($dato, ' ', $celda);
  /// Borro el contenido de las alarmas que vienen en la consulta:
  $al1 = 'F'.$i;
  $al2 = 'G'.$i;
  $hoja->setCellValue($al1, '');
  $hoja->setCellValue($al2, ''); 
  $alarmas1[] = $hoja->getCell($al1)->getValue();
  $alarmas2[] = $hoja->getCell($al2)->getValue();
}

/// Agrego línea con el total del stock:
$j = $i+1;
$hoja->mergeCells('A'.$j.':D'.$j.'');
$hoja->setCellValue('A'.$j.'', 'TOTAL');
$celdaTotal = "E".$j;
$hoja->setCellValue($celdaTotal, $total);

/// Defino el formato para la celda con el texto "Total":
$styleArrayTotal = array(
	'fill' => array(
        'color' => array('rgb' => 'F3FF00'),
        'type' => 'solid',
    ),
    'font' => array(
        'bold' => true,
        'italic' => true,
        'size' => 14,
        'color' => array('rgb' => 'ff0000'),
    ),
    'alignment' => array(
       'wrap' => true,
       'horizontal' => 'center',
       'vertical' => 'bottom',
    ),
);
$hoja->getStyle($celdaTotal)->applyFromArray($styleArrayTotal);

/// Defino el formato para la celda con el total:
$styleTotal = array(
	'fill' => array(
        'color' => array('rgb' => 'AEE2FA'),
        'type' => 'solid',
    ),
    'font' => array(
        'bold' => true,
        'size' => 14,
    ),
    'alignment' => array(
       'wrap' => true,
       'horizontal' => 'center',
       'vertical' => 'middle',
    ),
    'numberformat' => array(
       'code' =>  '[Blue][>=100]$#,##0;[Red][<70]$#,##0;$#,##0',
    ),
);
$hoja->getRowDimension($j)->setRowHeight(18);
$hoja->getStyle('A'.$j.'')->applyFromArray($styleTotal);



/// Defino el formato para la columna con el STOCK:
$styleStock = array(
	'fill' => array(
        'color' => array('rgb' => 'AEE2FA'),
        'type' => 'solid',
    ),
    'font' => array(
        'bold' => true,
        'size' => 11,
    ),
    'alignment' => array(
       'wrap' => true,
       'horizontal' => 'center',
       'vertical' => 'middle',
    ),
);
$rangoStock = 'E2:E'.$i.'';
$hoja->getStyle($rangoStock)->applyFromArray($styleStock);

/// Ajusto el auto size para que las celdas no se vean cortadas:
for ($col = ord('a'); $col <= ord('e'); $col++)
  {
  $hoja->getColumnDimension(chr($col))->setAutoSize(true);   
}
/// Defino el rango de celdas con datos para poder darle formato a todas juntas:
$rango = "A1:E".$j;
/// Defino el formato para las celdas:
$styleArray = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THICK,
			'color' => array('rgb' => '023184'),
		),
	),
    'alignment' => array(
       'wrap' => true,
       'horizontal' => 'center',
    )
);
$hoja->getStyle($rango)->applyFromArray($styleArray);


// Se guarda como Excel 2007:
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$dir = "D:/PROCESOS/PDFS";
$timestamp = date('Ymd_His');
$nombreArchivo = "reporteStock".$timestamp.".xlsx";
$salida = $dir."/".$nombreArchivo;
$objWriter->save($salida);
}
