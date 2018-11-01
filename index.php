<?php
if(isset($_SESSION['user_id'])) 
  {
  //Reanudamos la sesión:
  session_start(); 
  echo "la sesion sigue con el user id. La reanudo";
}
else {
  echo "User id NO seteado al comenzar index.php";
}

//Borro los mensajes de error:
$error_msg = "";
require_once('data/baseMysql.php');

//Si el usuario no está logueado aún, intento loguearlo:
if (!isset($_SESSION['user_id'])) 
  {echo "<br>user no seteado aun. Antes de chequear login";
  if (isset($_POST['submit'])) 
    {echo "<br>se hizo el submit.";
    //Conexión con la base de datos:
    $dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    //Recupero valores ingresados por el usuario:
    $user_username = mysqli_real_escape_string($dbc, trim($_POST['usuario']));
    $user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));
  
    //Si se ingresaron usuario y contrase?a los busco en la dB para ver si son auténticos:
    if (!empty($user_username) && !empty($user_password)) 
      {
      //Busco usuario y password en la base de datos para ver si existen:
      $query = "SELECT id_usuario, user, historialGeneral, historialProducto, tamPagina, limiteSelects FROM appusers WHERE user = '$user_username' AND password = SHA1('$user_password') limit 1";
      //echo $query."<br>";
      $data = consultarBD($query, $dbc);
      $registros = $data->num_rows;//echo "registros; ".$registros;
      $filas = obtenerResultados($data);
      if ($registros > 0) 
        {
        $_SESSION['ultima_actividad'] = time();
        foreach($filas as $fila)
          {
          //Si el usuario existe, seteo las variables de sesión y cookies (user_id y username), y lo redirijo a la página principal:
          $_SESSION['user_id'] = $fila->id_usuario;
          $_SESSION['username'] = $fila->user;
          echo "<br>se setea user_id: ".$_SESSION['user_id']."<br>se setea username: ".$_SESSION['username'];
          ///Recupero los parámetros del usuario:
          if (($fila->historialGeneral !== '')&&($fila->historialGeneral !== null)){
            $_SESSION["limiteHistorialGeneral"] = $fila->historialGeneral;
          }
          if (($fila->historialProducto !== '')&&($fila->historialProducto !== null)){
            $_SESSION["limiteHistorialProducto"] = $fila->historialProducto;
          }
          if (($fila->tamPagina !== '')&&($fila->tamPagina !== null)){
            $_SESSION["tamPagina"] = $fila->tamPagina;
          }
          if (($fila->limiteSelects !== '')&&($fila->limiteSelects !== null)){
            $_SESSION["limiteSelects"] = $fila->limiteSelects;
          }
        }
        //Obtenemos el timestamp del servidor de cuanto se hizo la petición
        //$hora = $_SERVER["REQUEST_TIME"];
        //$hora = time();
        //Definimos el valor de la sesión "ultima_actividad" como el timestamp del servidor
        //echo "<br>user_id: ".$_SESSION['user_id']."<br>ultima: ".$_SESSION['ultima_actividad']; sleep(2);
        
        //$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/actividades.php';
        //header('Content-Type: text/html; charset=utf-8');
        //header('Location: ' . $home_url);
      }
      else 
        {
        //El usuario y/o la contraseña son incorrectos:
        $error_msg = "Lo siento, el usuario y/o la contraseña ingresados no son correctos.<br>";
      }
    }
    else 
      {
      //El usuario y/o la contraseña no fueron ingresados:
      $error_msg = "Lo siento, se deben ingresar las credenciales para acceder al sitio.<br>";
    }
  mysqli_close($dbc);
  }
}

require_once ('head.php');
?>
<body>
<?php
require_once ('header.php');
?>
<main>
    <div id='main-content' class='container-fluid'>
<?php      
// If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
if (!isset($_SESSION['user_id'])) 
  {echo "<br>no seteado user_id: muestro form y mensajes si existen.";
?>
  <br>  
  <h1>Acceso al sistema:</h1>

  <h3>
<?php
  if (isset($_POST['submit']))
    {
    echo $error_msg;
  }
?>
  </h3>  
  <br>
  <form method='post' name='login' id='frmLogin' action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
  {echo "user_id SETEADO, REDIRIJO";
  //header('Location: movimiento.php');
?> 
  <h3>TEST USER_ID SETEADO</h3>  
  <script type="text/javascript">
    window.location.assign("movimiento.php");
  </script> 
<?php 
}
?>
    </div>
  </main>
<?php
require_once ('footer.php');
?>
</body>
