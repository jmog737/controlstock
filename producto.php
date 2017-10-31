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
  
  <body onload="inhabilitarProducto()">
    <?php require_once('header.php');
    if (isset($_SESSION['user_id'])) 
      {
    ?>
    
    <div id="main-content" class="container-fluid">
      
      
      <h2 id="titulo" class="encabezado">PRODUCTOS</h2>
      <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table class='tabla2' name='producto'>
          <th colspan="3" class="tituloTabla">PRODUCTOS</th>
          <tr>
            <th align='left'><font class='negra'>Buscar:</font></th>
            <td align='center' style="max-width: 250px" colspan="2"><input type='text' id='producto' name='producto' class='agrandar' size='9' onkeyup='showHintProd(this.value, "#producto"), ""'></td>
          </tr>
          <th colspan="3" class="centrado">DATOS DEL PRODUCTO</th>
          <tr>
            <th align='left'><font class='negra'>Entidad:</font></th>
            <td align='center' colspan="2"><input type="text" name="entidad" id="entidad" class="agrandar" style="width:100%; text-align: center"></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Nombre:</font></th>
            <td align='center' colspan="2"><input type="text" name="nombre" id="nombre" class="agrandar" maxlength='35' style="width:100%; text-align: center"></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Código EMSA:</font></th>
            <td align='center' colspan="2"><input type="text" name="codigo_emsa" id="codigo_emsa" class="agrandar" maxlength='35' style="width:100%; text-align: center"></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Código Origen:</font></th>
            <td align='center' colspan="2"><input type="text" name="codigo_origen" id="codigo_origen" class="agrandar" maxlength='35' style="width:100%; text-align: center"></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Contacto:</font></th>
            <td align='center' colspan="2"><input type="text" name="contacto" id="contacto" class="agrandar" maxlength='35' style="width:100%; text-align: center"></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>BIN:</font></th>
            <td align='center' colspan="2"><input type="text" name="bin" id="bin" class="agrandar" maxlength='35' style="width:100%; text-align: center"></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Stock:</font></th>
            <td align='center' colspan="2"><input type='text' id='stockProducto' name='stockProducto' class="agrandar" maxlength='35' size='9'></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Alarma1:</font></th>
            <td align='center' colspan="2"><input type='text' id='alarma1' name='alarma1' class="agrandar" maxlength='35' size='9'></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Alarma2:</font></th>
            <td align='center' colspan="2"><input type='text' id='alarma2' name='alarma2' class="agrandar" maxlength='35' size='9'></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Último Movimiento:</font></th>
            <td align='center' colspan="2"><input type='text' id='ultimoMovimiento' name='ultimoMovimiento' class="agrandar" maxlength='35' size='9'></td>
          </tr>
          <tr>
            <th align='left'><font class='negra'>Comentarios:</font></th>
            <td align='center' colspan="2"><input type='textarea' id='comentarios' name='comentarios' class="agrandar" maxlength='35' size='9'></td>
          </tr>
          <tr>
            <td style="width: 33%;border-right: 0px;"><input type="button" value="EDITAR" id="editarProducto" name="editarProducto" class='btn-info' align='center'/></td>
            <td style="width: 33%;border-left: 0px;border-right: 0px;"><input type="button" value="ACTUALIZAR" id="actualizarProducto" name="actualizarProducto" class='btn-warning' align='center'/></td>
            <td style="width: 33%;border-left: 0px;"><input type="button" value="ELIMINAR" id="eliminarProducto" name="eliminarProducto" class='btn-danger' align='center'/></td>
          </tr>
          <tr>
            <td colspan="3" class="pieTabla"><input type="button" value="NUEVO" id="agregarProducto" name="agregarProducto" class='btn-success' align='center'/></td>
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