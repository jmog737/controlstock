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
*  @date Junio 2017
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
      <h2 id="titulo" class="encabezado">CONSULTAS</h2>
      <h3>Seleccione el tipo de consulta a ejecutar.</h3>
      <div id='fila' class='row'>
        <div id='criterios' class='col-md-12 col-sm-12'>
          <table id="parametros" name="parametros" class="tabla2">
            <tr>
              <th colspan="5" class="tituloTabla">ACTIVIDADES</th>
            </tr>
            <tr>
              <td>
                <input type="radio" name="criterio" value="motivo" checked="checked">
              </td>
              <td>Motivo:</td>
              <td colspan="3"><input type="text" name="motivo" id="motivo"></td>
            </tr>
            <tr>
              <td>
                <input type="radio" name="criterio" value="fecha">
              </td>
              <td>Entre:</td>
              <td><input type="date" name="inicio" id="inicio" style="width:100%; text-align: center" min="2016-07-01"></td>
              <td>y:</td>
              <td><input type="date" name="fin" id="fin" style="width:100%; text-align: center" min="2016-10-01"></td>
            </tr>
            <tr>
              <th colspan="5">REFERENCIAS</th>
            </tr>
            <tr>
              <td>
                <input type="radio" name="criterio" value="codigo">
              </td>
              <td>Código:</td>
              <td colspan="3">
                <input type="text" name="codigo" id="codigo">
              </td>  
            </tr>
            <tr>
              <th colspan="5">SLOTS</th>
            </tr>
            <tr>
              <td rowspan="2"><input type="radio" name="criterio" value="slot"></td>
              <td>HSM:</td>
              <td colspan="3">
                <select id="nombreHsm" name="nombreHsm" style="width:100%">
                  <option value="ninguno" selected="yes">---SELECCIONAR---</option>
                  <option value="1">Producción</option>
                  <option value="2">Back Up</option>
                  <option value="3">Test</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Nombre:</td>
              <td colspan="3"><input type="text" name="nombreSlot" id="nombreSlot"></td>
            </tr>
            <tr>
              <th colspan="5">USUARIOS</th>
            </tr>
            <tr>
              <td rowspan="2"><input type="radio" name="criterio" value="usuario"></td>
              <td>Nombre:</td>
              <td colspan="3"><input type="text" name="nombreUsuario" id="nombreUsuario"></td>
            </tr>
            <tr>
              <td>Empresa:</td>
              <td colspan="3"><input type="text" name="empresa" id="empresa"></td>
            </tr>
            <tr>
              <th colspan="5">LLAVES</th>
            </tr>
            <tr>
              <td rowspan="3"><input type="radio" name="criterio" value="llave"></td>
              <td>Nombre:</td>
              <td colspan="3"><input type="text" name="nombreLlave" id="nombreLlave"></td>
            </tr>
            <tr>
              <td>Owner:</td>
              <td colspan="3"><input type="text" name="ownerLlave" id="ownerLlave"></td>
            </tr>
            <tr>
              <td>Versión:</td>
              <td colspan="3"><input type="text" name="versionLlave" id="versionLlave"></td>
            </tr>
            <tr>
              <th colspan="5">CERTIFICADOS</th>
            </tr>
            <tr>
              <td rowspan="3"><input type="radio" name="criterio" value="cert"></td>
              <td>Nombre:</td>
              <td colspan="3"><input type="text" name="nombreCert" id="nombreCert"></td>
            </tr>
            <tr>
              <td>Owner:</td>
              <td colspan="3"><input type="text" name="ownerCert" id="ownerCert"></td>
            </tr>
            <tr>
              <td>Versión:</td>
              <td colspan="3"><input type="text" name="versionCert" id="versionCert"></td>
            </tr>
            <tr>
              <td colspan="5" class="pieTabla">
                <input type="button" class="btn-success" name="consultar" id="realizarBusqueda" value="Consultar">
              </td>
            </tr>
          </table>
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
      window.location.href = "http://localhost/testKMS/index.php";
    </script>  
    <?php
    }        
    ?>      
    
    <?php require_once('footer.php');?>
  </body>
  
</html>