<?php 
  $user = $_SESSION["username"];
  $estilos = 'styles_'.$user.'.php';
  if (!isset($_SESSION["username"])||(!file_exists("css/".$estilos))){
    $estilos = 'styles.php';
  }
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
?>
<head>
  <title>CONTROL DE STOCK</title>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <link rel='stylesheet' href='css/bootstrap.css'>
  <link rel='stylesheet' href='css/<?php echo $estilos ?>'>
  <script src="js/jquery-3.2.1.min.js"></script>
  <script src='js/bootstrap.min.js'></script>
  <script src='js/script.js' type="text/javaScript"></script>
</head>
<?php 
require_once('..\..\fpdf\fpdf.php'); 
require_once('data/baseMysql.php');
?>
