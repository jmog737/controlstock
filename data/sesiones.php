<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
}
require_once('connectvars.php');

///Evitamos que nos salgan los NOTICES de PHP
//error_reporting(E_ALL ^ E_NOTICE);

///Si el tiempo de la petición es mayor al tiempo permitido de la duración, 
///destruye la sesión y crea una nueva
if (isset($_SESSION['ultima_actividad'])){
  $secondsInactive = time() - $_SESSION['ultima_actividad'];
  if($secondsInactive >= DURACION){
    header('Location: salir.php');
  }
//  else {
//    if (isset($_SESSION['user_id'])){
//      ///Definimos el valor de la sesión "ultima_actividad" como el timestamp del servidor
//      $_SESSION['ultima_actividad'] = time();
//    }
//  }
}
$_SESSION['ultima_actividad'] = time();
?>
