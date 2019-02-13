<?php
session_start();
//Comprobamos si esta definida la sesión 'tiempo'.
//if(isset($_SESSION['tiempo']) ) {
//  //Calculamos tiempo de vida inactivo.
//  $vida_session = time() - $_SESSION['tiempo'];
//  require_once('connectvars.php');
//  
////echo "tiempo anterior: ".$_SESSION['tiempo']."<br>time en sesiones: ".time()."<br>vida sesiones: ".$vida_session."<br>duracion: ".DURACION."<br>";
//
//  //Compraración para redirigir página, si la vida de sesión sea mayor a el tiempo insertado en inactivo.
//  if($vida_session > DURACION)
//    {
//    echo "ver por que me saca";
//    // Delete the session vars by clearing the $_SESSION array
//    $_SESSION = array();
//    // Destroy the session
//    session_unset();
//    session_destroy();sleep(4);
//    //Redirigimos pagina.
//    header('Location: index.php');
//  }
//  else {
//    //Activamos sesion tiempo.
//    //$_SESSION['tiempo'] = time();//echo "nuevo tiempo: ".$_SESSION['tiempo'];
//  }    
//}
?>
