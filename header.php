<header>
  <nav id='header-nav' class='navbar navbar-nav'>
    <div class='container-fluid'>
      <div class='navbar-header'>
        <a href='index.php' class='float-left '>
          <div id='logo-img'></div>
        </a>

        <div class='navbar-brand'>
          <a href='index.php'><h1>STOCK MANAGEMENT</h1></a>
        </div>

        <button id='navbarToggle' type='button' class='navbar-toggler' data-toggle='collapse' data-target='#collapsable-nav'>  
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <div id='collapsable-nav' class='collapse navbar-collapse'>
        <ul id='nav-list' class='navbar-nav'>
          <li id='navHomeButton' class='nav-item active' >
            <a class="nav-link" href='movimiento.php'>
              Home
            </a>
          </li>
          <?php
          if (isset($_SESSION['user_id'])) 
            {
          ?>  
          <li id='navMenuButton' class="nav-item dropdown">            
            <a href="#" class="nav-link dropdown-toggle d-inline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class=''></span><br class=''> Menu
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="movimiento.php">Movimientos</a></li>
              <li><a class="dropdown-item" href="producto.php">Productos</a></li>
              <li><a class="dropdown-item" href="busquedas.php">Consultas</a></li>
              <li role="separator" class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="estadisticas.php">Estadisticas</a></li>
<!--              <li role="separator" class="divider"></li>
              <li><a href="#">Administrar</a></li>-->
            </ul>
            
            <li class='d-block d-sm-none'><a href="movimiento.php">Movimientos</a></li>
            <li class='d-block d-sm-none'><a href="producto.php">Productos</a></li>
            <li class='d-block d-sm-none'><a href="busquedas.php">Consultas</a></li>
            <li class='d-block d-sm-none'><a href="estadisticas.php">Estadisticas</a></li>
<!--            <li role="separator" class="divider d-block d-sm-none"></li>
            <li class='d-block d-sm-none'><a href="#">Administrar</a></li>-->
            
          </li>
          <?php
          }
          ?>  
        </ul><!-- #nav-list -->
      </div><!-- .collapse .navbar-collapse -->
    </div><!-- .container -->
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