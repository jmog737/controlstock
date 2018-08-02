<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
}
require_once("connectvars.php");

///Obtenemos el timestamp del servidor de cuanto se hizo la petición
$hora = $_SERVER["REQUEST_TIME"];

$myObj = new \stdClass();
///Si el tiempo de la petición es mayor al tiempo permitido de la duración, 
///destruye la sesión y crea una nueva
if (isset($_SESSION['ultima_actividad']) && ($hora - $_SESSION['ultima_actividad']) > DURACION) {
  $myObj->oldUser = strtoupper($_SESSION['username']);
  session_unset();
  session_destroy();
  $myObj->user = "ERROR";
  $myObj->timestamp = 0;
  $myObj->user_id = 0;
  $myObj->sesion = 'expirada';
}
else {
  ///Definimos el valor de la sesión "ultima_actividad" como el timestamp del servidor
  $_SESSION['ultima_actividad'] = $hora;
  //if (isset($_SESSION['user_id'])){
    $myObj->user = $_SESSION['username'];echo "username: ".$_SESSION['username']."<br>";
    $myObj->oldUser = $_SESSION['username'];
    $myObj->user_id = $_SESSION['user_id'];
    $myObj->timestamp = $hora;
    $myObj->sesion = 'activa';
  //}
}

$myJSON = json_encode($myObj);

echo $myJSON;
?>