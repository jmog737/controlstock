<?php
//Reanudamos la sesión:
session_start();
require_once("data/sesiones.php");

require_once('data\baseMysql.php');
require_once('generarExcel.php');
require_once('generarPdfs.php');
require_once('enviarMails.php');

//***************************** DESTINATARIOS CORREOS ***********************************************************************************************
$paraListados = array();
$copiaListados = array();
$ocultosListados = array();

//**************** PRUEBAS ************************************************************
$copiaListados['Juan Martín Ortega'] = "juanortega@emsa.com.uy";
//**************** FIN PRUEBAS ********************************************************

//****************************************************IMPORTANTE:************************************************************************************
//                                              SETEO DE LAS CARPETAS
$dir = "D:/PROCESOS/PDFS";
$dir = "http:\\\\192.168.1.9\\Clasificadoras\\";echo $dir;
$rutaFotos = "D:/Servidor/disenos/controlstock/images/snapshots";
//***************************************************************************************************************************************************

//********************************************* Defino tamaño de la celda base: c1, y el número ************************************************
$pag = 1;
$c1 = 18;
$h = 7;
//******************************************************** FIN tamaños de celdas ***************************************************************

//******************************************************** INICIO Hora y título ****************************************************************
$fecha = date('d/m/Y');
$hora = date('H:i');
//********************************************************** FIN Hora y título *****************************************************************

//RECUPERO ID PASADO con el tipo de dato a exportar y sus parámetros:
$param = $_POST["param"];
$temp1 = explode("&", $param);
//echo $param;
foreach ($temp1 as $valor) {
  $temp2 = explode(":", $valor);
  switch ($temp2[0]) {
    case 'id': $id = $temp2[1];
               break;
    case 'query': $query = $temp2[1];
                  if (isset($temp2[2])) {
                    $query = $query.':'.$temp2[2];
                  }  
                  break;
    case 'consultaCSV': $consultaCSV = $temp2[1];
                        if (isset($temp2[2])) {
                          $consultaCSV = $consultaCSV.':'.$temp2[2];
                        } 
                        break;            
    case 'largos': $largos = $temp2[1];
                   $temp = explode('-', $largos);
                   break;
    case 'campos': $campos1 = utf8_decode($temp2[1]);
                   $campos = preg_split("/-/", $campos1);
                   break;
    case 'idProd': $idslot = $temp2[1];
                   break;
    case 'nombreProducto':  $nombreProducto1 = $temp2[1];
                            $sep = explode("[", $nombreProducto1);
                            $entidad0 = trim($sep[1]);
                            $nom2 = $temp2[2];
                            $tempo = explode("]", $nom2);
                            $codigo = trim($tempo[0]);
                            $entidad1 = explode("-", $tempo[1]);
                            $nombreProducto = trim($entidad1[3]);
                   break; 
    case 'entidad': $entidad = $temp2[1];
                    if ($entidad === 'todos') {
                      $entidad = 'todas las entidades.';
                    }
                    break;             
    case 'x': $x = $temp2[1];
              break;
    case 'tipo': $tipo = $temp2[1];
                  break;
    case 'inicio': $inicio = $temp2[1];
                   break;  
    case 'fin': $fin = $temp2[1];
                 break;   
    case 'mes': $mes = $temp2[1];
                 break;  
    case 'año': $año = $temp2[1];
                 break;            
    case 'idUser': $iduser = $temp2[1];
                   break;
    case 'mails': $mails = $temp2[1];
                  //$mails = preg_split("/,/", $mails1);
                  break;
    case 'tipoConsulta': $tipoConsulta = $temp2[1];
                         if ((($id === "4")||($id === "5")) && (isset($temp2[2]))) {
                           $tipoConsulta = $tipoConsulta.":".$temp2[2];
                         }  
                         break;            
    case 'mostrar': $mostrar1 = utf8_decode($temp2[1]);
                    $mostrar = preg_split("/-/", $mostrar1);
                    break;
    default: break;                
  }
}
// *** FIN RECUPERACIÓN DE PARÁMETROS *******************************

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
//echo "largos: ".sizeof($largoCampos)."<br>campos: ".sizeof($campos)."<br>mostrar: ".sizeof($mostrar)."<br>largototal: ".$largoCampos[sizeof($campos)];
//echo "id: ".$id."<br>query: ".$query."<br>largos: ".$largos."<br>campos: ".$campos1."<br>mostrar: ".$mostrar1."<br>x: ".$x."<br>iduser: ".$iduser."<br>tipo: ".$tipo."<br>inicio: ".$inicio."<br>fin: ".$fin."<br>mes: ".$mes."<br>año: ".$año."<br>FIN<br>";

