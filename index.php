<?php
require_once('data/sesiones.php');
require_once("data/config.php");

//Borro los mensajes de error:
global $error_msg;


//Si el usuario no está logueado aún, intento loguearlo:
if (!isset($_SESSION['user_id'])) 
  {
  require_once ('head.php');
?>
  <body>
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid'>

    <br>  
    <h1>Acceso al sistema:</h1>

   <h3>
<?php
  if (isset($_SESSION['error_msg']))
    {
    echo $_SESSION['error_msg'];
  }
?>
    </h3>  
    <br>
    <form method='post' name='login' id='frmLogin' action="data/login.php">
      <table class='tabla2' name='login'> 
        <th colspan='2' class="tituloTabla">INGRESO</th>
        <tr>
            <th align='left'><font class='negra'>Usuario:</font></th>
            <td align='center'><input type='text' name='usuario' title='Ingresar el nombre del usuario' placeholder='Nombre de Usuario' id='nombreUsuario' maxlength='15' size='9' autofocus='true' class='agrandar' value=' '></td>
        </tr>
        <tr>
            <th align='left'><font class='negra'>Password:</font></th>
            <td align='center'><input type='password' name='password' id='password' placeholder="Contraseña" title='Ingresar la contraseña para el usuario' maxlength='15' size='9' class='agrandar' value=''></td>
        </tr>    
        <tr>
            <td colspan='2' class='pieTabla'><input type='submit' value='Log In' name='submit' title='Ingresar al sistema' id='login' class='boton' align='center'/></td>
        </tr>
      </table>
    </form>      
<?php
}
else 
  {
  //var_dump($_SESSION);
  header('Location: movimiento.php');
?> 
<!--  <script type="text/javascript">
    window.location.assign("movimiento.php");
  </script> -->
<?php 
}
?>
    </div>
  </main>
<?php
require_once ('footer.php');
?>
</body>
