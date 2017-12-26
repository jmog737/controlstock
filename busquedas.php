<?php
//Reanudamos la sesión:
session_start();
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
    <div id='main-content' class='container-fluid'>
      <h2 id="titulo" class="encabezado">BÚSQUEDAS</h2>
      <h3>Seleccione el tipo de consulta a ejecutar.</h3>
      
      <div id='fila' class='row'>
        <div id='criterios' class='col-md-12 col-sm-12'>
<!--            <table id="parametros" name="parametros" class="tabla2">
              <tr>
                <th colspan="5" class="tituloTabla">STOCK</th>
              </tr>
              <tr>
                <td class="fondoVerde">
                  <input type="radio" name="criterio" value="entidadStock" checked="checked">
                </td>
                <th>Entidad:</th>
                <td colspan="3">
                  <select name='entidad' id='entidadStock' style='width: 100%'>
                    <option value='todos' selected="yes">---TODOS---</option>
                    <?php
                      foreach($entidades as $dato)
                        {
                        echo "<option value='".$dato."'>".$dato."</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="fondoVerde">
                  <input type="radio" name="criterio" value="productoStock">
                </td>
                <th>Producto:</th>
                <td align='center' colspan="3"><input type='text' id='productoStock' name='producto' class='agrandar' size='9' onkeyup='showHint(this.value, "#productoStock")'></td>
              </tr>
              <tr>
                <td class="fondoVerde">
                  <input type="radio" name="criterio" value="totalStock">
                </td>
                <td colspan="4" class="negrita" style="text-align: left">Total de plásticos en bóveda</td>
              </tr>
              <tr>
                <th colspan="5" class="centrado">MOVIMIENTOS</th>
              </tr>
              <tr>
                <td class="fondoVerde">
                  <input type="radio" name="criterio" value="entidadMovimiento">
                </td>
                <th>Entidad:</th>
                <td colspan="3">
                  <select name='entidad' id='entidadMovimiento' style='width: 100%'>
                    <option value='todos' selected="yes">---TODOS---</option>
                    <?php
                      foreach($entidades as $dato)
                        {
                        echo "<option value='".$dato."'>".$dato."</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="fondoVerde">
                  <input type="radio" name="criterio" value="productoMovimiento">
                </td>
                <th>Producto:</th>
                <td align='center' colspan="3"><input type='text' id='productoMovimiento' name='producto' class='agrandar' size='9' onkeyup='showHint(this.value, "#productoMovimiento")'></td>
              </tr>
              <tr>
                <td class="fondoNaranja">
                  <input type="radio" name="criterioFecha" value="intervalo">
                </td>
                <th>Entre:</th>
                <td><input type="date" name="inicio" id="inicio" style="width:100%; text-align: center" min="2016-07-01"></td>
                <td>y:</td>
                <td><input type="date" name="fin" id="fin" style="width:100%; text-align: center" min="2016-10-01"></td>
              </tr>
              <tr>
                <td class="fondoNaranja">
                  <input type="radio" name="criterioFecha" value="mes" checked="checked">
                </td>
                <th>Mes:</th>
                <td>
                  <select id="mes" name="mes" style="width:100%">
                    <option value="todos" selected="yes">--Seleccionar--</option>
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Setiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                  </select>
                </td>
                <th>Año:</th>
                <td>
                  <select id="año" name="año" style="width:100%">
                    <option value="2017" selected="yes">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                  </select>
                </td>
              </tr>  
              <tr>
                <th>
                  Tipo:
                </th>
                <td>
                  <select id="tipo" name="tipo" style="width:100%">
                    <option value="Todos" selected="yes">---TODOS---</option>
                    <option value='Retiro'>Retiro</option>
                    <option value="Ingreso">Ingreso</option>
                    <option value='Renovaci&oacute;n'>Reno</option>
                    <option value='Destrucci&oacute;n'>Destrucci&oacute;n</option>
                  </select>
                </td>
              </tr>
              <tr>
                <th>Usuario:</th>
                <td colspan="3">
                  <select name='usuario' id='usuario' style='width: 100%'>
                    <option value='todos'>---TODOS---</option>
                    <?php
                      foreach($usuarios as $dato)
                        {
                        echo "<option value='".$dato['iduser']."'>".$dato['nombre']." ".$dato['apellido']."</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td colspan="5" class="pieTabla">
                  <input type="button" class="btn btn-success" name="consultar" id="realizarBusqueda" value="Consultar" align='center'>
                </td>
              </tr>
            </table>-->
          <br>
        </div>
        <div id='resultado' class='col-md-6 col-sm-12'></div>
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
    
    <?php require_once('footer.php');?>
  </body>
  
</html>