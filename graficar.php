<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
} 
///Se deshabilita el reporte de errores dado que de estar habilitado NO genera la gráfica.
///Se vuelven a habilitar al finalizar el script.
error_reporting(NULL);
ini_set('error_reporting', NULL);
ini_set('display_errors',0);
/**
******************************************************
*  @file graficar.php
*  @brief Funciones para realizar las gráficas.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Diciembre 2017
*
*******************************************************/
require_once("data/baseMysql.php");
require_once('../../fpdf/mc_table.php');

/** Include JPgraph files */
require_once('../../jpgraph/jpgraph.php');
require_once('../../jpgraph/jpgraph_pie.php');
require_once('../../jpgraph/jpgraph_pie3d.php');
require_once('../../jpgraph/jpgraph_bar.php');
require_once ('../../jpgraph/jpgraph_line.php');

class PDF extends PDF_MC_Table
  {
  //Cabecera de página
  function Header()
    {
    global $fecha, $hora, $titulo, $hHeader;
    //Agrego logo de EMSA:
    $this->Image('images/logotipo.jpg', 3, 3, 50);
    $this->setY(10);
    $this->setX(20);
    //Defino características para el título y agrego el título:
    $this->SetFont('Arial', 'BU', 18);
    $this->Cell(200, $hHeader, utf8_decode($titulo), 0, 0, 'C');
    $this->Ln();

    $this->setY(8);
    $this->setX(187);
    $this->SetFont('Arial');
    $this->SetFontSize(10);
    $this->Cell(20, $hHeader, $fecha, 0, 0, 'C');

    $this->setY(11);
    $this->setX(187);
    $this->SetFont('Arial');
    $this->SetFontSize(10);
    $this->Cell(20, $hHeader, $hora, 0, 0, 'C');

    //Dejo el cursor donde debe empezar a escribir:
    $this->Ln(20);
    }

  //Pie de página
  function Footer()
    {
    global $hFooter;
    $this->SetY(-$hFooter);
    $this->SetFont('Arial', 'I', 8);
    $this->SetTextColor(0);
    $this->Cell(0, $hFooter, 'Pag. ' . $this->PageNo(), 0, 0, 'C');
    }
  
  function graficarBarras($subtitulo, $meses, $totales, $data1, $data2, $data3, $data4, $totalRango, $tipoRango, $avg1, $avg2, $avg3, $avg4, $avg5, $destino, $nombreGrafica){
    global $dirGraficas, $h;
    
    
    // Create the graph. These two calls are always required
    $graph = new Graph(830,350);
    $graph->SetScale("textint");
    $graph->title->Set("MOVIMIENTOS DE STOCK");
    $graph->subtitle->Set($subtitulo." (".$totalRango." ".$tipoRango.").");
    $graph->img->SetMargin(80,250,65,20);
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
    $graph->xaxis->SetTitlemargin(5);
    $graph->xaxis->SetLabelMargin(8);
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

    ///*************************************************** INICIO Gráficas con los consumos del período: ************************************
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
      
    $b1->value->Show();
    $b1->SetColor("white");
    $b1->SetFillColor("#1111cc");
    $b1->SetLegend("Retiros");
    $b1->SetWidth(0.8);
    $b1->value->SetAlign('left','center');
    $b1->value->SetMargin(30);
    $b1->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
    $b1->value->SetAngle(75);
    $b1->value->SetFormatCallback(formatoDato); 
    /*$b1->value->SetFormat('%d');*/   
    //$b1->value->HideZero();

    $b2->value->Show();
    $b2->SetColor("white");
    $b2->SetFillColor("#258246");
    $b2->SetLegend("Ingresos");
    $b2->SetWidth(0.8);
    $b2->value->SetMargin(30);
    $b2->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
    $b2->value->SetAngle(75);
    $b2->value->SetFormatCallback("formatoDato"); 
    /*$b2->value->SetFormat('%d');*/
    //$b2->value->HideZero();

    $b3->value->Show();
    $b3->SetColor("white");
    $b3->SetFillColor("#F08A1D");
    $b3->SetLegend("Renos");
    $b3->SetWidth(0.8);
    $b3->value->SetMargin(30);
    $b3->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
    $b3->value->SetAngle(75);
    $b3->value->SetFormatCallback(formatoDato); 
    /*$b3->value->SetFormat('%d');*/
    //$b3->value->HideZero();

    $b4->value->Show();
    $b4->SetColor("white");
    $b4->SetFillColor("#FF0719");
    $b4->SetLegend("Destrucciones");
    $b4->SetWidth(0.8);
    $b4->value->SetMargin(30);
    $b4->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
    $b4->value->SetAngle(75);
    $b4->value->SetFormatCallback(formatoDato); 
    /*$b4->value->SetFormat('%d');*/
    //$b4->value->HideZero();
    
    ///***************************************************** FIN Gráficas con los consumos del período: *************************************

    ///********************************************** INICIO Generación de las gráficas con los promedios: **********************************
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
    ///************************************************** FIN Generación de las gráficas con los promedios: *********************************

    ///******************************************************** INICIO Textos con los promedios: ********************************************
    $separacion = 0.07;
    $posPrimero = 0.5;

    $txt = new Text("DATOS:"); 
    $txt->SetFont(FF_FONT1,FS_BOLD); 
    $txt->Align('right');
    $txt->SetColor('red');
    $txt->SetPos(0.96,0.92*$posPrimero,'right','center');
    $txt->SetBox('#7c90d4','#7c90d4'); 
    $graph->AddText($txt); 

    $avg1 = number_format($avg1, 0, ',', '.');
    $retiros = number_format($totales[0], 0, ',', '.');
    $txt1 = new Text("Retiros: ".$retiros." (Avg: ".$avg1.")"); 
    $txt1->SetFont(FF_FONT1,FS_BOLD); 
    $txt1->SetColor('#023184:0.98');
    $txt1->SetPos(0.98,$posPrimero+0.8*$separacion,'right','center');
    $txt1->SetBox('navajowhite1','white'); 
    $graph->AddText($txt1); 

    $avg2 = number_format($avg2, 0, ',', '.');
    $ingresos = number_format($totales[1], 0, ',', '.');
    $txt2 = new Text("Ingresos: ".$ingresos." (Avg: ".$avg2.")"); 
    $txt2->SetFont(FF_FONT1,FS_BOLD); 
    /*$txt2->SetColor('#023184:0.98');*/
    $txt2->SetColor('#258246:0.98');
    $txt2->SetPos(0.98,$posPrimero+1.8*$separacion,'right','center');
    $txt2->SetBox('navajowhite1','white'); 
    $graph->AddText($txt2); 

    $avg3 = number_format($avg3, 0, ',', '.');
    $renos = number_format($totales[2], 0, ',', '.');
    $txt3 = new Text("Renos: ".$renos." (Avg: ".$avg3.")"); 
    $txt3->SetFont(FF_FONT1,FS_BOLD); 
    $txt3->SetColor('#023184:0.98');
    $txt3->SetPos(0.98,$posPrimero+2.8*$separacion,'right','center');
    $txt3->SetBox('navajowhite1','white'); 
    $graph->AddText($txt3); 

    $avg4 = number_format($avg4, 0, ',', '.');
    $destrucciones = number_format($totales[3], 0, ',', '.');
    $txt4 = new Text("Destrucciones: ".$destrucciones." (Avg: ".$avg4.")"); 
    $txt4->SetFont(FF_FONT1,FS_BOLD); 
    $txt4->SetColor('#023184:0.98');
    $txt4->SetPos(0.98,$posPrimero+3.8*$separacion,'right','center');
    $txt4->SetBox('navajowhite1','white'); 
    $graph->AddText($txt4); 

    $avg5 = number_format($avg5, 0, ',', '.');
    $txt5 = new Text("Consumos: ".$consumos." (Avg: ".$avg5.")"); 
    $txt5->SetFont(FF_FONT1,FS_BOLD); 
    $txt5->SetColor('red:0.98');
    $txt5->SetPos(0.98,$posPrimero+5.05*$separacion,'right','center');
    $txt5->SetBox('navajowhite1','white'); 
    $graph->AddText($txt5);
    ///************************************************************ FIN Textos con los promedios: *******************************************

    $graph->legend->SetShadow('#e2bd6e@1',1);
    $graph->legend->SetPos(0.5,0.95,'center','bottom');
    $graph->legend->SetPos(0.015,0.18,'right','top');
    //$graph->legend->SetLayout(LEGEND_VER);
    $graph->legend->SetColumns(1);

    if ($destino === 'pdf'){
      $timestamp = date('dmY_His');
      $nombreArchivo = $nombreGrafica.$timestamp.".png";
      $fileName = $dirGraficas.$nombreArchivo;

      // Stroke image to a file
      $graph->Stroke($fileName);
      $graph->img->Stream($fileName);

      //Defino tipo de letra y tamaño para el Título:
      $this->SetFont('Courier', 'BU', 18);
      $this->SetTextColor(255, 0, 0);

      //Establezco las coordenadas del borde de arriba a la izquierda de la tabla:
      $this->SetY(25);

      $titulo = utf8_decode("RESULTADO ESTADÍSTICAS");
      $tam = $this->GetStringWidth($titulo);
      $anchoPagina = $this->GetPageWidth();
      $xInicio = ($anchoPagina-$tam)/2;
      $this->SetX($xInicio);

      $this->Cell($tam, 1.5*$h, $titulo, 0, 0, 'C', 0);
      $this->Ln(20);
      $y = $this->getY();
      $this->Image($fileName, 20, $y, 0, 80);
    }
    else {
      $graph->Stroke();
      //Mandarlo al navegador
      $graph->img->Headers();
      $graph->img->Stream();
    }
    
  }

  function graficarTorta($subtitulo, $data, $totalRango, $tipoRango, $avg1, $avg2, $avg3, $avg4, $avg5, $destino, $nombreGrafica){
    global $dirGraficas, $h;

    // A new pie graph
    $graph = new PieGraph(750,350, 'auto');
    // Setup background

    $graph->title->Set("MOVIMIENTOS DE STOCK");
    $graph->subtitle->Set($subtitulo." (".$totalRango." ".$tipoRango.").");
    $graph->img->SetMargin(80,190,65,20);
    $graph->SetMargin(1,1,40,1);
    $graph->SetMarginColor('ivory3');

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

    $txt = new Text("PROMEDIOS:"); 
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

    if ($destino === 'pdf'){
      $timestamp = date('dmY_His');
      $nombreArchivo = $nombreGrafica.$timestamp.".png";
      $fileName = $dirGraficas.$nombreArchivo;

      // Stroke image to a file
      $graph->Stroke($fileName);
      $graph->img->Stream($fileName);

        //Defino tipo de letra y tamaño para el Título:
      $this->SetFont('Courier', 'BU', 18);
      $this->SetTextColor(255, 0, 0);

      //Establezco las coordenadas del borde de arriba a la izquierda de la tabla:
      $this->SetY(25);

      $titulo = utf8_decode("RESULTADO ESTADÍSTICAS");
      $tam = $this->GetStringWidth($titulo);
      $anchoPagina = $this->GetPageWidth();
      $xInicio = ($anchoPagina-$tam)/2;
      $this->SetX($xInicio);

      $this->Cell($tam, 1.5*$h, $titulo, 0, 0, 'C', 0);
      $this->Ln(20);
      $y = $this->getY();
      $this->Image($fileName, 20, $y, 170, 80);
    }
    else {
      $graph->Stroke();
      //Mandarlo al navegador
      $graph->img->Headers();
      $graph->img->Stream();
      
    } 
  }
}

