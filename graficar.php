<?php
//Reanudamos la sesión:
session_start();
/**
******************************************************
*  @file graficar.php
*  @brief Funciones para realizar las gráficas.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Diciembre 2017
*
*******************************************************/
require_once("data/config.php");
require_once("data/baseMysql.php");


/** Include JPgraph files */
require_once('../../jpgraph/jpgraph.php');
require_once('../../jpgraph/jpgraph_pie.php');
require_once('../../jpgraph/jpgraph_pie3d.php');
require_once('../../jpgraph/jpgraph_bar.php');
require_once ('../../jpgraph/jpgraph_line.php');

///********************************************************************** INICIO SETEO DE CARPETAS ************************************************************
//// AHORA SE SETEAN EN EL CONFIG.PHP
//$ip = "192.168.1.145";

//$dirGrafica = "//".$ip."/Reportes/";

///*********************************************************************** FIN SETEO DE CARPETAS **************************************************************

/// Recupero la consulta a ejecutar y el mes inicial:

$query = $_SESSION["consulta"];
$fechaInicio = $_SESSION["fechaInicio"];
$fechaFin = $_SESSION["fechaFin"];
$mensaje = $_SESSION["mensaje"];

/*
$query = $_POST["consulta"];
$fechaInicio = $_POST["fechaInicio"];
$fechaFin = $_POST["fechaFin"];
$mensaje = $_POST["mensaje"];
*/

//Conexión con la base de datos:
$dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$result = consultarBD($query, $dbc);

$datos = array();
$retiros = 0;
$ingresos = 0;
$destrucciones = 0;
$renos = 0;
$retirosTotal = 0;
$ingresosTotal = 0;
$renosTotal = 0;
$destruccionesTotal = 0;
$totalDatos = $result->num_rows;
$indice = $fechaInicio;
while (($fila = $result->fetch_array(MYSQLI_BOTH)) != NULL) { 
  $cantidad = $fila["cantidad"];
  $tipoActual = $fila["tipo"];  
  $fechaFila = $fila["fecha"];
  $fechaTemp = explode('-', $fechaFila);
  $indiceFila = $fechaTemp[0].$fechaTemp[1];
  if ($indiceFila !== $indice) {
    if (($retiros !== 0)||($ingresos !== 0)||($renos !== 0)||($destrucciones !== 0)) 
      {
      $datos[$indice]->retiros = $retiros;
      $datos[$indice]->ingresos = $ingresos;
      $datos[$indice]->renos = $renos;
      $datos[$indice]->destrucciones = $destrucciones;
    }
    else {
      array_splice($datos,$indice, 1);
    }
    $datos[$indiceFila]->retiros = 0;
    $datos[$indiceFila]->ingresos = 0;
    $datos[$indiceFila]->renos = 0;
    $datos[$indiceFila]->destrucciones = 0;
    
    $indice = $indiceFila;
    $retiros = 0;
    $ingresos = 0;
    $destrucciones = 0;
    $renos = 0;
  }
  switch ($tipoActual) {
    case "Retiro": $retiros = $retiros + $cantidad;
                   $retirosTotal = $retirosTotal + $cantidad;
                   break;
    case "Ingreso": $ingresos = $ingresos + $cantidad;
                    $ingresosTotal = $ingresosTotal + $cantidad;
                    break;
    case "Renovación": $renos = $renos + $cantidad;
                       $renosTotal = $renosTotal + $cantidad;
                       break;
    case "Destrucción": $destrucciones = $destrucciones + $cantidad;
                        $destruccionesTotal = $destruccionesTotal + $cantidad;
                        break;               
    default: break; 
  } 
}
/// Agrego para los casos en que haya un único mes y por ende nunca entre en el if pues habrá un único índice.
/// Se aclara que no hace falta chequear si alguno de los tipos es diferente de 0, pues de serlo la consulta 
/// hubiera sido nula y no se habría llegado hasta acá
$datos[$indice]->retiros = $retiros;
$datos[$indice]->ingresos = $ingresos;
$datos[$indice]->renos = $renos;
$datos[$indice]->destrucciones = $destrucciones;
///**************************************************** FIN de recuperación de los datos ************************************************************************

