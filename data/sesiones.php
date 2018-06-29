<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
}
require_once('connectvars.php');

///Evitamos que nos salgan los NOTICES de PHP
//error_reporting(E_ALL ^ E_NOTICE);

///Obtenemos el timestamp del servidor de cuanto se hizo la petición
$hora = $_SERVER["REQUEST_TIME"];

///Si el tiempo de la petición es mayor al tiempo permitido de la duración, 
///destruye la sesión y crea una nueva
if (isset($_SESSION['ultima_actividad']) && ($hora - $_SESSION['ultima_actividad']) > DURACION) {
  $_SESSION = array();
  session_unset();
  session_destroy();
}
else {
  ///Definimos el valor de la sesión "ultima_actividad" como el timestamp del servidor
  $_SESSION['ultima_actividad'] = $hora;
}

?>
