<?php
//  if(!isset($_SESSION)) 
//    {
//    //Reanudamos la sesión:
//    session_start(); 
//  } 
  // If the user is logged in, delete the session vars to log them out
 // if (isset($_SESSION['user_id'])) 
   //{
    // Delete the session vars by clearing the $_SESSION array
    //$_SESSION = array();
    // Destroy the session
    session_destroy();
    session_start();
    session_unset();
    session_destroy();
    //session_write_close();
    //setcookie(session_name(),'',0,'/');
    //session_regenerate_id(true); 
  //}
  header('Location: index.php');
?>
<!--  
<script> 
 //alert('Ha cerrado correctamente su sesión');
  window.location.href = "index.php";
</script>-->