///Función que da el formato de los valores en cada columna.
///Básicamente, se usa para agregar el separador de miles.
function formatoDato($aLabel) { 
  return  number_format($aLabel, 0, ',', '.');
};
  
///Función que calcula el total de díase entre el rango de fechas pasado como argumento.
///Básicamente arma un array con todas las fechas que hay en medio (incluyendo ambos extremos).
function DiasHabiles($fecha_inicial,$fecha_final) 
  { 
  list($year,$mes,$dia) = explode("-",$fecha_inicial);
  $ini = mktime(0, 0, 0, $mes , $dia, $year); 
  list($yearf,$mesf,$diaf) = explode("-",$fecha_final); 
  $fin = mktime(0, 0, 0, $mesf , $diaf, $yearf); 
  $newArray = array($ini);
  $r = 1; 
  while($ini != $fin) 
    { 
    $ini = mktime(0, 0, 0, $mes , $dia+$r, $year); 
    array_push($newArray, $ini);
    //$newArray[] = $ini;  
    $r++; 
  } 
  return $newArray; 
}

///Función que calcula los días hábiles.
///Recibe como parámetro un array con todas las fechas, y al mismo le descuenta los fines de semana y los feriados.
function Evalua($arreglo) 
  { 
  ///Array con los feriados de 2018:
  $feriados = array(
    "1-1",  //  Año Nuevo (irrenunciable) 
    "12-2",  // Carnaval 
    "13-2",  //  Carnaval 
    "28-3",  //  Viernes Santo (feriado religioso) 
    "29-3",  //  Sábado Santo (feriado religioso) 
    "1-5",  //  Día Nacional del Trabajo (irrenunciable) 
    "23-5",  //  Batalla de las piedras 
    "19-6",  // Natalicio de Artigas 
    "18-7",  // Jura de la Constitución 
    "15-10",  //  Aniversario del Descubrimiento de América 
    "2-11",  //  Día de Todos los Santos (feriado religioso)  
    "25-12"  //  Natividad del Señor (feriado religioso) (irrenunciable) 
    ); 

  $j= count($arreglo); 
  $diasRestar = 0;
  for($i=0;$i<=$j;$i++) 
    { 
    $dia = $arreglo[$i]; 

    $fecha = getdate($dia); 
    $feriado = $fecha['mday']."-".$fecha['mon']; 
    if(($fecha["wday"] == 0) || ($fecha["wday"] == 6)) 
      { 
      $diasRestar++; 
    } 
    elseif(in_array($feriado,$feriados)) 
      {    
      $diasRestar++; 
    } 
  } 
  $rlt = $j - $diasRestar; 
  return $rlt;
}

