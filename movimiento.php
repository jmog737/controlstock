<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
} 
require_once("data/sesiones.php");
/**
******************************************************
*  @file moviemiento.php
*  @brief Form para agregar un movimiento.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Setiembre 2017
*
*******************************************************/
?>
<!DOCTYPE html>
<html>
  <?php require_once('head.php');?>
  
  <body>
    <?php require_once('header.php');
    if (isset($_SESSION['user_id'])) 
      {//echo "seteado user id<br>";
    ?>
    <main>
      <div id='main-content' class='container-fluid'>

      </div>
    </main>
    <?php 
    }
    else {//echo "no seteado user id<br>";
    ?>
<!--      <script> 
        alert('Su sesión expiró (@movimiento). Por favor vuelva loguearse.'); 
        window.location.href = "../controlstock/index.php";
      </script>  -->
    <?php
    }        
    ?>  
      
    <?php require('footer.php');?>
  </body>
  
</html>