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

$query = $_GET["query"];

$result = consultarBD($query, $dbc);

$datos = array();
$datos['rows'] = $result->num_rows;
while (($fila = $result->fetch_array(MYSQLI_ASSOC)) != NULL) { 
  $datos['resultado'][] = $fila;
}

$json = json_encode($datos);
echo $json;
?>