///**************************************************** INICIO reacomodo de los datos por tipo ******************************************************************
$meses = array();
$totalRetiros = array();
$totalIngresos = array();
$totalRenos = array();
$totalDestrucciones = array();
$totales = array(0=>$retirosTotal, 1=>$ingresosTotal, 2=>$renosTotal, 3=>$destruccionesTotal);
foreach ($datos as $index => $valor){
  $temp = substr($index, 2, 2);
  $temp1 = substr($index, 4, 2);
  switch ($temp1){
    case '01': $mesCorto = 'Ene';
               break;
    case '02': $mesCorto = 'Feb';
               break;
    case '03': $mesCorto = 'Mar';
               break;
    case '04': $mesCorto = 'Abr';
               break;         
    case '05': $mesCorto = 'May';
               break;
    case '06': $mesCorto = 'Jun';
               break;
    case '07': $mesCorto = 'Jul';
               break;         
    case '08': $mesCorto = 'Ago';
               break;
    case '09': $mesCorto = 'Set';
               break;
    case '10': $mesCorto = 'Oct';
               break;
    case '11': $mesCorto = 'Nov';
               break;
    case '12': $mesCorto = 'Dic';
               break;
    default: break;         
  }
  $mes = $mesCorto." ".$temp.'\'';
  array_push($meses, $mes);
  array_push($totalRetiros , $datos[$index]->retiros);
  array_push($totalIngresos, $datos[$index]->ingresos);
  array_push($totalRenos, $datos[$index]->renos);
  array_push($totalDestrucciones, $datos[$index]->destrucciones);
}
///****************************************************** FIN reacomodo de los datos por tipo *********************************************************************

///********************************************************** INICIO cálculos estadísticos ************************************************************************
$añoInicio = substr($fechaInicio, 0, 4);
$mesInicio = substr($fechaInicio, 4, 2);
$fechaInicial = $añoInicio."-".$mesInicio."-01";

///Instancio objetos del tipo DateTime para las fechas:
$fechainicial1 = new DateTime($fechaInicial);
$fechafinal1 = new DateTime($fechaFin);

///Calculo la diferencia entre las fechas y la paso a cantidad de meses:
$diferencia = $fechainicial1->diff($fechafinal1);
$totalMeses = ($diferencia->y*12) + $diferencia->m;
//echo $totalMeses;
///Calculo el total de CONSUMOS para agregar el dato a las gráficas:
$consumosTotal = $retirosTotal + $destruccionesTotal + $renosTotal;

///Calculo los promedios según cada tipo:
$avgRetiros = ceil($retirosTotal/$totalMeses);
$avgIngresos = ceil($ingresosTotal/$totalMeses);
$avgRenos = ceil($renosTotal/$totalMeses);
$avgDestrucciones = ceil($destruccionesTotal/$totalMeses);
$avgConsumos = ceil($consumosTotal/$totalMeses);

/*
///Genero los array con los datos de los promedios para cada tipo:
$avgRetiros = array();
$avgIngresos = array();
$avgRenos = array();
$avgDestrucciones = array();
foreach($meses as $valor){
  array_push($avgRetiros, $promedioRetiros);
  array_push($avgIngresos, $promedioIngresos);
  array_push($avgRenos, $promedioRenos);
  array_push($avgDestrucciones, $promedioDestrucciones);
}*/
///*********************************************************** FIN cálculos estadísticos **************************************************************************

