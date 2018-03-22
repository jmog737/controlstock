<?php
//Reanudamos la sesión:
session_start();
require_once("data/sesiones.php");
require_once('data/baseMysql.php');
require_once('generarExcel.php');
require_once('generarPdfs.php');
//require_once('enviarMails.php');
require_once('data/config.php');

//phpinfo();
//***************************** DESTINATARIOS CORREOS ***********************************************************************************************
//$paraListados = array();
//$copiaListados = array();
//$ocultosListados = array();

//**************** PRUEBAS ************************************************************
//$copiaListados['Juan Martín Ortega'] = "juanortega@emsa.com.uy";
//**************** FIN PRUEBAS ********************************************************

//****************************************************IMPORTANTE:************************************************************************************
//                                              SETEO DE LAS CARPETAS
//// AHORA SE SETEAN EN EL CONFIG.PHP
//***************************************************************************************************************************************************

//********************************************* Defino tamaño de la celda base: c1, y el número ************************************************
$c1 = 18;
$h = 6;
$hHeader = 30;
$hFooter = 10;
//******************************************************** FIN tamaños de celdas ***************************************************************

//******************************************************** INICIO Hora y título ****************************************************************
$fecha = date('d/m/Y');
$hora = date('H:i');
//********************************************************** FIN Hora y título *****************************************************************

$id = $_POST["idTipo"];
$indice = $_POST["indice"];

$query = $_POST["query_$indice"];
$consultaCSV = $_POST["consultaCSV_$indice"];

$tipoConsulta = strip_tags($_POST["tipoConsulta_$indice"]);
//echo $tipoConsulta;
$mostrar1 = utf8_decode($_POST["mostrar"]);
$mostrar = preg_split("/-/", $mostrar1);

$campos1 = utf8_decode($_POST["campos"]);
$campos = preg_split("/-/", $campos1);

$largos = $_POST["largos"];
$temp = explode('-', $largos);

$x = $_POST["x"];

if (isset($_POST["idProd"])){
  $idProd = $_POST["idProd"];
}

///Caracteres a ser reemplazados en caso de estar presentes en el nombre del producto o la entidad
///Esto se hace para mejorar la lectura (en caso de espacios en blanco), o por requisito para el nombre de la hoja de excel
$aguja = array(0=>" ", 1=>".", 2=>"[", 3=>"]", 4=>"*", 5=>"/", 6=>"\\", 7=>"?", 8=>":", 9=>"_", 10=>"-");
///Se define el tamaño máximo aceptable para el nombre teniendo en cuenta que el excel admite un máximo de 31 caracteres, y que además, 
///ya se tienen 6 fijos del stock_ (movs_ es uno menos).
$tamMaximoNombre = 25;

if (isset($_POST["nombreProducto"])){
  $nombreProducto1 = $_POST["nombreProducto"];
  $sep = explode("[", $nombreProducto1);
  $entidad0 = trim($sep[1]);
  $nom2 = explode(":", $entidad0);
  $entidad = $nom2[0];
  $tempo = explode("]", $nom2[1]);
  $codigo = trim($tempo[0]);
  $entidad1 = explode("-", $tempo[1]);
  $nombreProducto = trim($entidad1[3]);
  $nombreProductoMostrar1 = str_replace($aguja, "", $nombreProducto);
  $nombreProductoMostrar = substr($nombreProductoMostrar1, 0, $tamMaximoNombre);
}
if (isset($_POST["entidad_$indice"])){
  $entidad = $_POST["entidad_$indice"];
  $entidadMostrar1 = str_replace($aguja, "", $entidad);
  $entidadMostrar = substr($entidadMostrar1, 0, $tamMaximoNombre);
  if ($entidad === 'todos') {
    $entidad = 'todas las entidades.';
    $entidadMostrar = 'Todos';
  }
}
//echo "id: $id<br>query: $query<br>consultaCSV: $consultaCSV<br>campos: $campos1<br>largos: $largos<br>mostrar: $mostrar1<br>tipoConsulta: $tipoConsulta<br>idProd: $idProd<br>nombreProducto: $nombreProducto<br>entidad: $entidad"
//        . "<br>x: $x<br>inicio: $inicio<br>fin: $fin<br>mes: $mes<br>año: $año<br>tipo: $tipo<br>usuario: $idUser<br>";

