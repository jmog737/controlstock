<?php
session_start();
require_once("connectvars.php");

///Obtenemos el timestamp del servidor de cuanto se hizo la petición
$hora = $_SERVER["REQUEST_TIME"];

///Si el tiempo de la petición es mayor al tiempo permitido de la duración, 
///destruye la sesión y crea una nueva
if (isset($_SESSION['ultima_actividad']) && ($hora - $_SESSION['ultima_actividad']) > DURACION) {
  session_unset();
  session_destroy();
  $myObj->user = "ERROR";
  $myObj->timestamp = 0;
}
else {
  ///Definimos el valor de la sesión "ultima_actividad" como el timestamp del servidor
  $_SESSION['ultima_actividad'] = $hora;
  $myObj->user = $_SESSION['username'];
  $myObj->timestamp = $hora;
}

$myJSON = json_encode($myObj);

echo $myJSON;
?>