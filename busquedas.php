<?php
require_once("data/sesiones.php");
/**
******************************************************
*  @file busquedas.php
*  @brief Formulario para ejecutar consultas.
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
      //Conexión con la base de datos:
      $dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      $consultarProductos = "select idprod, nombre_plastico as nombre from productos order by nombre_plastico asc";
      $result = consultarBD($consultarProductos, $dbc);

      $productos = array();
      while (($fila = $result->fetch_array(MYSQLI_ASSOC)) != NULL) { 
        $productos[] = $fila;
      }
      
      $consultarEntidades = "select distinct entidad from productos order by entidad asc, nombre_plastico asc";
      $result1 = consultarBD($consultarEntidades, $dbc);

      $entidades = array();
      while (($fila = $result1->fetch_array(MYSQLI_ASSOC)) != NULL) { 
        $entidades[] = $fila['entidad'];
      }
      
      $consultarUsuarios = "select iduser, apellido, nombre from usuarios order by sector asc, apellido asc, nombre asc";
      $result2 = consultarBD($consultarUsuarios, $dbc);

      $usuarios = array();
      while (($fila = $result2->fetch_array(MYSQLI_ASSOC)) != NULL) { 
        $usuarios[] = $fila;
      }
      
    ?>
    <main>
      <div id='main-content' class='container-fluid'>
          <h2 id="titulo" class="encabezado">BÚSQUEDAS</h2>
          <h3>Seleccione el tipo de consulta a ejecutar.</h3>

          <div id='fila' class='row col-md-12 col-sm-12'>
          </div>
      </div>
    </main>
    <?php 
    }
    else {
    ?> 
<!--    <script> 
      alert('Su sesión expiró. Por favor vuelva loguearse.'); 
      window.location.href = "../controlstock/index.php";
    </script>  -->
    <?php
    }        
    ?>      
    
    <?php require_once('footer.php');?>
  </body>
  
</html>