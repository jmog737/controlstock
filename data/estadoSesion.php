<?php
session_start(); 
require_once "connectvars.php";

$myObj = new stdClass();
$myObj->time = 0;
$myObj->user = '';
$myObj->sesion = '';
$myObj->user_id = 0;
$myObj->oldUser = '';
$myObj->oldTime = 0;
$myObj->sesion = 'expirada';
$myObj->duracion = 0;

//Comprobamos si esta definida la sesión 'tiempo'.
if(isset($_SESSION['tiempo']) ) {
  $myObj->duracion = DURACION;
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
    
    $myObj->oldTime = substr($_SESSION['tiempo'], -3);
    $myObj->user = "ERROR";
    $myObj->time = time();
    //$myObj->time = time();////***************** a cambiar por 0. Es solo para pruebas ****************
    $myObj->user_id = 0;
    $myObj->sesion = 'expirada';
  } 
  else {
    $myObj->oldUser = $_SESSION['username'];
    $myObj->oldTime = substr($_SESSION['tiempo'], -3);
    $myObj->user = $_SESSION['username'];
    $myObj->user_id = $_SESSION['user_id'];
    //Activamos sesion tiempo.
    $_SESSION['tiempo'] = time();  
    $myObj->time = $_SESSION['tiempo'];
    $myObj->sesion = 'activa';
  }
}

$myJSON = json_encode($myObj);

echo $myJSON;
?>