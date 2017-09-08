<?php
require_once ("baseMysql.php");

//Conexión con la base de datos:
$dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$query = $_GET["query"];

$result = consultarBD($query, $dbc);

if ($result === TRUE) {
  $dato["resultado"] = "OK";
}
else {
  $dato["resultado"] = "ERROR";
  }
$json = json_encode($dato);
echo $json;

?>