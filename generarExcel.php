<?php
session_start();
/**
******************************************************
*  @file generarExcel.php
*  @brief Archivo con las funciones que generan los archivos de excel.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Noviembre 2017
*
*******************************************************/

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once("data/config.php");

//phpinfo();
///********************************************************************** INICIO SETEO DE CARPETAS ************************************************************
//// AHORA SE SETEAN EN EL CONFIG.PHP
//$ip = "192.168.1.145";

//$dirExcel = "//".$ip."/Reportes/";

///*********************************************************************** FIN SETEO DE CARPETAS **************************************************************

function generarExcelStock($registros, $total) {
  global $nombreReporte;
  
$spreadsheet = new Spreadsheet();

$locale = 'es_UY'; 
$validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale); 
if (!$validLocale) { echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n"; }


// Set document properties
$spreadsheet->getProperties()->setCreator("Juan Martín Ortega")
							 ->setLastModifiedBy("Juan Martín Ortega")
							 ->setTitle("Stock")
							 ->setSubject("Datos exportados")
							 ->setDescription("Archivo excel con el resultado de la consulta realizada.")
							 ->setKeywords("stock excel php")
							 ->setCategory("Resultado");

/// Declaro hoja activa:
$hoja = $spreadsheet->getSheet(0);

$hoja->setTitle($nombreReporte);
$hoja->getTabColor()->setRGB('023184');

// Agrego los títulos:
$spreadsheet->setActiveSheetIndex(0)
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
      'fillType' => 'solid',
    ),
  'font' => array(
      'bold' => true,
    ),
  'alignment' => array(
      'horizontal' => HORIZONTAL_CENTER,
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
        'fillType' => 'solid',
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
    'numberFormat' => array(
        'formatCode' => '#,###0',
    ),
);
$hoja->getStyle($celdaTotalTarjetas)->applyFromArray($styleTotalPlasticos);