///********************************************************************** INICIO SETEO DE CARPETAS ******************************************
//// AHORA SE SETEAN EN EL CONFIG.PHP
//$ip = "192.168.1.145";

//$dirGrafica = "//".$ip."/Reportes/";

///*********************************************************************** FIN SETEO DE CARPETAS ********************************************

/// Recupero la consulta a ejecutar y el mes inicial:
$query = $_SESSION["consulta"];
$fechaInicio = $_SESSION["fechaInicio"];
$fechaFin = $_SESSION["fechaFin"];
$mensaje = $_SESSION["mensaje"];
$criterioFecha = $_SESSION["criterioFecha"];

/*
$query = $_POST["consulta"];
$fechaInicio = $_POST["fechaInicio"];
$fechaFin = $_POST["fechaFin"];
$mensaje = $_POST["mensaje"];
*/

///********************************************************************** RECUPERO DATOS ****************************************************
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
    $datos[$indiceFila] = new \stdClass();
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
///**************************************************** FIN de recuperación de los datos ****************************************************

///**************************************************** INICIO reacomodo de los datos por tipo **********************************************
$meses = array();
$totalRetiros = array();
$totalIngresos = array();
$totalRenos = array();
$totalDestrucciones = array();
$totales = array(0=>$retirosTotal, 1=>$ingresosTotal, 2=>$renosTotal, 3=>$destruccionesTotal);
foreach ($datos as $index => $valor){
  ///Extraigo el año a partir del índice:
  $temp = substr($index, 2, 2);
  ///Extraigo el número de mes a partir del índice:
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
///****************************************************** FIN reacomodo de los datos por tipo ***********************************************

///********************************************************** INICIO cálculos estadísticos **************************************************
/////Instancio objetos del tipo DateTime para las fechas:
$fechainicial1 = new DateTime($fechaInicio);
$fechafinal1 = new DateTime($fechaFin);

//echo "inicio: $fechainicio<br>fin: $fechaFin<br>";
switch ($criterioFecha) {
  case "intervalo": $total = Evalua(DiasHabiles($fechaInicio, $fechaFin));
                    if ($total == 1) {
                      $tipo = "día hábil";
                    }  
                    else {
                      $tipo = "días hábiles";
                    }  
                    break;
  default:  ///Calculo la diferencia entre las fechas y la paso a cantidad de meses:
            $diferencia = $fechainicial1->diff($fechafinal1);
            $total = ($diferencia->y*12) + $diferencia->m + 1;
            if ($total == 1) {
              $tipo = "mes";
            }  
            else {
              $tipo = "meses";
            } 
            break;
}

///Calculo el total de CONSUMOS para agregar el dato a las gráficas:
$consumosTotal = $retirosTotal + $destruccionesTotal + $renosTotal;

///Calculo los promedios según cada tipo:
$avgRetiros = ceil($retirosTotal/$total);
$avgIngresos = ceil($ingresosTotal/$total);
$avgRenos = ceil($renosTotal/$total);
$avgDestrucciones = ceil($destruccionesTotal/$total);
$avgConsumos = ceil($consumosTotal/$total);

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
///*********************************************************** FIN cálculos estadísticos ****************************************************

//********************************************* Defino tamaño de la celda base: c1, y el número *********************************************
$c1 = 18;
$h = 7;
$hFooter = 10;
$orientacion = 'P';
//******************************************************** FIN tamaños de celdas ************************************************************

//******************************************************** INICIO Hora y título *************************************************************
$fecha = date('d/m/Y');
$hora = date('H:i');
//********************************************************** FIN Hora y título **************************************************************

///Título para el encabezado de la página:
$titulo = "EXPORTACIÓN DE LA GRÁFICA";

//Instancio objeto de la clase:
$pdfGrafica = new PDF();
//Agrego una página al documento:
$pdfGrafica->AddPage();

$timestamp = date('dmy_His');

///Caracteres a ser reemplazados en caso de estar presentes en el nombre del producto o la entidad
///Esto se hace para mejorar la lectura (en caso de espacios en blanco), o por requisito para el nombre de la hoja de excel
$aguja = array(0=>" ", 1=>".", 2=>"[", 3=>"]", 4=>"*", 5=>"/", 6=>"\\", 7=>"?", 8=>":", 9=>"_", 10=>"-");
///Se define el tamaño máximo aceptable para el nombre teniendo en cuenta que el excel admite un máximo de 31 caracteres, y que además, 
///ya se tienen 6 fijos del stock_ (movs_ es uno menos).
$tamMaximoNombre = 25;

///A partir del contenido del subtítulo discrimino si es una gráfica para un producto o para una entidad
///y en base a esto, elijo el tipo de gráfica a mostrar:
$producto = strpos($mensaje, 'producto');
if ($producto !== FALSE) {
  $tempProd = explode("del producto ", $mensaje);
  $tipoGrafica = "producto";
}  
else {
  $tempProd = explode("de ", $mensaje);
  $tipoGrafica = "entidad";
} 
///Discrimino para escribir mensaje de fecha según período elegido:
$tempProd1 = stripos($tempProd[1], " entre");
if ($tempProd1 !== false){
  $tempProd2 = explode(" entre", $tempProd[1]);
  $nombreProducto = trim($tempProd2[0]);
}
else {
  $tempProd3 = stripos($tempProd[1], " de");
  if ($tempProd3 !== false){
    $tempProd4 = explode(" de", $tempProd[1]);
    $nombreProducto = trim($tempProd4[0]);
  }
  else {
    $tempProd5 = explode(" del", $tempProd[1]);
    $nombreProducto = trim($tempProd5[0]);
  }
}
$nombreProductoMostrar1 = str_replace($aguja, "", $nombreProducto);
$nombreProductoMostrar = substr($nombreProductoMostrar1, 0, $tamMaximoNombre);
$nombreGrafica = "gca_".$nombreProductoMostrar."_";

$nombreArchivo = $nombreGrafica.$timestamp.".pdf";
$salida = $dirGraficas.$nombreArchivo;

if ($tipoGrafica === "producto"){
  $pdfGrafica->graficarTorta($mensaje, $totales, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgConsumos, 'pdf', $nombreGrafica);
  $pdfGrafica->Output('F', $salida);
  $pdfGrafica->graficarTorta($mensaje, $totales, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgConsumos, '', $nombreGrafica);
}
else {
  $pdfGrafica->graficarBarras($mensaje, $meses, $totales, $totalRetiros, $totalIngresos, $totalRenos, $totalDestrucciones, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgConsumos, 'pdf', $nombreGrafica);
  $pdfGrafica->Output('F', $salida);
  $pdfGrafica->graficarBarras($mensaje, $meses, $totales, $totalRetiros, $totalIngresos, $totalRenos, $totalDestrucciones, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgConsumos, '', $nombreGrafica);
}
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);
?>