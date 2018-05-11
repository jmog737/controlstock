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

function generarExcelStock($registros) {
  global $nombreReporte, $zipSeguridad, $planilla, $pwdPlanillaManual, $pwdZip;
  
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
  
  /*
  ************ TEST PARAMETRIZACIÓN DE LAS CELDAS: ****************************/
  $colId = 'A';
  $colEntidad = chr(ord($colId)+1);
  $colNombre = chr(ord($colId)+2);
  $colBin = chr(ord($colId)+3);
  $colCodEMSA = chr(ord($colId)+4);
  $colCodOrigen = chr(ord($colId)+5);
  //$colComent = chr(ord($colId)+6);
  $colStock = chr(ord($colId)+6);
  $colAl1 = chr(ord($colId)+7);
  $colAl2 = chr(ord($colId)+8); 
  
  $mostrarId = 1;
  $mostrarEntidad = 1;
  $mostrarNombre = 1;
  $mostrarBin = 1;
  $mostrarCodEMSA = 1;
  $mostrarCodOrigen = 1;
  $mostrarComent = 1;
  $mostrarStock = 1;
  $mostrarAl1 = 1;
  $mostrarAl2 = 1;
  /**************** FIN PARAMETRIZACIÓN ***************************************/

  // Agrego los títulos:
  $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue($colId.'1', 'Id')
              ->setCellValue($colEntidad.'1', 'Entidad')
              ->setCellValue($colNombre.'1', 'Nombre')
              ->setCellValue($colBin.'1', 'BIN')
              ->setCellValue($colCodEMSA.'1', 'Cód. EMSA')
              ->setCellValue($colCodOrigen.'1', 'Cód. Origen')
              //->setCellValue($colComent.'1', 'Comentarios')
              ->setCellValue($colStock.'1', 'Stock');
  /// Formato de los títulos:
  $header = $colId.'1:'.$colStock.'1';
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
    $al2 = array_pop($dato);
    $al1 = array_pop($dato);
    $stock = array_pop($dato);
    $codOrigen = array_pop($dato);
    if (($codOrigen === null)||($codOrigen === '')){
      $codOrigen = 'NO ingresado';
    }
    $codEMSA = array_pop($dato);
    if (($codEMSA === null)||($codEMSA === '')){
      $codEMSA = 'NO ingresado';
    }
    $codBin = array_pop($dato);
    if (($codBin === null)||($codBin === '')){
      $codBin = 'ND o NC';
    }
    
    array_push($dato, $codBin);
    array_push($dato, $codEMSA);
    array_push($dato, $codOrigen);
    array_push($dato, $stock);
    array_push($dato, $al1);
    array_push($dato, $al2);
    
    /// Acomodo el índice pues empieza en 0, y en el 1 están los nombres de los campos:
    $i = $i + 2;
    $celda = $colId.$i;
    $hoja->fromArray($dato, ' ', $celda);
  }

  /// Agrego línea con el total del stock:
  $j = $i+1;
  $hoja->mergeCells($colId.$j.':'.$colCodOrigen.$j.'');
  $hoja->setCellValue($colId.$j.'', 'TOTAL');
  $celdaTotalTarjetas = $colStock.$j;
  ///Se comenta agregado de línea con el total pasado dado que ahora el total se calcula usando una fórmula de excel:
  //$hoja->setCellValue($celdaTotalTarjetas, $total);
  $hoja->setCellValue($celdaTotalTarjetas, '=sum('.$colStock.'2:'.$colStock.$i.')');


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
  $hoja->getStyle(''.$colId.$j.'')->applyFromArray($styleTextoTotal);


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
  $rangoStock = ''.$colStock.'2:'.$colStock.$i.'';
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
    $al1 = $colAl1.$k;
    $al2 = $colAl2.$k;
    $celda = $colStock.$k;
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

  /// Defino estilos para resaltar los comentarios:
  $styleDif = array(
      'fill' => array(
          'color' => array ('rgb' => 'ffff00'),
          'fillType' => 'solid',
      ),
  );

  $styleStock = array(
      'fill' => array(
          'color' => array ('rgb' => '38ff1d'),
          'fillType' => 'solid',
      ),
  );

  $stylePlastico = array(
      'fill' => array(
          'color' => array ('rgb' => 'FF9999'),
          'fillType' => 'solid',
      ),
  );

  $styleComentario = array(
      'fill' => array(
          'color' => array ('rgb' => 'd3d3d3'),
          'fillType' => 'solid',
      ),
  );

 ///Comento la parte del coloreado de los comentarios dado que ya NO se muestran los comentarios: 
 /* 
  /// Aplico color de fondo de la columna de comentarios según el mismo:
  for ($k = 2; $k <= $i; $k++) {
    $celda = $colComent.$k;
    $valorCelda = $hoja->getCell($celda)->getValue();

    $patron = "dif";
    $buscar = stripos($valorCelda, $patron);
    if ($buscar !== FALSE){
      $hoja->getStyle($celda)->applyFromArray($styleDif);
    }
    else {
      $patron = "stock";
      $buscar = stripos($valorCelda, $patron);
      if ($buscar !== FALSE){
        $hoja->getStyle($celda)->applyFromArray($styleStock);
      }
      else {
        $patron = "plastico";
        $patron1 = "plástico";
        $buscar = stripos($valorCelda, $patron);
        $buscar1 = stripos($valorCelda, $patron1);
        if (($buscar !== FALSE)||($buscar1 !== FALSE)){
          $hoja->getStyle($celda)->applyFromArray($stylePlastico);
        }
        else {
          if (($valorCelda !== null)&&($valorCelda !== '')){
            $hoja->getStyle($celda)->applyFromArray($styleComentario);
          }
        }
      }
    } 
  }  
*/

  /// Defino el rango de celdas con datos para poder darle formato a todas juntas:
  $rango = $colId."1:".$colStock.$j;
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

  /// Ajusto el auto size para que las celdas no se vean cortadas:
  for ($col = ord(''.$colId.''); $col <= ord(''.$colStock.''); $col++)
    {
    $hoja->getColumnDimension(chr($col))->setAutoSize(true);   
  }
  
  $timestamp = date('dmY_His');
  
  switch ($planilla){
    case "nada": break;
    case "misma": if ($zipSeguridad !== 'nada') {
                    $pwdPlanilla = $pwdZip;
                  }
                  break;
    case "fecha": $pwdPlanilla = $timestamp; 
                  break;
    case "random": $pwdPlanilla = $pwdPlanillaManual;
                   break;
    case "manual": $pwdPlanilla = $pwdPlanillaManual;
                   break;
    default: break;
  } 
  if ((($planilla !== "nada")&&($planilla !== 'misma'))||(($planilla === "misma")&&($zipSeguridad !== "nada"))){
    ///Agrego protección para la hoja activa:
    $hoja->getProtection()->setPassword($pwdPlanilla);
    $hoja->getProtection()->setSheet(true);
  }
  
  // Se guarda como Excel 2007:
  $writer = new Xlsx($spreadsheet);
  
  $nombreArchivo = $nombreReporte."_".$timestamp.".Xlsx";
  $salida = $GLOBALS["dirExcel"]."/".$nombreArchivo;
  $writer->save($salida);

  return $nombreArchivo;
}

