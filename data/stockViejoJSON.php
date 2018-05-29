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

$tamPage = $_SESSION["tamPagina"];

//Conexión con la base de datos:
$dbc = crearConexion(DB_HOST, $userDB, $pwDB, DB_NAME);

$tipo = $_GET["tipo"];

$query = array();
$query = (array)json_decode($_GET["query"],true);

///Comento escritura en el log para evitar sobrecargar el archivo.
//escribirLog($query);
$limite = $tamPage;

$datos = array();
for ($i = 0; $i < count($query); $i++){
  ///Para las consultas de stock, armo consulta para conocer el total de plásticos de la entidad (a mostrar en la última página):
  $datos["$i"]['suma'] = 0;
  $datos["$i"]['retiros'] = null;
  $datos["$i"]['renovaciones'] = null;
  $datos["$i"]['destrucciones'] = null;
  $datos["$i"]['ingresos'] = null;
  $datos["$i"]['stockViejo'] = null;
  $idprods = Array();

  if ($tipo === 'entidadStockViejo'){
    $test = stripos($query[$i], "productos.entidad='");
    $entidad = '';
    if ($test !== false){
      $temp = explode("productos.entidad='", $query[$i]);
      $temp1 = explode("'", $temp[1]);
      $entidad = $temp1[0];
    }
    $fecha = '';
    $test2 = stripos($query[$i], "and (fecha >");
    if ($test2 !== false){
      $temp4 = explode("and (fecha >", $query[$i]);
      $temp5 = explode(" order", $temp4[1]);
      $fecha = " and (fecha >".$temp5[0];
    }
    else {
      $test3 = stripos($query[$i], "and (fecha =");
      if ($test3 !== false){
        $temp6 = explode("and (fecha =", $query[$i]);
        $temp7 = explode(" order", $temp6[1]);
        $fecha = " and (fecha =".$temp7[0];
      }
    }
    $consultaRetiros = "select productos.idprod as idprod, sum(cantidad) as retiros from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='retiro'".$fecha;
    $consultaRenovaciones = "select productos.idprod as idprod, sum(cantidad) as renovaciones from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='renovación'".$fecha;
    $consultaDestrucciones = "select productos.idprod as idprod, sum(cantidad) as destrucciones from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='destrucción'".$fecha;
    $consultaIngresos = "select productos.idprod as idprod, sum(cantidad) as ingresos from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='ingreso'".$fecha;
    
    if ($entidad !== ''){
      $consultaRetiros = $consultaRetiros." and productos.entidad='".$entidad."'";
      $consultaRenovaciones = $consultaRenovaciones." and productos.entidad='".$entidad."'";
      $consultaDestrucciones = $consultaDestrucciones." and productos.entidad='".$entidad."'";
      $consultaIngresos = $consultaIngresos." and productos.entidad='".$entidad."'";
    }
    
    $consultaRetiros = $consultaRetiros." group by productos.idprod";
    $consultaRenovaciones = $consultaRenovaciones." group by productos.idprod";
    $consultaDestrucciones = $consultaDestrucciones." group by productos.idprod";
    $consultaIngresos = $consultaIngresos." group by productos.idprod";
    
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
    
    ///Ejecuto consulta "total" para concer el total de datos a devolver
    ///Sin embargo, sólo consulto el total de registros para que sea más rápido:
    $totalConsulta[$i] = "select count(*) as total from productos where entidad='".$entidad."'";
    
    $result1 = consultarBD($totalConsulta[$i], $dbc);
    while (($fila1 = $result1->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      $datos["$i"]['totalRows'] = $fila1["total"];
    }

    ///Recupero primera página para mostrar:
    $query[$i] = "select idprod, entidad, nombre_plastico, bin, codigo_emsa, codigo_origen, contacto, snapshot, ultimoMovimiento, stock, alarma1, alarma2, comentarios from productos where entidad='".$entidad."' and estado='activo' order by entidad asc, codigo_emsa asc, nombre_plastico asc, idprod asc";// limit ".$limite;
    $result = consultarBD($query[$i], $dbc);

    while (($fila = $result->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      $idprod1 = $fila['idprod'];
      $stockActual[$idprod1] = $fila['stock'];
      $datos["$i"]['resultado'][] = $fila;
    }
    
    foreach($stockActual as $index => $valor){
      if (!(array_key_exists($index , $datos["$i"]["retiros"]))){
        $datos["$i"]["retiros"][$index] = 0;
      }
      if (!(array_key_exists($index , $datos["$i"]["renovaciones"]))){
        $datos["$i"]["renovaciones"][$index] = 0;
      }
      if (!(array_key_exists($index , $datos["$i"]["destrucciones"]))){
        $datos["$i"]["destrucciones"][$index] = 0;
      }
      if (!(array_key_exists($index , $datos["$i"]["ingresos"]))){
        $datos["$i"]["ingresos"][$index] = 0;
      }
      $totalConsumos[$index] = $datos["$i"]["retiros"][$index] + $datos["$i"]["renovaciones"][$index] + $datos["$i"]["destrucciones"][$index];
      $datos["$i"]["stockViejo"][$index] = $valor + $totalConsumos[$index] - $datos["$i"]["ingresos"][$index];
      $datos["$i"]['suma'] = $datos["$i"]['suma'] + $datos["$i"]["stockViejo"][$index];
      $datos["$i"]['query'] = $query[$i];
    }
  }
  
  if ($tipo === 'productoStockViejo'){
    $test = stripos($query[$i], "where idprod=");
    $idprod = '';
    if ($test !== false){
      $temp = explode("where idprod=", $query[$i]);
      $temp1 = explode(" ", $temp[1]);
      $idprod = $temp1[0];
    }    
    $fecha = '';
    $test2 = stripos($query[$i], "and (fecha >");
    if ($test2 !== false){
      $temp4 = explode("and (fecha >", $query[$i]);
      $temp5 = explode(" order", $temp4[1]);
      $fecha = " and (fecha >".$temp5[0];
    }
    else {
      $test3 = stripos($query[$i], "and (fecha =");
      if ($test3 !== false){
        $temp6 = explode("and (fecha =", $query[$i]);
        $temp7 = explode(" order", $temp6[1]);
        $fecha = " and (fecha =".$temp7[0];
      }
    }
    
    $consultaRetiros = "select productos.idprod as idprod, sum(cantidad) as retiros from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='retiro' and productos.idprod='".$idprod."'".$fecha."group by productos.idprod";
    $resultRetiros = consultarBD($consultaRetiros, $dbc);
    while (($filaRetiros = $resultRetiros->fetch_array(MYSQLI_ASSOC)) != NULL) {
      //$idprod = $filaRetiros["idprod"];
      $datos["$i"]["retiros"][$idprod] = $filaRetiros["retiros"];
    }

    $consultaRenovaciones = "select productos.idprod as idprod, sum(cantidad) as renovaciones from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='renovación' and productos.idprod='".$idprod."'".$fecha."group by productos.idprod";
    $resultRenovaciones = consultarBD($consultaRenovaciones, $dbc);
    while (($filaRenovaciones = $resultRenovaciones->fetch_array(MYSQLI_ASSOC)) != NULL) {
      //$idprod = $filaRenovaciones["idprod"];
      $datos["$i"]["renovaciones"][$idprod] = $filaRenovaciones["renovaciones"];
    }

    $consultaDestrucciones = "select productos.idprod as idprod, sum(cantidad) as destrucciones from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='destrucción' and productos.idprod='".$idprod."'".$fecha."group by productos.idprod";
    $resultDestrucciones = consultarBD($consultaDestrucciones, $dbc);
    while (($filaDestrucciones = $resultDestrucciones->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      //$idprod = $filaDestrucciones["idprod"];
      $datos["$i"]["destrucciones"][$idprod] = $filaDestrucciones["destrucciones"];
    }

    $consultaIngresos = "select productos.idprod as idprod, sum(cantidad) as ingresos from productos inner join movimientos on movimientos.producto=productos.idprod where tipo='ingreso' and productos.idprod='".$idprod."'".$fecha."group by productos.idprod";
    $resultIngresos = consultarBD($consultaIngresos, $dbc);
    while (($filaIngresos = $resultIngresos->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      //$idprod = $filaIngresos["idprod"];
      $datos["$i"]["ingresos"][$idprod] = $filaIngresos["ingresos"];
    } 
    
    ///Recupero primera página para mostrar:
    $query[$i] = "select idprod, entidad, nombre_plastico, bin, codigo_emsa, codigo_origen, contacto, snapshot, ultimoMovimiento, stock, alarma1, alarma2, comentarios as prodcom from productos where idprod=".$idprod;
    $result = consultarBD($query[$i], $dbc);

    while (($fila = $result->fetch_array(MYSQLI_ASSOC)) != NULL) { 
      $stockActual[$idprod] = $fila['stock'];
      $datos["$i"]['resultado'][] = $fila;
    }
    
    foreach($stockActual as $index => $valor){
      if (!(array_key_exists($index , $datos["$i"]["retiros"]))){
        $datos["$i"]["retiros"][$index] = 0;
      }
      if (!(array_key_exists($index , $datos["$i"]["renovaciones"]))){
        $datos["$i"]["renovaciones"][$index] = 0;
      }
      if (!(array_key_exists($index , $datos["$i"]["destrucciones"]))){
        $datos["$i"]["destrucciones"][$index] = 0;
      }
      if (!(array_key_exists($index , $datos["$i"]["ingresos"]))){
        $datos["$i"]["ingresos"][$index] = 0;
      }
      $totalConsumos[$index] = $datos["$i"]["retiros"][$index] + $datos["$i"]["renovaciones"][$index] + $datos["$i"]["destrucciones"][$index];
      $datos["$i"]["stockViejo"][$index] = $valor + $totalConsumos[$index] - $datos["$i"]["ingresos"][$index];
      $datos["$i"]['suma'] = $datos["$i"]['suma'] + $datos["$i"]["stockViejo"][$index];
      $datos["$i"]['totalRows'] = 1;
      $datos["$i"]['query'] = $query[$i];
    }
  }  
  
}

///Devuelvo total de registros y datos SOLO de la primera página:
$json = json_encode($datos);
echo $json;
?>