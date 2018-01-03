<?php
/**
******************************************************
*  @file config.php
*  @brief Archivo con el seteo de las carpetas y direcciones usadas en todo el programa.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Enero 2018
*
*******************************************************/

$unidad = "D:";
$ip = "192.168.1.145";

//$dir = $unidad.":/PROCESOS/PDFS";
$dir = "//".$ip."/Reportes/";

$dirGrafica = $dir;
$dirExcel = $dir;

if (!file_exists($unidad)) {
  $unidad = "C:";
}

if (!file_exists($dir)){
  echo "No existe la carpeta: $dir. <br>Por favor verifique.";
}

$rutaFotos = "$unidad/Servidor/disenos/controlstock/images/snapshots";