function generarExcelBoveda($registros) {
  global $nombreReporte, $zipSeguridad, $planilla, $pwdPlanillaManual, $pwdZip;
  
  $spreadsheet = new Spreadsheet();

  $locale = 'es_UY'; 
  $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale); 
  if (!$validLocale) { echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n"; }


  // Set document properties
  $spreadsheet->getProperties()->setCreator("Juan Martín Ortega")
                               ->setLastModifiedBy("Juan Martín Ortega")
                               ->setTitle("StockBoveda")
                               ->setSubject("Datos exportados")
                               ->setDescription("Archivo excel con el total de plásticos en bóveda.")
                               ->setKeywords("stock excel php")
                               ->setCategory("Resultado");

  /// Declaro hoja activa:
  $hoja = $spreadsheet->getSheet(0);

  $hoja->setTitle($nombreReporte);
  $hoja->getTabColor()->setRGB('46A743');

  // Agrego los títulos:
  $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A1', 'Id')
              ->setCellValue('B1', 'Entidad')
              ->setCellValue('C1', 'Stock');
  /// Formato de los títulos:
  $header = 'A1:C1';
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
  $hoja->mergeCells('A'.$j.':B'.$j.'');
  $hoja->setCellValue('A'.$j.'', 'TOTAL');
  $celdaTotalTarjetas = "C".$j;
  ///Se comenta agregado de línea con el total pasado dado que ahora el total se calcula usando una fórmula de excel:
  //$hoja->setCellValue($celdaTotalTarjetas, $total);
  $hoja->setCellValue($celdaTotalTarjetas, '=sum(C2:C'.$i.')');


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
          'color' => array('rgb' => 'DADADA'),
          'fillType' => 'solid',
      ),
      'font' => array(
          'bold' => true,
          'italic' => true,
          'size' => 11,
      ),
      'alignment' => array(
         'wrap' => true,
         'horizontal' => 'center',
         'vertical' => 'middle',
      ),
      'numberFormat' => array(
          'formatCode' => '[Black]#,##0',
      ),
  );
  
  /// Ajusto el auto size para que las celdas no se vean cortadas:
  for ($col = ord('a'); $col <= ord('c'); $col++)
    {
    $hoja->getColumnDimension(chr($col))->setAutoSize(true);   
  }

  /// Defino el rango de celdas con datos para poder darle formato a todas juntas:
  $rango = "A1:C".$j;
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

  $rangoStock = 'C2:C'.$i.'';
  $hoja->getStyle($rangoStock)->applyFromArray($styleColumnaStock);
  
  $timestamp = date('dmY_His');
  
  switch ($planilla){
    case "nada": break;
    case "misma": if ($zipSeguridad !== 'nada') {
                    $pwdPlanilla = $pwdZip;
                  }
                  break;
    case "fecha": $pwdPlanilla = $timestamp; 
                  break;
    case "random": $pwdPlanilla = $pwdPlanillaManual;
                   break;
    case "manual": $pwdPlanilla = $pwdPlanillaManual;
                   break;
    default: break;
  } 
  if ((($planilla !== "nada")&&($planilla !== 'misma'))||(($planilla === "misma")&&($zipSeguridad !== "nada"))){
    ///Agrego protección para la hoja activa:
    $hoja->getProtection()->setPassword($pwdPlanilla);
    $hoja->getProtection()->setSheet(true);
  }
  
  // Se guarda como Excel 2007:
  $writer = new Xlsx($spreadsheet);

  $nombreArchivo = $nombreReporte."_".$timestamp.".Xlsx";
  $salida = $GLOBALS["dirExcel"]."/".$nombreArchivo;
  $writer->save($salida);

  return $nombreArchivo;
}

