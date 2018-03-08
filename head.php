<?php 
  $user = $_SESSION["username"];
  $estilos = 'styles_'.$user.'.php';
  if (!isset($_SESSION["username"])||(!file_exists("css/".$estilos))){
    $estilos = 'styles.php';
  }
  $estilos = "styles.css";
//  switch ($user) {
//    case 'jm':  $estilos = 'styles'.$user.'.php';
//                //$colores = 'css/colores.php';
//                break;
//    case 'diego': $estilos = 'styles1.php';
//                  //$colores = 'css/colores1.php';
//                  break;
//    case 'esther':  $estilos = 'styles2.php';
//                    //$colores = 'css/colores2.php';
//                    break;            
//    default:  $estilos = 'styles.php';
//              //$colores = 'css/colores.php';
//              break;
//  }
  //$estilos = 'styles.css';
  //$colores = "css/colores.php";
  //echo "hoja: ".$estilos;//<br>colores: ".$colores."<br>";
require_once('..\..\fpdf\fpdf.php'); 
require_once('data/baseMysql.php');
?>
<head>
  <input id="tamPagina" name="tamPagina" type="text" value="<?php echo $_SESSION["tamPagina"] ?>" style="color: black; display: none">
  <input id="limiteSeleccion" name="limiteSeleccion" type="text" value="<?php echo $limiteSeleccion ?>" style="color: black; display: none">
  <input id="limiteHistorial" name="limiteHistorial" type="text" value="<?php echo $_SESSION["limiteHistorial"] ?>" style="color: black; display: none">
  <input id="limiteHistorialGeneral" name="limiteHistorialGeneral" type="text" value="<?php echo $_SESSION["limiteHistorialGeneral"] ?>" style="color: black; display: none">
  <title>CONTROL DE STOCK</title>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <link rel='stylesheet' href='css/bootstrap.css'>
  <link rel='stylesheet' href='css/<?php echo $estilos ?>'>
  <script src="js/jquery-3.3.1.min.js"></script>
  <script src='js/bootstrap.bundle.min.js'></script>  
  <script src='js/script.js' type="text/javaScript"></script>
</head>

