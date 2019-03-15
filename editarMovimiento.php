<?php 
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file editarMovimiento.php
*  @brief Form para editar parte de un movimiento.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Enero 2018
*
*******************************************************/
?>
<!DOCTYPE html>
<html>
  <?php require_once('head.php');?>
  
  <body>
    <?php require('header.php');
    if (isset($_SESSION['user_id'])) 
      {
    ?>
    <main>
      <div id='main-content' class='container-fluid'>

      </div>
    </main>
    <?php 
    }
    else {
      
    }  
    ?>  
      
    <?php require('footer.php');?>
  </body>
  
</html>