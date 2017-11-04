<?php
//Reanudamos la sesión:
session_start();
require_once("data/sesiones.php");

require_once('data\baseMysql.php');
require_once('..\..\fpdf\mc_table.php');
require_once('generarExcel.php');
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
    $this->SetTextColor(0);
    $this->Cell(0, 10, 'Pag. ' . $this->PageNo(), 0, 0, 'C');
    }

  //Tabla tipo listado para el stock de una o todas las entidades, o también para el total de plásticos en bóveda:
  function tablaStockEntidad($total, $tipo)
    {
    global $x,$h, $totalCampos;
    global $registros, $campos, $largoCampos, $tituloTabla, $tipoConsulta, $entidad, $mostrar;

    $tamTabla = $largoCampos[$totalCampos];
    $anchoPagina = $this->GetPageWidth();
    $anchoTipo = 0.8*$anchoPagina;
    
    $x = round((($anchoPagina-$tamTabla)/2), 2);
    $xTipo = round((($anchoPagina - $anchoTipo)/2), 2);
    
    $tamEntidad = $this->GetStringWidth($entidad);    
    
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

    $tipoTotal = "Consulta del stock de: $entidad";
    $tam = $this->GetStringWidth($tipoTotal);
    $xInicio = $xTipo + (($anchoTipo-$tam)/2);
    $this->SetX($xInicio);

    $nb = $this->NbLines($anchoTipo,$tipoTotal);
    $h0=$h*$nb;
    
    $tam1 = $this->GetStringWidth("Consulta del stock de:");
    
    if ($tipo) {
      $this->SetX($xTipo);
      $this->MultiCell($anchoTipo, 7, utf8_decode($tipoConsulta), 0, 'C', 0);
    }
    else {
      if ($nb > 1) {
        $this->Cell($tam1,$h, "Consulta del stock de:",0, 0, 'R', 0);
        $this->SetTextColor(255, 0, 0);
        $this->Cell($tamEntidad,$h, $entidad,0, 0,'L', 0);
        $this->SetTextColor(0);
      }
      else {
        $this->Cell($tam1,$h0, "Consulta del stock de:",0, 0,'R', 0);
        $this->SetTextColor(255, 0, 0);
        $this->Cell($tamEntidad,$h0, $entidad,0, 0,'L', 0);
        $this->SetTextColor(0);
      }  
    }
    
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
    foreach ($campos as $i => $dato) {
      if ($mostrar[$i]) {
        $this->Cell($largoCampos[$i], 6, $campos[$i], 'LRBT', 0, 'C', true);
        if ($campos[$i] === 'Stock') {
          $indiceStock = $i;
        }
        if ($campos[$i] === 'Entidad') {
          $indiceEntidad = $i;
        }
      }
    }
      
    $this->Ln();
    $this->SetX($x);
    $this->SetFont('Courier', '', 9);    
    $fill = true;
    foreach ($registros as $dato) {  
      //Calculate the height of the row
      $nb=0;
      $h0 = 0;
      for($i=0;$i<count($dato);$i++) {
        $dat = '';
        $tamDat = 0;
        $this->SetFont('Courier', '', 9);
        $dat = trim(utf8_decode($dato[$i]));
        $tamDat = $this->GetStringWidth($dat);
        $w1 = $largoCampos[$i];
        if ($mostrar[$i]){
          $nb=max($nb,$this->NbLines($w1,$dat));
        }
      }
      $h0=$h*$nb;
      
      //Issue a page break first if needed
      $this->CheckPageBreak($h0);
      
      $this->setFillColor(220, 223, 232);

      //Draw the cells of the row
      for($i=0;$i<count($dato);$i++)
        {
        if ($mostrar[$i]) {
          
          $w = $largoCampos[$i];
          $datito = trim(utf8_decode($dato[$i]));
          $nb1 = $this->NbLines($w, $datito);

          //Save the current position
          $x1=$this->GetX();
          $y=$this->GetY();
          
          if ($fill) {
            $f = 'F';
          }
          else {
            $f = '';
          }
          //Draw the border
          $this->Rect($x1,$y,$w,$h0, $f);
          
          if ($i === $indiceStock) {
            //Detecto si el stock actual está o no por debajo del valor de alarma. En base a eso elijo el color de fondo del stock:
            $alarma1 = $dato[$i+1];
            $alarma2 = $dato[$i+2];
            $stock = $dato[$i];
            $datito = number_format($stock, 0, ",", ".");
            $a = 'C';
            $fillActual = $fill;
            $this->SetFont('Courier', 'BI', 12);            
            if (($stock < $alarma1) && ($stock > $alarma2)){
              /// seteo color alarma de Advertencia (amarilla):
              $this->SetFillColor(255, 255, 51);
              $this->SetTextColor(0);
              $fill = 1;
            }
            else {
              if ($stock < $alarma2){
                /// seteo color de alarma Crítica (roja):
                $this->SetFillColor(231, 56, 67);
                $this->SetTextColor(255);
                $fill = 1;
              }
              else {
                /// seteo color de stock regular (verde):
                $this->SetFillColor(113, 236, 113);
                $fill = 1;
              }
            }
          }
          else {
            if ($i === $indiceEntidad) {
              $a = 'L';
            }
            else 
              {
              $a = 'C';
            }
            //$datito = utf8_decode($dato[$i]);
            
            $this->SetFont('Courier', '', 9);
            $this->setFillColor(220, 223, 232);
            $this->SetTextColor(0);
          }
          $h1 = $h0/$nb1;
          //Print the text
          if ($nb1 > 1) {
            $this->MultiCell($w,$h1, $datito,1,$a, $fill);
            }
          else {
            $this->MultiCell($w,$h0, $datito,1,$a, $fill);
            }  
          
          //Put the position to the right of the cell
          $this->SetXY($x1+$w,$y);
        }
      }
      //Go to the next line
      $this->Ln($h0);
      $this->SetX($x);
      $fill = $fillActual;
      $fill = !$fill;
    }
    
    if ($total !== -1) {
      //Agrego fila final de la tabla con el total de plásticos:
      $this->SetFont('Courier', 'B', 12);
      $this->SetFillColor(255, 204, 120);
      $this->SetTextColor(0);
      $largoTemp = $largoCampos[$totalCampos] - $largoCampos[$totalCampos-1];
      $this->Cell($largoTemp, 6, 'TOTAL:', 'LRBT', 0, 'C', true);
      $this->SetFont('Courier', 'BI', 14);
      $this->SetTextColor(255,0,0);
      $this->SetFillColor(153, 255, 102);
      $this->Cell($largoCampos[$totalCampos-1], 6, number_format($total, 0, ",", "."), 'LRBT', 0, 'C', true);
    }
  }
  
  function tablaMovimientos($tablaProducto) 
    {
    global $h, $x, $totalCampos, $c1;
    global $registros, $campos, $largoCampos, $rutaFotos, $tituloTabla, $tipoConsulta, $codigo, $mostrar;

    $anchoPagina = $this->GetPageWidth();
    $anchoTipo = 0.8*$anchoPagina;

    //Defino color de fondo:
    $this->SetFillColor(255, 156, 233);
    //Defino color para los bordes:
    $this->SetDrawColor(0, 0, 0);
    //Defino grosor de los bordes:
    $this->SetLineWidth(.3);
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 12);
    //Defino el color para el texto:
    $this->SetTextColor(0);
    
    $subTitulo = utf8_decode($tipoConsulta);
    $tam1 = $this->GetStringWidth($subTitulo);
    $xTipo = round((($anchoPagina - $anchoTipo)/2), 2);
    $this->SetY(25);
    $this->SetX($xTipo);
    
    $nb = $this->NbLines($anchoTipo,$subTitulo);
    $h0=$h*$nb;
    if ($nb > 1) {
      $this->MultiCell($anchoTipo,$h, $subTitulo,1, 'C', 0);
    }
    else {
      $this->MultiCell($anchoTipo,$h0, $subTitulo,1,'C', 0);
    }
    $this->Ln(10);
    
    //************************************************************** INICIO TABLA PRODUCTO ****************************************************
    if ($tablaProducto) {
      $cCampo = 1.5*$c1;
      $cResto = 4*$c1;
      $cFoto = 3.4*$c1;
      $nombre = trim(utf8_decode($registros[0][2]));
      $entidad = trim(utf8_decode($registros[0][1]));
      $tamEntidad = $this->GetStringWidth($entidad);
      $tamNombre = $this->GetStringWidth($nombre);
      //$tamCodigo = $this->GetStringWidth($codigo);
      if ((($tamNombre) || ($tamEntidad)) < $cResto) {
        $cResto = 1.52*$tamNombre;
      }
      
      $tamTabla = $cCampo + $cResto;

      $x = ($anchoPagina-$tamTabla)/2;
      $xTipo = ($anchoPagina - $anchoTipo)/2;

      //Defino color de fondo:
      $this->SetFillColor(255, 156, 233);
      //Defino color para los bordes:
      $this->SetDrawColor(0, 0, 0);
      //Defino grosor de los bordes:
      $this->SetLineWidth(.3);
      //Defino tipo de letra y tamaño para el Título:
      $this->SetFont('Courier', 'B', 12);

      ///Agrego un snapshot de la tarjeta debajo de la tabla (si es que existe!!):
      $foto = $registros[0][6];
      if (($foto !== null) && ($foto !== '')) {
        $rutita = $rutaFotos."/".$foto;
        $xFoto = $this->GetX();
        $yFoto = $this->GetY();
        $this->Image($rutita, 77, $yfoto, $cFoto, 30);
        $this->Ln();
      }
      
      //************************************** TÍTULO *****************************************************************************************
      $this->SetX($x);
      //Defino color de fondo:
      $this->SetFillColor(153, 255, 102);
      //Escribo el título:
      $this->Cell($tamTabla, 7, "DETALLES DEL PRODUCTO", 1, 0, 'C', 1);
      $this->Ln();
      //**************************************  FIN TÍTULO ************************************************************************************

      //Restauro color de fondo y tipo de letra para el contenido:
      $this->SetFillColor(255, 204, 120);
      $this->SetTextColor(0);
      $this->SetFont('Courier');
      $this->SetX($x);

      $nb1 = $this->NbLines($cResto,$nombre);
      $h0=$h*$nb1;

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
        $this->MultiCell($cResto,$h, $nombre,'LRT','C', 0);
        }
      else {
        $this->MultiCell($cResto,$h0, $nombre,1,'C', 0);
        }  

      //Put the position to the right of the cell
      $this->SetXY($x,$y+$h0);


      $nb = $this->NbLines($cResto, $entidad);
      $h0=$h*$nb;

      $this->SetFont('Courier', 'B', 10);
      $this->Cell($cCampo, $h0, "Entidad:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      //Save the current position
      $x1=$this->GetX();
      $y=$this->GetY();
      //Draw the border
      $this->Rect($x1,$y,$cResto,$h0);
      //Print the text
      if ($nb > 1) {
        $this->MultiCell($cResto,$h, trim(utf8_decode($registros[0][1])),'LRT','C', 0);
        }
      else {
        $this->MultiCell($cResto,$h, trim(utf8_decode($registros[0][1])),1,'C', 0);
        }  

      //Put the position to the right of the cell
      $this->SetXY($x,$y+$h0);

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

      $contacto = $registros[0][5];
      if (($contacto === '')||($contacto === null)) {
        $contacto = '';
      }
      $this->SetFont('Courier', 'B', 10);
      $this->Cell($cCampo, 6, "Contacto:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      $this->Cell($cResto, 6, $contacto, 'LRBT', 0, 'C', false);
      $this->Ln();
      $this->SetX($x);      
      
      $comentarios = trim(utf8_decode($registros[0][10]));
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
      $alarma1 = $registros[0][8];
      $alarma2 = $registros[0][9];
      $stock = $registros[0][7];

      $this->SetFont('Courier', 'B', 10);
      $this->Cell($cCampo, 6, "Stock:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', 'BI', 16);
      if (($stock < $alarma1) && ($stock > $alarma2)){
        $this->SetFillColor(255, 255, 51);
        $this->SetTextColor(0);
      }
      else {
        if ($stock < $alarma2){
          $this->SetFillColor(231, 56, 67);
          $this->SetTextColor(255);
        }
        else {
          $this->SetFillColor(113, 236, 113);
        }
      }

      $this->Cell($cResto, 6, number_format($stock, 0, ",", "."), 'LRBT', 0, 'C', true);
      $this->Ln(15);
      $this->SetX($x);
    }
    //**************************************************************** FIN TABLA PRODUCTO ******************************************************
    
    //*********************************** Comienza generación de la tabla con los movimientos: *************************************************
    $tamTabla = $largoCampos[$totalCampos];
    //$tamNombre = $this->GetStringWidth($nombreProducto); 
    $x = round((($anchoPagina-$tamTabla)/2), 2);
  
    //Defino color para los bordes:
    $this->SetDrawColor(0, 0, 0);
    //Defino grosor de los bordes:
    $this->SetLineWidth(.3);
    //Defino tipo de letra y tamaño para el Título:
    $this->SetFont('Courier', 'B', 12);
    //Defino color para el texto:
    $this->SetTextColor(0);  
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
    
    /// Recupero los índices de cada campo para poder ordenarlos luego:
    foreach ($campos as $i => $dato) {
      switch ($dato) {
        case "Id": $indId = $i;
                   break;
        case "Entidad": $indEntidad = $i;
                        break;
        case "Nombre": $indNombre = $i;
                       break;
        case "Fecha": $indFecha = $i;
                      break;
        case "Hora": $indHora = $i;
                     break;
        case "Tipo": $indTipo = $i;
                     break;
        case "Cantidad":  $indCantidad = $i;
                          break;
        case "Comentarios": $indComentarios = $i;
                            break;
        case "BIN": $indBin = $i;
                    break;
        case "Código":  $indCodigo = $i;
                        break;
        case "Contacto":  $indContacto = $i;
                        break;              
        case "Snapshot":  $indSnapshot = $i;
                          break;
        case "Alarma":  $indAlarma = $i;
                        break;
        case "ComentariosProd": $indComProd = $i;
                                break;  
        default: break;
      }
    }
    
    /// Imprimo los nombres de cada campo, siempre y cuando, se hayan marcado como visibles:
    /// Esto hay que hacerlo uno a uno para que queden en el orden requerido que es diferente al de la consulta
    if ($mostrar[$indId]) {
      $this->Cell($largoCampos[$indId], 6, $campos[$indId], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indFecha]) {
      $this->Cell($largoCampos[$indFecha], 6, $campos[$indFecha], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indHora]) {
      $this->Cell($largoCampos[$indHora], 6, $campos[$indHora], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indEntidad]) {
      $this->Cell($largoCampos[$indEntidad], 6, $campos[$indEntidad], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indNombre]) {
      $this->Cell($largoCampos[$indNombre], 6, $campos[$indNombre], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indBin]) {
      $this->Cell($largoCampos[$indBin], 6, $campos[$indBin], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indCodigo]) {
      $this->Cell($largoCampos[$indCodigo], 6, $campos[$indCodigo], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indContacto]) {
      $this->Cell($largoCampos[$indContacto], 6, $campos[$indContacto], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indSnapshot]) {
      $this->Cell($largoCampos[$indSnapshot], 6, $campos[$indSnapshot], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indTipo]) {
      $this->Cell($largoCampos[$indTipo], 6, $campos[$indTipo], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indCantidad]) {
      $this->Cell($largoCampos[$indCantidad], 6, $campos[$indCantidad], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indComentarios]) {
      $this->Cell($largoCampos[$indComentarios], 6, $campos[$indComentarios], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indAlarma]) {
      $this->Cell($largoCampos[$indAlarma], 6, $campos[$indAlarma], 'LRBT', 0, 'C', true);
    }
    if ($mostrar[$indComProd]) {
      $this->Cell($largoCampos[$indComProd], 6, $campos[$indComProd], 'LRBT', 0, 'C', true);
    }
    
    $this->Ln();
    $this->SetX($x);
    $this->SetFont('Courier', '', 9);    
    $fill = false;
    foreach ($registros as $dato) {
      
      //Calculate the height of the row
      $nb=0;
      $h0 = 0;
      for($i=0;$i<count($dato);$i++) {
        $dat = '';
        $tamDat = 0;
        $this->SetFont('Courier', '', 9);
        $dat = trim(utf8_decode($dato[$i]));
        $tamDat = $this->GetStringWidth($dat);
        //echo "dato: $dat<br>tam: $tamDat<br>largoCampo: $largoCampos[$i]<br>mostrar: $mostrar[$i]<br>**********<br>";
        $w1 = $largoCampos[$i];
        if ($mostrar[$i]){
          $nb=max($nb,$this->NbLines($w1,$dat));
        }
      }
      //echo "nb: $nb<br>-------<br>";
      //En base a la cantidad de líneas requeridas, calculo el alto de la fila;
      $h0=$h*$nb;
      //Issue a page break first if needed
      $this->CheckPageBreak($h0);
      
      $this->setFillColor(220, 223, 232);

      /// Chequeo si se tiene que mostrar el campo Id, y de ser así lo muestro:
      if ($mostrar[$indId]) 
        {
        $w = $largoCampos[$indId];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indId])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indId])),'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indId])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Fecha, y de ser así lo muestro:
      if ($mostrar[$indFecha]) 
        {
        $w = $largoCampos[$indFecha];
        $fechaTemp = explode("-", $dato[$indFecha]);
        $fecha = $fechaTemp[2]."/".$fechaTemp[1]."/".$fechaTemp[0];
        $nb1 = $this->NbLines($w,$fecha);

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, $fecha,'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, $fecha,1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Hora, y de ser así lo muestro:
      if ($mostrar[$indHora]) 
        {
        $w = $largoCampos[$indHora];
        $hora = $dato[$indHora];
        $nb1 = $this->NbLines($w,$hora);

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, $hora,'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, $hora,1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Entidad, y de ser así lo muestro:
      if ($mostrar[$indEntidad]) 
        {
        $w = $largoCampos[$indEntidad];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indEntidad])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indEntidad])),1,'C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indEntidad])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Nombre, y de ser así lo muestro:
      if ($mostrar[$indNombre]) 
        {
        $w = $largoCampos[$indNombre];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indNombre])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indNombre])),1,'C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indNombre])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Bin, y de ser así lo muestro:
      if ($mostrar[$indBin]) 
        {
        $w = $largoCampos[$indBin];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indBin])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indBin])),'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indBin])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Codigo, y de ser así lo muestro:
      if ($mostrar[$indCodigo]) 
        {
        $w = $largoCampos[$indCodigo];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indCodigo])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indCodigo])),'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indCodigo])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Codigo, y de ser así lo muestro:
      if ($mostrar[$indContacto]) 
        {
        $w = $largoCampos[$indContacto];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indContacto])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indContacto])),'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indContacto])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Snapshot, y de ser así lo muestro:
      if ($mostrar[$indSnapshot]) 
        {
        $w = $largoCampos[$indSnapshot];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indSnapshot])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indSnapshot])),'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indSnapshot])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Tipo, y de ser así lo muestro:
      if ($mostrar[$indTipo]) 
        {
        $w = $largoCampos[$indTipo];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indTipo])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indTipo])),'LRT','L', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indTipo])),1,'L', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Cantidad, y de ser así lo muestro:
      if ($mostrar[$indCantidad]) 
        {
        $w = $largoCampos[$indCantidad];
        $cantidad1 = trim(utf8_decode($dato[$indCantidad]));
        $cantidad = number_format($cantidad1, 0, ",", ".");
        $nb1 = $this->NbLines($w,$cantidad);

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        $this->SetFont('Courier', 'BI', 14);
        $this->SetTextColor(255,0,0);
        
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, $cantidad,'LRT','R', $fill);
          }
        else {
          $this->MultiCell($w,$h0, $cantidad,1,'R', $fill);
          } 
        
        $this->SetFont('Courier', '', 9);
        $this->setFillColor(220, 223, 232);
        $this->SetTextColor(0);  
          
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Stock, y de ser así lo muestro:
      if ($mostrar[$indStock]) 
        {
        $w = $largoCampos[$indStock];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indStock])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indStock])),'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indStock])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      /// Chequeo si se tiene que mostrar el campo Comentarios, y de ser así lo muestro:
      if ($mostrar[$indComentarios]) 
        {
        $w = $largoCampos[$indComentarios];
        $nb1 = $this->NbLines($w,trim(utf8_decode($dato[$indComentarios])));

        //Save the current position
        $x1=$this->GetX();
        $y=$this->GetY();
        
        if ($fill) {
          $f = 'F';
        }
        else {
          $f = '';
        }
        //Draw the border
        $this->Rect($x1,$y,$w,$h0, $f);
        $h1 = $h0/$nb1;
        //Print the text
        if ($nb1 > 1) {
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indComentarios])),1,'C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indComentarios])),1,'C', $fill);
          }  
        //Put the position to the right of the cell
        $this->SetXY($x1+$w,$y);
      }
      
      //Go to the next line
      $this->Ln($h0);
      $this->SetX($x);
      $fill = !$fill;
    }
  }
  
  //Tabla tipo listado con el detalle del producto:
  function tablaProducto()
    {
    global $h, $c1;
    global $registros, $codigo, $bin, $rutaFotos, $nombreProducto;
    
    $cCampo = 1.5*$c1;
    $cResto = 4*$c1;
    $cFoto = 3.4*$c1;
    $nombre = trim(utf8_decode($registros[0][2]));
    $entidad = trim(utf8_decode($registros[0][1]));
    $tamEntidad = $this->GetStringWidth($entidad);
    $tamNombre = $this->GetStringWidth($nombre);
    $tamCodigo = $this->GetStringWidth($codigo);
    if ((($tamNombre) || ($tamEntidad)) < $cResto) {
      $cResto = 1.52*$tamCodigo;
    }
    
    $tamTabla = $cCampo + $cResto;
    $anchoPagina = $this->GetPageWidth();
    $anchoTipo = 0.8*$anchoPagina;
    
    $x = ($anchoPagina-$tamTabla)/2;
    $xTipo = ($anchoPagina - $anchoTipo)/2;
    //echo "pag: ".$ancho."<br>tabla: ".$tamTabla."<br>x:".$x;
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
    
    $tipoTotal = "Stock del producto: $nombre";
    $tam = $this->GetStringWidth($tipoTotal);
    $xInicio = $xTipo + (($anchoTipo-$tam)/2);
    $this->SetX($xInicio);

    $nb = $this->NbLines($anchoTipo,$tipoTotal);
    $h0=$h*$nb;
    
    //Save the current position
    $x1=$this->GetX();
    $y=$this->GetY();
    $tam1 = $this->GetStringWidth("Stock del producto:");
    
    //Print the text
    if ($nb > 1) {
      //$this->MultiCell($anchoTipo,$h, trim(utf8_decode($tipoConsulta)),0,'C', 0);
      $this->Cell($tam1,$h, "Stock del producto:",0, 0, 'R', 0);
      $this->SetTextColor(255, 0, 0);
      $this->Cell($tamNombre,$h, $nombreProducto,0, 0,'L', 0);
      $this->SetTextColor(0);
      }
    else {
      //$this->MultiCell($anchoTipo,$h, trim(utf8_decode($tipoConsulta)),0,'C', 0);
      $this->Cell($tam1,$h0, "Stock del producto:",0, 0,'R', 0);
      $this->SetTextColor(255, 0, 0);
      $this->Cell($tamNombre,$h0, $nombreProducto,0, 0,'L', 0);
      $this->SetTextColor(0);
      }  

    $this->SetXY($x,$y+$h0);

    //$this->Ln();
    
    ///Agrego un snapshot de la tarjeta debajo de la tabla (si es que existe!!):
    $foto = $registros[0][6];
    if (($foto !== null) && ($foto !== '')) {
      $rutita = $rutaFotos."/".$foto;
      $xFoto = $this->GetX();
      $yFoto = $this->GetY();
      $this->Ln();
      $this->Image($rutita, 77, $yfoto, $cFoto, 30);
    }
    
    $this->Ln(10);
    
    //************************************** TÍTULO *****************************************************************************************
    $this->SetX($x);
    //Defino color de fondo:
    $this->SetFillColor(153, 255, 102);
    //Escribo el título:
    $this->Cell($tamTabla, 7, "DETALLES DEL PRODUCTO", 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    $this->SetFillColor(255, 204, 120);
    $this->SetTextColor(0);
    $this->SetFont('Courier');
    $this->SetX($x);
    
    $nb1 = $this->NbLines($cResto,$nombre);//echo "Nombre: ".$nombre."<br>Tamaño Nombre: ".$tamNombre."<br>Resto: ".$cResto."<br>Nb: ".$nb1;
    $h0=$h*$nb1;
    
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
      $this->MultiCell($cResto,$h, $nombre,'LRT','C', 0);
      }
    else {
      $this->MultiCell($cResto,$h0, $nombre,1,'C', 0);
      }  
      
    //Put the position to the right of the cell
    $this->SetXY($x,$y+$h0);
    
    
    $nb = $this->NbLines($cResto, $entidad);
    $h0=$h*$nb;
    
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, $h0, "Entidad:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    //Save the current position
    $x1=$this->GetX();
    $y=$this->GetY();
    //Draw the border
    $this->Rect($x1,$y,$cResto,$h0);
    //Print the text
    if ($nb > 1) {
      $this->MultiCell($cResto,$h, trim(utf8_decode($registros[0][1])),'LRT','C', 0);
      }
    else {
      $this->MultiCell($cResto,$h, trim(utf8_decode($registros[0][1])),1,'C', 0);
      }  
      
    //Put the position to the right of the cell
    $this->SetXY($x,$y+$h0);
    
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
    
    $contacto = $registros[0][5];
    if (($contacto === '')||($contacto === null)) {
      $contacto = '';
    }
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, 6, "Contacto:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->Cell($cResto, 6, $contacto, 'LRBT', 0, 'C', false);
    $this->Ln();
    $this->SetX($x);  
    
    $comentarios = trim(utf8_decode($registros[0][10]));
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
    $alarma1 = $registros[0][8];
    $alarma2 = $registros[0][9];
    $stock = $registros[0][7];
    
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, 6, "Stock:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', 'BI', 16);
    if (($stock < $alarma1) && ($stock > $alarma2)){
      $this->SetFillColor(255, 255, 51);
      $this->SetTextColor(0);
    }
    else {
      if ($stock < $alarma2){
        $this->SetFillColor(231, 56, 67);
        $this->SetTextColor(255);
      }
      else {
        $this->SetFillColor(113, 236, 113);
      }
    }
    
    
    $this->Cell($cResto, 6, number_format($stock, 0, ",", "."), 'LRBT', 0, 'C', true);
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
    $deNombre = "Stock Manager";
    $pwd = "em123sa";
    $responderMail = "ensobrado@emsa.com.uy";
    $responderNombre = "Stock Manager";
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
        // Activo condificacción utf-8
        //$mail->CharSet = 'UTF-8';
        $mail->CharSet = 'ISO-8859-1';
        
        //$asunto = "=?ISO-8859-1?B?".$asunto."=?=";
        //Datos del mail a enviar:
        $mail->Subject = $asunto;echo $mail->Subject."<br>";
        
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
                         if (($id === "4") && (isset($temp2[2]))) {
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
            $indiceStock = 7;
            break;
  case "2": $tituloTabla = "STOCK DEL PRODUCTO";
            $titulo = "STOCK DEL PRODUCTO";
            $nombreReporte = "stockProducto";
            $asunto = "Reporte con el stock del Producto";
            $nombreCampos = "(select 'Entidad', 'Nombre', 'BIN', 'Stock')";
            $indiceStock = 7;
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
            $indiceStock = 11;
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
            $indiceStock = 11;
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
$salida = $dir."/".$nombreArchivo;

///Guardo el archivo en el disco, y además lo muestro en pantalla:
$pdfResumen->Output($salida, 'F');
$pdfResumen->Output($salida, 'I');

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
$fileDir = $dir."/".$nombreZip;

$excel = $dir."/".$archivo;

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
  //$asunto = $asunto;
  $cuerpo = utf8_decode("<html><body><h4>Se adjunta el reporte generado del stock</h4></body></html>");
  $respuesta = enviarMail($para, '', '', $asunto, $cuerpo, "REPORTE", $nombreZip, $fileDir);
  echo $respuesta;
}


?>
