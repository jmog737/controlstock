<header>
  <nav id='header-nav' class='navbar navbar-expand' role="navigation">
    <div class='container-fluid'>
      <div class='navbar-header'>
        <a href='index.php' title="Ir al inicio" class='float-left '>
          <div id='logo-img'></div>
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
          <li id='navHomeButton' class='nav-item active d-none d-sm-block align-middle' >
            <a class="nav-link" href="movimiento.php"><img src="images/home.png" alt="HOME" title="Ir al inicio"></a>  
          </li>
          <?php
          if (isset($_SESSION['user_id'])) 
            {
          ?>            
          <li id='navMenuButton' class="nav-item dropdown align-bottom">            
            <a href="#" class="nav-link dropdown-toggle d-none d-sm-block pt-3" title="Desplegar el MENU" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Menu
            </a>
            <div id="drop" class="dropdown-menu dropdown-menu-right  " aria-labelledby="navbarDropdown">
              <a class="dropdown-item" title="Ingresar Movimientos" href="movimiento.php">Movimientos</a>
              <a class="dropdown-item" title="Editar Productos" href="producto.php">Productos</a>
              <a class="dropdown-item" title="Realizar Consultas" href="busquedas.php">Consultas</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" title="Ver Estad&iacute;siticas" href="estadisticas.php">Estadisticas</a>
<!--              <li role="separator" class="divider"></li>
              <li><a href="#">Administrar</a></li>-->
            </div>
            
            <a class="d-block d-sm-none" title="Ingresar Movimientos" href="movimiento.php">Movimientos</a>
            <a class='d-block d-sm-none' title="Editar Productos" href="producto.php">Productos</a>
            <a class='d-block d-sm-none' title="Realizar Consultas" href="busquedas.php">Consultas</a>
            <a class='d-block d-sm-none' title="Ver Estad&iacute;siticas" href="estadisticas.php">Estadisticas</a> 
          </li>
          <?php
          }
          ?>  
        </ul><!-- #nav-list -->
      </div><!-- .collapse .navbar-collapse -->
    </div><!-- .container -->
  </nav><!-- #header-nav -->
  <script>verificarSesion();</script>
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
</header>


