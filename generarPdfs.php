<?php
/**
******************************************************
*  @file generarPdfs.php
*  @brief Archivo con las funciones que generan los archivos PDF.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Noviembre 2017
*
*******************************************************/
require_once('..\..\fpdf\mc_table.php');

///*************************** COLORES USADOS PARA LAS TABLAS: *******************************************************************

///Título de la tabla:
///$this->SetFillColor(2, 49, 132);
///$this->SetTextColor(255, 255, 255);

/// Nombre de los campos y línea con el total: 
///$this->SetFillColor(103, 167, 253);
///$this->SetTextColor(255, 255, 255);

///Resaltado TOTAL:
///$this->SetFillColor(0, 255, 255);

///*************************** FIN COLORES USADOS PARA LAS TABLAS ****************************************************************



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
    //$this->SetFillColor(153, 255, 102);
    //Defino color de fondo para el título de la tabla:
    $this->SetFillColor(2, 49, 132);
    //Defino el color del texto para el título de la tabla:
    $this->SetTextColor(255, 255, 255);
    //Escribo el título:
    $this->Cell($largoCampos[$totalCampos], 7, utf8_decode($tituloTabla), 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para los nombres de los campos (que será el mismo que para la fila con el total):
    //$this->SetFillColor(255, 204, 120);
    //$this->SetTextColor(0);
    $this->SetFillColor(103, 167, 253);
    $this->SetTextColor(255, 255, 255);
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
        if ($campos[$i] === 'Mensaje') {
          $indiceMensaje = $i;
        }
        if ($campos[$i] === utf8_decode('Últ. Mov.')) {
          $indiceUltMov = $i;
        }
      }
    }
      
    $this->Ln();
    $this->SetX($x);
    $this->SetFont('Courier', '', 9);    
    $fill = 1;
    foreach ($registros as $dato) { 
      
      //Calculate the height of the row
      $nb=0;
      $h0 = 0;
      for($i=0;$i<count($dato);$i++) {
        $dat = '';
        $tamDat = 0;
        $this->SetFont('Courier', '', 9);
        $dat = trim(utf8_decode($dato[$i]));
        if ($i === $indiceUltMov) {
          $separo = explode(" ", $dat);
          $dat = $separo[0];
        }
        
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
          $this->SetFont('Courier', '', 9);
          $datito = trim(utf8_decode($dato[$i]));
          if ($i === $indiceUltMov) {
            $separo = explode(" ", $datito);
            $datito = $separo[0];
          }
          $tamDat1 = $this->GetStringWidth($datito);
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
          //$this->RoundedRect($x1,$y,$w,$h0, 3.5, '1234', $f);
          
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
                if (!$tipo) {
                  /// seteo color de stock regular (verde):
                  $this->SetFillColor(113, 236, 113);
                  $fill = 1;
                }
                else {
                  $this->SetFillColor(103, 167, 253);
                  $this->SetTextColor(0);
                  $fill = 0;
                }        
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
            if ($i === $indiceMensaje) {
              $patron = "dif";
              $buscar = stripos($datito, $patron);
              if ($buscar !== FALSE){
                $this->SetFillColor(255, 255, 51);
                $fill = 1;
              }
              else 
                {
                $this->setFillColor(220, 223, 232);
              }
            }
            if ($i === $indiceUltMov) {
              $separo = explode(" ", $datito);
              $datito = $separo[0];
            }
            
            $this->SetFont('Courier', '', 9);
            $this->SetTextColor(0);
          }
          
          $h1 = $h0/$nb1;
          
          //Print the text
          if ($nb1 > 1) {
            $this->MultiCell($w, $h1, $datito,1, $a, $fill);
          }
          else {
            $this->MultiCell($w, $h0, $datito,1, $a, $fill);
          }  
          
          if ($i === $indiceStock) {
            $fill = $fillActual;
          }
          //Put the position to the right of the cell
          $this->SetXY($x1+$w,$y);
        }
      }
      //Go to the next line
      $this->Ln($h0);
      $this->SetX($x);
      $fill = $fillActual;
      if ($fill === 1) {
        $fill = 0;
      }
      else {
        $fill = 1;
      }
    }
    
    if ($total !== -1) {
      //Agrego fila final de la tabla con el total de plásticos:
      $this->SetFont('Courier', 'B', 12);
      //$this->SetFillColor(255, 204, 120);
      //$this->SetTextColor(0);*****************************************************************************************************
      $this->SetFillColor(103, 167, 253);
      $this->SetTextColor(255, 255, 255);
      $largoParaTotal = $largoCampos[$totalCampos-1];
      if ($tipo) {
        $largoTemp = $largoCampos[$totalCampos] - $largoParaTotal;
      }
      else {
        $largoParaTotal = $largoParaTotal + $largoCampos[$totalCampos-4];
        $largoTemp = $largoCampos[$totalCampos] - $largoParaTotal;
      }
      $this->Cell($largoTemp, 6, 'TOTAL:', 'LRBT', 0, 'C', true);
      $this->SetFont('Courier', 'BI', 14);
      $this->SetTextColor(255,0,0);
      //$this->SetFillColor(153, 255, 102);
      $this->SetFillColor(0, 255, 255);
      if ($tipo) {
        $this->Cell($largoParaTotal, 6, number_format($total, 0, ",", "."), 'LRBT', 0, 'C', true);
      }
      else {
        $this->Cell($largoParaTotal, 6, number_format($total, 0, ",", "."), 'LRBT', 0, 'C', true);
      }
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
      $cCampo = 2.2*$c1;
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
      //$this->SetFillColor(153, 255, 102);
      ///Título de la tabla:
      $this->SetFillColor(2, 49, 132);
      $this->SetTextColor(255, 255, 255);
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

      $this->SetFillColor(103, 167, 253);
      
      $this->SetTextColor(255, 255, 255);
      $this->SetFont('Courier', 'B', 10);
      $this->Cell($cCampo, $h0, "Nombre:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      $this->SetTextColor(0);
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
      $this->SetTextColor(255, 255, 255);
      $this->Cell($cCampo, $h0, "Entidad:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      $this->SetTextColor(0);
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
      $this->SetTextColor(255, 255, 255);
      $this->Cell($cCampo, 6, utf8_decode("Código:"), 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      $this->SetTextColor(0);
      $this->Cell($cResto, 6, $codigo, 'LRBT', 0, 'C', false);
      $this->Ln();
      $this->SetX($x);

      $bin = $registros[0][3];
      if (($bin === '')||($bin === null)) {
        $bin = 'N/D o N/C';
      }
      $this->SetFont('Courier', 'B', 10);
      $this->SetTextColor(255, 255, 255);
      $this->Cell($cCampo, 6, "BIN:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      $this->SetTextColor(0);
      $this->Cell($cResto, 6, $bin, 'LRBT', 0, 'C', false);
      $this->Ln();
      $this->SetX($x);

      $contacto = $registros[0][5];
      if (($contacto === '')||($contacto === null)) {
        $contacto = '';
      }
      $this->SetFont('Courier', 'B', 10);
      $this->SetTextColor(255, 255, 255);
      $this->Cell($cCampo, 6, "Contacto:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      $this->SetTextColor(0);
      $this->Cell($cResto, 6, $contacto, 'LRBT', 0, 'C', false);
      $this->Ln();
      $this->SetX($x);      
      
      $comentarios = trim(utf8_decode($registros[0][11]));
      if (($comentarios === '')||($comentarios === null)) {
        $comentarios = '';
      }
      $nb = $this->NbLines($cResto,$comentarios);
      $h0=$h*$nb;

      $this->SetFont('Courier', 'B', 10);
      $this->SetTextColor(255, 255, 255);
      $this->Cell($cCampo, $h0, "Comentarios:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      $this->SetTextColor(0);
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

      $ultimoMovimiento = trim(utf8_decode($registros[0][7]));
      if (($ultimoMovimiento === '')||($ultimoMovimiento === null)) {
        $ultimoMovimiento = '';
      }
      $nb = $this->NbLines($cResto,$ultimoMovimiento);
      $h0=$h*$nb;

      $this->SetFont('Courier', 'B', 10);
      $this->SetTextColor(255, 255, 255);
      $this->Cell($cCampo, $h0, utf8_decode("Último Movimiento:"), 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', '', 9);
      $this->SetTextColor(0);
      //Save the current position
      $x1=$this->GetX();
      $y=$this->GetY();
      //Draw the border
      $this->Rect($x1,$y,$cResto,$h0);
      //Print the text
      if ($nb > 1) {
        $this->MultiCell($cResto,$h, $ultimoMovimiento,'LRT','C', 0);
        }
      else {
        $this->MultiCell($cResto,$h, $ultimoMovimiento,1,'C', 0);
        } 
      
      //Put the position to the right of the cell
      $this->SetXY($x,$y+$h0);
      
      //Detecto si el stock actual está o no por debajo del valor de alarma. En base a eso elijo el color de fondo del stock:
      $alarma1 = $registros[0][9];
      $alarma2 = $registros[0][10];
      $stock = $registros[0][8];

      $this->SetFont('Courier', 'B', 10);
      $this->SetTextColor(255, 255, 255);
      $this->Cell($cCampo, 6, "Stock:", 'LRBT', 0, 'L', true);
      $this->SetFont('Courier', 'BI', 16);
      $this->SetTextColor(0);
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
    $this->SetTextColor(255, 255, 255);  
    //************************************** TÍTULO *****************************************************************************************
    $this->SetX($x);
    //Defino color de fondo:
    //$this->SetFillColor(153, 255, 102);
    $this->SetFillColor(2, 49, 132);
    //
    //Escribo el título:
    $this->Cell($largoCampos[$totalCampos], 7, utf8_decode($tituloTabla), 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    //$this->SetFillColor(255, 204, 120);
    $this->SetFillColor(103, 167, 253);
    
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
    $this->SetTextColor(0); 
    $fill = false;
    $productoViejo = $registros[0][$indNombre];
    $subtotalRetiro = 0;
    $subtotalRetiroMostrar = 0;
    $subtotalIngreso = 0;
    $subtotalIngresoMostrar = 0;
    $subtotalReno = 0;
    $subtotalRenoMostrar = 0;
    $subtotalDestruccion = 0;
    $subtotalDestruccionMostrar = 0;
    $totalConsumos = 0;
    $totalConsumosMostrar = 0;
          
    foreach ($registros as $dato) {
      $nombre = trim(utf8_decode($dato[$indNombre]));
      $cantidad1 = trim(utf8_decode($dato[$indCantidad]));
      $cantidad = number_format($cantidad1, 0, ",", ".");
      $tipo = trim($dato[$indTipo]);
      
      if ($productoViejo !== $nombre) {
        $productoViejo = $nombre;
        
        /// A definir más adelante el color para el fondo del separador. Por ahora es blanco (es decir, fill está como false):
        $this->setFillColor(220, 223, 232);
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        $tamSubTotal = $largoCampos[$indCantidad] + $largoCampos[$indComentarios];
        $tamTextoSubtotal = $tamTabla-$tamSubTotal;
        
        if ($subtotalRetiro > 0) {
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0);  
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, "Total Retiros:",1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(165, 156, 149);
          $this->setFillColor(200, 202, 212);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $subtotalRetiroMostrar,1,1,'C', true);
          
          $subtotalRetiro = 0;
          $subtotalRetiroMostrar = 0;
        }
        
        if ($subtotalReno > 0){
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0); 
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, "Total Renovaciones:",1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(165, 156, 149);
          $this->setFillColor(200, 202, 212);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $subtotalRenoMostrar,1,1,'C', true);
          
          $subtotalReno = 0;
          $subtotalRenoMostrar = 0;
        }
        
        if ($subtotalDestruccion > 0) {
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0);  
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, utf8_decode("Total Destrucciones:"),1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(165, 156, 149);
          $this->setFillColor(200, 202, 212);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $subtotalDestruccionMostrar,1,1,'C', true);
          
          $subtotalDestruccion = 0;
          $subtotalDestruccionMostrar = 0;
        }
        
        if ($totalConsumos > 0) {
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0);  
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, utf8_decode("Total de Consumos:"),1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(220, 223, 232);
          $this->setFillColor(239, 165, 105);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $totalConsumosMostrar,1,1,'C', true);
          
          $totalConsumos = 0;
          $totalConsumosMostrar = 0;
        }
        
        if ($subtotalIngreso > 0) {
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0);  
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, "Total de Ingresos:",1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(220, 223, 232);
          $this->setFillColor(127, 128, 129);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $subtotalIngresoMostrar,1,1,'C', true);
          
          $subtotalIngreso = 0;
          $subtotalIngresoMostrar = 0;
        }
        
        $this->SetFont('Courier', '', 9);
        $this->setFillColor(220, 223, 232);
        $this->SetTextColor(0);  
        $this->SetX($x);
        
        switch ($tipo) {
          case "Ingreso": $subtotalIngreso = $cantidad1;
                          $subtotalIngresoMostrar = number_format($subtotalIngreso, 0, ",", ".");
                          break;
          case "Retiro":  $subtotalRetiro = $cantidad1;
                          $subtotalRetiroMostrar = number_format($subtotalRetiro, 0, ",", ".");
                          break;
          case "Renovación":  $subtotalReno = $cantidad1;
                        $subtotalRenoMostrar = number_format($subtotalReno, 0, ",", ".");
                        break;
          case "Destrucción": $subtotalDestruccion = $cantidad1;
                              $subtotalDestruccionMostrar = number_format($subtotalDestruccion, 0, ",", ".");
                              break;
          default: break;
        }
        $totalConsumos = $subtotalRetiro + $subtotalReno + $subtotalDestruccion;
        $totalConsumosMostrar = number_format($totalConsumos, 0, ",", ".");
        //$subtotal = $cantidad1;
        //$subtotalMostrar = number_format($subtotal, 0, ",", ".");
        
        $this->CheckPageBreak($h);
        $this->Cell($tamTabla,$h, "",0,1,'C', false);
        $this->SetX($x);   
      }
      else {
        //$subtotal = $subtotal + $cantidad1;
        //$subtotalMostrar = number_format($subtotal, 0, ",", ".");
        switch ($tipo) {
          case "Ingreso": $subtotalIngreso = $subtotalIngreso + $cantidad1;
                          $subtotalIngresoMostrar = number_format($subtotalIngreso, 0, ",", ".");
                          break;
          case "Retiro":  $subtotalRetiro = $subtotalRetiro + $cantidad1;
                          $subtotalRetiroMostrar = number_format($subtotalRetiro, 0, ",", ".");
                          break;
          case "Renovación":  $subtotalReno = $subtotalReno + $cantidad1;
                        $subtotalRenoMostrar = number_format($subtotalReno, 0, ",", ".");
                        break;
          case "Destrucción": $subtotalDestruccion = $subtotalDestruccion + $cantidad1;
                              $subtotalDestruccionMostrar = number_format($subtotalDestruccion, 0, ",", ".");
                              break;
          default: break;
        }
        if ($tipo !== 'Ingreso') {
          $totalConsumos = $totalConsumos + $cantidad1;
          $totalConsumosMostrar = number_format($totalConsumos, 0, ",", ".");
        }
      }
      
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
        $fecha = $dato[$indFecha];
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
          $this->MultiCell($w,$h1, trim(utf8_decode($dato[$indTipo])),'LRT','C', $fill);
          }
        else {
          $this->MultiCell($w,$h0, trim(utf8_decode($dato[$indTipo])),1,'C', $fill);
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
    
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    $tamSubTotal = $largoCampos[$indCantidad] + $largoCampos[$indComentarios];
    $tamTextoSubtotal = $tamTabla-$tamSubTotal;

    if ($subtotalRetiro > 0) {
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0);  
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, "Total Retiros:",1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(165, 156, 149);
          $this->setFillColor(200, 202, 212);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $subtotalRetiroMostrar,1,1,'C', true);
          
          $subtotalRetiro = 0;
          $subtotalRetiroMostrar = 0;
        }
        
        if ($subtotalReno > 0){
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0); 
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, "Total Renovaciones:",1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(165, 156, 149);
          $this->setFillColor(200, 202, 212);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $subtotalRenoMostrar,1,1,'C', true);
          
          $subtotalReno = 0;
          $subtotalRenoMostrar = 0;
        }
        
        if ($subtotalDestruccion > 0) {
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0);  
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, utf8_decode("Total Destrucciones:"),1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(165, 156, 149);
          $this->setFillColor(200, 202, 212);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $subtotalDestruccionMostrar,1,1,'C', true);
          
          $subtotalDestruccion = 0;
          $subtotalDestruccionMostrar = 0;
        }
        
        if ($totalConsumos > 0) {
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0);  
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, utf8_decode("Total de Consumos:"),1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(220, 223, 232);
          $this->setFillColor(239, 165, 105);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $totalConsumosMostrar,1,1,'C', true);
          
          $totalConsumos = 0;
          $totalConsumosMostrar = 0;
        }
        
        if ($subtotalIngreso > 0) {
          $this->SetFont('Courier', 'B', 9);
          $this->setFillColor(220, 223, 232);
          $this->SetTextColor(0);  
          $this->SetX($x);
          $this->Cell($tamTextoSubtotal,$h, "Total de Ingresos:",1,0,'C', false);

          $this->SetFont('Courier', 'BI', 14);
          $this->setFillColor(220, 223, 232);
          $this->setFillColor(127, 128, 129);
          $this->SetTextColor(0);  
          $this->Cell($tamSubTotal,$h, $subtotalIngresoMostrar,1,1,'C', true);
          
          $subtotalIngreso = 0;
          $subtotalIngresoMostrar = 0;
        }
        
    /*
    $this->SetFont('Courier', 'B', 9);
    $this->setFillColor(220, 223, 232);
    $this->SetTextColor(0);  
    $this->Cell($tamTextoSubtotal,$h, "SubTotal:",1,0,'C', false);

    $this->SetFont('Courier', 'BI', 14);
    $this->setFillColor(220, 223, 232);
    $this->setFillColor(127, 128, 129);
    $this->SetTextColor(0);  
    $this->Cell($tamSubTotal,$h, $subtotalMostrar,1,1,'C', true);*/
  }
  
  //Tabla tipo listado con el detalle del producto:
  function tablaProducto()
    {
    global $h, $c1;
    global $registros, $codigo, $bin, $rutaFotos, $nombreProducto;
    
    $cCampo = 2.2*$c1;
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
    //$this->SetFillColor(153, 255, 102);
    ///Título de la tabla:
    $this->SetFillColor(2, 49, 132);
    $this->SetTextColor(255, 255, 255);
    //Escribo el título:
    $this->Cell($tamTabla, 7, "DETALLES DEL PRODUCTO", 1, 0, 'C', 1);
    $this->Ln();
    //**************************************  FIN TÍTULO ************************************************************************************
    
    //Restauro color de fondo y tipo de letra para el contenido:
    //$this->SetFillColor(255, 204, 120);
    //$this->SetTextColor(0);
    $this->SetFillColor(103, 167, 253);
    $this->SetTextColor(255, 255, 255);
    $this->SetFont('Courier');
    $this->SetX($x);
    
    $nb1 = $this->NbLines($cResto,$nombre);//echo "Nombre: ".$nombre."<br>Tamaño Nombre: ".$tamNombre."<br>Resto: ".$cResto."<br>Nb: ".$nb1;
    $h0=$h*$nb1;
    
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, $h0, "Nombre:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->SetTextColor(0);
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
    $this->SetTextColor(255, 255, 255);
    $this->Cell($cCampo, $h0, "Entidad:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->SetTextColor(0);
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
    $this->SetTextColor(255, 255, 255);
    $this->SetFont('Courier', 'B', 10);
    $this->Cell($cCampo, 6, utf8_decode("Código:"), 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->SetTextColor(0);
    $this->Cell($cResto, 6, $codigo, 'LRBT', 0, 'C', false);
    $this->Ln();
    $this->SetX($x);
    
    $bin = $registros[0][3];
    if (($bin === '')||($bin === null)) {
      $bin = 'N/D o N/C';
    }
    $this->SetFont('Courier', 'B', 10);
    $this->SetTextColor(255, 255, 255);
    $this->Cell($cCampo, 6, "BIN:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->SetTextColor(0);
    $this->Cell($cResto, 6, $bin, 'LRBT', 0, 'C', false);
    $this->Ln();
    $this->SetX($x);
    
    $contacto = $registros[0][5];
    if (($contacto === '')||($contacto === null)) {
      $contacto = '';
    }
    $this->SetFont('Courier', 'B', 10);
    $this->SetTextColor(255, 255, 255);
    
    $this->Cell($cCampo, 6, "Contacto:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->SetTextColor(0);
    $this->Cell($cResto, 6, $contacto, 'LRBT', 0, 'C', false);
    $this->Ln();
    $this->SetX($x);  
    
    $comentarios = trim(utf8_decode($registros[0][11]));
    if (($comentarios === '')||($comentarios === null)) {
      $comentarios = '';
    }
    $nb = $this->NbLines($cResto,$comentarios);
    $h0=$h*$nb;
    
    $this->SetFont('Courier', 'B', 10);
    $this->SetTextColor(255, 255, 255);
    $this->Cell($cCampo, $h0, "Comentarios:", 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->SetTextColor(0);
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
    
    $ultimoMovimiento = trim(utf8_decode($registros[0][7]));
    if (($ultimoMovimiento === '')||($ultimoMovimiento === null)) {
      $ultimoMovimiento = '';
    }
    $nb = $this->NbLines($cResto,$ultimoMovimiento);
    $h0=$h*$nb;
    
    $this->SetFont('Courier', 'B', 10);
    $this->SetTextColor(255, 255, 255);
    $this->Cell($cCampo, $h0, utf8_decode("Último Movimiento:"), 'LRBT', 0, 'L', true);
    $this->SetFont('Courier', '', 9);
    $this->SetTextColor(0);
    //Save the current position
    $x1=$this->GetX();
    $y=$this->GetY();
    //Draw the border
    $this->Rect($x1,$y,$cResto,$h0);
    //Print the text
    if ($nb > 1) {
      $this->MultiCell($cResto,$h, $ultimoMovimiento,'LRT','C', 0);
      }
    else {
      $this->MultiCell($cResto,$h, $ultimoMovimiento,1,'C', 0);
      } 
      
    //Put the position to the right of the cell
    $this->SetXY($x,$y+$h0);
    
    //Detecto si el stock actual está o no por debajo del valor de alarma. En base a eso elijo el color de fondo del stock:
    $alarma1 = $registros[0][9];
    $alarma2 = $registros[0][10];
    $stock = $registros[0][8];
    
    $this->SetFont('Courier', 'B', 10);
    $this->SetTextColor(255, 255, 255);
    $this->Cell($cCampo, 6, "Stock:", 'LRBT', 0, 'L', true);
    $this->SetTextColor(0);
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
  
  ///**************************************************************** TESTS BORDES REDONDEADOS *****************************************************
  
  function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

  function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
  {
      $h = $this->h;
      $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
          $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
  }
 
  
  ///************************************************************* FIN TESTS BORDES REDONDEADOS ****************************************************
  
  
}