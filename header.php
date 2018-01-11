<header>
  <nav id='header-nav' class='navbar navbar-default'>
    <div class='container'>
      <div class='navbar-header'>
        <a href='index.php' class='pull-left visible-xs visible-sm visible-md visible-lg'>
          <div id='logo-img'></div>
        </a>

        <div class='navbar-brand'>
          <a href='index.php'><h1>STOCK MANAGEMENT</h1></a>
        </div>

        <button id='navbarToggle' type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#collapsable-nav'>
          <span class='sr-only'>Toggle navigation</span>
          <span class='icon-bar'></span>
          <span class='icon-bar'></span>
        </button>
      </div>

      <div id='collapsable-nav' class='collapse navbar-collapse'>
        <ul id='nav-list' class='nav navbar-nav navbar-right'>
          <li id='navHomeButton' class='hidden-xs' >
            <a href='movimiento.php'>
              <span class='glyphicon glyphicon-home'></span> Home
            </a>
          </li>
          <?php
          if (isset($_SESSION['user_id'])) 
            {
          ?>  
          <li id='navMenuButton' class="dropdown">            
            <a href="#" class="dropdown-toggle hidden-xs" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <span class='glyphicon glyphicon-align-justify'></span><br class='hidden-xs'> Menu
            </a>
            <ul class="dropdown-menu menu hidden-xs">
              <li><a href="movimiento.php">Movimientos</a></li>
              <li><a href="producto.php">Productos</a></li>
              <li><a href="busquedas.php">Consultas</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="estadisticas.php">Estadisticas</a></li>
<!--              <li role="separator" class="divider"></li>
              <li><a href="#">Administrar</a></li>-->
            </ul>
            
            <li class='visible-xs'><a href="movimiento.php">Movimientos</a></li>
            <li class='visible-xs'><a href="producto.php">Productos</a></li>
            <li class='visible-xs'><a href="busquedas.php">Consultas</a></li>
            <li class='visible-xs'><a href="estadisticas.php">Estadisticas</a></li>
<!--            <li role="separator" class="divider visible-xs"></li>
            <li class='visible-xs'><a href="#">Administrar</a></li>-->
            
          </li>
          <?php
            }
          ?>  
        </ul><!-- #nav-list -->
      </div><!-- .collapse .navbar-collapse -->
    </div><!-- .container -->
    <p>
      <input id="usuarioSesion" name="usuarioSesion" type="text" value="" style="color: black; display: none">
      <input id="userID" name="userID" type="text" value="" style="color: black; display: none">
      <input id="timestampSesion" name="timestampSesion" type="text" value="" style="color: black; display: none">
      <input id="duracionSesion" name="duracionSesion" type="text" value="<?php echo DURACION?>" style="color: black; display: none">
    </p>
  </nav><!-- #header-nav -->
  <script>verificarSesion();</script>
  
</header>


<!-- Modal para cambiar la contraseña -->
<div class="modal fade" id="modalPwd" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header tituloModal">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cambiar Contraseña al usuario: <?php echo $_SESSION["username"]?></h4>
      </div>
      <div class="modal-body">
        <table id="tblModal" class="tblModal">
<!--          <tr>
            <td>Nuevo nombre:</td>
            <td>
              <input type="text" id="nombreUser" name="nombreUser" class="agrandar">
            </td>
          </tr>-->
          <tr>
            <td>Introducir NUEVA contraseña:</td>
            <td>
              <input type="password" id="pw1" class="agrandar" autofocus="true">
            </td>
          </tr>
          <tr>
            <td>Repetir NUEVA contraseña:</td>
            <td>
              <input type="password" id="pw2" class="agrandar">
            </td>
          </tr>
        </table>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" id="btnModal">Actualizar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>   
  </div>
</div><!-- FIN Modal para cambiar la contraseña -->