///A partir del contenido del subtítulo discrimino si es una gráfica para un producto o para una entidad
///y en base a esto, elijo el tipo de gráfica a mostrar:
$producto = strpos($mensaje, 'producto');
if ($producto !== FALSE) {
  //graficarTorta($mensaje, $totales, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgConsumos);
}
else {
  graficarBarras($mensaje, $meses, $totales, $totalRetiros, $totalIngresos, $totalRenos, $totalDestrucciones, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgConsumos);
}

function graficarBarras($subtitulo, $meses, $totales, $data1, $data2, $data3, $data4, $avg1, $avg2, $avg3, $avg4, $avg5){
  global $dirGrafica;
  
  // Create the graph. These two calls are always required
  $graph = new Graph(750,350);
  $graph->SetScale("textint");
  $graph->title->Set("MOVIMIENTOS DE STOCK");
  $graph->subtitle->Set($subtitulo);
  $graph->img->SetMargin(80,190,65,20);
  $graph->SetBackgroundGradient('#e2bd6e','#023184:0.98',GRAD_HOR,BGRAD_MARGIN);
  //$graph->SetShadow();
  
  //$theme_class=new UniversalTheme;
  //$graph->SetTheme($theme_class);  
  //$theme_class = new GreenTheme;
  //$graph->SetTheme($theme_class);
  
  $graph->SetFrame(true,'red',0);
  // Setup titles and X-axis labels
  $graph->xaxis->title->Set('Mes');
  $graph->xaxis->title->SetColor('white');
  $graph->xaxis->SetColor('white'); 
  $graph->xaxis->SetTitlemargin(30);
  $graph->xaxis->SetLabelMargin(15);
  // Setup Y-axis title
  $graph->yaxis->title->Set('Cantidad');
  $graph->yaxis->title->SetColor('white');
  $graph->yaxis->SetColor('white');
  $graph->yaxis->SetTitlemargin(60);
  $graph->yaxis->SetLabelMargin(15);

  $graph->ygrid->SetFill(false);
  $graph->xaxis->SetTickLabels($meses);
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(false,false);

  ///*************************************************** INICIO Gráficas con los consumos del período: *****************************************************
  // Create the bar plots
  $b1 = new BarPlot($data1);
  $b2 = new BarPlot($data2);
  $b3 = new BarPlot($data3);
  $b4 = new BarPlot($data4);

  $consumosTemp = $totales[0] + $totales[2] + $totales[3];
  $consumos = number_format($consumosTemp, 0, ',', '.');
  
  // Create the grouped bar plot
  $gbplot = new GroupBarPlot(array($b1,$b2,$b3,$b4));
  // ...and add it to the graPH
  $graph->Add($gbplot);

  $b1->SetColor("white");
  $b1->SetFillColor("#1111cc");
  $b1->SetLegend("Retiros");
  $b1->value->SetFormat('%d');
  $b1->value->Show();
  $b1->value->SetFont(FF_ARIAL,FS_NORMAL, 9);
  $b1->value->SetAngle(60);
  //$b1->value->HideZero();
  
  $b2->SetColor("white");
  $b2->SetFillColor("#258246");
  $b2->SetLegend("Ingresos");
  $b2->value->SetFormat('%d');
  $b2->value->Show();
  $b2->value->SetFont(FF_ARIAL,FS_NORMAL, 9);
  $b2->value->SetAngle(60);
  //$b2->value->HideZero();
  
  $b3->SetColor("white");
  $b3->SetFillColor("#F08A1D");
  $b3->SetLegend("Renos");
  $b3->value->SetFormat('%d');
  $b3->value->Show();
  $b3->value->SetFont(FF_ARIAL,FS_NORMAL, 9);
  $b3->value->SetAngle(60);
  //$b3->value->HideZero();
  
  $b4->SetColor("white");
  $b4->SetFillColor("#FF0719");
  $b4->SetLegend("Destrucciones");
  $b4->value->SetFormat('%d');
  $b4->value->Show();
  $b4->value->SetFont(FF_ARIAL,FS_NORMAL, 9);
  $b4->value->SetAngle(60);
  //$b4->value->HideZero();
  ///***************************************************** FIN Gráficas con los consumos del período: *******************************************************
  
  ///********************************************** INICIO Generación de las gráficas con los promedios: ****************************************************
  /// Ver si agregar las líneas o no porque no queda del todo bien. De tener que agregarlas hay que volver a pasar el array con los promedios....
  /*
  $a1 = new LinePlot($promedio1);
  //$graph->Add($a1);
  $a1->SetColor("#1111cc");
  $a1->SetLegend('Promedio Retiros');
  $a1->mark->setType(MARK_CIRCLE);
  $a1->value->SetFormat('%d');
  $a1->value->Show();
  $a1->value->SetColor('#1111cc');
  
  $a2 = new LinePlot($promedio2);
  //$graph->Add($a2);
  $a2->SetColor("#258246");
  $a2->SetLegend('Promedio Ingresos');
  $a2->mark->setType(MARK_CROSS);
  
  $a3 = new LinePlot($promedio3);
  //$graph->Add($a3);
  $a3->SetColor("#F08A1D");
  $a3->SetLegend('Promedio Renovaciones');
  $a3->mark->setType(MARK_STAR);
  
  $a4 = new LinePlot($promedio4);
  //$graph->Add($a4);
  $a4->SetColor("#FF0719");
  $a4->SetLegend('Promedio Destrucciones');
  $a4->mark->setType(MARK_DIAMOND);
  */
  ///************************************************** FIN Generación de las gráficas con los promedios: ****************************************************
  
  ///******************************************************** INICIO Textos con los promedios: ***************************************************************
  $separacion = 0.07;
  $posPrimero = 0.45;
  
  $txt = new Text("PROMEDIOS"); 
  $txt->SetFont(FF_FONT1,FS_BOLD); 
  $txt->Align('right');
  $txt->SetColor('red');
  $txt->SetPos(0.96,$posPrimero,'right','center');
  $txt->SetBox('#7c90d4','#7c90d4'); 
  $graph->AddText($txt); 
  
  $avg1 = number_format($avg1, 0, ',', '.');
  $txt1 = new Text("Retiros: ".$avg1); 
  $txt1->SetFont(FF_FONT1,FS_BOLD); 
  $txt1->SetColor('#023184:0.98');
  $txt1->SetPos(0.98,$posPrimero+$separacion,'right','center');
  $txt1->SetBox('navajowhite1','white'); 
  $graph->AddText($txt1); 
  
  $avg2 = number_format($avg2, 0, ',', '.');
  $txt2 = new Text("Ingresos: ".$avg2); 
  $txt2->SetFont(FF_FONT1,FS_BOLD); 
  $txt2->SetColor('#023184:0.98');
  $txt2->SetPos(0.98,$posPrimero+2*$separacion,'right','center');
  $txt2->SetBox('navajowhite1','white'); 
  $graph->AddText($txt2); 
  
  $avg3 = number_format($avg3, 0, ',', '.');
  $txt3 = new Text("Renos: ".$avg3); 
  $txt3->SetFont(FF_FONT1,FS_BOLD); 
  $txt3->SetColor('#023184:0.98');
  $txt3->SetPos(0.98,$posPrimero+3*$separacion,'right','center');
  $txt3->SetBox('navajowhite1','white'); 
  $graph->AddText($txt3); 
  
  $avg4 = number_format($avg4, 0, ',', '.');
  $txt4 = new Text("Destrucciones: ".$avg4); 
  $txt4->SetFont(FF_FONT1,FS_BOLD); 
  $txt4->SetColor('#023184:0.98');
  $txt4->SetPos(0.98,$posPrimero+4*$separacion,'right','center');
  $txt4->SetBox('navajowhite1','white'); 
  $graph->AddText($txt4); 
  
  $avg5 = number_format($avg5, 0, ',', '.');
  $txt5 = new Text("Consumos: ".$avg5." (Total ".$consumos.")"); 
  $txt5->SetFont(FF_FONT1,FS_BOLD); 
  $txt5->SetColor('#023184:0.98');
  $txt5->SetPos(0.98,$posPrimero+5*$separacion,'right','center');
  $txt5->SetBox('navajowhite1','white'); 
  $graph->AddText($txt5);
  ///************************************************************ FIN Textos con los promedios: **************************************************************
  
  $graph->legend->SetShadow('#e2bd6e@1',1);
  $graph->legend->SetPos(0.5,0.95,'center','bottom');
  $graph->legend->SetPos(0.015,0.18,'right','top');
  $graph->legend->SetLayout(LEGEND_VER);
  $graph->legend->SetColumns(1);
  
  // Get the handler to prevent the library from sending the
  // image to the browser
  $gdImgHandler = $graph->Stroke(_IMG_HANDLER);

  // Stroke image to a file and browser

  // Default is PNG so use ".png" as suffix
  $timestamp = date('Ymd_His');
  $nombreArchivo = "graficaEntidad".$timestamp.".jpg";
  $fileName = $dirGrafica."Graficas/".$nombreArchivo;
  $_SESSION["nombreGrafica"] = $nombreArchivo;
  $graph->img->Stream($fileName);

  // Send it back to browser
  $graph->img->Headers();
  $graph->img->Stream();

}

