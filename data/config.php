<?php
session_start();
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


$dir = "//".$ip."/Reportes/";
//$dir = "//".$hostname."/Reportes/";

$dirExcel = $dir;
$dirLog = $dir."Logs/";
$dirGraficas = $dir."/graficas/";
$rutaFotos = "images/snapshots";

if (!file_exists($unidad)) {
  $unidad = "C:";
}

if (!file_exists($dir)){
  echo "No existe la carpeta: $dir. <br>Por favor verifique.";
}

if (!isset($_SESSION["tamPagina"])){
  $_SESSION["tamPagina"] = 50;
}
if (!isset($_SESSION["limiteSelects"])){
  $_SESSION["limiteSelects"] = 15;
}
if (!isset($_SESSION["limiteHistorialProducto"])){
  $_SESSION["limiteHistorialProducto"] = 5;
}
if (!isset($_SESSION["limiteHistorialGeneral"])){
  $_SESSION["limiteHistorialGeneral"] = 10;
}
$limiteSeleccion = 8;