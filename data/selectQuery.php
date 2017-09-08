<?php
require_once ("baseMysql.php");

//Conexión con la base de datos:
$dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

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