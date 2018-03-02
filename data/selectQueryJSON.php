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

$tamPage = 15;

//Conexión con la base de datos:
//$dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$dbc = crearConexion(DB_HOST, $userDB, $pwDB, DB_NAME);

$tipo = $_GET["tipo"];

$query = array();
$query = (array) json_decode($_GET["query"],true);

///Comento escritura en el log para evitar sobrecargar el archivo.
//escribirLog($query);
$limite = $tamPage;

$datos = array();
for ($i = 0; $i < count($query); $i++){
  ///Para las consultas de stock, armo consulta para conocer el total de plásticos de la entidad (a mostrar en la última página):
  $datos["$i"]['suma'] = null;
  $datos["$i"]['retiros'] = null;
  $datos["$i"]['renovaciones'] = null;
  $datos["$i"]['destrucciones'] = null;
  $datos["$i"]['ingresos'] = null;
  if ($tipo === 'entidadStock'){
    $temp = explode("where", $query[$i]);
    $consultaSuma = "select sum(stock) as total from productos where ".$temp[1];
    $result0 = consultarBD($consultaSuma, $dbc);
    while (($fila0 = $result0->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      $datos["$i"]['suma'] = $fila0["total"];
    }
  }
  if ($tipo === 'totalStock'){
    $consultaSuma = "select sum(stock) as total from productos where estado='activo'";
    $result0 = consultarBD($consultaSuma, $dbc);
    while (($fila0 = $result0->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      $datos["$i"]['suma'] = $fila0["total"];
    }
  }
  if ($tipo === 'entidadMovimiento'){
    $test = stripos($query[$i], "productos.entidad='");
    $entidad = '';
    if ($test !== false){
      $temp = explode("productos.entidad='", $query[$i]);
      $temp1 = explode("'", $temp[1]);
      $entidad = $temp1[0];
    }
    $consultaRetiros = "select productos.idprod as idprod, sum(cantidad) as retiros from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='retiro'";
    $consultaRenovaciones = "select productos.idprod as idprod, sum(cantidad) as renovaciones from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='renovación'";
    $consultaDestrucciones = "select productos.idprod as idprod, sum(cantidad) as destrucciones from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='destrucción'";
    $consultaIngresos = "select productos.idprod as idprod, sum(cantidad) as ingresos from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='ingreso'";
    
    if ($entidad !== ''){
      $consultaRetiros = $consultaRetiros." and productos.entidad='".$entidad."'";
      $consultaRenovaciones = $consultaRenovaciones." and productos.entidad='".$entidad."'";
      $consultaDestrucciones = $consultaDestrucciones." and productos.entidad='".$entidad."'";
      $consultaIngresos = $consultaIngresos." and productos.entidad='".$entidad."'";
    }
    
    $consultaRetiros = $consultaRetiros." group by productos.nombre_plastico";
    $consultaRenovaciones = $consultaRenovaciones." group by productos.nombre_plastico";
    $consultaDestrucciones = $consultaDestrucciones." group by productos.nombre_plastico";
    $consultaIngresos = $consultaIngresos." group by productos.nombre_plastico";
    
    
    $resultRetiros = consultarBD($consultaRetiros, $dbc);
    while (($filaRetiros = $resultRetiros->fetch_array(MYSQLI_ASSOC)) != NULL) {
      $idprod = $filaRetiros["idprod"];
      $datos["$i"]["retiros"][$idprod] = $filaRetiros["retiros"];
    }
    $resultRenovaciones = consultarBD($consultaRenovaciones, $dbc);
    while (($filaRenovaciones = $resultRenovaciones->fetch_array(MYSQLI_ASSOC)) != NULL) {
      $idprod = $filaRenovaciones["idprod"];
      $datos["$i"]["renovaciones"][$idprod] = $filaRenovaciones["renovaciones"];
    }
    $resultDestrucciones = consultarBD($consultaDestrucciones, $dbc);
    while (($filaDestrucciones = $resultDestrucciones->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      $idprod = $filaDestrucciones["idprod"];
      $datos["$i"]["destrucciones"][$idprod] = $filaDestrucciones["destrucciones"];
    }
    $resultIngresos = consultarBD($consultaIngresos, $dbc);
    while (($filaIngresos = $resultIngresos->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      $idprod = $filaIngresos["idprod"];
      $datos["$i"]["ingresos"][$idprod] = $filaIngresos["ingresos"];
    }
    
  }
  
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