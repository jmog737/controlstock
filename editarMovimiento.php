<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
} 
require_once("data/sesiones.php");
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
    <?php require_once('header.php');
    if (isset($_SESSION['user_id'])) 
      {
    ?>
    <main>
      <div id='main-content' class='container-fluid'>
        <div id='fila' class='row'>
          <h2 id="titulo" class="encabezado">MOVIMIENTOS</h2>
          <div id='selector' class='col-md-6 col-sm-12'></div>
          <div id='content' class='col-md-6 col-sm-12'></div>
        </div>
      </div>
    </main>
    <?php 
    }
    else {
    ?>
<!--      <script> 
        alert('Su sesión expiró. Por favor vuelva loguearse.'); 
        window.location.href = "../controlstock/index.php";
      </script>  -->
    <?php
    }        
    ?>  
      
    <?php require('footer.php');?>
  </body>
  
</html>