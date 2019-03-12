<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 

if ( isset($_POST["usuario"]) && isset($_POST["password"]) ) {
  unset($_SESSION["username"]);
  try {
    $userDB = trim($_POST['usuario']);
    $pwDB = trim($_POST['password']);
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=controlstock;charset=utf8','conectar', 'conectar');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
      $_SESSION['error_msg'] = "¡Error!: ".$e->getMessage();
      return;
      //die();
  }
  
  $sql = "SELECT COUNT(*) FROM appusers WHERE user = '$userDB' AND password = SHA1('$pwDB') limit 1";
  $resultado = $pdo->query($sql);
  if ($resultado !== false) {
    /* Comprobar el número de filas que coinciden con la sentencia SELECT */
    if ($resultado->fetchColumn() > 0) {
      $stmt = $pdo->query("SELECT id_usuario, user, historialGeneral, historialProducto, tamPagina, limiteSelects FROM appusers WHERE user = '$userDB' AND password = SHA1('$pwDB') limit 1");
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $_SESSION['tiempo'] = time();
      //Si el usuario existe, seteo las variables de sesión y cookies (user_id y username), y lo redirijo a la página principal:
      $_SESSION['user_id'] = $row['id_usuario'];
      $_SESSION['username'] = $row['user'];
      ///Recupero los parámetros del usuario:
      if (($row['historialGeneral'] !== '')&&($row['historialGeneral'] !== null)){
        $_SESSION["limiteHistorialGeneral"] = $row['historialGeneral'];
      }
      if (($row['historialProducto'] !== '')&&($row['historialProducto'] !== null)){
        $_SESSION["limiteHistorialProducto"] = $row['historialProducto'];
      }
      if (($row['tamPagina'] !== '')&&($row['tamPagina'] !== null)){
        $_SESSION["tamPagina"] = $row['tamPagina'];
      }
      if (($row['limiteSelects'] !== '')&&($row['limiteSelects'] !== null)){
        $_SESSION["limiteSelects"] = $row['limiteSelects'];
      }

      $_SESSION["success"] = "Logged in.";
      header( 'Location: movimiento.php' ) ;
      return;
    }
    /* No coincide ningua fila -- hacer algo en consecuencia */
    else {
      $_SESSION['error_msg'] = "Lo siento, el usuario y/o la contraseña ingresados no son correctos.<br>";
      header('Location: index.php');
      return;
    }
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

      <br>  
      <h1>Acceso al sistema:</h1>
      <h3>
<?php
  if ( isset($_SESSION["error_msg"]) ) {
    echo('<p style="color:white">'.$_SESSION["error_msg"]."</p>\n");
    unset($_SESSION["error_msg"]);
  }
?>
      </h3>  
      <br>
      <form method='post' name='login' id='frmLogin'>
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
    </div>
  </main>
<?php
require_once ('footer.php');
?>
</body>
