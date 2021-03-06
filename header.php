<header>
  <nav id='header-nav' class='navbar navbar-expand' role="navigation">
    <div class='container-fluid'>
      <div class='navbar-header'>
        <a href='index.php' title="Ir al inicio" class='float-left d-none d-sm-block'>
          <div id='logo-img' class='d-none d-sm-block'></div>
        </a>

        <div class='navbar-brand'>
          <a href='index.php' title="Ir al Inicio"><h1>STOCK MANAGEMENT</h1></a>
        </div>

        <button id='navbarToggle' type='button' class='navbar-toggler' data-toggle='collapse' data-target='#collapsable-nav' aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">  
          <span class="navbar-toggler-icon"></span>
        </button>
        
      </div>

      <div id='collapsable-nav' class='collapse navbar-collapse align-middle'>
        <ul id='nav-list' class='navbar-nav ml-auto my-auto pr-2 pb-2'>
        <?php
          //require_once 'data/config.php';
          if (isset($_SESSION['user_id'])) 
            {
          ?> 
          <li id='navHomeButton' class='nav-item active d-none d-sm-block align-middle' >
            <a class="nav-link" href="index.php"><img src="images/home.png" alt="HOME" title="Ir al inicio"></a>  
          </li>
          <li id='navMenuButton' class="nav-item dropdown align-bottom">            
            <a href="#" class="nav-link dropdown-toggle d-none d-sm-block pt-3" title="Desplegar el MENU" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Menu
            </a>
            <div id="drop" class="dropdown-menu dropdown-menu-right  " aria-labelledby="navbarDropdown">
              <a class="dropdown-item" title="Ingresar Movimientos" href="movimiento.php">Movimientos</a>
              <a class="dropdown-item" title="Realizar Consultas" href="busquedas.php">Consultas</a>
              <a class="dropdown-item" title="Editar Productos" href="producto.php">Productos</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" title="Ver Estad&iacute;siticas" href="estadisticas.php">Estadisticas</a>
<!--              <li role="separator" class="divider"></li>
              <li><a href="#">Administrar</a></li>-->
            </div>
            
            <a class="d-block d-sm-none" title="Ingresar Movimientos" href="movimiento.php">Movimientos</a>
            <a class='d-block d-sm-none' title="Realizar Consultas" href="busquedas.php">Consultas</a>
            <a class='d-block d-sm-none' title="Editar Productos" href="producto.php">Productos</a>
            <a class='d-block d-sm-none' title="Ver Estad&iacute;siticas" href="estadisticas.php">Estadisticas</a> 
          </li>
          <?php
          }
          else{
          ?>  
            
          <?php
          }
          ?>  
        </ul><!-- #nav-list -->
      </div><!-- .collapse .navbar-collapse -->
    </div><!-- .container -->
  </nav><!-- #header-nav -->
  <script lang="javasript/text">
    var dir = window.location.pathname;
    var temp = dir.split("/");
    var tam = temp.length;
    var pagina = temp[tam-1];
    if (pagina !== 'index.php'){
      verificarSesion('', 's');
      var duracion0 = <?php echo DURACION ?>;
      /// Se agrega un tiempo extra cosa de estar seguro que venció el tiempo (si queda en el límite habrá veces 
      ///que lo detecta y otras que no teniendo que esperar nuevamente un tiempo de DURACION para volver a probar
      var tiempoChequeo = parseInt(duracion0*1000, 10)+2000;
      
      setInterval(function(){
        verificarSesion('¡Llamé desde setInterval!', 'n');
      }, tiempoChequeo);
    }
  </script>
<!-- Modal para cambiar la contraseña -->
<div class="modal fade" id="modalPwd" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header tituloModal">
        <h4 class="modal-title">Cambiar Contraseña al usuario: <?php echo $_SESSION["username"]?></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <table id="tblModalPwd" class="tblModal">
<!--          <tr>
            <td>Nuevo nombre:</td>
            <td>
              <input type="text" id="nombreUser" name="nombreUser" class="agrandar">
            </td>
          </tr>-->
          <tr>
            <td>Introducir NUEVA contraseña:</td>
            <td>
              <input type="password" id="pw1" placeholder="Contraseña NUEVA" title="Ingresar la NUEVA contraseña" class="agrandar" autofocus="true">
            </td>
          </tr>
          <tr>
            <td>Repetir NUEVA contraseña:</td>
            <td>
              <input type="password" id="pw2" placeholder="Contraseña NUEVA" title="Repetir la NUEVA contraseña" class="agrandar">
            </td>
          </tr>
        </table>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" title="Cambiar la contraseña" id="btnModal">Actualizar</button>
        <button type="button" class="btn btn-primary" title="Cerrar ventana SIN modificar la contraseña" data-dismiss="modal">Cerrar</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal para cambiar la contraseña -->

