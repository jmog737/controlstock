<?php
session_start(); 
 
$myObj = new stdClass();
$myObj->time = 0;
$myObj->user = '';
$myObj->sesion = '';
$myObj->user_id = 0;
$myObj->oldUser = '';
$myObj->sesion = 'expirada';

//Comprobamos si esta definida la sesión 'tiempo'.
if(isset($_SESSION['tiempo']) ) {
  //Calculamos tiempo de vida inactivo.
  $vida_session = time() - $_SESSION['tiempo'];
  require_once('connectvars.php');
  //Compraración para redirigir página, si la vida de sesión sea mayor a el tiempo insertado en inactivo.
  if($vida_session > DURACION)
    {
    if (isset($_SESSION['username'])){
      $myObj->oldUser = strtoupper($_SESSION['username']);
    }
    else {
      $myObj->oldUser = 'ERROR';
    }
    $myObj->user = "ERROR";
    $myObj->time = 0;
    $myObj->user_id = 0;
    $myObj->sesion = 'expirada';
  } 
  else {
    //Activamos sesion tiempo.
    $_SESSION['tiempo'] = time();
    $myObj->user = $_SESSION['username'];
    $myObj->oldUser = $_SESSION['username'];
    $myObj->user_id = $_SESSION['user_id'];
    $myObj->time = time();
    $myObj->sesion = 'activa';
  }
}

$myJSON = json_encode($myObj);

echo $myJSON;
?>