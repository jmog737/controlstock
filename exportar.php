<?php
//Reanudamos la sesión:
session_start();
require_once("data/sesiones.php");

require_once('data\baseMysql.php');
require_once('..\..\fpdf\mc_table.php');
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
$rutaFotos = "D:/Servidor/disenos/controlstock/images/snapshots";
//***************************************************************************************************************************************************

class PDF extends PDF_MC_Table
  {
  //Cabecera de página
  function Header()
    {
    global $fecha, $hora, $titulo, $x;
    //Agrego logo de EMSA:
    $this->Image('images/logotipo.jpg', 3, 3, 50);
    $this->setY(10);
    $this->setX(10);
    //Defino características para el título y agrego el título:
    $this->SetFont('Arial', 'BU', 18);
    $this->Cell(200, 3, utf8_decode($titulo), 0, 0, 'C');
    $this->Ln();

    $this->setY(8);
    $this->setX(187);
    $this->SetFont('Arial');
    $this->SetFontSize(10);
    $this->Cell(20, 3, $fecha, 0, 0, 'C');

    $this->setY(11);
    $this->setX(187);
    $this->SetFont('Arial');
    $this->SetFontSize(10);
    $this->Cell(20, 3, $hora, 0, 0, 'C');

    //Dejo el cursor donde debe empezar a escribir:
    $this->Ln(20);
    $this->setX($x);
    }

  //Pie de página
  function Footer()
    {
    global $pag;
    $this->SetY(-10);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, 'Pag. ' . $this->PageNo(), 0, 0, 'C');
    }

  //Tabla tipo listado para el stock de una o todas las entidades, o también para el total de plásticos en bóveda:
  function tablaStockEntidad($total)
    {
    global $totalCampos, $x;
    global $registros, $campos, $largoCampos, $tituloTabla, $tipoConsulta, $mostrar;

    //Defino color de fondo:
    $this->SetFillColor(255, 156, 233);
    //Defino color para los bordes:
    $this->SetDrawColor(0, 0, 0);
    //Defino grosor de los bordes:
    $this->SetLineWidth(.3);
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 12);
    //Establezco las coordenadas del borde de arriba a la izquierda de la tabla:
    $this->SetY(25);

    $this->SetX($x);
    $this->MultiCell($largoCampos[$totalCampos], 7, utf8_decode($tipoConsulta), 0, 'C', 0);
    $this->Ln(10);
    
    //************************************** TÍTULO *****************************************************************************************
    $this->SetX($x);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell($largoCampos[$totalCampos], 7, utf8_decode($tituloTabla), 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX($x);

    $this->SetFont('Courier', 'B', 10);
    $j = 0;
    foreach ($campos as $i => $dato) {
      $this->Cell($largoCampos[$j], 6, $campos[$i], 'LRBT', 0, 'C', true);
      $j++;
      }
      
    $this->Ln();
    $this->SetX($x);
    $this->SetFont('Courier', '', 9);    
    $fill = false;
    foreach ($registros as $i => $dato) {
      $this->EnhancedRow($dato, $mostrar, $fill);
      $this->SetX($x);
      $fill = !$fill;
    }
    
    //Agrego fila final de la tabla con el total de plásticos:
    $this->SetFont('Courier', 'B', 12);
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $largoTemp = $largoCampos[$totalCampos] - $largoCampos[$totalCampos-1];
    $this->Cell($largoTemp, 6, 'TOTAL:', 'LRBT', 0, 'C', true);
    $this->SetFont('Courier', 'B', 13);
    $this->SetTextColor(255,0,0);
    $this->SetFillColor(153, 255, 102);
    $this->Cell($largoCampos[$totalCampos-1], 6, $total, 'LRBT', 0, 'C', true);
  }
  
  //Tabla tipo listado con el detalle del producto:
  function tablaProducto()
    {
    global $totalCampos, $x, $h;
    global $registros, $largoCampos, $tipoConsulta, $c1, $rutaFotos;
    
    $cCampo = 1.5*$c1;
    $cResto = 3.2*$c1;
    
    //Defino color de fondo:
    $this->SetFillColor(255, 156, 233);
    //Defino color para los bordes:
    $this->SetDrawColor(0, 0, 0);
    //Defino grosor de los bordes:
    $this->SetLineWidth(.3);
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 12);
    //Establezco las coordenadas del borde de arriba a la izquierda de la tabla:
    $this->SetY(25);
    
    $this->SetX(35);
    $nb = $this->NbLines(140,trim(utf8_decode($tipoConsulta)));
    $h0=$h*$nb;
    
    //Save the current position
    $x1=$this->GetX();
    $y=$this->GetY();
    //Draw the border
    $this->Rect($x1,$y,140,$h0);
    //Print the text
    if ($nb > 1) {
      $this->MultiCell(140,$h, trim(utf8_decode($tipoConsulta)),'','C', 0);
      }
    else {
      $this->MultiCell(140,$h, trim(utf8_decode($tipoConsulta)),'','C', 0);
      }  
      
    //Put the position to the right of the cell
    $this->SetXY($x,$y+$h0);
    
    //$this->MultiCell($largoCampos[$totalCampos], 7, utf8_decode($tipoConsulta), 0, 'C', 0);
    $this->Ln(10);
    
    //************************************** TÍTULO *****************************************************************************************
    $this->SetX($x);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell($cCampo+$cResto, 7, "DETALLES DEL PRODUCTO", 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX($x);
    
    $nb = $this->NbLines($cResto,trim(utf8_decode($registros[0][2])));
    $h0=$h*$nb;
    
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, $h0, "Nombre:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    //Save the current position
    $x1=$this->GetX();
    $y=$this->GetY();
    //Draw the border
    $this->Rect($x1,$y,$cResto,$h0);
    //Print the text
    if ($nb > 1) {
      $this->MultiCell($cResto,$h, trim(utf8_decode($registros[0][2])),'LRT','C', 0);
      }
    else {
      $this->MultiCell($cResto,$h, trim(utf8_decode($registros[0][2])),1,'C', 0);
      }  
      
    //Put the position to the right of the cell
    $this->SetXY($x,$y+$h0);
    
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, 6, "Entidad:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->Cell($cResto, 6, $registros[0][1], 'LRBT', 0, 'C', false);
    $this->Ln();
    $this->SetX($x);
    
    $codigo = $registros[0][4];
    if (($codigo === '')||($codigo === null)) {
      $codigo = 'No ingresado';
    }
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, 6, utf8_decode("Código:"), 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->Cell($cResto, 6, $codigo, 'LRBT', 0, 'C', false);
    $this->Ln();
    $this->SetX($x);
    
    $bin = $registros[0][3];
    if (($bin === '')||($bin === null)) {
      $bin = 'N/D o N/C';
    }
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, 6, "BIN:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->Cell($cResto, 6, $bin, 'LRBT', 0, 'C', false);
    $this->Ln();
    $this->SetX($x);
    
    $comentarios = trim(utf8_decode($registros[0][8]));
    if (($comentarios === '')||($comentarios === null)) {
      $comentarios = '';
    }
    $nb = $this->NbLines($cResto,$comentarios);
    $h0=$h*$nb;
    
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, $h0, "Comentarios:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    //Save the current position
    $x1=$this->GetX();
    $y=$this->GetY();
    //Draw the border
    $this->Rect($x1,$y,$cResto,$h0);
    //Print the text
    if ($nb > 1) {
      $this->MultiCell($cResto,$h, $comentarios,'LRT','C', 0);
      }
    else {
      $this->MultiCell($cResto,$h, $comentarios,1,'C', 0);
      } 
    //Put the position to the right of the cell
    $this->SetXY($x,$y+$h0);
    
    //Detecto si el stock actual está o no por debajo del valor de alarma. En base a eso elijo el color de fondo del stock:
    $alarma = $registros[0][7];
    $stock = $registros[0][6];
    
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, 6, "Stock:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', 'BI', 16);
    if ($stock < $alarma) {
      $this->SetFillColor(231, 56, 67);
      $this->SetTextColor(255);
    }
    else {
      $this->SetFillColor(255, 255, 255);
    }
    $this->Cell($cResto, 6, $stock, 'LRBT', 0, 'C', true);
    $this->Ln();
    $this->SetX($x);
    
    ///Agrego un snapshot de la tarjeta debajo de la tabla (si es que existe!!):
    $foto = $registros[0][5];
    if (($foto !== null) && ($foto !== '')) {
      $rutita = $rutaFotos."/".$foto;
      $xFoto = $this->GetX();
      $yFoto = $this->GetY();
      $this->Ln();
      $this->Image($rutita, 77, $yfoto, $cResto, 30);
    }
    
    $this->Ln();
    $this->SetX($x);
  }
  
}
  
  
function enviarMail($para, $copia, $ocultos, $asunto, $cuerpo, $correo, $adjunto, $path)
    {
    require_once('..\..\mail\class.phpmailer.php');

    //************* DATOS DEL USUARIO Y DEL SERVIDOR **************************************************
    $host = "mail.emsa.com.uy";
    $usuario = "ensobrado@emsa.com.uy";
    $deMail = "ensobrado@emsa.com.uy";
    $deNombre = "Key Manager";
    $pwd = "em123sa";
    $responderMail = "ensobrado@emsa.com.uy";
    $responderNombre = "KM";
    //**************************************************************************************************
    
    $mail = new PHPMailer(true);
    $mail->IsSMTP();

    try
        {
        //Datos del HOST:
        $mail->Host       = $host; // SMTP server
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->Host       = $host; // sets the SMTP server
        $mail->Port       = 25;                    // set the SMTP port for the GMAIL server

        //Datos del usuario del correo:
        $mail->Username   = $usuario; // SMTP account username
        $mail->Password   = $pwd;        // SMTP account password

        //Direcciones del remitente y a quien responder:
        $mail->SetFrom($deMail, $deNombre);
        $mail->AddReplyTo($responderMail, $responderNombre);

        foreach ($para as $ind => $dir)
            {
            //Direcciones a las cuales se manda el mail (Para):
            $mail->AddAddress($dir, $ind);
            }

        if (count($copia)>0)
            {    
            foreach ($copia as $ind => $dir)
                {
                //Direcciones a las cuales se manda el mail (Cc):
                $mail->AddCC($dir, $ind);
                }
            }    

        if (count($ocultos)>0)
            {   
            foreach ($ocultos as $ind => $dir)
                {
                //Direcciones a las cuales se manda el mail (Bcc):
                $mail->AddBCC($dir, $ind);
                }
            }

        //Datos del mail a enviar:
        $mail->Subject = $asunto;
        $mail->AltBody = 'Para ver este mensaje por favor use un cliente de correo con compatibilidad con HTML!'; // optional - MsgHTML will create an alternate automatically
        $mail->MsgHTML($cuerpo);
        
        if (!empty($adjunto))
            {
            $mail->AddAttachment($path, $adjunto);
            }

        $mail->Send();

        $mensajeMail = "Mail con el $correo enviado.";
        }
    catch (phpmailerException $e)
        {
        $mensajeMail = "Error al enviar el mail con el $correo. Por favor verifique.<br>".$e->errorMessage();
        }
    catch (Exception $e)
        {
        $mensajeMail = $mensajeMail." ".$e->getMessage();
        }
    return $mensajeMail;
    }

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

