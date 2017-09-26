<?php
//Reanudamos la sesión:
session_start();
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
      {
      $queryBoveda = "select iduser, apellido, nombre from usuarios where estado='activo' and sector='Bóveda' order by apellido asc, nombre asc";
      
      //Conexión con la base de datos:
      $dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      
      $result = consultarBD($queryBoveda, $dbc);

      $datosBoveda = array();
      while (($fila = $result->fetch_array(MYSQLI_ASSOC)) != NULL) { 
        $datosBoveda[] = $fila;
      }
      
      $queryGrabaciones = "select iduser, apellido, nombre from usuarios where estado='activo' and sector='Grabaciones' order by apellido asc, nombre asc";
      $result1 = consultarBD($queryGrabaciones, $dbc);

      $datosGrabaciones = array();
      while (($fila = $result1->fetch_array(MYSQLI_ASSOC)) != NULL) { 
        $datosGrabaciones[] = $fila;
      }
    ?>
    
    <div id='main-content' class='container-fluid'>
      <h2 id="titulo" class="encabezado">INGRESO DE MOVIMIENTOS</h2>
      <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table class='tabla2' name='movimiento'> 
          <th colspan="2" class="tituloTabla">MOVIMIENTO</th>
          <tr>
            <th align='left'><font class='negra'>Fecha:</font></th>
            <td align='center'><input type="date" name="fecha" id="fecha" style="width:100%; text-align: center" min="2017-09-01" value="<?php echo date("Y-m-d");?>"></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Producto:</font></th>
            <td align='center'><input type='text' id='producto' name='producto' class='agrandar' size='9' onkeyup='showHint(this.value, "#producto")'></td>
          </tr>  
          <tr>
            <th align='left'><font class='negra'>Tipo:</font></th>
            <td align='center'>
              <select id="tipo" name="tipo" style="width:100%">
                <option value='Retiro' selected='yes'>Retiro</option>
                <option value='Devoluci&oacute;n'>Devolución</option>
              </select>
            </td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Cantidad:</font></th>
            <td align='center'><input type='text' id='cantidad' name='cantidad' class='agrandar' maxlength='35' size='9'></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Repetir Cantidad:</font></th>
            <td align='center'><input type='text' id='cantidad2' name='cantidad2' class='agrandar' maxlength='35' size='9'></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Comentarios:</font></th>
            <td align='center'><input type='textarea' id='comentarios' name='comentarios' class='agrandar' maxlength='35' size='9'></td>
          </tr>
          <th colspan="2">CONTROL</th>
          <tr>
            <th align='left'><font class='negra'>Control 1:</font></th>
            <td>
              <select id="usuarioBoveda" name="usuarioBoveda" style="width:100%">
                <option value='ninguno' selected='yes'>---Seleccionar---</option>
              <?php
                foreach($datosBoveda as $dato)
                  {
                  echo "<option value='".$dato['iduser']."' name='".$dato['nombre']." ".$dato['apellido']."'>".$dato['nombre']." ".$dato['apellido']."</option>";
                }
                foreach($datosGrabaciones as $dato)
                  {
                  echo "<option value='".$dato['iduser']."' name='".$dato['nombre']." ".$dato['apellido']."'>".$dato['nombre']." ".$dato['apellido']."</option>";
                }
              ?>
              </select> 
            </td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Control 2:</font></th>
            <td>
              <select id='usuarioGrabaciones' name='usuarioGrabaciones' style='width: 100%'>
                <option value='ninguno' selected='yes'>---Seleccionar---</option>
              <?php
                foreach($datosBoveda as $dato)
                  {
                  echo "<option value='".$dato['iduser']."' name='".$dato['nombre']." ".$dato['apellido']."'>".$dato['nombre']." ".$dato['apellido']."</option>";
                }
                foreach($datosGrabaciones as $dato)
                  {
                  echo "<option value='".$dato['iduser']."' name='".$dato['nombre']." ".$dato['apellido']."'>".$dato['nombre']." ".$dato['apellido']."</option>";
                }
              ?>
              </select>
            </td>
          </tr>
          <tr>
            
          </tr>
          <tr>
            <td colspan="2" class="pieTabla"><input type="button" value="AGREGAR" id="agregarMovimiento" name="agregarMovimiento" class='btn-success' align='center'/></td>
          </tr>
        </table>
      </form> 
    </div>
    <?php 
            }
    else {
    ?>
      <script> 
        alert('Su sesión expiró. Por favor vuelva loguearse.'); 
        window.location.href = "http://localhost/controlstock/index.php";
      </script>  
    <?php
    }        
    ?>  
      
    <?php require('footer.php');?>
  </body>
  
</html>