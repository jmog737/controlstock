<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
}
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
//$dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$dbc = crearConexion(DB_HOST, $userDB, $pwDB, DB_NAME);

$queries = (array)json_decode($_GET["query"],true);
$tam = count($queries);

$log = $_GET["log"];

$queryInsert = $queries[0];

if ($tam > 1){
  $queryUpdate = $queries[1];
}


$result = consultarBD($queryInsert, $dbc);
$dato = array();
if ($result === TRUE) {
  $dato["resultado"] = "OK";
  if ($log === "SI") {
    escribirLog($queryInsert);
  }
  if ($tam > 1){
    $result1 = consultarBD($queryUpdate, $dbc);
    if ($result1 === TRUE) {
      //$dato["resultado"] = "OK";
      if ($log === "SI") {
        escribirLog($queryUpdate);
      }
    }  
    else {
      $dato["resultado"] = "ERROR UPDATE";
    } 
  }
}
else {
  $dato["resultado"] = "ERROR INSERT";
}
$json = json_encode($dato);
echo $json;
?>