foreach ($temp1 as $valor) {
  $temp2 = explode(":", $valor);
  switch ($temp2[0]) {
    case 'id': $id = $temp2[1];
               break;
    case 'query': $query = $temp2[1];
                  break;
    case 'largos': $largos = $temp2[1];
                   $temp = explode('-', $largos);
                   break;
    case 'campos': $campos1 = utf8_decode($temp2[1]);
                   $campos = preg_split("/-/", $campos1);
                   break;
    case 'idProd': $idslot = $temp2[1];
                   break;
    case 'nombreProducto': $nombreProducto = $temp2[1];
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
                  break;
    case 'tipoConsulta': $tipoConsulta = $temp2[1];
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
foreach ($temp as $valor) {
 $largo = $c1*$valor;
 array_push($largoCampos, $largo);
 $largoTotal += $largo;
}
array_push($largoCampos, $largoTotal);

//echo "id: ".$id."<br>query: ".$query."<br>largos: ".$largos."<br>campos: ".$campos1."<br>mostrar: ".$mostrar1."<br>x: ".$x."<br>iduser: ".$iduser."<br>tipo: ".$tipo."<br>inicio: ".$inicio."<br>fin: ".$fin."<br>mes: ".$mes."<br>año: ".$año."<br>FIN<br>";
//echo $query;
switch ($id) {
  case "1": $tituloTabla = "LISTADO DE STOCK";
            $titulo = "STOCK POR ENTIDAD";
            $nombreReporte = "stockEntidad";
            $asunto = "Reporte con el Listado de Stock";
            $indiceStock = 6;
            break;
  case "2": $tituloTabla = "STOCK DEL PRODUCTO";
            $titulo = "STOCK DEL PRODUCTO";
            $nombreReporte = "stockProducto";
            $asunto = "Reporte con el stock del Producto";
            $indiceStock = 6;
            break;
  case "3": $tituloTabla = "STOCK TOTAL EN BÓVEDA";
            $titulo = "PLÁSTICOS EN BÓVEDA";
            $nombreReporte = "stockBoveda";
            $asunto = "Reporte con el total de plásticos en bóveda";
            $indiceStock = 2;
            break;
  case "4": $tituloTabla = "MOVIMIENTOS DE LA ENTIDAD";
            $titulo = "MOVIMIENTOS DE LA ENTIDAD";
            $nombreReporte = "movimientosEntidad";
            $asunto = "Reporte con los movimientos de la Entidad";
            $indiceStock = 10;
            break;
  case "5": $tituloTabla = "MOVIMIENTOS DEL PRODUCTO";
            $titulo = "MOVIMIENTOS PRODUCTO";
            $nombreReporte = "movimientosProducto";
            $asunto = "Reporte con los movimientos del Producto";
            $indiceStock = 10;
            break;       
  default: break;
}

//Instancio objeto de la clase:
$pdfResumen = new PDF();
//Agrego una página al documento:
$pdfResumen->AddPage();
//echo $query;
$totalCampos = sizeof($campos);
$pdfResumen->SetWidths($largoCampos);

// Conectar con la base de datos
$con = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$resultado1 = consultarBD($query, $con);

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
switch ($id) {
  case "1": $pdfResumen->tablaStockEntidad($total);
            break;
  case "2": $x = 65;//echo "nombre: ".$nombreProducto;
            $pdfResumen->tablaProducto();
            break;
  case "3": $pdfResumen->tablaStockEntidad($total);
            break;
  case "4": break;
  case "5": break;
  default: break;
}    

$timestamp = date('Ymd_His');
$nombreArchivo = $nombreReporte.$timestamp.".pdf";
$salida = $dir."/".$nombreArchivo;

///Guardo el archivo en el disco, y además lo muestro en pantalla:
$pdfResumen->Output($salida, 'F');
$pdfResumen->Output($salida, 'I');

if (isset($mails)){
  $destinatarios = explode(",", $mails);
  foreach ($destinatarios as $valor){
    $para["$valor"] = $valor;
  }
  $asunto = $asunto." (MAIL DE TEST!!!)";
  $cuerpo = utf8_decode("<html><body><h4>Se adjunta el reporte generado de KM</h4></body></html>");
  enviarMail($para, '', '', $asunto, $cuerpo, "REPORTE", $nombreArchivo, $salida);
}


?>
