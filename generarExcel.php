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

$locale = 'es_UY'; 
$validLocale = PHPExcel_Settings::setLocale($locale); 
if (!$validLocale) { echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n"; }

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

$hoja->setTitle('Stock');
$hoja->getTabColor()->setRGB('023184');

// Agrego los títulos:
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Id')
            ->setCellValue('B1', 'Entidad')
            ->setCellValue('C1', 'Nombre')
            ->setCellValue('D1', 'BIN')
            ->setCellValue('E1', 'Stock');
/// Formato de los títulos:
$header = 'A1:E1';
$styleHeader = array(
    'fill' => array(
        'color' => array('rgb' => 'AEE2FA'),
        'type' => 'solid',
      ),
    'font' => array(
        'bold' => true,
      ),
    'alignment' => array(
        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
      ),
    );
$hoja->getStyle($header)->applyFromArray($styleHeader);

/// Datos de los campos:
foreach ($registros as $i => $dato) {
  /// Acomodo el índice pues empieza en 0, y en el 1 están los nombres de los campos:
  $i = $i + 2;
  $celda = 'A'.$i;
  $hoja->fromArray($dato, ' ', $celda);
}

/// Agrego línea con el total del stock:
$j = $i+1;
$hoja->mergeCells('A'.$j.':D'.$j.'');
$hoja->setCellValue('A'.$j.'', 'TOTAL');
$celdaTotalTarjetas = "E".$j;
$hoja->setCellValue($celdaTotalTarjetas, $total);


/// Defino el formato para la celda con el total de tarjetas:
$styleTotalPlasticos = array(
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
    'numberformat' => array(
        'code' => '#,###0',
    ),
);
$hoja->getStyle($celdaTotalTarjetas)->applyFromArray($styleTotalPlasticos);

/// Defino el formato para la celda con el texto "Total":
$styleTextoTotal = array(
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
);
$hoja->getRowDimension($j)->setRowHeight(18);
$hoja->getStyle('A'.$j.'')->applyFromArray($styleTextoTotal);


/// Defino el formato para la columna con el STOCK:
$styleColumnaStock = array(
	'fill' => array(
        'color' => array('rgb' => 'A9FF96'),
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
    'numberformat' => array(
        'code' => '[Blue]#,##0',
    ),
);
$rangoStock = 'E2:E'.$i.'';
$hoja->getStyle($rangoStock)->applyFromArray($styleColumnaStock);

/// Defino estilos para las alarmas 1 y 2:
$styleAl1 = array(
    'fill' => array(
        'color' => array ('rgb' => 'FAFF98'),
        'type' => 'solid',
    ),
);
        
$styleAl2 = array(
    'fill' => array(
        'color' => array ('rgb' => 'FC4A3F'),
        'type' => 'solid',
    ),
);

/// Aplico color de fondo de la columna de stock según el valor y las alarmas para dicho produco:
for ($k = 2; $k < 40; $k++) {
  $al1 = "F".$k;
  $al2 = "G".$k;
  $celda = "E".$k;
  $valorAlarma1 = $hoja->getCell($al1)->getValue();
  $valorAlarma2 = $hoja->getCell($al2)->getValue();
  $valorCelda = $hoja->getCell($celda)->getValue();

  if (($valorCelda > $valorAlarma2) && ($valorCelda < $valorAlarma1)){
    $hoja->getStyle($celda)->applyFromArray($styleAl1);
  }
  
  if ($valorCelda < $valorAlarma2) {
    $hoja->getStyle($celda)->applyFromArray($styleAl2);
  }
  /// Borro el contenido de las alarmas que vienen en la consulta:
  $hoja->setCellValue($al1, '');
  $hoja->setCellValue($al2, ''); 
}  

/// Ajusto el auto size para que las celdas no se vean cortadas:
for ($col = ord('a'); $col <= ord('e'); $col++)
  {
  $hoja->getColumnDimension(chr($col))->setAutoSize(true);   
}

/// Defino el rango de celdas con datos para poder darle formato a todas juntas:
$rango = "A1:E".$j;
/// Defino el formato para las celdas:
$styleGeneral = array(
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
$hoja->getStyle($rango)->applyFromArray($styleGeneral);


// Se guarda como Excel 2007:
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$dir = "D:/PROCESOS/PDFS";
$timestamp = date('Ymd_His');
$nombreArchivo = "reporteStock".$timestamp.".xlsx";
$salida = $dir."/".$nombreArchivo;
$objWriter->save($salida);
return $nombreArchivo;
}

function generarExcelMovimientos($registros) {
  // Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$locale = 'es_UY'; 
$validLocale = PHPExcel_Settings::setLocale($locale); 
if (!$validLocale) { echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n"; }

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

$hoja->setTitle('Movimientos');
$hoja->getTabColor()->setRGB('E02309');

// Agrego los títulos:
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Id')
            ->setCellValue('B1', 'Fecha')
            ->setCellValue('C1', 'Hora')
            ->setCellValue('D1', 'Entidad')
            ->setCellValue('E1', 'Nombre')
            ->setCellValue('F1', 'BIN')
            ->setCellValue('G1', 'Tipo')
            ->setCellValue('H1', 'Cantidad')
            ->setCellValue('I1', 'Comentarios');
/// Formato de los títulos:
$header = 'A1:I1';
$styleHeader = array(
    'font' => array(
        'bold' => true,
      ),
    'alignment' => array(
        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
      ),
    'fill' => array(
        'color' => array('rgb' => 'AEE2FA'),
        'type' => 'solid',
      ),
    );
$hoja->getStyle($header)->applyFromArray($styleHeader);

/// Datos de los campos:
foreach ($registros as $i => $dato) {
  /// Acomodo el índice pues empieza en 0, y en el 1 están los nombres de los campos:
  $i = $i + 2;
  $celda = 'A'.$i;
  $hoja->fromArray($dato, ' ', $celda);
}
$j = $i+1;
/*
/// Agrego línea con el total del stock:
$j = $i+1;
$hoja->mergeCells('A'.$j.':D'.$j.'');
$hoja->setCellValue('A'.$j.'', 'TOTAL');
$celdaTotalTarjetas = "E".$j;
$hoja->setCellValue($celdaTotalTarjetas, $total);


/// Defino el formato para la celda con el total de tarjetas:
$styleTotalPlasticos = array(
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
    'numberformat' => array(
        'code' => '#,###0',
    ),
);
$hoja->getStyle($celdaTotalTarjetas)->applyFromArray($styleTotalPlasticos);

/// Defino el formato para la celda con el texto "Total":
$styleTextoTotal = array(
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
);
$hoja->getRowDimension($j)->setRowHeight(18);
$hoja->getStyle('A'.$j.'')->applyFromArray($styleTextoTotal);
*/

/// Defino el formato para la columna con el STOCK:
$styleColumnaStock = array(
	'fill' => array(
        'color' => array('rgb' => 'A9FF96'),
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
    'numberformat' => array(
        'code' => '[Blue]#,##0',
    ),
);
$rangoStock = 'H2:H'.$i.'';
$hoja->getStyle($rangoStock)->applyFromArray($styleColumnaStock);


/// Estilos para las alarmas aunque en el caso de los movimientos no tiene sentido: REVISAR!!!:
$styleAl1 = array(
    'fill' => array(
        'color' => array ('rgb' => 'FAFF98'),
        'type' => 'solid',
    ),
);
        
$styleAl2 = array(
    'fill' => array(
        'color' => array ('rgb' => 'FC4A3F'),
        'type' => 'solid',
    ),
);

/// Aplico color de fondo de la columna de stock según el valor y las alarmas para dicho produco:
for ($k = 2; $k <= $i; $k++) {
  $al1 = "J".$k;
  $al2 = "K".$k;
  $celda = "H".$k;
  $valorAlarma1 = $hoja->getCell($al1)->getValue();
  $valorAlarma2 = $hoja->getCell($al2)->getValue();
  $valorCelda = $hoja->getCell($celda)->getValue();

  if (($valorCelda > $valorAlarma2) && ($valorCelda < $valorAlarma1)){
    $hoja->getStyle($celda)->applyFromArray($styleAl1);
  }
  
  if ($valorCelda < $valorAlarma2) {
    $hoja->getStyle($celda)->applyFromArray($styleAl2);
  }
  /// Borro el contenido de las alarmas que vienen en la consulta:
  $hoja->setCellValue($al1, '');
  $hoja->setCellValue($al2, ''); 
}  

/// Ajusto el auto size para que las celdas no se vean cortadas:
for ($col = ord('a'); $col <= ord('i'); $col++)
  {
  $hoja->getColumnDimension(chr($col))->setAutoSize(true);   
}

/// Defino el rango de celdas con datos para poder darle formato a todas juntas:
$rango = "A1:I".$i;
/// Defino el formato para las celdas:
$styleGeneral = array(
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
$hoja->getStyle($rango)->applyFromArray($styleGeneral);


// Se guarda como Excel 2007:
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$dir = "D:/PROCESOS/PDFS";
$timestamp = date('Ymd_His');
$nombreArchivo = "reporteMovimientos".$timestamp.".xlsx";
$salida = $dir."/".$nombreArchivo;
$objWriter->save($salida);
return $nombreArchivo;
}
