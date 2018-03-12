<?php
require_once ("baseMysql.php");

if (isset($_SESSION["username"])){
  $userDB = $_SESSION["username"];
  $pwDB = $_SESSION['username'];
}
else {
  $userDB = DB_USER;
  $pwDB = DB_PASSWORD;
}

//Conexión con la base de datos:
$dbc = crearConexion(DB_HOST, $userDB, $pwDB, DB_NAME);

$query = "update appusers";

$newPageSize = $_GET["tamPagina"];
$newHistoryProducto = $_GET["tamHistorialProducto"];
$newHistoryGeneral = $_GET["tamHistorialGeneral"];

$cambioPagina = false;
$cambioHistoryProducto = false;
$cambioHistoryGeneral = false;

if (is_numeric($newPageSize)){
  if ($newPage > 0){
    $_SESSION["tamPagina"] = $newPageSize;
    $datos["pagina"] = $_SESSION["tamPagina"];
    $queryPage = " tamPagina=".$newPageSize;
    $cambioPagina = true;
  }
}
if (is_numeric($newHistoryProducto)){
  if ($newHistoryProducto > 0){
    $_SESSION["limiteHistorialProducto"] = $newHistoryProducto;
    $datos["historialProducto"] = $_SESSION["limiteHistorialProducto"];
    $queryProducto = " historialProducto=".$newHistoryProducto;
    $cambioHistoryProducto = true;
  }
}
if (is_numeric($newHistoryGeneral)){
  if ($newHistoryGeneral > 0){
    $_SESSION["limiteHistorialGeneral"] = $newHistoryGeneral;
    $datos["historialGeneral"] = $_SESSION["limiteHistorialGeneral"];
    $queryGeneral = " historialGeneral=".$newHistoryGeneral;
    $cambioHistoryGeneral = true;
  }
}

if ($cambioPagina){
  $query = $query." set".$queryPage;
  $datos["resultadoPagina"] = 'OK';
}
else {
  $datos["resultadoPagina"] = 'ERROR'; 
}

if ($cambioHistoryProducto){
  if ($cambioPagina){
    $query = $query.",".$queryProducto;
  }
  else {
    $query = $query." set".$queryProducto;
  }
  $datos["resultadoHistoryProducto"] = 'OK';
}
else {
  $datos["resultadoHistoryProducto"] = 'ERROR';
}

if ($cambioHistoryGeneral){
  if (($cambioPagina)||($cambioHistoryProducto)){
    $query = $query.",".$queryGeneral;
  }
  else {
    $query = $query." set".$queryGeneral;
  }
  $datos["resultadoHistoryGeneral"] = 'OK';
}
else {
  $datos["resultadoHistoryGeneral"] = 'ERROR';
}

$query = $query." where id_usuario=".$_SESSION['user_id'];

if ($cambioPagina || $cambioHistoryGeneral || $cambioHistoryProducto){
  $result1 = consultarBD($query, $dbc);
  if ($result1 === TRUE) {
  $datos["resultadoDB"] = "OK";
  }
  else {
    $datos["resultadoDB"] = "ERROR";
  }
}
else {
  $datos["resultadoDB"] = "ERROR";
}

//$datos["resultadoDB"] = $query;
$json = json_encode($datos);
echo $json;
?>