switch ($id) {
  case "1": $tituloTabla = "LISTADO DE STOCK";
            $titulo = "STOCK POR ENTIDAD";
            $nombreReporte = "stockEntidad";
            $asunto = "Reporte con el Stock de la Entidad";
            $nombreCampos = "(select 'Entidad', 'Nombre', 'BIN', 'Stock')";
            $indiceStock = 8;
            break;
  case "2": $tituloTabla = "STOCK DEL PRODUCTO";
            $titulo = "STOCK DEL PRODUCTO";
            $nombreReporte = "stockProducto";
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
            $nombreReporte = "movimientosEntidad";
            $asunto = "Reporte con los movimientos de la Entidad";
            $nombreCampos = "(select 'Fecha', 'Hora', 'Entidad', 'Nombre', 'BIN', 'Tipo', 'Cantidad', 'Comentarios')";
            $indiceStock = 12;
            if (isset($inicio)) {
              $inicioTemp = explode("-", $inicio);
              $inicioMostrar = $inicioTemp[2]."/".$inicioTemp[1]."/".$inicioTemp[0];
              $finTemp = explode("-", $fin);
              $finMostrar = $finTemp[2]."/".$finTemp[1]."/".$finTemp[0];
              //$tipoConsulta = $tipoConsulta." ".$inicioMostrar." y ".$finMostrar;
            }
            break;
  case "5": $tituloTabla = "MOVIMIENTOS DEL PRODUCTO";
            $titulo = "MOVIMIENTOS POR PRODUCTO";
            $nombreReporte = "movimientosProducto";
            $asunto = "Reporte con los movimientos del Producto";
            $indiceStock = 12;
            break;       
  default: break;
}

//Instancio objeto de la clase:
$pdfResumen = new PDF();
//Agrego una página al documento:
$pdfResumen->AddPage();
//$pdfResumen->SetAutoPageBreak(true, 18);
$totalCampos = sizeof($campos);
$pdfResumen->SetWidths($largoCampos);

//echo $query."<br>-------------------------------<br>".$consultaCSV."<br>******<br>";

// Conectar con la base de datos
$con = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$resultado1 = consultarBD($query, $con);

/// Ejecuto la consulta:
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

///Ejecuto consulta para la generación del excel:
$resultado2 = consultarBD($consultaCSV, $con);
$filas1 = obtenerResultadosArray($resultado2);
$registros1 = array();
$j = 1;
$total1 = 0;
foreach($filas1 as $fila)
  {
  array_unshift($fila, $j);
  $j++;
  //Acumulo el total de plásticos ya sea en stock o movidos:
  $total1 = $total1 + $fila[4];
  $registros1[] = $fila;
}

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

$timestamp = date('Ymd_His');
$nombreArchivo = $nombreReporte.$timestamp.".pdf";
$salida = $dir.$nombreArchivo;
//phpinfo();
///Guardo el archivo en el disco, y además lo muestro en pantalla:
$pdfResumen->Output($salida, 'F');
//$pdfResumen->Output($salida, 'I');

switch ($id) {
  case "1": $archivo = generarExcelStock($registros1, $total1);
            break;
  case "2": $archivo = generarExcelStock($registros1, $total1);
            break;
  case "3": break;
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

//************ GENERACION ZIP FILE *************************************************************************************    
$zip = new ZipArchive;
$nombreZip = "lista".$timestamp.".zip";
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
//***********************************************************************************************************************  

/// Envío de mails:
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

?>