<!-- Modal para cambiar los parámetros de visualización -->
<div class="modal fade" id="modalParametros" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header tituloModal">
        <h4 class="modal-title">Cambiar Par&aacute;metros del usuario: <?php echo $_SESSION["username"]?></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <table id="tblModalParametros" class="tblModal">
<!--          <tr>
            <td>Nuevo nombre:</td>
            <td>
              <input type="text" id="nombreUser" name="nombreUser" class="agrandar">
            </td>
          </tr>-->
          <tr>
            <td>Tamaño de p&aacute;gina:</td>
            <td>
              <input type="text" id="pageSize" placeholder="NUEVO tamaño de página" title="Ingresar el NUEVO tamaño de p&aacute;gina" class="agrandar" autofocus="true">
            </td>
          </tr>
          <tr>
            <td>Tamaño Selects:</td>
            <td>
              <input type="text" id="tamSelects" placeholder="NUEVO tamaño de SELECTS" title="Ingresar el NUEVO tamaño de los selects" class="agrandar" autofocus="true">
            </td>
          </tr>
          <tr>
            <td>Historial General:</td>
            <td>
              <input type="text" id="tamHistorialGeneral" placeholder="NUEVO tamaño del historial general" title="Ingresar el NUEVO tamaño para el historial general" class="agrandar">
            </td>
          </tr>
          <tr>
            <td>Historial Producto:</td>
            <td>
              <input type="text" id="tamHistorialProducto" placeholder="NUEVO tamaño del historial del producto" title="Ingresar el NUEVO tamaño para el historial del producto" class="agrandar">
            </td>
          </tr>
        </table>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" title="Cambiar par&acute;metros" id="btnParam">Actualizar</button>
        <button type="button" class="btn btn-primary" title="Cerrar ventana SIN modificar los par&aacute;metros" data-dismiss="modal">Cerrar</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal para cambiar los parámetros -->

<!-- Modal de movimiento REPETIDO -->
<div class="modal fade" id="modalMovRepetido" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header tituloModal">
        <h4 class="modal-title">¡ATENCI&Oacute;N: MOVIMIENTO REPETIDO!</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" tabindex="32"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <table id="tblModalMovRepetido" class="tblModal">
          <tr>
            <td colspan="2">YA hay un movimiento con los mismos parámetros:</td>
          </tr>
          <tr>
            <td><strong>Producto:</strong></td>
            <td><input type="text" id="mdlProducto" disabled=""></input></td>
          </tr>
          <tr>
            <td><strong>Fecha:</strong></td>
            <td><input type="text" id="mdlFecha" disabled=""></input></td>
          </tr>
          <tr>
            <td><strong>Tipo:</strong></td>
            <td><input type="text" id="mdlTipo" disabled=""></input></td>
          </tr>
          <tr>
            <td><strong>Cantidad:</strong></td>
            <td><input type="text" id="mdlCantidad" disabled=""></input></td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="2">¿Desea igualmente agregarlo?</td>
          </tr>
        </table>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" title="AGREGAR el movimiento" id="btnModalRepetido" tabindex="31">AGREGAR</button>
        <button type="button" class="btn btn-primary" title="Cerrar ventana SIN realizar el movimiento" id="btnModalRepCerrar" data-dismiss="modal" tabindex="30">CANCELAR</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal de movimiento REPETIDO -->

<!-- Modal de cambio de FECHA -->
<div class="modal fade" id="modalCbioFecha" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header tituloModal">
        <h4 class="modal-title">¡ATENCI&Oacute;N: CAMBIO DE FECHA!</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" tabindex="32"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        ¡Esto <strong><i>puede</i></strong> generar diferencias con los reportes <strong>YA</strong> enviados!
        <table id="tblModalCbioFecha" class="tblModal">
          <tr>
            <th>Fecha Actual:</th>
            <td><input type="text" id="mdlFechaActual" disabled="" size="6"></input></td>
          </tr>
          <tr>
            <th>Fecha Nueva:</th>
            <td><input type="text" id="mdlFechaNueva" disabled="" size="6" align="left"></input></td>
          </tr>
          <tr>
            <td> </td>
          </tr>
        </table>
        ¿Desea continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" title="AGREGAR el movimiento" id="btnModalCbioFecha" tabindex="31">AGREGAR</button>
        <button type="button" class="btn btn-primary" title="Cerrar ventana SIN realizar el movimiento" id="btnModalCbioFechaCerrar" data-dismiss="modal" tabindex="30">CANCELAR</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal de cambio de FECHA -->

</header>


