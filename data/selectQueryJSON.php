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

$tamPage = 40;

//Conexión con la base de datos:
//$dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$dbc = crearConexion(DB_HOST, $userDB, $pwDB, DB_NAME);

$query = array();
$query = (array) json_decode($_GET["query"],true);

///Comento escritura en el log para evitar sobrecargar el archivo.
//escribirLog($query);
$limite = $tamPage;

$datos = array();
for ($i = 0; $i < count($query); $i++){
  ///Ejecuto consulta "total" para concer el total de datos a devolver:
  $result1 = consultarBD($query[$i], $dbc);
  $datos["$i"]['totalRows'] = $result1->num_rows;
  
  ///Recupero primera página para mostrar:
  $query[$i] = $query[$i]." limit ".$limite;
  $result = consultarBD($query[$i], $dbc);

  //$datos["$i"]['rows'] = $result->num_rows;
  
  while (($fila = $result->fetch_array(MYSQLI_ASSOC)) != NULL) { 
    $datos["$i"]['resultado'][] = $fila;
  }
  
}

///Devuelvo total de registros y datos SOLO de la primera página:
$json = json_encode($datos);
echo $json;
?>