function graficarTorta($subtitulo, $data, $avg1, $avg2, $avg3, $avg4, $avg5){
  global $dirGrafica;
  
  // A new pie graph
  $graph = new PieGraph(750,350, 'auto');
  // Setup background
  
  $graph->title->Set("MOVIMIENTOS DE STOCK");
  $graph->subtitle->Set($subtitulo);
  $graph->img->SetMargin(80,190,65,20);
  $graph->SetMargin(1,1,40,1);
  $graph->SetMarginColor('ivory3');
  //$graph->SetShadow(false);
  //
  //$graph->SetBackgroundGradient('#e2bd6e','#023184:0.98',GRAD_HOR,BGRAD_MARGIN);
  //$graph->SetBackgroundImage("images/snapshots/421301.jpg", BGIMG_FILLFRAME);
  //$graph->SetShadow();
  
  //$theme_class=new UniversalTheme;
  //$graph->SetTheme($theme_class);  
  //$theme_class = new GreenTheme;
  //$graph->SetTheme($theme_class);
  
  //$graph->SetFrame(true,'red',10);
  
  // Setup the pie plot
  $p1 = new PiePlot3D($data);
  $p1->SetShadow('silver', 30);
  $p1->ShowBorder(true, true);
  
  
  $p1->SetSliceColors(array('dodgerblue2','forestgreen','lightgoldenrod1', 'firebrick1'));
  // Adjust size and position of plot
  $p1->SetSize(0.4);
  $p1->SetCenter(0.5,0.5);
  $p1->SetHeight(20);
  $p1->SetAngle(50);
  
  $retiros = number_format($data[0], 0, ',', '.');
  $ingresos = number_format($data[1], 0, ',', '.');
  $renos = number_format($data[2], 0, ',', '.');
  $destrucciones = number_format($data[3], 0, ',', '.');
  $consumosTemp = $data[0]+$data[2]+$data[3];
  $consumos = number_format($consumosTemp, 0, ',', '.');
  $p1->SetLegends(array("Retiros: $retiros","Ingresos: $ingresos","Renos: $renos","Destrucciones: $destrucciones"));
  
  $graph->legend->SetShadow('#e2bd6e@1',1);
  $graph->legend->SetPos(0.5,0.95,'center','bottom');
  $graph->legend->SetPos(0.015,0.18,'right','top');
  $graph->legend->SetLayout(LEGEND_VER);
  $graph->legend->SetColumns(1);
  
  // Setup the labels
  $p1->SetLabelType(PIE_VALUE_ADJPERCENTAGE);    
  $p1->SetLabelPos(1.0);
  
  $p1->value->SetColor("blue");
  $p1->value->SetFont(FF_FONT1,FS_BOLD);    
  $p1->value->SetFormat('%d%%');  
  //$p1->value->HideZero(true);
  //$p1->SetEdge("black", 5);
  // Explode all slices
  $p1->ExplodeAll(14);
  $graph->Add($p1);
  
  ///******************************************************** INICIO Textos con los promedios: ***************************************************************
  $separacion = 0.07;
  $posPrimero = 0.45;
  
  $txt = new Text("PROMEDIOS"); 
  $txt->SetFont(FF_FONT1,FS_BOLD); 
  $txt->Align('right');
  $txt->SetColor('red');
  $txt->SetPos(0.965,$posPrimero,'right','center');
  $txt->SetBox('#b19dda','#7c90d4'); 
  $graph->AddText($txt); 
  
  $avg1 = number_format($avg1, 0, ',', '.');
  $txt1 = new Text("Retiros: ".$avg1); 
  $txt1->SetFont(FF_FONT1,FS_BOLD); 
  $txt1->SetColor('#023184:0.98');
  $txt1->SetPos(0.98,$posPrimero+$separacion,'right','center');
  $txt1->SetBox('navajowhite1','white'); 
  $graph->AddText($txt1); 
  
  $avg2 = number_format($avg2, 0, ',', '.');
  $txt2 = new Text("Ingresos: ".$avg2); 
  $txt2->SetFont(FF_FONT1,FS_BOLD); 
  $txt2->SetColor('#023184:0.98');
  $txt2->SetPos(0.98,$posPrimero+2*$separacion,'right','center');
  $txt2->SetBox('navajowhite1','white'); 
  $graph->AddText($txt2); 
  
  $avg3 = number_format($avg3, 0, ',', '.');
  $txt3 = new Text("Renos: ".$avg3); 
  $txt3->SetFont(FF_FONT1,FS_BOLD); 
  $txt3->SetColor('#023184:0.98');
  $txt3->SetPos(0.98,$posPrimero+3*$separacion,'right','center');
  $txt3->SetBox('navajowhite1','white'); 
  $graph->AddText($txt3); 
  
  $avg4 = number_format($avg4, 0, ',', '.');
  $txt4 = new Text("Destrucciones: ".$avg4); 
  $txt4->SetFont(FF_FONT1,FS_BOLD); 
  $txt4->SetColor('#023184:0.98');
  $txt4->SetPos(0.98,$posPrimero+4*$separacion,'right','center');
  $txt4->SetBox('navajowhite1','white'); 
  $graph->AddText($txt4); 
  
  $avg5 = number_format($avg5, 0, ',', '.');
  $txt5 = new Text("Consumos: ".$avg5." (Total ".$consumos.")"); 
  $txt5->SetFont(FF_FONT1,FS_BOLD); 
  $txt5->SetColor('#023184:0.98');
  $txt5->SetPos(0.98,$posPrimero+5*$separacion,'right','center');
  $txt5->SetBox('navajowhite1','white'); 
  $graph->AddText($txt5); 
  ///************************************************************ FIN Textos con los promedios: **************************************************************
  
  // Get the handler to prevent the library from sending the
  // image to the browser
  $gdImgHandler = $graph->Stroke(_IMG_HANDLER);

  // Stroke image to a file and browser
  
  
  // Default is PNG so use ".png" as suffix
  $timestamp = date('Ymd_His');
  $nombreArchivo = "graficaProducto".$timestamp.".jpg";
  $fileName = $dirGrafica."Graficas/".$nombreArchivo;
  $_SESSION["nombreGrafica"] = $nombreArchivo;
  $graph->img->Stream($fileName);

  // Send it back to browser
  $graph->img->Headers();
  $graph->img->Stream();
}

?>