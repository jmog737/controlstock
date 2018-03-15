<?php
session_start();
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

$query = $_GET["query"];
$log = $_GET["log"];

$result = consultarBD($query, $dbc);

if ($result === TRUE) {
  $dato["resultado"] = "OK";
  if ($log === "SI") {
    escribirLog($query);
  }
}
else {
  $dato["resultado"] = "ERROR";
  }
$json = json_encode($dato);
echo $json;

?>