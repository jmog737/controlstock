<?php
require_once('baseMysql.php');

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
  
  $data = consultarBD($query, $dbc);
  $registros = $data->num_rows;
  $filas = obtenerResultados($data);
  
  if ($registros > 0) 
    {
    $_SESSION['tiempo'] = time();
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
    header('Location: ../movimiento.php');
  }
  else 
    {
    //El usuario y/o la contraseña son incorrectos:
    $_SESSION['error_msg'] = "Lo siento, el usuario y/o la contraseña ingresados no son correctos.<br>";
    header('Location: ../index.php');
  }
}
else 
  {
  //El usuario y/o la contraseña no fueron ingresados:
  $_SESSION['error_msg'] = "Lo siento, se deben ingresar las credenciales para acceder al sitio.<br>";
  header('Location: ../index.php');
}
mysqli_close($dbc);
?>