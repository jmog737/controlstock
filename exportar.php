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
    global $registros, $campos, $largoCampos, $tituloTabla, $tipoConsulta;

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
      $this->Cell($largoCampos[$j], 6, $campos[$i], 'LRBT', 0, 'C', true);
      $j++;
      }
      
    $this->Ln();
    $this->SetX($x);
    $this->SetFont('Courier', '', 9);    
    $fill = false;
    foreach ($registros as $i => $dato) {
      
      $this->Row($dato, $fill);
      $this->SetX($x);
      $fill = !$fill;
    }
  }
  
  //Tabla con todos los detalles de la actividad
  function detalleActividad() {
    global $datos, $tituloTabla;
    global $c1;
    
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

    //************************************** GENERAL ********************************************************************************************
    $this->SetX(45);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell(7*$c1, 7, $tituloTabla, 1, 0, 'C', 1);
    $this->Ln();

    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX(45);
    
    ///Línea con el motivo de la actividad:
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, "Motivo:", 1, 0, 'C', 1);
    $this->SetFont('Courier');
    $this->Cell(6*$c1, 7, $datos["actividad"]["motivo"], 1, 0, 'C', 0);
    $this->Ln();
    $this->SetX(45);
    
    ///Línea con la fecha y horas de la actividad. 
    ///Previo a eso, reacomodo las hora para una mejor visualización:
    $h1 = explode(':', $datos["actividad"]["horaInicio"]);
    $horaInicio = $h1[0].':'.$h1[1];
    $h2 = explode(':', $datos["actividad"]["horaFin"]);
    $horaFin = $h2[0].':'.$h2[1];
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, "Fecha:", 1, 0, 'C', 1);
    $this->SetFont('Courier');
    $this->Cell(2*$c1, 7, $datos["actividad"]["fecha"], 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, "Inicio:", 1, 0, 'C', 1);
    $this->SetFont('Courier');
    $this->Cell($c1, 7, $horaInicio, 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, "Fin:", 1, 0, 'C', 1);
    $this->SetFont('Courier');
    $this->Cell($c1, 7, $horaFin, 1, 0, 'C', 0);
    $this->Ln();
    $this->SetX(45);
    
    ///Línea con el subtitulo de Personal de la Empresa:
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Defino tipo de letra y tamaño para el SubTítulo:
    $this->SetFont('Courier', 'B', 9);
    //Escribo el subtítulo:
    $this->Cell(7*$c1, 7, "PERSONAL DE LA EMPRESA", 1, 0, 'C', 1);
    $this->Ln();
    $this->SetX(45);
    $this->SetFillColor(255, 204, 120);
    $this->Cell(3.5*$c1, 7, "Nombre", 1, 0, 'C', 1);
    $this->Cell(3.5*$c1, 7, "Rol", 1, 0, 'C', 1);
    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->Ln();
    $this->SetX(45);
    
    ///Línea con el nombre y rol del primer usuario de la empresa:
    $this->SetFont('Courier');
    $this->Cell(3.5*$c1, 7, utf8_decode($datos["usuario1"]["nombre"])." ".utf8_decode($datos["usuario1"]["apellido"]), 1, 0, 'C', 0);
    $this->Cell(3.5*$c1, 7, $datos["actividad"]["rolUsuario1"], 1, 0, 'C', 0);
    $this->Ln();
    $this->SetX(45);
    ///Línea con el nombre y rol del segundo usuario de la empresa:
    $this->SetFont('Courier');
    $this->Cell(3.5*$c1, 7, utf8_decode($datos["usuario2"]["nombre"])." ".utf8_decode($datos["usuario2"]["apellido"]), 1, 0, 'C', 0);
    $this->Cell(3.5*$c1, 7, $datos["actividad"]["rolUsuario2"], 1, 0, 'C', 0);
    $this->Ln();
    $this->SetX(45);
    
    ///Chequeo si existen referencias para la actividad:
    if ($datos["referencia"] !== null) {
      //Defino tipo de letra y tamaño para el SubTítulo:
      $this->SetFont('Courier', 'B', 9);
      $this->SetFillColor(153, 255, 102);
      //Escribo el subtítulo:
      $this->Cell(7*$c1, 7, "REFERENCIAS", 1, 0, 'C', 1);
      $this->Ln();
      $this->SetX(45);
      $this->setFillColor(220, 223, 232);
      $fill = false;
      foreach ($datos["referencia"] as $indice => $registro) {
        ///Línea con el código y resumen de la referencia:
        $this->SetFont('Courier', 'BI', 9);
        $this->Cell(2*$c1, 7, utf8_decode($registro["codigo"]), 1, 0, 'C', $fill);
        $this->SetFont('Courier');
        $this->Cell(5*$c1, 7, utf8_decode($registro["resumen"]), 1, 0, 'C', $fill);
        $this->Ln();
        $this->SetX(45);
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
    
  function detalleLlave($registro) {
    $nombre = $registro->nombre;
    $owner = $registro->owner;
    $kcv = $registro->kcv;
    $tipo = $registro->tipo;
    $bits = $registro->bits;
    $exponente = $registro->exponente; if ($exponente === "0") {$exponente = "N/A";}
    $generacion = $registro->modoGeneracion;
    $accion = $registro->accion;
    $version = $registro->version;
    $obs = $registro->observaciones;
    $estado = $registro->estado;   
    
    $uso_encrypt = $registro->uso_encrypt;
    $uso_decrypt = $registro->uso_decrypt;
    $uso_sign = $registro->uso_sign;
    $uso_verify = $registro->uso_verify;
    $uso_wrap = $registro->uso_wrap;
    $uso_unwrap = $registro->uso_unwrap;
    $uso_import = $registro->uso_import;
    $uso_export = $registro->uso_export;
    $uso_derive = $registro->uso_derive;
    
    $att_sensitive = $registro->att_sensitive;
    $att_trusted = $registro->att_trusted;
    $att_modifiable = $registro->att_modifiable;
    $att_wrapwtrusted = $registro->att_wrapwtrusted;
    $att_private = $registro->att_private;
    $att_unwrapmask = $registro->att_unwrapmask;
    $att_extractable = $registro->att_extractable;
    $att_derivemask = $registro->att_derivemask;
    $att_exportable = $registro->att_exportable;
    $att_deletable = $registro->att_deletable;
    
    //echo "nombre: $nombre <br>owner: $owner<br>kcv: $kcv <br>tipo: $tipo <br>bits: $bits <br> exponente: $exponente  <br>generacion: $generacion <br>accion: $accion <br>version: $version <br>observaciones: $obs<br><br>";
    //echo "uso_encrypt: $uso_encrypt <br>uso_decrypt: $uso_decrypt<br>uso_sign: $uso_sign<br>uso_verify: $uso_verify<br>uso_export: $uso_export<br>uso_import: $uso_import<br>uso_wrap: $uso_wrap<br>uso_unwrap: $uso_unwrap<br>uso_derive: $uso_derive<br><br>";
    //echo "att_sensitive: $att_sensitive<br>att_modifiable: $att_modifiable<br>att_trusted: $att_trusted<br>att_wrapwtrusted: $att_wrapwtrusted<br>att_private: $att_private<br>att_unwrapmask: $att_unwrapmask<br>att_derivemask: $att_derivemask<br>att_extractable: $att_extractable<br>att_exportable: $att_exportable<br>att_deletable: $att_deletable<br>";
    
    global $c1, $x, $tituloTabla;
    
    //Defino color de fondo:
    $this->SetFillColor(255, 156, 233);
    //Defino color para los bordes:
    $this->SetDrawColor(0, 0, 0);
    //Defino grosor de los bordes:
    $this->SetLineWidth(.3);
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 10);
    //Establezco las coordenadas del borde de arriba a la izquierda de la tabla:
    $this->SetY(25);
    
    $h = 7;
    
    //************************************** TÍTULO *****************************************************************************************
    $this->SetX($x);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell(6.1*$c1, $h, $tituloTabla, 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX($x);

    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.8*$c1, $h, 'NOMBRE:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'I', 10); 
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.7*$c1, $h, utf8_decode($nombre), 1, 0, 'C', 1);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.7*$c1, $h, 'OWNER:', 1, 0, 'L', 1);
    
    $this->SetFillColor(231, 151, 246);
    $this->SetFont('Courier', 'I', 10);
    $this->Cell(1.1*$c1, $h, utf8_decode($owner), 1, 0, 'C', 1);
    
    $this->SetFont('Courier', 'B', 9);
    $this->SetFillColor(255, 204, 120);
    $this->Cell(0.7*$c1, $h, 'KCV:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.1*$c1, $h, utf8_decode($kcv), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, $h, 'TIPO:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.5*$c1, $h, utf8_decode($tipo), 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, $h, utf8_decode('TAMAÑO:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(0.8*$c1, $h, utf8_decode($bits), 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(1.2*$c1, $h, 'EXPONENTE:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(0.6*$c1, $h, utf8_decode($exponente), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
       
    //Calculate the height of the row
    $w = 1.3*$c1;
    $nb = $this->NbLines($w,utf8_decode($generacion));
    $h0=$h*$nb;//echo "nb $nb <br>h0 $h0";
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(1.2*$c1, $h0, utf8_decode('GENERACIÓN:'), 1, 0, 'L', 1);
    $this->SetFont('Courier');
        
    //Save the current position
    $x1=$this->GetX();
    $y=$this->GetY();
    //Draw the border
    $this->Rect($x1,$y,$w,$h0);
    //Print the text
    if ($nb > 1) {
      $this->MultiCell($w,$h, utf8_decode($generacion),'LRT','C', 0);
      }
    else {
      $this->MultiCell($w,$h, utf8_decode($generacion),1,'C', 0);
      }  
      
    //Put the position to the right of the cell
    $this->SetXY($x1+$w,$y);
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.8*$c1, $h0, utf8_decode('ACCIÓN:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.3*$c1, $h0, utf8_decode($accion), 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.9*$c1, $h0, utf8_decode('VERSIÓN:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(0.6*$c1, $h0, utf8_decode($version), 1, 0, 'C', 0);
    
    $this->Ln($h0);
    $this->SetX($x);
    
    //Calculate the height of the row
    $w1 = 4.5*$c1;
    $nb1 = $this->NbLines($w1,utf8_decode($obs));//echo $nb;
    $h1=$h*$nb1;
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(1.6*$c1, $h1, utf8_decode('OBSERVACIONES:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->MultiCell($w1, $h, utf8_decode($obs), 1, 'C', 0);
    
    $this->SetX($x);
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 10);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell(6.1*$c1, $h, "USOS", 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', '',9);
    //Defino color de fondo para señalar las opciones:
    $this->SetFillColor(255, 255, 0);
    
    $this->Cell(1.2*$c1, $h, " Encrypt" , 1, 0, 'C', $uso_encrypt);
    $this->Cell(1.2*$c1, $h, " Sign", 1, 0, 'C', $uso_sign);
    $this->Cell(1.2*$c1, $h, " Wrap", 1, 0, 'C', $uso_wrap);
    $this->Cell(1.2*$c1, $h, " Export", 1, 0, 'C', $uso_export);
    $this->Cell(1.3*$c1, $h, " Derive", 1, 0, 'C', $uso_derive);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->Cell(1.2*$c1, $h, " Decrypt" , 1, 0, 'C', $uso_decrypt);
    $this->Cell(1.2*$c1, $h, " Verify", 1, 0, 'C', $uso_verify);
    $this->Cell(1.2*$c1, $h, " Unwrap", 1, 0, 'C', $uso_unwrap);
    $this->Cell(1.2*$c1, $h, " Import", 1, 0, 'C', $uso_import);
    $this->Cell(1.3*$c1, $h, " ", 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 10);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell(6.1*$c1, $h, "ATRIBUTOS", 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', '',9);
    //Defino color de fondo para señalar las opciones:
    $this->SetFillColor(255, 255, 0);
    
    $this->Cell(1.2*$c1, $h, "Sensitive" , 1, 0, 'C', $att_sensitive);
    $this->Cell(1.2*$c1, $h, "Modifiable", 1, 0, 'C', $att_modifiable);
    $this->Cell(1.2*$c1, $h, "Private", 1, 0, 'C', $att_private);
    $this->Cell(1.3*$c1, $h, "Extractable", 1, 0, 'C', $att_extractable);
    $this->Cell(1.2*$c1, $h, "Exportable", 1, 0, 'C', $att_exportable);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->Cell(0.85*$c1, $h, "Trusted" , 1, 0, 'C', $att_trusted);
    $this->Cell(1.55*$c1, $h, "Wrap w/trusted", 1, 0, 'C', $att_wrapwtrusted);
    $this->Cell(1.3*$c1, $h, "Unwrap Mask", 1, 0, 'C', $att_unwrapmask);
    $this->Cell(1.3*$c1, $h, "Derive Mask", 1, 0, 'C', $att_derivemask);
    $this->Cell(1.1*$c1, $h, "Deletable", 1, 0, 'C', $att_deletable);
  
    }
  
  function detalleCertificado($registro) {
    $nombre = $registro->nombre;
    $owner = $registro->owner;
    $version = $registro->version;
    $bandera = $registro->bandera;
    $vencimiento = $registro->vencimiento;
    $estado = $registro->estado;
    $obs = $registro->observaciones;
    $accion = $registro->accion;
    //echo "nombre: $nombre<br>owner: $owner<br>bandera: $bandera<br>vencimiento: $vencimiento<br>accion: $accion<br>version: $version<br>observaciones: $obs<br>";
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
    $this->Cell(6.9*$c1, 7, $tituloTabla, 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX($x);

    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, 'NOMBRE:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'I', 10); 
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.8*$c1, 7, utf8_decode($nombre), 1, 0, 'C', 1);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.8*$c1, 7, 'OWNER:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'I', 10); 
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.3*$c1, 7, utf8_decode($owner), 1, 0, 'C', 1);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, 'BANDERA:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'I', 10); 
    $this->SetFillColor(231, 151, 246);
    $this->Cell($c1, 7, utf8_decode($bandera), 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(1.4*$c1, 7, 'VENCIMIENTO:', 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.4*$c1, 7, utf8_decode($vencimiento), 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.9*$c1, 7, utf8_decode('ACCIÓN:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(1.5*$c1, 7, utf8_decode($accion), 1, 0, 'C', 0);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, 7, utf8_decode('VERSIÓN:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->Cell(0.7*$c1, 7, utf8_decode($version), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(1.6*$c1, 7, utf8_decode('OBSERVACIONES:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->MultiCell(5.3*$c1, 7, utf8_decode($obs), 1, 'C', 0); 
    }
    
  function detalleSlot($registro, $registro1) {
    $nombre = $registro->nombre;
    $slotobserva = $registro->slotobserva;
    $estadoslot = $registro->estadoslot;
    $hsm = $registro->hsm;
    $serie = $registro->serie;
    $marca = $registro->marca;
    $hsmobserva = $registro->hsmobserva;
    $modelo = $registro->modelo;
    $estadohsm = $registro->estadohsm;
    //echo "nombre: $nombre<br>slotobserva: $slotobserva<br>estadoslot: $estadoslot<br>hsm: $hsm<br>serie: $serie<br>marca: $marca<br>hsmobserva: $hsmobserva<br>estadohsm: $estadohsm<br>modelo: $modelo<br>";
    global $c1, $x, $tituloTabla;
    
    $h = 7;
    
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
    $this->Cell(6.9*$c1, $h, $tituloTabla, 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX($x);

    $this->SetFont('Courier', 'B', 9);
    $this->Cell($c1, $h, 'SLOT:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'I', 10); 
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.8*$c1, $h, utf8_decode($nombre), 1, 0, 'C', 1);
    
    $this->Cell(2*$c1, $h, '', 0, 0, 'C', 0);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.8*$c1, $h, 'ESTADO:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier');
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.3*$c1, $h, utf8_decode($estadoslot), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    //Calculate the height of the row
    $w1 = 5.3*$c1;
    $nb1 = $this->NbLines($w1,utf8_decode($slotobserva));//echo $nb;
    $h1=$h*$nb1;
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(1.6*$c1, $h1, utf8_decode('OBSERVACIONES:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->MultiCell($w1, $h, utf8_decode($slotobserva), 1, 'C', 0);
    
    $this->SetX($x);
    $this->SetFont('Courier', 'B', 9);
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell(6.9*$c1, $h, 'DETALLES DEL HSM', 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 9);
    $this->SetFillColor(255, 204, 120);
    $this->Cell($c1, $h, 'HSM:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'I', 10);
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.8*$c1, $h, utf8_decode($hsm), 1, 0, 'C', 1);
    
    $this->Cell(2*$c1, $h, '', 0, 0, 'C', 0);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.8*$c1, $h, 'SERIE:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier'); 
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.3*$c1, $h, utf8_decode($serie), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 9);
    $this->SetFillColor(255, 204, 120);
    $this->Cell($c1, $h, 'MARCA:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier'); 
    
    $this->Cell(1.8*$c1, $h, utf8_decode($marca), 1, 0, 'C', 0);
    
    $this->Cell(2*$c1, $h, '', 0, 0, 'C', 0);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(0.8*$c1, $h, 'MODELO:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier'); 
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.3*$c1, $h, utf8_decode($modelo), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    //Calculate the height of the row
    $w1 = 5.3*$c1;
    $nb1 = $this->NbLines($w1,utf8_decode($hsmobserva));
    $h1=$h*$nb1;
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(1.6*$c1, $h1, utf8_decode('OBSERVACIONES:'), 1, 0, 'L', 1);
    $this->SetFont('Courier'); 
    $this->MultiCell($w1, $h, utf8_decode($hsmobserva), 1, 'C', 0);
    
    $this->SetX($x);
    $this->SetFont('Courier', 'B', 9);
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell(6.9*$c1, $h, 'DETALLES DE LOS USUARIOS', 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 9);
    $this->Cell(2.4*$c1, $h, 'USUARIO', 1, 0, 'C', 1);
    $this->Cell(1.5*$c1, $h, 'EMPRESA', 1, 0, 'C', 1);
    $this->Cell(3.0*$c1, $h, 'USUARIO KMS', 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);
    $this->SetFont('Courier');
    $this->setFillColor(220, 223, 232);
    $fill = false;
    foreach ($registro1 as $i => $valor) {
      $this->Cell(2.4*$c1, $h, utf8_decode($valor->apellido.', '.$valor->nombre), 1, 0, 'L', $fill);
      $this->Cell(1.5*$c1, $h, utf8_decode($valor->empresa), 1, 0, 'C', $fill);
      $this->Cell(3.0*$c1, $h, utf8_decode($valor->usuariokms), 1, 0, 'C', $fill);
      $this->Ln();
      $this->SetX($x);
      $fill = !$fill;
    }
    
    }
    
  function detalleReferencia($registro, $registros1, $registros2, $registros3){
    $codigo = $registro->codigo;
    $lugar = $registro->lugar;
    $slot = $registro->slot;
    $hsm = $registro->hsm;
    $plataforma = $registro->plataforma;
    $aplicacion = $registro->aplicacion;
    $estado = $registro->estado;
    $resumen = $registro->resumen;
    $detalles = $registro->detalles;
    //echo "nombre: $nombre<br>slotobserva: $slotobserva<br>estadoslot: $estadoslot<br>hsm: $hsm<br>serie: $serie<br>marca: $marca<br>hsmobserva: $hsmobserva<br>estadohsm: $estadohsm<br>modelo: $modelo<br>";
    global $c1, $x, $tituloTabla, $titulo;

    $h = 7;

    //Defino color de fondo:
    $this->SetFillColor(255, 156, 233);
    //Defino color para los bordes:
    $this->SetDrawColor(0, 0, 0);
    //Defino grosor de los bordes:
    $this->SetLineWidth(.3);
    
    //Establezco las coordenadas del borde de arriba a la izquierda de la tabla:
    $this->SetY(25);

    $this->SetX($x+4);
    $this->SetTextColor(255, 0, 0);
    $this->SetFont('Courier', 'BUI', 22);
    $this->Cell(6.9*$c1, $h, $codigo, 0, 0, 'C', 0);
    $this->Ln(16);
    $this->SetTextColor(0, 0, 0);
    //************************************** TÍTULO *****************************************************************************************
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 12);
    $this->SetX($x);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell(6.9*$c1, $h, $tituloTabla, 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************

    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($c1, $h, 'LUGAR:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'I', 10); 
    
    $this->Cell(2.0*$c1, $h, utf8_decode($lugar), 1, 0, 'C', 0);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 10);
    $this->Cell(0.6*$c1, $h, 'HSM:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier');
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.3*$c1, $h, utf8_decode($hsm), 1, 0, 'C', 0);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 10);
    $this->Cell(0.7*$c1, $h, 'SLOT:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'BI', 11);
    $this->SetFillColor(231, 151, 246);
    $this->Cell(1.3*$c1, $h, utf8_decode($slot), 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFont('Courier', 'B', 10);
    $this->SetFillColor(255, 204, 120);
    $this->Cell(1.4*$c1, $h, 'PLATAFORMA:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier', 'I', 10); 
    $this->Cell(1.6*$c1, $h, utf8_decode($plataforma), 1, 0, 'C', 0);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 10);
    $this->Cell(0.6*$c1, $h, utf8_decode('APP:'), 1, 0, 'L', 1);
    
    $this->SetFont('Courier');
    $this->Cell(1.3*$c1, $h, utf8_decode($aplicacion), 1, 0, 'C', 0);
    
    $this->SetFillColor(255, 204, 120);
    $this->SetFont('Courier', 'B', 10);
    $this->Cell(0.9*$c1, $h, 'ESTADO:', 1, 0, 'L', 1);
    
    $this->SetFont('Courier');
    $this->Cell(1.1*$c1, $h, utf8_decode($estado), 1, 0, 'C', 0);
    
    $this->Ln();
    $this->SetX($x);
    
    $this->SetFillColor(153, 255, 102);
    $this->SetFont('Courier', 'B', 10);
    $this->Cell(6.9*$c1, $h, 'RESUMEN', 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);

    //Calculate the height of the row
    $w1 = 6.9*$c1;
    $nb1 = $this->NbLines($w1,utf8_decode($resumen));
    $h1=$h*$nb1;
    
    $this->SetFont('Courier'); 
    $this->MultiCell($w1, $h, utf8_decode($resumen), 1, 'C', 0);
    
    $this->SetX($x);
    
    $this->SetFillColor(153, 255, 102);
    $this->SetFont('Courier', 'B', 10);
    $this->Cell(6.9*$c1, $h, 'DETALLES', 1, 0, 'C', 1);
    
    $this->Ln();
    $this->SetX($x);

    //Calculate the height of the row
    $w1 = 6.9*$c1;
    $nb1 = $this->NbLines($w1,utf8_decode($detalles));
    $h1=$h*$nb1;
    
    $this->SetFont('Courier'); 
    $this->MultiCell($w1, $h, utf8_decode($detalles), 1, 'C', 0);
    
    /// COMIENZA CARGA DE LOS INVOLUCRADOS: ***************************************************************************************************
    $totalInvolucrados = count($registros1);
    if ($totalInvolucrados >= 1) {
      $this->SetX($x);
    
      $this->SetFillColor(153, 255, 102);
      $this->SetFont('Courier', 'B', 12);
      $this->Cell(6.9*$c1, $h, 'INVOLUCRADOS', 1, 0, 'C', 1);
      
      $this->Ln();
      $this->SetX($x);
      
      $this->SetFillColor(255, 204, 120);
      $this->SetFont('Courier', 'B', 10);
      $this->Cell(2.4*$c1, $h, 'USUARIO', 1, 0, 'C', 1);
      $this->Cell(1.5*$c1, $h, 'EMPRESA', 1, 0, 'C', 1);
      $this->Cell(3.0*$c1, $h, 'ROL', 1, 0, 'C', 1);
      
      $this->Ln();
      $this->SetX($x);
      
      $this->SetFont('Courier');
      $this->setFillColor(220, 223, 232);
      $fill = false;
      foreach ($registros1 as $i => $valor) {
        $this->Cell(2.4*$c1, $h, utf8_decode($valor->apellido.', '.$valor->nombre), 1, 0, 'L', $fill);
        $this->Cell(1.5*$c1, $h, utf8_decode($valor->empresa), 1, 0, 'C', $fill);
        $this->Cell(3.0*$c1, $h, utf8_decode($valor->rol), 1, 0, 'C', $fill);
        $this->Ln();
        $this->SetX($x);
        $fill = !$fill;
      }
    }
    
    /// COMIENZA CARGA DE LOS OBJETOS: ******************************************************************************************************
    $totalLlaves = count($registros2);
    $totalCerts = count($registros3);
     
    if ($totalLlaves > 0) {
      $titulo = "DETALLE DE LOS OBJETOS";
      $this->AddPage();
      $this->SetY(25);
      $this->SetX($x+4);
      $this->SetTextColor(255, 0, 0);
      $this->SetFont('Courier', 'BUI', 22);
      $this->Cell(6.9*$c1, $h, $codigo, 0, 0, 'C', 0);
      $this->Ln(16);
      $this->SetTextColor(0, 0, 0);
    
      $this->SetX($x);
      $this->SetFillColor(153, 255, 102);
      $this->SetFont('Courier', 'B', 12);
      $this->Cell(6.9*$c1, $h, 'LLAVES', 1, 0, 'C', 1);

      $this->Ln();
      $this->SetX($x);
      $this->SetFillColor(255, 204, 120);
      $this->SetFont('Courier', 'B', 10);
      $this->Cell(0.6*$c1, $h, utf8_decode('ÍTEM'), 1, 0, 'C', 1);
      $this->Cell(1.7*$c1, $h, utf8_decode('ACCIÓN'), 1, 0, 'C', 1);
      $this->Cell(2.0*$c1, $h, utf8_decode('NOMBRE'), 1, 0, 'C', 1);
      $this->Cell(1.5*$c1, $h, utf8_decode('OWNER'), 1, 0, 'C', 1);
      $this->Cell(1.1*$c1, $h, utf8_decode('KCV'), 1, 0, 'C', 1);

      $this->Ln();
      $this->SetX($x);

      $this->SetFont('Courier');
      $this->setFillColor(220, 223, 232);
      $fill = false;
      foreach ($registros2 as $i => $valor) {
        $this->Cell(0.6*$c1, $h, $i, 1, 0, 'C', $fill);
        $this->Cell(1.7*$c1, $h, utf8_decode($valor->accion), 1, 0, 'C', $fill);
        $this->Cell(2.0*$c1, $h, utf8_decode($valor->nombre), 1, 0, 'C', $fill);
        $this->Cell(1.5*$c1, $h, utf8_decode($valor->owner), 1, 0, 'C', $fill);
        $this->SetFont('Courier', 'BI', 9);
        $this->Cell(1.1*$c1, $h, utf8_decode($valor->kcv), 1, 0, 'C', $fill);
        $this->SetFont('Courier');
        $this->Ln();
        $this->SetX($x);
        $fill = !$fill;
      }
    }
      
    if ($totalCerts > 0) {
      $titulo = "DETALLE DE LOS OBJETOS";
      $this->AddPage();
      $this->SetY(25);
      $this->SetX($x+4);
      $this->SetTextColor(255, 0, 0);
      $this->SetFont('Courier', 'BUI', 22);
      $this->Cell(6.9*$c1, $h, $codigo, 0, 0, 'C', 0);
      $this->Ln(16);
      $this->SetTextColor(0, 0, 0);
    
      $this->SetX($x);
      $this->SetFillColor(153, 255, 102);
      $this->SetFont('Courier', 'B', 12);
      $this->Cell(6.9*$c1, $h, 'CERTIFICADOS', 1, 0, 'C', 1);

      $this->Ln();
      $this->SetX($x);
      $this->SetFillColor(255, 204, 120);
      $this->SetFont('Courier', 'B', 10);
      $this->Cell(0.6*$c1, $h, utf8_decode('ÍTEM'), 1, 0, 'C', 1);
      $this->Cell(1.5*$c1, $h, utf8_decode('ACCIÓN'), 1, 0, 'C', 1);
      $this->Cell(1.9*$c1, $h, utf8_decode('NOMBRE'), 1, 0, 'C', 1);
      $this->Cell(1.5*$c1, $h, utf8_decode('OWNER'), 1, 0, 'C', 1);
      $this->Cell(1.4*$c1, $h, utf8_decode('VENCIMIENTO'), 1, 0, 'C', 1);
      
      $this->Ln();
      $this->SetX($x);

      $this->SetFont('Courier');
      $this->setFillColor(220, 223, 232);
      $fill = false;
      foreach ($registros3 as $i => $valor) {
        $this->Cell(0.6*$c1, $h, $i, 1, 0, 'C', $fill);
        $this->Cell(1.5*$c1, $h, utf8_decode($valor->accion), 1, 0, 'C', $fill);
        $this->Cell(1.9*$c1, $h, utf8_decode($valor->nombre), 1, 0, 'C', $fill);
        $this->Cell(1.5*$c1, $h, utf8_decode($valor->owner), 1, 0, 'C', $fill);
        $this->Cell(1.4*$c1, $h, utf8_decode($valor->vencimiento), 1, 0, 'C', $fill);
        $this->Ln();
        $this->SetX($x);
        $fill = !$fill;
      }
    }
      
  }
  
}
  
function recuperarActividad($actividad) {
  $datos = array();
  //Conexión con la base de datos:
  $dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $consultaActividad = "select * from actividades where idactividades=".$actividad."";
  $result = consultarBD($consultaActividad, $dbc);
  while (($fila = $result->fetch_array(MYSQLI_ASSOC)) != NULL) { 
    $datos['actividad'] = $fila;
  }
  $usuario1 = $datos['actividad']['usuario1'];
  $usuario2 = $datos['actividad']['usuario2'];

  // Reacomodo fecha a formato habitual: dd/mm/aaaa
  $fecha = $datos['actividad']['fecha'];
  $separo = explode('-', $fecha);
  $fecha = $separo[2]."/".$separo[1].'/'.$separo[0];
  $datos["actividad"]["fecha"] = $fecha;

  $consultaUsuario1 = "select nombre, apellido from usuarios where idusuarios='".$usuario1."'";
  $result1 = consultarBD($consultaUsuario1, $dbc);
  while (($fila = $result1->fetch_array(MYSQLI_ASSOC)) != NULL) { 
    $datos['usuario1'] = $fila;
  }
  
  $consultaUsuario2 = "select nombre, apellido from usuarios where idusuarios='".$usuario2."'";
  $result2 = consultarBD($consultaUsuario2, $dbc);
  while (($fila = $result2->fetch_array(MYSQLI_ASSOC)) != NULL) { 
    $datos['usuario2'] = $fila;
  }
  
  $consultaRef = "select idreferencias, codigo, resumen, slot from referencias where estado='activa' and actividad='".$actividad."'";
  $result3 = consultarBD($consultaRef, $dbc);
  if ($result3->num_rows > 0) {
    while (($fila = $result3->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      $datos['referencia'][] = $fila;
    }
  }
  else {
    $datos['referencia'] = null;
    }
  return $datos;
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
    case 'idslot': $idslot = $temp2[1];
                   break;
    case 'idkey': $idkey = $temp2[1];
                  break;
    case 'idcert': $idcert = $temp2[1];
                   break;
    case 'idref': $idref = $temp2[1];
                   break;             
    case 'actividad': $idactivity = $temp2[1];
                      break;
    case 'x': $x = $temp2[1];
              break;
    case 'iduser': $iduser = $temp2[1];
                   break;
    case 'mails': $mails = $temp2[1];
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
//echo "id: ".$id."<br>query: ".$query."<br>largos: ".$largos."<br>campos: ".$campos1."<br>x: ".$x."<br>idref: ".$idref."<br>idslot: ".$idslot."<br>idkey: ".$idkey."<br>idcert: ".$idcert."<br>actividad: ".$idactivity."<br>FIN<br>";



switch ($id) {
  case "1": $query = "select DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, motivo from actividades order by fecha desc, idactividades asc";
            $largoCampos = array($c1, 1.5*$c1, 3.5*$c1, 6*$c1);
            $campos = array("Id", "Fecha", "Motivo");
            $tituloTabla = utf8_decode("LISTADO DE ACTIVIDADES");
            $titulo = "LISTADO DE ACTIVIDADES";
            $nombreReporte = "listadoActividades";
            $asunto = "Reporte KM con el Listado de las Actividades";
            $x = 55;
            break;
  case "2": $datos = recuperarActividad($idactivity);
            $tituloTabla = utf8_decode("DETALLE DE LA ACTIVIDAD");
            $titulo = "ACTIVIDAD";
            break;
  case "3": $query = "select referencias.codigo, referencias.lugar, referencias.plataforma, referencias.aplicacion, referencias.estado, referencias.resumen, referencias.detalles, slots.nombre as slot, hsm.nombre as hsm from referencias inner join slots on referencias.slot=slots.idslots inner join hsm on slots.hsm=hsm.idhsm where referencias.idreferencias = ".$idref;
            $query1 = "select apellido, nombre, empresa, rol from referencias inner join involucrados on referencias.idreferencias=involucrados.referencia inner join usuarios on involucrados.usuario=usuarios.idusuarios where referencias.idreferencias = ".$idref;
            $query2 = "select accion, nombre, llaves.owner, kcv from llaves inner join tareas on tareas.idtareas=llaves.tarea inner join referencias on referencias.idreferencias=tareas.referencia where referencias.idreferencias = ".$idref." order by llaves.owner, llaves.nombre";
            $query3 = "select accion, nombre, certificados.owner, DATE_FORMAT(vencimiento, '%d/%m/%Y') as vencimiento from certificados inner join tareas on tareas.idtareas=certificados.tarea inner join referencias on referencias.idreferencias=tareas.referencia where referencias.idreferencias = ".$idref. " order by certificados.owner";
            $tituloTabla = utf8_decode("DETALLES DE LA REFERENCIA");
            $titulo = "REFERENCIA";
            break;
  case "4": $query = "select * from llaves inner join tareas on llaves.tarea=tareas.idtareas where idkeys = ".$idkey;
            $tituloTabla = utf8_decode("DETALLE DE LA LLAVE");
            $titulo = "LLAVE";
            break;
  case "5": $query = "select nombre, owner, version, bandera, DATE_FORMAT(vencimiento, '%d/%m/%Y') as vencimiento, estado, accion, tareas.observaciones from certificados inner join tareas on certificados.tarea=tareas.idtareas where idcertificados = ".$idcert;
            $tituloTabla = utf8_decode("DETALLES DEL CERTIFICADO");
            $titulo = "CERTIFICADO";
            break;
  case "6": $tituloTabla =  utf8_decode("DETALLES DE LA CONSULTA");
            $tipoConsulta = utf8_decode($_POST["tipoConsulta"]);
            $titulo = 'RESULTADO DE LA CONSULTA';
            $nombreReporte = "detalleConsulta";
            $asunto = "Reporte KM con el detalle de la consulta realizada";
            break;
  case "7": $tituloTabla =  utf8_decode("DETALLES DEL USUARIO");
            $query = "select * from usuarios where idusuarios = ".$iduser;
            $titulo = "INFORMACIÓN DEL USUARIO";
            break;
  case "8": $tituloTabla =  utf8_decode("DETALLES DEL SLOT");
            $titulo = "SLOT";
            $query = "select slots.nombre, slots.observaciones as slotobserva, slots.estado as estadoslot, hsm.nombre as hsm, hsm.estado as estadohsm, hsm.marca, hsm.modelo, hsm.serie, hsm.observaciones as hsmobserva from slots inner join hsm on hsm.idhsm=slots.hsm where idslots = ".$idslot; /* ****** VER CONSULTA********/
            $query1 = "select apellido, nombre, empresa, usuariokms, slotusers.observaciones as obs from slotusers inner join usuarios on slotusers.usuario=usuarios.idusuarios where slotusers.slot= ".$idslot;
            break;        
  default: break;
}

//Instancio objeto de la clase:
$pdfResumen = new PDF();
//Agrego una página al documento:
$pdfResumen->AddPage();

if ($id !== "2") {
  $totalCampos = sizeof($campos);//echo "consulta: ".$query;
  // Conectar con la base de datos
  $con = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $resultado1 = consultarBD($query, $con);
}

if (($id == 1)||($id == 6)) {
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
}
else {
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
    case "7": $x = 65;
              $pdfResumen->detalleUsuario($registro);
              $nombreReporte = "detalleUsuario";
              $asunto = "Reporte KM con el detalle del Usuario";
              break;
    case "8": $x = 47;
              $resultado2 = consultarBD($query1, $con);
              $registros2 = obtenerResultados($resultado2);
              $pdfResumen->detalleSlot($registro, $registros2);
              $nombreReporte = "detalleSlot";
              $asunto = "Reporte KM con el detalle del Slot";
              break;
    default: break;
  }
}

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
