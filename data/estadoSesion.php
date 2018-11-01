<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
}
require_once("connectvars.php");

///Obtenemos el timestamp del servidor de cuanto se hizo la petición
//$hora = $_SERVER["REQUEST_TIME"];
$hora = time();
$myObj = new stdClass();
$myObj->timestamp = 0;
$myObj->user = '';
$myObj->sesion = '';
$myObj->user_id = 0;
$myObj->oldUser = '';

///Si el tiempo de la petición es mayor al tiempo permitido de la duración, 
///destruye la sesión y crea una nueva
if (isset($_SESSION['ultima_actividad'])){
  $secondsInactive = time() - $_SESSION['ultima_actividad'];
  if($secondsInactive >= DURACION){
    if (isset($_SESSION['username'])){
      $myObj->oldUser = strtoupper($_SESSION['username']);
    }
    else {
      $myObj->oldUser = 'ERROR';
    }
    $myObj->user = "ERROR";
    $myObj->timestamp = 0;
    $myObj->user_id = 0;
    $myObj->sesion = 'expirada';
  }
  else {
    ///Definimos el valor de la sesión "ultima_actividad" como el timestamp del servidor
    $_SESSION['ultima_actividad'] = time();
    //if (isset($_SESSION['user_id'])){
    $myObj->user = $_SESSION['username'];
    $myObj->oldUser = $_SESSION['username'];
    $myObj->user_id = $_SESSION['user_id'];
    $myObj->timestamp = time();
    $myObj->sesion = 'activa';
    //}
  }
}

$myJSON = json_encode($myObj);

echo $myJSON;
?>