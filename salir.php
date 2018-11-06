<?php
session_start();
// Delete the session vars by clearing the $_SESSION array
$_SESSION = array();
// Destroy the session
session_unset();
session_destroy();
//Redirigimos pagina.
header('Location: index.php');
?>
<!--  
<script> 
 //alert('Ha cerrado correctamente su sesión');
  window.location.href = "index.php";
</script>-->

