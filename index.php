<?php
session_start();
//require_once("data/sesiones.php");
//Borro los mensajes de error:
$error_msg = "";
require_once('data/baseMysql.php');

//Si el usuario no está logueado aún, intento loguearlo:
if (!isset($_SESSION['user_id'])) 
  {
  if (isset($_POST['submit'])) 
    {
    //Conexión con la base de datos:
    $dbc = crearConexion(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    //Recupero valores ingresados por el usuario:
    $user_username = mysqli_real_escape_string($dbc, trim($_POST['usuario']));
    $user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));
  
    //Si se ingresaron usuario y contrase?a los busco en la dB para ver si son auténticos:
    if (!empty($user_username) && !empty($user_password)) 
      {
      //Busco usuario y password en la base de datos para ver si existen:
      $query = "SELECT id_usuario, user FROM appusers WHERE user = '$user_username' AND password = SHA('$user_password')";
      $data = consultarBD($query, $dbc);
      $filas = obtenerResultados($data);
      $registros = $data->num_rows;
      
      if ($registros == 1) 
        {
        foreach($filas as $fila)
          {
          //Si el usuario existe, seteo las variables de sesión y cookies (user_id y username), y lo redirijo a la página principal:
          $_SESSION['user_id'] = $fila->id_usuario;
          $_SESSION['username'] = $fila->user;
        }
        //Obtenemos el timestamp del servidor de cuanto se hizo la petición
        $hora = $_SERVER["REQUEST_TIME"];

        //Definimos el valor de la sesión "ultima_actividad" como el timestamp del servidor
        $_SESSION['ultima_actividad'] = $hora;
        ?>
     
        
      <?php
        
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

// If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
if (empty($_SESSION['user_id'])) 
  {
?>
  <div id='main-content' class='container-fluid'>
    
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
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <table class='tabla2' name='login'> 
        <th colspan="2" class="tituloTabla">INGRESO</th>
        <tr>
            <th align='left'><font class='negra'>Usuario:</font></th>
            <td align='center'><input type='text' name='usuario' id="nombreUsuario" maxlength='15' size='9' autofocus="true" class="agrandar" value=" "></td>
        </tr>
        <tr>
            <th align='left'><font class='negra'>Password:</font></th>
            <td align='center'><input type='password' name='password' id="password" maxlength='15' size='9' class="agrandar" value=""></td>
        </tr>    
        <tr>
            <td colspan="2" class="pieTabla"><input type="submit" value="LogIn" name="submit" class='boton' align='center'/></td>
        </tr>
      </table>
    </form>
    
    </div>
<?php
  }
  else 
    {
    
?>
    <body>
    <div id='main-content' class='container-fluid'>
      <script type="text/javascript">
        window.location.href = "../controlstock/movimiento.php";
      </script> 
    </div> <!-- fin del div contenido -->
<?php       
  }
  require_once ('footer.php');
?>
</body>
