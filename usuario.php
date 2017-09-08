<?php
//Reanudamos la sesión:
session_start();
require_once("data/sesiones.php");
/**
******************************************************
*  @file usuario.php
*  @brief Detalle de los usuarios.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Abril 2017
*
*******************************************************/
?>
<!DOCTYPE html>
<html>
  <?php require_once('head.php');?>
  
  <body>
    <?php require_once('header.php');
    if (isset($_SESSION['user_id'])) 
            {
    ?>
    
    <div id='main-content' class='container-fluid'>
         
    </div>
    <?php 
            }
    else {
    ?>
      <script> 
        alert('Su sesión expiró. Por favor vuelva loguearse.'); 
        window.location.href = "http://localhost/testKMS/index.php";
      </script>  
    <?php
    }        
    ?>  
      
    <?php require('footer.php');?>
  </body>
  
</html>