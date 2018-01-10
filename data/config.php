<?php
require_once("connectvars.php");
/**
******************************************************
*  @file config.php
*  @brief Archivo con el seteo de las carpetas y direcciones usadas en todo el programa.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Enero 2018
*
*******************************************************/

///Consulto nombre del HOST y en base al mismo, configuro la IP (porque el HSA tiene diferente rango de IPs):
$hostname = getHostName();
if ($hostname === "JUANMA") {
  $ip = "192.168.1.145";
}
else {
  $ip = "192.168.10.56";
}
//echo "host: ".$hostname."<br>ip: ".$ip;

$unidad = "D:";


//var_dump($_SERVER);
//echo "ip: ".$_SERVER["REMOTE_ADDR"];

//$dir = $unidad.":/PROCESOS/PDFS";
$dir = "//".$ip."/Reportes/";

$dirGrafica = $dir;
$dirExcel = $dir;
$dirLog = $dir;

if (!file_exists($unidad)) {
  $unidad = "C:";
}

if (!file_exists($dir)){
  echo "No existe la carpeta: $dir. <br>Por favor verifique.";
}

$rutaFotos = "$unidad/Servidor/disenos/controlstock/images/snapshots";