function generarExcelMovimientos($registros) {
  global $nombreReporte, $zipSeguridad, $planilla, $pwdPlanillaManual, $pwdZip;
  
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

  $colId = 'A';
  $colFecha = chr(ord($colId)+1);
  $colHora = chr(ord($colId)+2);
  $colEntidad = chr(ord($colId)+3);
  $colNombre = chr(ord($colId)+4);
  $colBin = chr(ord($colId)+5);
  $colCodEMSA = chr(ord($colId)+6);
  $colCodOrigen = chr(ord($colId)+7);
  $colTipo = chr(ord($colId)+8);
  $colCantidad = chr(ord($colId)+9);
  //$colComent = chr(ord($colId)+10);
  
  $colVacia1 = chr(ord($colId)+10);
  //$colVacia2 = chr(ord($colId)+11);
  
  $colNombreTotales = chr(ord($colId)+11);
  $colRetiros = chr(ord($colId)+12);
  $colRenos = chr(ord($colId)+13);
  $colDestrucciones = chr(ord($colId)+14);
  $colConsumos = chr(ord($colId)+15);
  $colIngresos = chr(ord($colId)+16);
  ///**************************************** INICIO formato encabezado ********************************
  // Agrego los títulos:
  $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue($colId.'1', 'Id')
              ->setCellValue($colFecha.'1', 'Fecha')
              ->setCellValue($colHora.'1', 'Hora')
              ->setCellValue($colEntidad.'1', 'Entidad')
              ->setCellValue($colNombre.'1', 'Nombre')
              ->setCellValue($colBin.'1', 'BIN')
              ->setCellValue($colCodEMSA.'1', 'Cód. EMSA')
              ->setCellValue($colCodOrigen.'1', 'Cód. Origen')
              ->setCellValue($colTipo.'1', 'Tipo')
              ->setCellValue($colCantidad.'1', 'Cantidad')
              /*->setCellValue($colComent.'1', 'Comentarios')*/;
  
  /// Formato de los títulos:
  $header = $colId.'1:'.$colCantidad.'1';
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
    
    $cantidad = array_pop($dato);
    $tipo = array_pop($dato);
    
    $codOrigen = array_pop($dato);
    if (($codOrigen === null)||($codOrigen === '')){
      $codOrigen = 'NO ingresado';
    }

    $codEMSA = array_pop($dato);
    if (($codEMSA === null)||($codEMSA === '')){
      $codEMSA = 'NO ingresado';
    }
    
    $bin = array_pop($dato); 
    if (($bin === null)||($bin === '')){
      $bin = 'ND o NC';
    }
    
    array_push($dato, $bin);
    array_push($dato, $codEMSA);
    array_push($dato, $codOrigen);
    array_push($dato, $tipo);
    array_push($dato, $cantidad);
    /// Acomodo el índice pues empieza en 0, y en el 1 están los nombres de los campos:
    $i = $i + 2;
    $celda = $colId.$i;
    $hoja->fromArray($dato, ' ', $celda);
  }
  $j = $i+1;
  ///*************************************** FIN ESCRIBO DATOS *****************************************
  
  ///************************************ MUESTRO TOTALES **********************************************
  $hoja->mergeCells($colNombreTotales.'1:'.$colIngresos.'1');
  $hoja->setCellValue($colNombreTotales."1", "TOTALES");
  $hoja->setCellValue($colNombreTotales."2", "Nombre");
  $hoja->setCellValue($colRetiros."2", "Retiros");
  $hoja->setCellValue($colRenos."2", "Renovaciones");
  $hoja->setCellValue($colDestrucciones.'2', 'Destrucciones');
  $hoja->setCellValue($colConsumos.'2', 'Consumos');
  $hoja->setCellValue($colIngresos.'2', 'Ingresos');
  
  $resumen = Array();
  $ids = Array();
  $id = '';
  foreach ($registros as $datito){
    $idprod = $datito[0];
    $nombre = $datito[5];
    $tipo = $datito[9];
    $cantidad = $datito[10];
    switch ($tipo){
      case 'Retiro':  $resumen["$idprod"]['retiros'] = $resumen["$idprod"]['retiros'] + $cantidad;
                      break;
      case 'Renovación':  $resumen["$idprod"]['renos'] = $resumen["$idprod"]['renos'] + $cantidad;
                          break;
      case 'Destrucción': $resumen["$idprod"]['destrucciones'] = $resumen["$idprod"]['destrucciones'] + $cantidad;
                          break;
      case 'Ingreso': $resumen["$idprod"]['ingresos'] = $resumen["$idprod"]['ingresos'] + $cantidad;
                      break;
      default: break;
    }
    if ($id !== $idprod){
       $ids[] = $idprod;
       $id = $idprod;
       $resumen["$idprod"]['nombre'] = $nombre;
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
    $hoja->setCellValue($colNombreTotales.$n, $resumen["$id1"]['nombre']);
    $hoja->setCellValue($colRetiros.$n, $resumen["$id1"]['retiros']);
    $hoja->setCellValue($colRenos.$n, $resumen["$id1"]['renos']);
    $hoja->setCellValue($colDestrucciones.$n, $resumen["$id1"]['destrucciones']);
    $hoja->setCellValue($colConsumos.$n,'='.$colRetiros.$n.'+'.$colRenos.$n.'+'.$colDestrucciones.$n);
    $hoja->setCellValue($colIngresos.$n, $resumen["$id1"]['ingresos']);
    $n++;
  }
  $finDatos = $n - 1;
  
  ///Línea con el total por categoría y totales generales:
  $hoja->setCellValue($colNombreTotales.$n, "TOTALES");
  $hoja->setCellValue($colRetiros.$n, '=SUM('.$colRetiros.'3:'.$colRetiros.$finDatos.')');
  $hoja->setCellValue($colRenos.$n, '=SUM('.$colRenos.'3:'.$colRenos.$finDatos.')');
  $hoja->setCellValue($colDestrucciones.$n, '=SUM('.$colDestrucciones.'3:'.$colDestrucciones.$finDatos.')');
  $hoja->setCellValue($colConsumos.$n, '=SUM('.$colConsumos.'3:'.$colConsumos.$finDatos.')');
  $hoja->setCellValue($colIngresos.$n, '=SUM('.$colIngresos.'3:'.$colIngresos.$finDatos.')');
  
  ///**************************************** Formato Título *******************************************
  $header1 = $colNombreTotales.'1:'.$colIngresos.'1';
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
  $nombreCampos = $colNombreTotales.'2:'.$colIngresos.'2';
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
  $nombreProductos = $colNombreTotales.'3:'.$colNombreTotales.$finDatos;
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
  $rangoTotales = $colRetiros.'3:'.$colDestrucciones.$n;
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
  $rangoConsumos = $colConsumos.'3:'.$colConsumos.$finDatos;
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
  $rangoIngresos = $colIngresos.'3:'.$colIngresos.$finDatos;
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
  $rangoTotal = $colNombreTotales.$n;
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
  $rangoConsumosTotal = $colConsumos.$n;
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
  $rangoIngresosTotal = $colIngresos.$n;
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
  $rangoTotalGeneral = $colRetiros.$n.':'.$colDestrucciones.$n;
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

  ///*********************************** Formato para la CANTIDAD: ****************************************
  $styleColumnaCantidad = array(
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
  $rangoStock = $colCantidad.'2:'.$colCantidad.$i.'';
  $hoja->getStyle($rangoStock)->applyFromArray($styleColumnaCantidad);
  ///*********************************** FIN Formato para la CANTIDAD ************************************
  
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
  $rangoFecha = $colFecha.'2:'.$colFecha.$i.'';
  $hoja->getStyle($rangoFecha)->applyFromArray($styleColumnaFecha);
  ///*********************************** FIN Formato para la FECHA: ************************************
  
  ///**************************************** FORMATO GENERAL: *****************************************
  /// Defino el rango de celdas con datos para poder darle formato a todas juntas:
  $rango = $colId."1:".$colCantidad.$i;
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
  for ($col = ord($colId); $col <= ord($colIngresos); $col++)
    {
    $hoja->getColumnDimension(chr($col))->setAutoSize(true);
  }
  ///Elimino seteo de ajuste máximo en caso de ser muy grande del campo COMENTARIOS pues ya NO se muestra el mismo:
  
  ///Se agrega seteo fijo de la columna de separación entre los movimientos y los totales:
  $hoja->calculateColumnWidths();
  $hoja->getColumnDimension($colVacia1)->setAutoSize(false);
  $hoja->getColumnDimension($colVacia1)->setWidth(2);
  ///****************************************** FIN AJUSTE ANCHO COLUMNAS ******************************
  
  $timestamp = date('dmY_His');
  
  switch ($planilla){
    case "nada": break;
    case "misma": if ($zipSeguridad !== 'nada') {
                    $pwdPlanilla = $pwdZip;
                  }
                  break;
    case "fecha": $pwdPlanilla = $timestamp; 
                  break;
    case "random": $pwdPlanilla = $pwdPlanillaManual;
                   break;
    case "manual": $pwdPlanilla = $pwdPlanillaManual;
                   break;
    default: break;
  } 
  if ((($planilla !== "nada")&&($planilla !== 'misma'))||(($planilla === "misma")&&($zipSeguridad !== "nada"))){
    ///Agrego protección para la hoja activa:
    $hoja->getProtection()->setPassword($pwdPlanilla);
    $hoja->getProtection()->setSheet(true);
  }
  
  /// Se guarda como Excel 2007:
  $writer = new Xlsx($spreadsheet);

  $nombreArchivo = $nombreReporte."_".$timestamp.".Xlsx";
  $salida = $GLOBALS["dirExcel"]."/".$nombreArchivo;
  $writer->save($salida);

  return $nombreArchivo;
}