/// Defino el formato para la celda con el texto "Total":
$styleTextoTotal = array(
	'fill' => array(
        'color' => array('rgb' => 'AEE2FA'),
        'fillType' => 'solid',
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
        'fillType' => 'solid',
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
    'numberFormat' => array(
        'formatCode' => '[Blue]#,##0',
    ),
);
$rangoStock = 'E2:E'.$i.'';
$hoja->getStyle($rangoStock)->applyFromArray($styleColumnaStock);

/// Defino estilos para las alarmas 1 y 2:
$styleAl1 = array(
    'fill' => array(
        'color' => array ('rgb' => 'FAFF98'),
        'fillType' => 'solid',
    ),
);
        
$styleAl2 = array(
    'fill' => array(
        'color' => array ('rgb' => 'FC4A3F'),
        'fillType' => 'solid',
    ),
);

/// Aplico color de fondo de la columna de stock según el valor y las alarmas para dicho produco:
for ($k = 2; $k <= $i; $k++) {
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
		'allBorders' => array(
			'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
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
$writer = new Xlsx($spreadsheet);

$timestamp = date('dmY_His');
$nombreArchivo = $nombreReporte."_".$timestamp.".Xlsx";
$salida = $GLOBALS["dirExcel"]."/".$nombreArchivo;
$writer->save($salida);

return $nombreArchivo;
}


function generarExcelMovimientos($registros) {
  global $nombreReporte;
  
  $spreadsheet = new Spreadsheet();

  $locale = 'es_UY'; 
  $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale); 
  if (!$validLocale) { echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n"; }

  ///**************************************** PARAMETROS BASICOS ***************************************
  // Set document properties
  $spreadsheet->getProperties()->setCreator("Juan Martín Ortega")
                               ->setLastModifiedBy("Juan Martín Ortega")
                               ->setTitle("Stock")
                               ->setSubject("Datos exportados")
                               ->setDescription("Archivo excel con el resultado de la consulta realizada.")
                               ->setKeywords("stock excel php")
                               ->setCategory("Resultado");

  //$spreadsheet->getDefaultStyle()->getFont()->setName('Courier New');
  
  /// Declaro hoja activa:
  $hoja = $spreadsheet->getSheet(0);

  $hoja->setTitle($nombreReporte);
  $hoja->getTabColor()->setRGB('E02309');
  ///************************************ FIN PARAMETROS BASICOS ***************************************

  ///**************************************** INICIO formato encabezado ********************************
  // Agrego los títulos:
  $spreadsheet->setActiveSheetIndex(0)
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
          'horizontal' => HORIZONTAL_CENTER,
        ),
      'fill' => array(
          'color' => array('rgb' => 'AEE2FA'),
          'fillType' => 'solid',
        ),
      );
  $hoja->getStyle($header)->applyFromArray($styleHeader);
  ///******************************************** FIN formato encabezado *******************************
  
  ///*************************************** ESCRIBO DATOS *********************************************
  
  /// Datos de los campos:
  foreach ($registros as $i => $dato) {
    ///Elimino el primer elemento del array dado que se agregó idprod como primer elemento para no tener
    ///problemas con los nombres de producto repetidos. El array con los idprod queda en $dato1:
    $dato1 = array_shift($dato);
    /// Acomodo el índice pues empieza en 0, y en el 1 están los nombres de los campos:
    $i = $i + 2;
    $celda = 'A'.$i;
    $hoja->fromArray($dato, ' ', $celda);
  }
  $j = $i+1;
  ///*************************************** FIN ESCRIBO DATOS *****************************************
  
  ///************************************ MUESTRO TOTALES **********************************************
  $hoja->mergeCells('L1:Q1');
  $hoja->setCellValue("L1", "TOTALES");
  $hoja->setCellValue("L2", "Nombre");
  $hoja->setCellValue("M2", "Retiros");
  $hoja->setCellValue("N2", "Renovaciones");
  $hoja->setCellValue('O2', 'Destrucciones');
  $hoja->setCellValue('P2', 'Consumos');
  $hoja->setCellValue('Q2', 'Ingresos');
  
  $resumen = Array();
  $ids = Array();
  $id = '';
  foreach ($registros as $datito){
    switch ($datito[7]){
      case 'Retiro':  $resumen["$datito[0]"]['retiros'] = $resumen["$datito[0]"]['retiros'] + $datito[8];
                      break;
      case 'Renovación':  $resumen["$datito[0]"]['renos'] = $resumen["$datito[0]"]['renos'] + $datito[8];
                          break;
      case 'Destrucción': $resumen["$datito[0]"]['destrucciones'] = $resumen["$datito[0]"]['destrucciones'] + $datito[8];
                          break;
      case 'Ingreso': $resumen["$datito[0]"]['ingresos'] = $resumen["$datito[0]"]['ingresos'] + $datito[8];
                      break;
      default: break;
    }
    if ($id !== $datito[0]){
       $ids[] = $datito[0];
       $id = $datito[0];
       $resumen["$datito[0]"]['nombre'] = $datito[5];
    }
  }
  
  $n = 3;
  foreach ($ids as $id1){
    if ((!isset($resumen["$id1"]['retiros']))) {
      $resumen["$id1"]['retiros'] = 0;
    }
    if ((!isset($resumen["$id1"]['ingresos']))) {
      $resumen["$id1"]['ingresos'] = 0;
    }
    if ((!isset($resumen["$id1"]['renos']))) {
      $resumen["$id1"]['renos'] = 0;
    }
    if ((!isset($resumen["$id1"]['destrucciones']))) {
      $resumen["$id1"]['destrucciones'] = 0;
    }
    $hoja->setCellValue('L'.$n, $resumen["$id1"]['nombre']);
    $hoja->setCellValue('M'.$n, $resumen["$id1"]['retiros']);
    $hoja->setCellValue('N'.$n, $resumen["$id1"]['renos']);
    $hoja->setCellValue('O'.$n, $resumen["$id1"]['destrucciones']);
    $hoja->setCellValue('P'.$n,'=M'.$n.'+N'.$n.'+O'.$n);
    $hoja->setCellValue('Q'.$n, $resumen["$id1"]['ingresos']);
    $n++;
  }
  $finDatos = $n - 1;
  
  ///Línea con el total por categoría y totales generales:
  $hoja->setCellValue("L".$n, "TOTALES");
  $hoja->setCellValue('M'.$n, '=SUM(M3:M'.$finDatos.')');
  $hoja->setCellValue('N'.$n, '=SUM(N3:N'.$finDatos.')');
  $hoja->setCellValue('O'.$n, '=SUM(O3:O'.$finDatos.')');
  $hoja->setCellValue('P'.$n, '=SUM(P3:P'.$finDatos.')');
  $hoja->setCellValue('Q'.$n, '=SUM(Q3:Q'.$finDatos.')');
  
  ///**************************************** Formato Título *******************************************
  $header1 = 'L1:Q1';
  $styleTituloTotales = array(
      'font' => array(
          'bold' => true,
          'underline' => true,
        ),
      'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ), 
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'center',
         'vertical' => 'middle',
      ),
      'fill' => array(
          'color' => array('rgb' => 'AEE2FA'),
          'fillType' => 'solid',
        ),
      );
  $hoja->getStyle($header1)->applyFromArray($styleTituloTotales);
  ///************************************** FIN Formato Título *****************************************
  
  ///************************************ Formato nombre CAMPOS ****************************************
  $nombreCampos = 'L2:Q2';
  $styleCamposTotales = array(
    'fill' => array(
          'color' => array('rgb' => 'b3a8ac'),
          'fillType' => 'solid',
        ),
    'font' => array(
        'bold' => true,
      ),
    'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ), 
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'center',
         'vertical' => 'middle',
      ),
  );
  $hoja->getStyle($nombreCampos)->applyFromArray($styleCamposTotales);
  ///************************************ FIN Formato nombre CAMPOS ************************************
  
  ///************************************ Formato Nombre Productos *************************************
  $nombreProductos = 'L3:L'.$finDatos;
  $styleNombreProductos = array(
    'font' => array(
        'bold' => true,
        'italic' => true,
      ),
    'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ), 
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'left',
         'vertical' => 'middle',
      ),
  );
  $hoja->getStyle($nombreProductos)->applyFromArray($styleNombreProductos);
  ///********************************** FIN Formato Nombre Productos ***********************************
  
  ///************************************ Formato Totales Categoría ************************************
  $rangoTotales = 'M3:O'.$n;
  $styleTotales = array(
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'right',
         'vertical' => 'middle',
      ),
    'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ),  
    'numberFormat' => array(
        'formatCode' => '[Blue]#,##0',
      ),
  );
  $hoja->getStyle($rangoTotales)->applyFromArray($styleTotales);
  ///********************************** FIN Formato Totales Categoría **********************************
  
  ///***************************************** Formato Consumos ****************************************
  $rangoConsumos = 'P3:P'.$finDatos;
  $colorConsumos = array(
    'fill' => array(
          'color' => array('rgb' => 'ffff99'),
          'fillType' => 'solid',
        ),
    'font' => array(
        'bold' => true,
        'size' => 13,
      ),
    'numberFormat' => array(
        'formatCode' => '[Red]#,##0',
      ),
    'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ), 
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'right',
         'vertical' => 'middle',
      ),
  );
  $hoja->getStyle($rangoConsumos)->applyFromArray($colorConsumos);
  ///*************************************** FIN Formato Consumos **************************************
  
  ///***************************************** Formato Ingresos ****************************************
  $rangoIngresos = 'Q3:Q'.$finDatos;
  $resaltarIngresos = array(
    'fill' => array(
          'color' => array('rgb' => 'cefdd5'),
          'fillType' => 'solid',
        ),
    'font' => array(
        'bold' => true,
        'size' => 13,
      ),
    'numberFormat' => array(
        'formatCode' => '[Red]#,##0',
      ),
    'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ), 
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'right',
         'vertical' => 'middle',
      ),
  );
  $hoja->getStyle($rangoIngresos)->applyFromArray($resaltarIngresos);
  ///*************************************** FIN Formato Ingresos **************************************
  
  ///*************************************** Formato Palabra TOTAL *************************************
  $rangoTotal = 'L'.$n;
  $resaltarPalabraTotal = array(
    'fill' => array(
          'color' => array('rgb' => 'AEE2FA'),
          'fillType' => 'solid',
        ),
    'font' => array(
        'bold' => true,
        'size' => 13,
      ),
    'numberFormat' => array(
        'formatCode' => '[Red]#,##0',
      ),
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'center',
         'vertical' => 'middle',
      ),
    'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ), 
  );
  $hoja->getStyle($rangoTotal)->applyFromArray($resaltarPalabraTotal);
  ///************************************* FIN Formato Palabra TOTAL ***********************************
  
  ///************************************** Formato Consumos Total *************************************
  $rangoConsumosTotal = 'P'.$n;
  $colorConsumosTotal = array(
    'fill' => array(
          'color' => array('rgb' => 'feff00'),
          'fillType' => 'solid',
        ),
    'font' => array(
        'bold' => true,
        'size' => 13,
        'italic' => true,
      ),
    'numberFormat' => array(
        'formatCode' => '[Red]#,##0',
      ),
    'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ), 
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'right',
         'vertical' => 'middle',
      ),
  );
  $hoja->getStyle($rangoConsumosTotal)->applyFromArray($colorConsumosTotal);
  ///*********************************** FIN Formato Consumos Total ************************************
  
  ///************************************** Formato Ingresos Total *************************************
  $rangoIngresosTotal = 'Q'.$n;
  $resaltarIngresosTotal = array(
    'fill' => array(
          'color' => array('rgb' => '00ff11'),
          'fillType' => 'solid',
        ),
    'font' => array(
        'bold' => true,
        'size' => 13,
        'italic' => true,
      ),
    'numberFormat' => array(
        'formatCode' => '[Red]#,##0',
      ),
    'borders' => array(
              'allBorders' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => array('rgb' => '023184'),
                ),
              ), 
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'right',
         'vertical' => 'middle',
      ),
  );
  $hoja->getStyle($rangoIngresosTotal)->applyFromArray($resaltarIngresosTotal);
  ///************************************* FIN Formato Ingresos Total **********************************
  
  ///****************************************** Muestro TOTALES ****************************************
  $rangoTotalGeneral = 'M'.$n.':O'.$n;
  $resaltarTotalGeneral = array(
    'fill' => array(
          'color' => array('rgb' => '888888'),
          'fillType' => 'solid',
        ),
    'font' => array(
        'bold' => true,
        'size' => 13,
        'color' => array('rgb' => '00ff11'),
        'italic' => true,
      ),
    'numberFormat' => array(
        'formatCode' => '[Red]#,##0',
      ),
    'alignment' => array(
         'wrap' => true,
         'horizontal' => 'right',
         'vertical' => 'middle',
      ),
  );
  $hoja->getStyle($rangoTotalGeneral)->applyFromArray($resaltarTotalGeneral);
  ///************************************ FIN MUESTRO TOTALES ******************************************
  
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

  ///*********************************** Formato para el STOCK: ****************************************
  $styleColumnaStock = array(
      'fill' => array(
          'color' => array('rgb' => 'A9FF96'),
          'fillType' => 'solid',
      ),
      'font' => array(
          'bold' => true,
          'size' => 12,
      ),
      'alignment' => array(
         'wrap' => true,
         'horizontal' => 'center',
         'vertical' => 'middle',
      ),
      'numberFormat' => array(
          'formatCode' => '[Blue]#,##0',
      ),
  );
  $rangoStock = 'H2:H'.$i.'';
  $hoja->getStyle($rangoStock)->applyFromArray($styleColumnaStock);
  ///*********************************** FIN Formato para el STOCK: ************************************
  
  ///*********************************** Formato para la FECHA: ****************************************
  $styleColumnaFecha = array(
        'fill' => array(
          'color' => array('rgb' => 'A9FF96'),
          'fillType' => 'solid',
      ),
      'alignment' => array(
         'wrap' => true,
         'horizontal' => 'center',
         'vertical' => 'middle',
      ),
      'numberFormat' => array(
          'formatCode' => 'DD/MM/YYYY',
      ),
  );
  $rangoFecha = 'B2:B'.$i.'';
  $hoja->getStyle($rangoFecha)->applyFromArray($styleColumnaFecha);
  ///*********************************** FIN Formato para la FECHA: ************************************
  
  ///************************************* Formato de las ALARMAS: *************************************
  $styleAl1 = array(
      'fill' => array(
          'color' => array ('rgb' => 'FAFF98'),
          'fillType' => 'solid',
      ),
  );

  $styleAl2 = array(
      'fill' => array(
          'color' => array ('rgb' => 'FC4A3F'),
          'fillType' => 'solid',
      ),
  );
  ///************************************* FIN Formato de las ALARMAS: *********************************
  
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
  
  ///**************************************** FORMATO GENERAL: *****************************************
  /// Defino el rango de celdas con datos para poder darle formato a todas juntas:
  $rango = "A1:I".$i;
  /// Defino el formato para las celdas:
  $styleGeneral = array(
      'borders' => array(
            'allBorders' => array(
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            'color' => array('rgb' => '023184'),
          ),
      ),
      'alignment' => array(
        'wrap' => false,
        'horizontal' => 'center',
        'vertical' => 'middle',
      )
  );
  $hoja->getStyle($rango)->applyFromArray($styleGeneral);
  ///**************************************** FIN FORMATO GENERAL: *************************************
  
  ///**************************************** INICIO AJUSTE ANCHO COLUMNAS *****************************
  /// Ajusto el auto size para que las celdas no se vean cortadas:
  for ($col = ord('A'); $col <= ord('Q'); $col++)
    {
    $hoja->getColumnDimension(chr($col))->setAutoSize(true); 
  }
  
  $maxWidth = 75;
  $hoja->calculateColumnWidths();
  $colWidth = $hoja->getColumnDimension('I')->getWidth();
  if ($colWidth > $maxWidth) {
      $hoja->getColumnDimension('I')->setAutoSize(false);
      $hoja->getColumnDimension('I')->setWidth($maxWidth);
  }
  ///****************************************** FIN AJUSTE ANCHO COLUMNAS ******************************
  
  /// Se guarda como Excel 2007:
  $writer = new Xlsx($spreadsheet);

  $timestamp = date('dmY_His');
  $nombreArchivo = $nombreReporte."_".$timestamp.".Xlsx";
  $salida = $GLOBALS["dirExcel"]."/".$nombreArchivo;
  $writer->save($salida);

  return $nombreArchivo;
}