$largoCampos = array();
$largoTotal = 0;
$i = 0;
foreach ($temp as $valor) {
  $largo = $c1*$valor;
  array_push($largoCampos, $largo);
  if ($mostrar[$i]) {
    $largoTotal += $largo;
  }
  $i++;
}
array_push($largoCampos, $largoTotal);

switch ($id) {
  case "1": $tituloTabla = "LISTADO DE STOCK";
            $titulo = "STOCK POR ENTIDAD";
            $nombreReporte = "stock_".$entidadMostrar;
            $asunto = "Reporte con el Stock de la Entidad";
            $nombreCampos = "(select 'Entidad', 'Nombre', 'BIN', 'Stock')";
            $indiceStock = 8;
            break;
  case "2": $tituloTabla = "STOCK DEL PRODUCTO";
            $titulo = "STOCK DEL PRODUCTO";
            $nombreReporte = "stock_".$nombreProductoMostrar;
            $asunto = "Reporte con el stock del Producto";
            $nombreCampos = "(select 'Entidad', 'Nombre', 'BIN', 'Stock')";
            $indiceStock = 8;
            break;
  case "3": $tituloTabla = "STOCK TOTAL EN BÓVEDA";
            $titulo = "PLÁSTICOS EN BÓVEDA";
            $nombreReporte = "stockBoveda";
            $asunto = "Reporte con el total de tarjetas en stock";
            $nombreCampos = "(select 'Entidad', 'Stock')";
            $indiceStock = 2;
            break;
  case "4": $tituloTabla = "MOVIMIENTOS DE LA/S ENTIDAD/ES";
            $titulo = "MOVIMIENTOS POR ENTIDAD";
            $nombreReporte = "movs_".$entidadMostrar;
            $asunto = "Reporte con los movimientos de la Entidad";
            $nombreCampos = "(select 'Fecha', 'Hora', 'Entidad', 'Nombre', 'BIN', 'Tipo', 'Cantidad', 'Comentarios')";
            $indiceStock = 12;
            if (isset($inicio)) {
              $inicioTemp = explode("-", $inicio);
              $inicioMostrar = $inicioTemp[2]."/".$inicioTemp[1]."/".$inicioTemp[0];
              $finTemp = explode("-", $fin);
              $finMostrar = $finTemp[2]."/".$finTemp[1]."/".$finTemp[0];
            }
            break;
  case "5": $tituloTabla = "MOVIMIENTOS DEL PRODUCTO";
            $titulo = "MOVIMIENTOS POR PRODUCTO";
            $nombreReporte = "movs_".$nombreProductoMostrar;
            $asunto = "Reporte con los movimientos del Producto";
            $indiceStock = 12;
            if (isset($inicio)) {
              $inicioTemp = explode("-", $inicio);
              $inicioMostrar = $inicioTemp[2]."/".$inicioTemp[1]."/".$inicioTemp[0];
              $finTemp = explode("-", $fin);
              $finMostrar = $finTemp[2]."/".$finTemp[1]."/".$finTemp[0];
            }
            break;       
  default: break;
}

// Conectar con la base de datos
$con = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
/// Ejecuto la consulta:
$resultado1 = consultarBD($query, $con);
//echo "consulta: $query<br>";
$totalRegistros = $resultado1->num_rows;

//Instancio objeto de la clase:
$pdfResumen = new PDF('P','mm','A4');
$pdfResumen->AddPage();

$totalCampos = sizeof($campos);
$pdfResumen->SetWidths($largoCampos);

