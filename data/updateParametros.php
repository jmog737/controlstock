<?php
session_start();

$newPageSize = $_GET["tamPagina"];
$newHistory = $_GET["tamHistorial"];

$cambioPagina = false;
$cambioHistory = false;

if (is_numeric($newPageSize)){
  $_SESSION["tamPagina"] = $newPageSize;
  $datos["pagina"] = $_SESSION["tamPagina"];
  $cambioPagina = true;
}
if (is_numeric($newHistory)){
  $_SESSION["limiteHistorial"] = $newHistory;
  $datos["historial"] = $_SESSION["limiteHistorial"];
  $cambioHistory = true;
}

if ($cambioPagina){
  $datos["resultadoPagina"] = 'OK';
}
else {
  $datos["resultadoPagina"] = 'ERROR';
}

if ($cambioHistory){
  $datos["resultadoHistory"] = 'OK';
}
else {
  $datos["resultadoHistory"] = 'ERROR';
}

$json = json_encode($datos);
echo $json;
?>