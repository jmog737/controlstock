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
//$dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$dbc = crearConexion(DB_HOST, $userDB, $pwDB, DB_NAME);

$query = array();
$query = (array) json_decode($_GET["query"],true);

///Comento escritura en el log para evitar sobrecargar el archivo.
//escribirLog($query);

$datos = array();
for ($i = 0; $i < count($query); $i++){
  $result = consultarBD($query[$i], $dbc);

  $datos["$i"]['rows'] = $result->num_rows;
  while (($fila = $result->fetch_array(MYSQLI_ASSOC)) != NULL) { 
    $datos["$i"]['resultado'][] = $fila;
  }
  
}

$json = json_encode($datos);
echo $json;
?>