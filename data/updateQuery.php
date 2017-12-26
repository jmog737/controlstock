<?php
require_once ("baseMysql.php");

//Conexión con la base de datos:
$dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$query = $_GET["query"];

$result = consultarBD($query, $dbc);

if ($result === TRUE) {
  $dato["resultado"] = "OK";
  
  //******************* TEST escritura LOG **************************************
  //recupero la fecha actual para generar nombre del archivo:
    $hoy = getdate();
    $fecha = $hoy[mday].$hoy[month].$hoy[year];
    $hora = $hoy[hours]."_".$hoy[minutes]."_".$hoy[seconds].".txt";
    $ip = "192.168.1.145";

    //$dir = $unidad.":/PROCESOS/PDFS";
    $ruta = "//".$ip."/Reportes/";
    
    //armo nombre del arhivo según fecha y hora actuales:
    $archivo = $ruta."log_".$fecha.".txt";//."@".$hora;
    $query = $query."\r\n";
    $gestor = fopen($archivo, "ab");
    if (!$gestor)
        {
        $mensaje = "No se puede ABRIR el archivo ($archivo). Por favor verifique.";
        exit;
        }
    else {
    if (fwrite($gestor, $query) === FALSE)
        {
        $mensaje = "No se puede ESCRIBIR en el archivo ($archivo). Por favor verifique.";
        exit;
        }
    else
        {
        $mensaje = "¡¡¡PROCESO CORRECTO!!!";
        }
    fclose($gestor);    
    }
  //******************* FIN TEST ************************************************
  
}
else {
  $dato["resultado"] = "ERROR";
  }
$json = json_encode($dato);
echo $json;

?>