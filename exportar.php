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
$dir = "D:/PROCESOS/KMS";

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

  //Tabla tipo listado
  function agregarTablaListado()
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
    $this->MultiCell($largoCampos[$totalCampos], 7, $tipoConsulta, 0, 'C', 0);
    $this->Ln(10);
    
    //************************************** TÍTULO *****************************************************************************************
    $this->SetX($x);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell($largoCampos[$totalCampos], 7, $tituloTabla, 1, 0, 'C', 1);
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
      if ($mostrar[$i]){
      $this->Cell($largoCampos[$j], 6, $campos[$i], 'LRBT', 0, 'C', true);
      $j++;
      }
      }
      
    $this->Ln();
    $this->SetX($x);
    $this->SetFont('Courier', '', 9);    
    $fill = false;
    foreach ($registros as $i => $dato) {
      if ($mostrar[$i]) {
      $this->Row($dato, $fill);
      $this->SetX($x);
      $fill = !$fill;
      }
    }
  }
  
  function detalleUsuario($registro) {
    $nombre = $registro->nombre;
    $apellido = $registro->apellido;
    $empresa = $registro->empresa;
    $estado = $registro->estado;
    $obs = $registro->observaciones;
    $tel = $registro->telefono;
    $mail = $registro->mail;
    
    global $c1, $x, $tituloTabla;
    
    //Defino color de fondo:
    $this->SetFillColor(255, 156, 233);
    //Defino color para los bordes:
    $this->SetDrawColor(0, 0, 0);
    //Defino grosor de los bordes:
    $this->SetLineWidth(.3);
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 9);
    //Establezco las coordenadas del borde de arriba a la izquierda de la tabla:
    $this->SetY(25);
    
    //************************************** TÍTULO *****************************************************************************************
    $this->SetX($x);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell(5*$c1, 7, $tituloTabla, 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX($x);

    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, 'APELLIDO:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.5*$c1, 7, utf8_decode($apellido), 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, 'NOMBRE:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.5*$c1, 7, utf8_decode($nombre), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, 'EMPRESA:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.5*$c1, 7, utf8_decode($empresa), 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, 'ESTADO:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.5*$c1, 7, utf8_decode($estado), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, 'MAIL:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(4*$c1, 7, utf8_decode($mail), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, utf8_decode('TELÉFONO:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(4*$c1, 7, utf8_decode($tel), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(1.6*$c1, 7, utf8_decode('OBSERVACIONES:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->MultiCell(3.4*$c1, 7, utf8_decode($obs), 1, 'C', 0); 
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
    case 'mostrar': $mostrar1 = utf8_decode($temp2[1]);
                    $mostrar = preg_split("/-/", $mostrar1);
                    break;
    default: break;                
  }
}

$largoCampos = array();
$largoTotal = 0;
foreach ($temp as $valor) {
 $largo = $c1*$valor;
 array_push($largoCampos, $largo);
 $largoTotal += $largo;
}
array_push($largoCampos, $largoTotal);
//var_dump($mostrar);
//echo "id: ".$id."<br>query: ".$query."<br>largos: ".$largos."<br>campos: ".$campos1."<br>x: ".$x."<br>iduser: ".$iduser."<br>tipo: ".$tipo."<br>inicio: ".$inicio."<br>fin: ".$fin."<br>mes: ".$mes."<br>año: ".$año."<br>FIN<br>";



switch ($id) {
  case "1": //$query = "select DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, motivo from actividades order by fecha desc, idactividades asc";
            $tituloTabla = utf8_decode("LISTADO DE STOCK");
            $titulo = "STOCK DE LA ENTIDAD";
            $nombreReporte = "stockEntidad";
            $asunto = "Reporte con el Listado de Stock";
            break;
  case "2": $tituloTabla = utf8_decode("STOCK DEL PRODUCTO");
            $titulo = "PRODUCTO";
            $nombreReporte = "stockProducto";
            $asunto = "Reporte con el stock del Producto";
            break;
  case "3": $tituloTabla = utf8_decode("STOCK TOTAL EN BÓVEDA");
            $titulo = "PLÁSTICOS EN BÓVEDA";
            $nombreReporte = "stockBoveda";
            $asunto = "Reporte con el total de plásticos en bóveda";
            break;
  case "4": $tituloTabla = utf8_decode("MOVIMIENTOS DE LA ENTIDAD");
            $titulo = "MOVIMIENTOS DE LA ENTIDAD";
            $nombreReporte = "movimientosEntidad";
            $asunto = "Reporte con los movimientos de la Entidad";
            break;
  case "5": $tituloTabla = utf8_decode("MOVIMIENTOS DEL PRODUCTO");
            $titulo = "MOVIMIENTOS PRODUCTO";
            $nombreReporte = "movimientosProducto";
            $asunto = "Reporte con los movimientos del Producto";
            break;       
  default: break;
}

//Instancio objeto de la clase:
$pdfResumen = new PDF();
//Agrego una página al documento:
$pdfResumen->AddPage();
//echo $query;

$totalCampos = sizeof($campos);
// Conectar con la base de datos
$con = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$resultado1 = consultarBD($query, $con);

$pdfResumen->SetWidths($largoCampos);
$filas = obtenerResultadosArray($resultado1);
$registros = array();
$i = 1;
foreach($filas as $fila)
  {
  array_unshift($fila, $i);
  $i++;
  $registros[] = $fila;
}
$pdfResumen->agregarTablaListado();    

//else {
  /*
  if ($id !== "2") {
    $filas = obtenerResultados($resultado1);
    $registro = $filas[1];
  }  
  switch ($id) {
    case "2": $x = 45;
              $pdfResumen->detalleActividad($datos);
              $nombreReporte = "detalleActividad";
              $asunto = "Reporte KM con el detalle de la Actividad";
              break;
    case "3": $x = 45;
              $resultado2 = consultarBD($query1, $con);
              $registros2 = obtenerResultados($resultado2);
              $resultado3 = consultarBD($query2, $con);
              $registros3 = obtenerResultados($resultado3);
              $resultado4 = consultarBD($query3, $con);
              $registros4 = obtenerResultados($resultado4);
              $pdfResumen->detalleReferencia($registro, $registros2, $registros3, $registros4);
              $nombreReporte = "detalleReferencia";
              $asunto = "Reporte KM con el detalle de la Referencia";
              break;
    case "4": $x = 54;
              $pdfResumen->detalleLlave($registro);
              $nombreReporte = "detalleLlave";
              $asunto = "Reporte KM con el detalle de la llave";
              break;
    case "5": $x = 45;
              $pdfResumen->detalleCertificado($registro);
              $nombreReporte = "detalleCertificado";
              $asunto = "Reporte KM con el detalle del Certificado";
              break;
    default: break;
  }
  */
//}

//$nombreReporte = "resultado";
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
