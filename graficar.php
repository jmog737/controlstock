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
require_once("css/colores.php");

/** Include JPgraph files */
require_once('../../jpgraph/jpgraph.php');
require_once('../../jpgraph/jpgraph_pie.php');
require_once('../../jpgraph/jpgraph_pie3d.php');
require_once('../../jpgraph/jpgraph_bar.php');
require_once ('../../jpgraph/jpgraph_line.php');

class PDF extends PDF_MC_Table
  {
  ///Constantes usadas por las funciones de ajuste de las imágenes:
  const DPI_300 = 300;
  const MM_IN_INCH = 25.4;
  const GRAFICA_HEIGHT_MM = 100;
  const GRAFICA_WIDTH_MM = 1.85*self::GRAFICA_HEIGHT_MM;
  const GRAFICA_HEIGHT_PX = 400;
  const GRAFICA_WIDTH_PX = 1.85*self::GRAFICA_HEIGHT_PX;
  
  //Cabecera de página
  function Header()
    {
    global $fecha, $hora, $titulo, $hHeader;
    //Agrego logo de EMSA:
    $this->Image('images/logotipo.jpg', 3, 3, 50);
    $this->setY(10);
    $this->setX(20);
    //Defino características para el título y agrego el título:
    $this->SetFont('Arial', 'BU', 24);
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
  
  ///Función auxiliar que ajusta el tamaño de la imagen a los parámetros de ancho y alto pasados:
  ///Esta función está también en generarPDFs. Ver de compartirla.
  function resizeToFit($imgFilename, $ancho, $alto) {
    list($anchoImgPx, $altoImgPx) = getimagesize($imgFilename);
    
    ///Convierto de px a mm (usando los DPI estipulados):
    $anchoImgMm = $anchoImgPx*self::MM_IN_INCH/self::DPI_300;
    $altoImgMm = $altoImgPx*self::MM_IN_INCH/self::DPI_300;
    
    $widthScale = $ancho / $anchoImgMm;
    $heightScale = $alto / $altoImgMm;
    $scale = min($widthScale, $heightScale);
    
    return array(
        round($scale * $anchoImgMm),
        round($scale * $altoImgMm));        
    }  
  
  function graficarBarras($subtitulo, $meses, $totales, $data1, $data2, $data3, $data4, $data5, $data6, $totalRango, $tipoRango, $avg1, $avg2, $avg3, $avg4, $avg41, $avg42, $avg5, $destino, $nombreGrafica){
    global $dirGraficas, $h;
    include "css/colores.php";
    /*
    global $colorRetirosGrafica, $colorRenosGrafica, $colorDestruccionesGrafica, $colorIngresosGrafica, $colorLeyendaRetiros, $colorLeyendaRenos, $colorBordeAjusteRetiros, $colorBordeAjusteIngresos;
    global $colorLeyendaDestrucciones, $colorLeyendaIngresos, $colorLeyendaConsumos, $colorGradiente1, $colorGradiente2, $colorTituloLeyenda, $colorFondoTituloLeyenda1, $colorFondoTituloLeyenda2;
    global $colorNombreEjeX, $colorNombreEjeY, $colorEjeX, $colorEjeY, $colorFrame, $colorBordeRetiros, $colorBordeIngresos, $colorBordeRenos, $colorBordeDestrucciones, $colorLeyendaAjusteRetiros, $colorLeyendaAjusteIngresos;
    global $colorAjusteRetirosGrafica, $colorAjusteIngresosGrafica, $colorFondoLeyendaAjusteRetiros1, $colorFondoLeyendaAjusteRetiros2, $colorFondoLeyendaAjusteIngresos1, $colorFondoLeyendaAjusteIngresos2;
    global $colorFondoLeyendaRetiros1, $colorFondoLeyendaRetiros2, $colorFondoLeyendaRenos1, $colorFondoLeyendaRenos2, $colorFondoLeyendaDestrucciones1, $colorFondoLeyendaDestrucciones2, $colorFondoLeyendaIngresos1, $colorFondoLeyendaIngresos2;
    global $colorFondoLeyendaConsumos1, $colorFondoLeyendaConsumos2, $colorShadowLeyenda, $colorFondoLeyenda, $colorTextoLeyenda, $colorBordeLeyenda;
    */  
    
    // Create the graph. These two calls are always required
    $graph = new Graph(self::GRAFICA_WIDTH_PX, self::GRAFICA_HEIGHT_PX);
    $graph->SetScale("textint");
    $graph->title->Set("MOVIMIENTOS DE STOCK");
    $graph->subtitle->Set($subtitulo." (".$totalRango." ".$tipoRango.").");
    $graph->img->SetMargin(80,250,65,20);
    $graph->SetBackgroundGradient($colorGradiente1, $colorGradiente2, GRAD_HOR,BGRAD_MARGIN);
    //$graph->SetShadow();

    //$theme_class=new UniversalTheme;
    //$graph->SetTheme($theme_class);  
    //$theme_class = new GreenTheme;
    //$graph->SetTheme($theme_class);

    $graph->SetFrame(true, $colorFrame, 0);
    // Setup titles and X-axis labels
    $graph->xaxis->title->Set('Mes');
    $graph->xaxis->title->SetColor($colorNombreEjeX);
    $graph->xaxis->SetColor($colorEjeX); 
    $graph->xaxis->SetTitlemargin(8.2);
    $graph->xaxis->SetLabelMargin(8);
    // Setup Y-axis title
    $graph->yaxis->title->Set('Cantidad de Plásticos');
    $graph->yaxis->title->SetColor($colorNombreEjeY);
    $graph->yaxis->SetColor($colorEjeY);
    $graph->yaxis->SetTitlemargin(60);
    $graph->yaxis->SetLabelMargin(15);

    $graph->ygrid->SetFill(false);
    $graph->xaxis->SetTickLabels($meses);
    $graph->yaxis->HideLine(false);
    $graph->yaxis->HideTicks(false,false);

    ///*************************************************** INICIO Gráficas con los consumos del período: ************************************
    /// Primero detecto si es la gráfica de UN solo tipo, o de varios:
    /// b1 - Retiros
    /// b2 - Ingresos
    /// b3 - Renos
    /// b4 - Destrucciones
    /// b5 - Ajustes Retiros
    /// b6 - Ajustes Ingresos
    
    $tipoMov = '';
    $unMov = false;
    $mostrarB1 = true;
    $mostrarB2 = true;
    $mostrarB3 = true;
    $mostrarB4 = true;
    $mostrarB5 = true;
    $mostrarB6 = true;
    if (stripos($subtitulo, 'Movimientos totales') !== FALSE) {
      $tipoMov = 'Todos';
    }
    elseif (stripos($subtitulo, 'Movimientos') !== FALSE){
      $tipoMov = 'Clientes';
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    elseif (stripos($subtitulo, 'Ajustes') !== FALSE){
      $tipoMov = 'Ajustes';
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
    }
    elseif (stripos($subtitulo, 'AJUSTE Retiros') !== FALSE) {
      $tipoMov = 'AjuRet';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
      $mostrarB6 = false;
    }
    elseif (stripos($subtitulo, 'Renovaciones') !== FALSE) {
      $tipoMov = 'Renos';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB4 = false;
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    elseif (stripos($subtitulo, 'AJUSTE Ingresos') !== FALSE) {
      $tipoMov = 'AjuIng';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
      $mostrarB5 = false;     
    }
    elseif (stripos($subtitulo, 'Destrucciones') !== FALSE) {
      $tipoMov = 'Destrucciones';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    elseif (stripos($subtitulo, 'Retiros') !== FALSE) {
      $tipoMov = 'Retiros';
      $unMov = true;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    else {
      $tipoMov = 'Ingresos';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    
    $barras = array();
    // Create the bar plots
    if ($mostrarB1){
      $b1 = new BarPlot($data1);
      array_push($barras, $b1);
    }
    if ($mostrarB2){
      $b2 = new BarPlot($data2);
      array_push($barras, $b2);
    }
    if ($mostrarB3){
      $b3 = new BarPlot($data3);
      array_push($barras, $b3);
    }
    if ($mostrarB4){
      $b4 = new BarPlot($data4);
      array_push($barras, $b4);
    }
    if ($mostrarB5){
      $b5 = new BarPlot($data5);
      array_push($barras, $b5);
    }
    if ($mostrarB6){
      $b6 = new BarPlot($data6);
      array_push($barras, $b6);
    }
    
    if (($tipoMov === 'Clientes')||($tipoMov === 'Todos')){
      $consumosTemp = $totales[0] + $totales[2] + $totales[3];
      $consumos = number_format($consumosTemp, 0, ',', '.');
    }
    
    $gbplot = new GroupBarPlot($barras);
    $graph->Add($gbplot);
     
    if ($mostrarB1){
      $b1->value->Show();
      $b1->SetColor($colorBordeRetiros);
      $b1->SetFillColor($colorRetirosGrafica);
      $b1->SetLegend("Retiros");
      $b1->SetWidth(0.8);
      $b1->value->SetAlign('left','center');
      $b1->value->SetMargin(30);
      $b1->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
      $b1->value->SetAngle(75);
      $b1->value->SetFormatCallback(formatoDato); 
      /*$b1->value->SetFormat('%d');*/   
      $b1->value->HideZero();
    }
    
    if ($mostrarB2){
      $b2->value->Show();
      $b2->SetColor($colorBordeIngresos);
      $b2->SetFillColor($colorIngresosGrafica);
      $b2->SetLegend("Ingresos");
      $b2->SetWidth(0.8);
      $b2->value->SetMargin(30);
      $b2->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
      $b2->value->SetAngle(75);
      $b2->value->SetFormatCallback("formatoDato"); 
      /*$b2->value->SetFormat('%d');*/
      $b2->value->HideZero();
    }
    
    if ($mostrarB3){
      $b3->value->Show();
      $b3->SetColor($colorBordeRenos);
      $b3->SetFillColor($colorRenosGrafica);
      $b3->SetLegend("Renos");
      $b3->SetWidth(0.8);
      $b3->value->SetMargin(30);
      $b3->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
      $b3->value->SetAngle(75);
      $b3->value->SetFormatCallback(formatoDato); 
      /*$b3->value->SetFormat('%d');*/
      $b3->value->HideZero();
    }
    
    if ($mostrarB4){
      $b4->value->Show();
      $b4->SetColor($colorBordeDestrucciones);
      $b4->SetFillColor($colorDestruccionesGrafica);
      $b4->SetLegend("Destrucciones");
      $b4->SetWidth(0.8);
      $b4->value->SetMargin(30);
      $b4->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
      $b4->value->SetAngle(75);
      $b4->value->SetFormatCallback(formatoDato); 
      /*$b4->value->SetFormat('%d');*/
      $b4->value->HideZero();
    }
    
    if ($mostrarB5){
      $b5->value->Show();
      $b5->SetColor($colorBordeAjusteRetiros);
      $b5->SetFillColor($colorAjusteRetirosGrafica);
      $b5->SetLegend("AJUSTE Retiros");
      $b5->SetWidth(0.8);
      $b5->value->SetAlign('left','center');
      $b5->value->SetMargin(30);
      $b5->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
      $b5->value->SetAngle(75);
      $b5->value->SetFormatCallback(formatoDato); 
      $b5->value->HideZero();
    }
    
    if ($mostrarB6){
      $b6->value->Show();
      $b6->SetColor($colorBordeAjusteIngresos);
      $b6->SetFillColor($colorAjusteIngresosGrafica);
      $b6->SetLegend("AJUSTE Ingresos");
      $b6->SetWidth(0.8);
      $b6->value->SetAlign('left','center');
      $b6->value->SetMargin(30);
      $b6->value->SetFont(FF_ARIAL,FS_NORMAL, 11);
      $b6->value->SetAngle(75);
      $b6->value->SetFormatCallback(formatoDato); 
      $b6->value->HideZero();
    }
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

    ///***************************************************** Cálculo Posición de Legend y Textos según tipo: ********************************
    if ($unMov){
      $posLegendX = 0.03;
      $posLegendY = 0.10;
      $posTituloX = 0.96;
      $posTituloY = 0.22;
      $separacion = 0;
      $posPrimeroX = 0.98;
      $posPrimeroY = $posTituloY + 0.08;
    }
    elseif ($tipoMov === 'Ajustes'){
      $posLegendX = 0.03;
      $posLegendY = 0.10;
      $posTituloX = 0.96;
      $posTituloY = 0.26;
      $separacion = 0.07;
      $posPrimeroX = 0.98;
      $posPrimeroY = $posTituloY + 0.08;
    }
    elseif ($tipoMov === 'Clientes'){
      $posLegendX = 0.03;
      $posLegendY = 0.10;
      $posTituloX = 0.96;
      $posTituloY = 0.35;
      $separacion = 0.07;
      $posPrimeroX = 0.98;
      $posPrimeroY = $posTituloY + 0.08;
    }
    else {
      $posLegendX = 0.03;
      $posLegendY = 0.10;
      $posTituloX = 0.96;
      $posTituloY = 0.42;
      $separacion = 0.07;
      $posPrimeroX = 0.98;
      $posPrimeroY = $posTituloY + 0.08;
    }
    
    ///************************************************** FIN Cálculo Posición de Legend y Textos según tipo: *******************************
    
    ///****************************************************************Formato LEGEND *******************************************************
    $graph->legend->SetShadow($colorShadowLeyenda,1);
    $graph->legend->SetPos($posLegendX, $posLegendY,'right','top');
    //$graph->legend->SetLayout(LEGEND_VER);
    $graph->legend->SetColumns(1);
    $graph->legend->SetColor($colorTextoLeyenda, $colorBordeLeyenda);
    $graph->legend->SetFillColor($colorFondoLeyenda);
    ///************************************************************ FIN Formato LEGEND ******************************************************
    
    ///******************************************************** INICIO Textos con los promedios: ********************************************
    $txt = new Text("DATOS:"); 
    $txt->SetFont(FF_FONT1,FS_BOLD); 
    $txt->Align('right');
    $txt->SetColor($colorTituloLeyenda);
    $txt->SetPos($posTituloX, $posTituloY,'right','center');
    $txt->SetBox($colorFondoTituloLeyenda1, $colorFondoTituloLeyenda2); 
    $graph->AddText($txt); 

    $indSeparacion = 0;
    
    if (($mostrarB1)||($tipoMov === 'Todos')||($tipoMov === 'Clientes')){
      $avg1 = number_format($avg1, 0, ',', '.');
      $retiros = number_format($totales[0], 0, ',', '.');
      $txt1 = new Text("Retiros: ".$retiros." (Avg: ".$avg1.")"); 
      $txt1->SetFont(FF_FONT1,FS_BOLD); 
      $txt1->SetColor($colorLeyendaRetiros);
      $txt1->SetPos($posPrimeroX, $posPrimeroY,'right','center');
      $txt1->SetBox($colorFondoLeyendaRetiros1, $colorFondoLeyendaRetiros2); 
      $graph->AddText($txt1); 
      $indSeparacion ++;
    }
    
    if (($mostrarB3)||($tipoMov === 'Todos')||($tipoMov === 'Clientes')){
      $avg3 = number_format($avg3, 0, ',', '.');
      $renos = number_format($totales[2], 0, ',', '.');
      $txt3 = new Text("Renos: ".$renos." (Avg: ".$avg3.")"); 
      $txt3->SetFont(FF_FONT1,FS_BOLD); 
      $txt3->SetColor($colorLeyendaRenos);
      $txt3->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt3->SetBox($colorFondoLeyendaRenos1, $colorFondoLeyendaRenos2); 
      $graph->AddText($txt3);
      $indSeparacion++;
    }
    
    if (($mostrarB4)||($tipoMov === 'Todos')||($tipoMov === 'Clientes')){
      $avg4 = number_format($avg4, 0, ',', '.');
      $destrucciones = number_format($totales[3], 0, ',', '.');
      $txt4 = new Text("Destrucciones: ".$destrucciones." (Avg: ".$avg4.")"); 
      $txt4->SetFont(FF_FONT1,FS_BOLD); 
      $txt4->SetColor($colorLeyendaDestrucciones);
      $txt4->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt4->SetBox($colorFondoLeyendaDestrucciones1, $colorFondoLeyendaDestrucciones2); 
      $graph->AddText($txt4);
      $indSeparacion++;
    }
    
    if (($tipoMov === 'Clientes')||($tipoMov === 'Todos')){
      $avg5 = number_format($avg5, 0, ',', '.');
      $txt5 = new Text("Consumos: ".$consumos." (Avg: ".$avg5.")"); 
      $txt5->SetFont(FF_FONT1,FS_BOLD); 
      $txt5->SetColor($colorLeyendaConsumos);
      $txt5->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt5->SetBox($colorFondoLeyendaConsumos1, $colorFondoLeyendaConsumos2); 
      $graph->AddText($txt5);
      $indSeparacion++;
    }
    
    if (($mostrarB2)||($tipoMov === 'Todos')||($tipoMov === 'Clientes')){
      $avg2 = number_format($avg2, 0, ',', '.');
      $ingresos = number_format($totales[1], 0, ',', '.');
      $txt2 = new Text("Ingresos: ".$ingresos." (Avg: ".$avg2.")"); 
      $txt2->SetFont(FF_FONT1,FS_BOLD); 
      $txt2->SetColor($colorLeyendaIngresos);
      $txt2->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt2->SetBox($colorFondoLeyendaIngresos1, $colorFondoLeyendaIngresos2); 
      $graph->AddText($txt2); 
      $indSeparacion++;
    }
    
    if (($mostrarB5)||($tipoMov === 'Ajustes')||($tipoMov === 'Todos')){
      $avg41 = number_format($avg41, 0, ',', '.');
      $ajusteRetiros = number_format($totales[4], 0, ',', '.');
      $txt41 = new Text("AJUSTE Retiros: ".$ajusteRetiros." (Avg: ".$avg41.")"); 
      $txt41->SetFont(FF_FONT1,FS_BOLD); 
      $txt41->SetColor($colorLeyendaAjusteRetiros);
      $txt41->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt41->SetBox($colorFondoLeyendaAjusteRetiros1, $colorFondoLeyendaAjusteRetiros2); 
      $graph->AddText($txt41);
      $indSeparacion++;
    }
    
    if (($mostrarB6)||($tipoMov === 'Ajustes')||($tipoMov === 'Todos')){
      $avg42 = number_format($avg42, 0, ',', '.');
      $ajusteIngresos = number_format($totales[5], 0, ',', '.');
      $txt42 = new Text("AJUSTE Ingresos: ".$ajusteIngresos." (Avg: ".$avg42.")"); 
      $txt42->SetFont(FF_FONT1,FS_BOLD); 
      $txt42->SetColor($colorLeyendaAjusteIngresos);
      $txt42->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt42->SetBox($colorFondoLeyendaAjusteIngresos1, $colorFondoLeyendaAjusteIngresos2); 
      $graph->AddText($txt42);
    }
    ///************************************************************ FIN Textos con los promedios: *******************************************
    
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

      $titulo = utf8_decode("GRÁFICA CON LAS ESTADÍSTICAS:");
      $tam = $this->GetStringWidth($titulo);
      $anchoPagina = $this->GetPageWidth();
      $xInicio = ($anchoPagina-$tam)/2;
      $this->SetX($xInicio);

      $this->Cell($tam, 1.5*$h, $titulo, 0, 0, 'C', 0);
      $this->Ln(20);
      
      list($anchoGrafica, $altoGrafica) = $this->resizeToFit($fileName, self::GRAFICA_WIDTH_MM, self::GRAFICA_HEIGHT_MM);
      $xGrafica = round(($anchoPagina - $anchoGrafica)/2);
      $y = $this->getY();
      $this->Image($fileName, $xGrafica, $y, $anchoGrafica, $altoGrafica);
    }
    else {
      $graph->Stroke();
      $graph->img->Headers();
      $graph->img->Stream();
    }  
  }

  function graficarTorta($subtitulo, $datos, $totalRango, $tipoRango, $avg1, $avg2, $avg3, $avg4, $avg41, $avg42, $avg5, $destino, $nombreGrafica){
    global $dirGraficas, $h;
    include "css/colores.php"; 
//    global $coloresTorta, $colorLeyendaRetiros, $colorLeyendaRenos, $colorShadowLeyendaPie, $colorTituloLeyendaPie;
//    global $colorFondoTituloLeyendaPie1, $colorFondoTituloLeyendaPie2, $colorFondoLeyenda, $colorTextoLeyenda, $colorBordeLeyenda;
//    global $colorLeyendaDestrucciones, $colorLeyendaIngresos, $colorLeyendaConsumos, $colorBackgroundTorta;
//    global $colorFondoLeyendaRetiros1, $colorFondoLeyendaRetiros2, $colorFondoLeyendaRenos1, $colorFondoLeyendaRenos2, $colorFondoLeyendaDestrucciones1, $colorFondoLeyendaDestrucciones2, $colorFondoLeyendaIngresos1, $colorFondoLeyendaIngresos2;
//    global $colorFondoLeyendaConsumos1, $colorFondoLeyendaConsumos2, $colorPorcentajes;
//    global $colorFondoLeyendaAjusteRetiros1, $colorFondoLeyendaAjusteRetiros2, $colorFondoLeyendaAjusteIngresos1, $colorFondoLeyendaAjusteIngresos2;
//    global $colorLeyendaAjusteRetiros, $colorLeyendaAjusteIngresos; 
      
    // A new pie graph
    $graph = new PieGraph(self::GRAFICA_WIDTH_PX, self::GRAFICA_HEIGHT_PX, 'auto');
    // Setup background

    $graph->title->Set("MOVIMIENTOS DE STOCK");
    $graph->subtitle->Set($subtitulo." (".$totalRango." ".$tipoRango.").");
    $graph->img->SetMargin(80,190,65,20);
    $graph->SetMargin(1,1,40,1);
    $graph->SetMarginColor($colorBackgroundTorta);

    ///*************************************************** INICIO Gráficas con los consumos del período: ************************************
    /// Primero detecto si es la gráfica de UN solo tipo, o de varios:
    /// b1 - Retiros
    /// b2 - Ingresos
    /// b3 - Renos
    /// b4 - Destrucciones
    /// b5 - Ajustes Retiros
    /// b6 - Ajustes Ingresos
    
    $tipoMov = '';
    $unMov = false;
    $mostrarB1 = true;
    $mostrarB2 = true;
    $mostrarB3 = true;
    $mostrarB4 = true;
    $mostrarB5 = true;
    $mostrarB6 = true;
    if (stripos($subtitulo, 'Movimientos totales') !== FALSE) {
      $tipoMov = 'Todos';
    }
    elseif (stripos($subtitulo, 'Movimientos') !== FALSE){
      $tipoMov = 'Clientes';
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    elseif (stripos($subtitulo, 'Ajustes') !== FALSE){
      $tipoMov = 'Ajustes';
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
    }
    elseif (stripos($subtitulo, 'AJUSTE Retiros') !== FALSE) {
      $tipoMov = 'AjuRet';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
      $mostrarB6 = false;
    }
    elseif (stripos($subtitulo, 'Renovaciones') !== FALSE) {
      $tipoMov = 'Renos';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB4 = false;
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    elseif (stripos($subtitulo, 'AJUSTE Ingresos') !== FALSE) {
      $tipoMov = 'AjuIng';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
      $mostrarB5 = false;     
    }
    elseif (stripos($subtitulo, 'Destrucciones') !== FALSE) {
      $tipoMov = 'Destrucciones';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    elseif (stripos($subtitulo, 'Retiros') !== FALSE) {
      $tipoMov = 'Retiros';
      $unMov = true;
      $mostrarB2 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    else {
      $tipoMov = 'Ingresos';
      $unMov = true;
      $mostrarB1 = false;
      $mostrarB3 = false;
      $mostrarB4 = false;
      $mostrarB5 = false;
      $mostrarB6 = false;
    }
    
    ///************************* Comienza separación del array con los datos para mejora manipulación según el caso *************************
    $ajusteIngresos = array_pop($datos);
    $ajusteRetiros = array_pop($datos);
    $destrucciones = array_pop($datos);
    $renos = array_pop($datos);
    $ingresos = array_pop($datos);
    $retiros = array_pop($datos);
    
    $retiros = number_format($retiros, 0, ',', '.');
    $ingresos = number_format($ingresos, 0, ',', '.');
    $renos = number_format($renos, 0, ',', '.');
    $destrucciones = number_format($destrucciones, 0, ',', '.');
    $ajusteRetiros = number_format($ajusteRetiros, 0, ',', '.');
    $ajusteIngresos = number_format($ajusteIngresos, 0, ',', '.');
    $consumosTemp = $retiros+$renos+$destrucciones;
    $consumos = number_format($consumosTemp, 0, ',', '.');
    
    $colorAjusteIngresos = array_pop($coloresTorta);
    $colorAjusteRetiros = array_pop($coloresTorta);
    $colorDestrucciones = array_pop($coloresTorta);
    $colorRenos = array_pop($coloresTorta);
    $colorIngresos = array_pop($coloresTorta);
    $colorRetiros = array_pop($coloresTorta);
    
    $colores = array();    
    ///*************************** FIN separación del array con los datos para mejora manipulación según el caso ****************************
    
    switch ($tipoMov){
      case 'Retiros': $p1 = new PiePlot3D($retiros);
                      $colores[] = $colorRetiros;
                      break;
      case 'Renos': $p1 = new PiePlot3D($renos);
                    $colores[] = $colorRenos;
                    break;
      case 'Destrucciones': $p1 = new PiePlot3D($destrucciones);
                            $colores[] = $colorDestrucciones;
                            break;
      case 'Ingresos': $p1 = new PiePlot3D($ingresos);
                       $colores[] = $colorIngresos;
                       break;
      case 'AjuRet':  $p1 = new PiePlot3D($ajusteRetiros);
                      $colores[] = $colorAjusteRetiros;
                      break;
      case 'AjuIng':  $p1 = new PiePlot3D($ajusteIngresos);
                      $colores[] = $colorAjusteIngresos;
                      break;
      case 'Ajustes': $data = array();
                      $data[]= $ajusteRetiros;
                      $data[] = $ajusteIngresos;
                      $colores[] = $colorAjusteRetiros;
                      $colores[] = $colorAjusteIngresos;
                      $p1 = new PiePlot3D($data);
                      break;
      case 'Clientes': $data = array();
                       $data[] = $retiros;
                       $data[] = $renos;
                       $data[] = $destrucciones;
                       $data[] = $ingresos;
                       $colores[] = $colorRetiros;
                       $colores[] = $colorRenos;
                       $colores[] = $colorDestrucciones;
                       $colores[] = $colorIngresos;
                       $p1 = new PiePlot3D($data);
                       break;
      case 'Todos': $data = array();
                    $data[] = $retiros;
                    $data[] = $renos;
                    $data[] = $destrucciones;
                    $data[] = $ingresos;
                    $data[] = $ajusteRetiros;
                    $data[] = $ajusteIngresos;
                    $colores[] = $colorRetiros;
                    $colores[] = $colorRenos;
                    $colores[] = $colorDestrucciones;
                    $colores[] = $colorIngresos;
                    $colores[] = $colorAjusteRetiros;
                    $colores[] = $colorAjusteIngresos;
                    $p1 = new PiePlot3D($data);
                    break;               
      default: break;              
    }
    
    $p1->ShowBorder(true, true);
    
    // Adjust size and position of plot
    $p1->SetSize(0.4);
    $p1->SetCenter(0.43,0.5);
    $p1->SetHeight(20);
    $p1->SetAngle(50);
    
    $leyendas = array();
    $retirosLeyenda = "Retiros: $retiros";
    $renosLeyenda = "Renos: $renos";
    $destruccionesLeyenda = "Destrucciones: $destrucciones";
    $ingresosLeyenda = "Ingresos: $ingresos";
    $ajuRetirosLeyenda = "Ajuste Retiros: $ajusteRetiros";
    $ajuIngresosLeyenda = "Ajuste Ingresos: $ajusteIngresos";
    
    if (($mostrarB1)||($tipoMov === 'Clientes')||($tipoMov === 'Todos')){ 
      $leyendas[] = $retirosLeyenda;
    }
    if (($mostrarB3)||($tipoMov === 'Clientes')||($tipoMov === 'Todos')){
      $leyendas[] = $renosLeyenda;
    }
    if (($mostrarB4)||($tipoMov === 'Clientes')||($tipoMov === 'Todos')){
      $leyendas[] = $destruccionesLeyenda;
    }
    if (($mostrarB2)||($tipoMov === 'Clientes')||($tipoMov === 'Todos')){
      $leyendas[] = $ingresosLeyenda;
    }
    if (($mostrarB5)||($tipoMov === 'Ajustes')||($tipoMov === 'Todos')){ 
      $leyendas[] = $ajuRetirosLeyenda;
    }
    if (($mostrarB6)||($tipoMov === 'Ajustes')||($tipoMov === 'Todos')){ 
      $leyendas[] = $ajuIngresosLeyenda;
    }
    
    $p1->SetLegends($leyendas);
    
    ///***************************************************** Cálculo Posición de Legend y Textos según tipo: ********************************
    if ($unMov){
      $posLegendX = 0.03;
      $posLegendY = 0.12;
      $posTituloX = 0.955;
      $posTituloY = $posLegendY + 0.12;
      $separacion = 0;
      $posPrimeroX = 0.97;
      $posPrimeroY = $posTituloY + 0.09;
    }
    elseif ($tipoMov === 'Ajustes'){
      $posLegendX = 0.03;
      $posLegendY = 0.12;
      $posTituloX = 0.935;
      $posTituloY = $posLegendY + 0.15;
      $separacion = 0.07;
      $posPrimeroX = 0.97;
      $posPrimeroY = $posTituloY + 0.08;
    }
    elseif ($tipoMov === 'Clientes'){
      $posLegendX = 0.03;
      $posLegendY = 0.11;
      $posTituloX = 0.96;
      $posTituloY = $posLegendY + 0.22;
      $separacion = 0.07;
      $posPrimeroX = 0.97;
      $posPrimeroY = $posTituloY + 0.08;
    }
    else {
      $posLegendX = 0.03;
      $posLegendY = 0.11;
      $posTituloX = 0.96;
      $posTituloY = $posLegendY + 0.31;
      $separacion = 0.07;
      $posPrimeroX = 0.97;
      $posPrimeroY = $posTituloY + 0.08;
    }
    ///************************************************** FIN Cálculo Posición de Legend y Textos según tipo: *******************************  
    $graph->legend->SetShadow($colorShadowLeyendaPie,1);
    $graph->legend->SetPos($posLegendX, $posLegendY,'right','top');
    $graph->legend->SetLayout(LEGEND_VER);
    $graph->legend->SetColumns(1);
    $graph->legend->SetColor($colorTextoLeyenda, $colorBordeLeyenda);
    $graph->legend->SetFillColor($colorFondoLeyenda);

    // Setup the labels
    $p1->SetLabelType(PIE_VALUE_ADJPERCENTAGE);    
    $p1->SetLabelMargin(10);

    $p1->value->SetColor($colorPorcentajes);
    $p1->value->SetFont(FF_FONT1,FS_BOLD);    
    $p1->value->SetFormat('%d%%');  
    $p1->value->HideZero();
    $p1->value->Show(); 
    $p1->ExplodeAll(18);
    $graph->Add($p1);
    
    ///Se setean los colores para las "porciones". HAY QUE HACERLO LUEGO DEL ADD PORQUE DE LO CONTRARIO NO LOS TOMA!!!
    $p1->SetSliceColors($colores);
    
    ///******************************************************** INICIO Textos con los promedios: ***************************************************************
    $txt = new Text("PROMEDIOS:"); 
    $txt->SetFont(FF_FONT1,FS_BOLD); 
    $txt->Align('right');
    $txt->SetColor($colorTituloLeyendaPie);
    $txt->SetPos($posTituloX, $posTituloY,'right','center');
    $txt->SetBox($colorFondoTituloLeyendaPie1, $colorFondoTituloLeyendaPie2); 
    $graph->AddText($txt); 

    $indSeparacion = 0;
    if (($mostrarB1)||($tipoMov === 'Todos')||($tipoMov === 'Clientes')){
      $avg1 = number_format($avg1, 0, ',', '.');
      $txt1 = new Text("Retiros: ".$avg1); 
      $txt1->SetFont(FF_FONT1,FS_BOLD); 
      $txt1->SetColor($colorLeyendaRetiros);
      $txt1->SetPos($posPrimeroX, $posPrimeroY,'right','center');
      $txt1->SetBox($colorFondoLeyendaRetiros1, $colorFondoLeyendaRetiros2); 
      $graph->AddText($txt1); 
      $indSeparacion++;
    }
    
    if (($mostrarB3)||($tipoMov === 'Todos')||($tipoMov === 'Clientes')){
      $avg3 = number_format($avg3, 0, ',', '.');
      $txt3 = new Text("Renos: ".$avg3); 
      $txt3->SetFont(FF_FONT1,FS_BOLD); 
      $txt3->SetColor($colorLeyendaRenos);
      $txt3->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt3->SetBox($colorFondoLeyendaRenos1, $colorFondoLeyendaRenos2); 
      $graph->AddText($txt3); 
      $indSeparacion++;
    }
    
    if (($mostrarB4)||($tipoMov === 'Todos')||($tipoMov === 'Clientes')){
      $avg4 = number_format($avg4, 0, ',', '.');
      $txt4 = new Text("Destrucciones: ".$avg4); 
      $txt4->SetFont(FF_FONT1,FS_BOLD); 
      $txt4->SetColor($colorLeyendaDestrucciones);
      $txt4->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt4->SetBox($colorFondoLeyendaDestrucciones1, $colorFondoLeyendaDestrucciones2); 
      $graph->AddText($txt4); 
      $indSeparacion++;
    }
    
    if (($tipoMov === 'Clientes')||($tipoMov === 'Todos')){
      $avg5 = number_format($avg5, 0, ',', '.');
      $txt5 = new Text("Consumos: ".$avg5." (Total ".$consumos.")"); 
      $txt5->SetFont(FF_FONT1,FS_BOLD); 
      $txt5->SetColor($colorLeyendaConsumos);
      $txt5->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt5->SetBox($colorFondoLeyendaConsumos1, $colorFondoLeyendaConsumos2); 
      $graph->AddText($txt5); 
      $indSeparacion++;
    }
  
    if (($mostrarB2)||($tipoMov === 'Todos')||($tipoMov === 'Clientes')){ 
      $avg2 = number_format($avg2, 0, ',', '.');
      $txt2 = new Text("Ingresos: ".$avg2); 
      $txt2->SetFont(FF_FONT1,FS_BOLD); 
      $txt2->SetColor($colorLeyendaIngresos);
      $txt2->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt2->SetBox($colorFondoLeyendaIngresos1, $colorFondoLeyendaIngresos2); 
      $graph->AddText($txt2); 
      $indSeparacion++;
    }
    
    if (($mostrarB5)||($tipoMov === 'Todos')||($tipoMov === 'Ajustes')){
      $avg41 = number_format($avg41, 0, ',', '.');
      $txt41 = new Text("Ajuste Retiros: ".$avg41); 
      $txt41->SetFont(FF_FONT1,FS_BOLD); 
      $txt41->SetColor($colorLeyendaAjusteRetiros);
      $txt41->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt41->SetBox($colorFondoLeyendaAjusteRetiros1, $colorFondoLeyendaAjusteRetiros2); 
      $graph->AddText($txt41); 
      $indSeparacion++;
    }
    
    if (($mostrarB6)||($tipoMov === 'Todos')||($tipoMov === 'Ajustes')){
      $avg42 = number_format($avg42, 0, ',', '.');
      $txt42 = new Text("Ajuste Ingresos: ".$avg42); 
      $txt42->SetFont(FF_FONT1,FS_BOLD); 
      $txt42->SetColor($colorLeyendaAjusteIngresos);
      $txt42->SetPos($posPrimeroX, $posPrimeroY+$indSeparacion*$separacion,'right','center');
      $txt42->SetBox($colorFondoLeyendaAjusteIngresos1, $colorFondoLeyendaAjusteIngresos2); 
      $graph->AddText($txt42); 
    }
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

      $titulo = utf8_decode("GRÁFICA CON LAS ESTADÍSTICAS:");
      $tam = $this->GetStringWidth($titulo);
      $anchoPagina = $this->GetPageWidth();
      $xInicio = ($anchoPagina-$tam)/2;
      $this->SetX($xInicio);

      $this->Cell($tam, 1.5*$h, $titulo, 0, 0, 'C', 0);
      $this->Ln(20);
      
      list($anchoGrafica, $altoGrafica) = $this->resizeToFit($fileName, self::GRAFICA_WIDTH_MM, self::GRAFICA_HEIGHT_MM);
      $xGrafica = round(($anchoPagina - $anchoGrafica)/2);
      $y = $this->getY();
      $this->Image($fileName, $xGrafica, $y, $anchoGrafica, $altoGrafica);
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
$ajusteRetiros = 0;
$ajusteIngresos = 0;

$retirosTotal = 0;
$ingresosTotal = 0;
$renosTotal = 0;
$destruccionesTotal = 0;
$ajusteRetirosTotal = 0;
$ajusteIngresosTotal = 0;

$totalDatos = $result->num_rows;
$indice = $fechaInicio;
while (($fila = $result->fetch_array(MYSQLI_BOTH)) != NULL) { 
  $cantidad = $fila["cantidad"];
  $tipoActual = $fila["tipo"];  
  $fechaFila = $fila["fecha"];
  $fechaTemp = explode('-', $fechaFila);
  $indiceFila = $fechaTemp[0].$fechaTemp[1];
  if ($indiceFila !== $indice) {
    if (($retiros !== 0)||($ingresos !== 0)||($renos !== 0)||($destrucciones !== 0)||($ajusteRetiros !== 0)||($ajusteIngresos !== 0)) 
      {
      $datos[$indice]->retiros = $retiros;
      $datos[$indice]->ingresos = $ingresos;
      $datos[$indice]->renos = $renos;
      $datos[$indice]->destrucciones = $destrucciones;
      $datos[$indice]->ajusteRetiros = $ajusteRetiros;
      $datos[$indice]->ajusteIngresos = $ajusteIngresos;
    }
    else {
      array_splice($datos,$indice, 1);
    }
    $datos[$indiceFila] = new \stdClass();
    $datos[$indiceFila]->retiros = 0;
    $datos[$indiceFila]->ingresos = 0;
    $datos[$indiceFila]->renos = 0;
    $datos[$indiceFila]->destrucciones = 0;
    $datos[$indiceFila]->ajusteRetiros = 0;
    $datos[$indiceFila]->ajusteIngresos = 0;
    
    $indice = $indiceFila;
    $retiros = 0;
    $ingresos = 0;
    $destrucciones = 0;
    $renos = 0;
    $ajusteRetiros = 0;
    $ajusteIngresos = 0;
  }
  switch ($tipoActual) {
    case "Retiro": $retiros += $cantidad;
                   $retirosTotal += $cantidad;
                   break;
    case "Ingreso": $ingresos += $cantidad;
                    $ingresosTotal += $cantidad;
                    break;
    case "Renovación": $renos += $cantidad;
                       $renosTotal += $cantidad;
                       break;
    case "Destrucción": $destrucciones += $cantidad;
                        $destruccionesTotal += $cantidad;
                        break;  
    case "AJUSTE Retiro": $ajusteRetiros += $cantidad;
                          $ajusteRetirosTotal += $cantidad;
                          break;
    case "AJUSTE Ingreso": $ajusteIngresos += $cantidad;
                           $ajusteIngresosTotal += $cantidad;
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
$datos[$indice]->ajusteRetiros = $ajusteRetiros;
$datos[$indice]->ajusteIngresos = $ajusteIngresos;
///**************************************************** FIN de recuperación de los datos ****************************************************

///********************************************************** Recupero TIPO Movimiento ******************************************************
$tipoMov = '';
$unMov = false;
if (stripos($mensaje, 'Movimientos totales') !== FALSE) {
  $tipoMov = 'Todos';
}
elseif (stripos($mensaje, 'Movimientos') !== FALSE){
  $tipoMov = 'Clientes';
}
elseif (stripos($mensaje, 'Ajustes') !== FALSE){
  $tipoMov = 'Ajustes';
}
elseif (stripos($mensaje, 'AJUSTE Retiros') !== FALSE) {
  $tipoMov = 'AjuRet';
  $unMov = true;
}
elseif (stripos($mensaje, 'Renovaciones') !== FALSE) {
  $tipoMov = 'Renos';
  $unMov = true;
}
elseif (stripos($mensaje, 'AJUSTE Ingresos') !== FALSE) {
  $tipoMov = 'AjuIng';
  $unMov = true;
}
elseif (stripos($mensaje, 'Destrucciones') !== FALSE) {
  $tipoMov = 'Destrucciones';
  $unMov = true;
}
elseif (stripos($mensaje, 'Retiros') !== FALSE) {
  $tipoMov = 'Retiros';
  $unMov = true;
}
else {
  $tipoMov = 'Ingresos';
  $unMov = true;
}  
///******************************************************** FIN Recupero Tipo Movimiento ***************************************************    

///**************************************************** INICIO reacomodo de los datos por tipo **********************************************
$meses = array();
$totalRetiros = array();
$totalIngresos = array();
$totalRenos = array();
$totalDestrucciones = array();
$totalAjusteRetiros = array();
$totalAjusteIngresos = array();
$totales = array(0=>$retirosTotal, 1=>$ingresosTotal, 2=>$renosTotal, 3=>$destruccionesTotal, 4=>$ajusteRetirosTotal, 5=>$ajusteIngresosTotal);
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
  array_push($totalAjusteRetiros, $datos[$index]->ajusteRetiros);
  array_push($totalAjusteIngresos, $datos[$index]->ajusteIngresos);
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
//            $diferencia = $fechainicial1->diff($fechafinal1);
//            $total = ($diferencia->y*12) + $diferencia->m;
//            if ($total == 1) {
//              $tipo = "mes ".$dias1."d - ".$mesesitos."m - ".$añitos."a";
//            }  
//            else {
//              $tipo = "meses ".$dias1."d - ".$mesesitos."m - ".$añitos."a";
//            } 
            $temp0 = explode('-', $fechaInicio);
            $mesInicio = $temp0[1];
            $añoInicio = $temp0[0];
            $temp1 = explode('-', $fechaFin);
            $mesFin = $temp1[1];
            $añoFin = $temp1[0];
            if ($añoInicio === $añoFin){
              if ($mesInicio === $mesFin){
                $tipo = "mes";
                $total = 1;
              }
              else {
                $tipo = "meses";
                $total = $mesFin - $mesInicio + 1;
              }
            }
            else {
              $tipo = "meses";
              $total = 12 - $mesInicio + $mesFin + 1;
              if (($añoFin - $añoInicio)>1){
                $total += ($añoFin - $añoInicio)*12;
              }
            }
            //$tipo = "total: ".$total." ".$tipo;
            break;
}

///Calculo el total de CONSUMOS para agregar el dato a las gráficas:
$consumosTotal = $retirosTotal + $destruccionesTotal + $renosTotal;

///Calculo los promedios según cada tipo:
$avgRetiros = ceil($retirosTotal/$total);
$avgIngresos = ceil($ingresosTotal/$total);
$avgRenos = ceil($renosTotal/$total);
$avgDestrucciones = ceil($destruccionesTotal/$total);
$avgAjusteRetiros = ceil($ajusteRetirosTotal/$total);
$avgAjusteIngresos = ceil($ajusteIngresosTotal/$total);
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
$titulo = "ESTADÍSTICAS";

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
  if ($unMov){
    $pdfGrafica->graficarBarras($mensaje, $meses, $totales, $totalRetiros, $totalIngresos, $totalRenos, $totalDestrucciones, $totalAjusteRetiros, $totalAjusteIngresos, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgAjusteRetiros, $avgAjusteIngresos, $avgConsumos, 'pdf', $nombreGrafica);
    $pdfGrafica->Output('F', $salida);
    $pdfGrafica->graficarBarras($mensaje, $meses, $totales, $totalRetiros, $totalIngresos, $totalRenos, $totalDestrucciones, $totalAjusteRetiros, $totalAjusteIngresos, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgAjusteRetiros, $avgAjusteIngresos, $avgConsumos, '', $nombreGrafica); 
  }
  else {
    $pdfGrafica->graficarTorta($mensaje, $totales, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgAjusteRetiros, $avgAjusteIngresos, $avgConsumos, 'pdf', $nombreGrafica);
    $pdfGrafica->Output('F', $salida);
    $pdfGrafica->graficarTorta($mensaje, $totales, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgAjusteRetiros, $avgAjusteIngresos, $avgConsumos, '', $nombreGrafica);
  }
}
else {
  $pdfGrafica->graficarBarras($mensaje, $meses, $totales, $totalRetiros, $totalIngresos, $totalRenos, $totalDestrucciones, $totalAjusteRetiros, $totalAjusteIngresos, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgAjusteRetiros, $avgAjusteIngresos, $avgConsumos, 'pdf', $nombreGrafica);
  $pdfGrafica->Output('F', $salida);
  $pdfGrafica->graficarBarras($mensaje, $meses, $totales, $totalRetiros, $totalIngresos, $totalRenos, $totalDestrucciones, $totalAjusteRetiros, $totalAjusteIngresos, $total, $tipo, $avgRetiros, $avgIngresos, $avgRenos, $avgDestrucciones, $avgAjusteRetiros, $avgAjusteIngresos, $avgConsumos, '', $nombreGrafica);
}
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);
?>