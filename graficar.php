<?php
/**
******************************************************
*  @file graficar.php
*  @brief Funciones para realizar las gráficas.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Diciembre 2017
*
*******************************************************/

/** Include JPgraph files */
require_once('..\..\jpgraph\jpgraph.php');
require_once('..\..\jpgraph\jpgraph_pie.php');
require_once('..\..\jpgraph\jpgraph_bar.php');

function graficarBarras($meses, $data1, $data2, $data3, $data4){
  // Create the graph. These two calls are always required
  $graph = new Graph(700,300,'auto');
  $graph->SetScale("textlin");
  $graph->title->Set("Movimientos");
  $graph->img->SetMargin(80,10,40,20);
  //$graph->SetShadow();
  //$theme_class=new UniversalTheme;
  //$graph->SetTheme($theme_class);
  $graph->SetFrame(true,'red',0);
  // Setup titles and X-axis labels
  $graph->xaxis->title->Set('Mes');
  $graph->xaxis->SetTitlemargin(30);
  $graph->xaxis->SetLabelMargin(15);
  // Setup Y-axis title
  $graph->yaxis->title->Set('Cantidad');
  $graph->yaxis->SetTitlemargin(60);
  $graph->yaxis->SetLabelMargin(15);

  //$graph->yaxis->SetTickPositions($data1, $data2, $data3, $data4);
  //$graph->SetBox(false);

  $graph->ygrid->SetFill(false);
  $graph->xaxis->SetTickLabels($meses);
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(false,false);

  // Create the bar plots
  $b1plot = new BarPlot($data1);
  $b2plot = new BarPlot($data2);
  $b3plot = new BarPlot($data3);
  $b4plot = new BarPlot($data4);

  // Create the grouped bar plot
  $gbplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot,$b4plot));
  // ...and add it to the graPH
  $graph->Add($gbplot);

  $b1plot->SetColor("white");
  $b1plot->SetFillColor("#F08A1D");
  $b1plot->SetLegend("Retiros");
  $b1plot->value->Show();
  $b1plot->value->SetFont(FF_ARIAL,FS_NORMAL, 8);
  $b1plot->SetFormat('01.2f');
  
  $b2plot->SetColor("white");
  $b2plot->SetFillColor("#258246");
  $b2plot->SetLegend("Ingresos");
  $b2plot->value->Show();
  $b2plot->value->SetFont(FF_ARIAL,FS_NORMAL, 8);
  
  $b3plot->SetColor("white");
  $b3plot->SetFillColor("#1111cc");
  $b3plot->SetLegend("Renos");
  $b3plot->value->Show();
  $b3plot->value->SetFont(FF_ARIAL,FS_NORMAL, 8);
  
  $b4plot->SetColor("white");
  $b4plot->SetFillColor("#FF0719");
  $b4plot->SetLegend("Destrucciones");
  $b4plot->value->Show();
  $b4plot->value->SetFont(FF_ARIAL,FS_NORMAL, 8);
  
  $graph->legend->SetShadow('gray@0.4',5);
  $graph->legend->SetPos(0.01,0.05,'right','top');
  
  // Display the graph
  $graph->Stroke();

}

$unidad = "D:";
$ip = "192.168.1.145";

//$dir = $unidad.":/PROCESOS/PDFS";
$dirExcel = "//".$ip."/Reportes/";

if (!file_exists($unidad)) {
  $unidad = "C:";
}

if (!file_exists($dirExcel)){
  echo "No existe la carpeta. Por favor verifique.";
}

$mesesTemp = $_POST["meses"];
$meses = explode(",", $mesesTemp);

$retirosTemp = $_POST["retiros"];
$retiros = explode(",", $retirosTemp);

$ingresosTemp = $_POST["ingresos"];
$ingresos = explode(",", $ingresosTemp);

$renosTemp = $_POST["renos"];
$renos = explode(",", $renosTemp);

$destruccionesTemp = $_POST["destrucciones"];
$destrucciones = explode(",", $destruccionesTemp);

graficarBarras($meses, $retiros, $ingresos, $renos, $destrucciones);

?>
