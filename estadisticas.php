<?php
//Reanudamos la sesión:
session_start();
require_once("data/sesiones.php");

if ($_POST["hacerGrafica"] === "yes") {
  $_SESSION["consulta"] = $_POST["consulta"];
  $_SESSION["fechaInicio"] = $_POST["fechaInicio"];
  $_SESSION["fechaFin"] = $_POST["fechaFin"];
  $_SESSION["mensaje"] = $_POST["mensaje"];
}

/**
******************************************************
*  @file estadisticas.php
*  @brief Form para agregar un movimiento.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Noviembre 2017
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
        window.location.href = "../controlstock/index.php";
      </script>  
    <?php
    }        
    ?>  
      
    <?php require('footer.php');?>
  </body>
  
</html>