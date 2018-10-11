<?php
  if(!isset($_SESSION)) 
    {
    //Reanudamos la sesión:
    session_start(); 
  } 
  // If the user is logged in, delete the session vars to log them out
  if (isset($_SESSION['user_id'])) 
   {
    // Delete the session vars by clearing the $_SESSION array
    $_SESSION = array();
    // Destroy the session
    session_destroy();
  }
?>
  
<script> 
 //alert('Ha cerrado correctamente su sesión');
  window.location.href = "index.php";
</script>

