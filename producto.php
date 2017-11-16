<?php
//Reanudamos la sesión:
session_start();
require_once("data/sesiones.php");
/**
******************************************************
*  @file producto.php
*  @brief Form para agregar/editar un producto.
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
      {
    ?>
    
    <div id='main-content' class='container-fluid'>
      <div id='fila' class='row'>
        <h2 id="titulo" class="encabezado">PRODUCTOS</h2>
        <div id='selector' class='col-md-6 col-sm-12'></div>
        <div id='content' class='col-md-6 col-sm-12'></div>
      </div>
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