$filas = obtenerResultadosArray($resultado1);
$registros = array();
$i = 1;
$total = 0;
foreach($filas as $fila)
  {
  array_unshift($fila, $i);
  $i++;
  //Acumulo el total de plásticos ya sea en stock o movidos:
  $total = $total + $fila[$indiceStock];
  $registros[] = $fila;
}
//echo "total: ".$total."<br>";
///Ejecuto consulta para la generación del excel:
$resultado2 = consultarBD($consultaCSV, $con);
$filas1 = obtenerResultadosArray($resultado2);
$registros1 = array();
$j = 1;
$total1 = 0;
foreach($filas1 as $fila)
  {
  if (($id == 4)||($id == 5)){
    $primerColumna = array_shift($fila);
    array_unshift($fila, $j);
    array_unshift($fila, $primerColumna);
  }
  else {
    if (($id == 1)||($id == 2)){
      $coment = array_pop($fila);
      $al2 = array_pop($fila);
      $al1 = array_pop($fila);
      $stock = array_pop($fila);
      array_push($fila, $coment);
      array_push($fila, $stock);
      array_push($fila, $al1);
      array_push($fila, $al2);
    }
    array_unshift($fila, $j); //echo $coment." - ".$al1." - ".$al2." - ".$stock."<br>";
  }
  $j++;
  //Acumulo el total de plásticos ya sea en stock o movidos:
  $total1 = $total1 + $fila[5];
  $registros1[] = $fila;
}

//Según el ID, genero los PDFs correspondientes:
switch ($id) {
  case "1": $pdfResumen->tablaStockEntidad($total, false);
            break;
  case "2": $pdfResumen->tablaProducto();
            break;
  case "3": $pdfResumen->tablaStockEntidad($total, true);
            break;
  case "4": $pdfResumen->tablaMovimientos(false);
            break;
  case "5": $pdfResumen->tablaMovimientos(true);
            break;
  default: break;
}    

$timestamp = date('dmY_His');
$nombreArchivo = $nombreReporte."_".$timestamp.".pdf";
$salida = $dir.$nombreArchivo;

///Guardo el archivo en el disco, y además lo muestro en pantalla:
$pdfResumen->Output($salida, 'F');
$pdfResumen->Output($salida, 'I');

///Según el ID, genero los listados en Excel:
switch ($id) {
  case "1": $archivo = generarExcelStock($registros1, $total1);
            break;
  case "2": $archivo = generarExcelStock($registros1, $total1);
            break;
  case "3": $archivo = generarExcelBoveda($registros1);
            break;
  case "4": $archivo = generarExcelMovimientos($registros1);
            break;
  case "5": $archivo = generarExcelMovimientos($registros1);
            break;
  default: break;
}    

/*
/// Exportación de la consulta a CSV:
$nombreCSV = $nombreReporte.$timestamp."_CSV.csv";
$dirCSV = $dir."/".$nombreCSV;
$exportarCSV = $nombreCampos." union all (".$consultaCSV." into outfile '".$dirCSV."' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n')";
$resultado2 = consultarBD($exportarCSV, $con);
//echo $exportarCSV;
*/

///************************************************************ GENERACION ZIP FILE *********************************************************
$zip = new ZipArchive;
$nombreZip = $nombreReporte."_".$timestamp.".zip";
$fileDir = $dir.$nombreZip;

$excel = $dir.$archivo;

if ($zip->open($fileDir, ZIPARCHIVE::CREATE ) !== TRUE) 
    {
    exit("No se pudo abrir el archivo\n");
    } 
//agrego el pdf correspondiente al reporte para EMSA:
$zip->addFile($salida, $nombreArchivo);
$zip->addFile($excel, $archivo);

$zip->close();
///********************************************************** FIN GENERACION ZIP FILE *******************************************************

///************************************************************** ENVÍO DE MAILS ************************************************************
if (isset($mails)){
  $destinatarios = explode(",", $mails);
  foreach ($destinatarios as $valor){
    $para["$valor"] = $valor;
  }
  $asunto = $asunto." (MAIL DE TEST!!!)";

  $cuerpo = utf8_decode("<html><body><h4>Se adjunta el reporte generado del stock</h4></body></html>");
  $respuesta = enviarMail($para, '', '', $asunto, $cuerpo, "REPORTE", $nombreZip, $fileDir);
  echo $respuesta;
}
///************************************************************ FIN ENVÍO DE MAILS **********************************************************

?>
