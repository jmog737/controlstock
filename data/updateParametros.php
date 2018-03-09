<?php
session_start();

$newPageSize = $_GET["tamPagina"];
$newHistoryProducto = $_GET["tamHistorialProducto"];
$newHistoryGeneral = $_GET["tamHistorialGeneral"];

$cambioPagina = false;
$cambioHistoryProducto = false;
$cambioHistoryGeneral = false;

if (is_numeric($newPageSize)){
  $_SESSION["tamPagina"] = $newPageSize;
  $datos["pagina"] = $_SESSION["tamPagina"];
  $cambioPagina = true;
}
if (is_numeric($newHistoryProducto)){
  $_SESSION["limiteHistorialProducto"] = $newHistoryProducto;
  $datos["historialProducto"] = $_SESSION["limiteHistorialProducto"];
  $cambioHistoryProducto = true;
}
if (is_numeric($newHistoryGeneral)){
  $_SESSION["limiteHistorialGeneral"] = $newHistoryGeneral;
  $datos["historialGeneral"] = $_SESSION["limiteHistorialGeneral"];
  $cambioHistoryGeneral = true;
}

if ($cambioPagina){
  $datos["resultadoPagina"] = 'OK';
}
else {
  $datos["resultadoPagina"] = 'ERROR';
}

if ($cambioHistoryProducto){
  $datos["resultadoHistoryProducto"] = 'OK';
}
else {
  $datos["resultadoHistoryProducto"] = 'ERROR';
}

if ($cambioHistoryGeneral){
  $datos["resultadoHistoryGeneral"] = 'OK';
}
else {
  $datos["resultadoHistoryGeneral"] = 'ERROR';
}

$json = json_encode($datos);
echo $json;
?>