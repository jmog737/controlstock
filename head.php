<head>
  <title>CONTROL DE STOCK</title>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <link rel='stylesheet' href='css/bootstrap.css'>
<?php 
  
  switch ($_SESSION["username"]) {
    case 'jm': $estilos = 'styles.css';
               break;
    case 'diego': $estilos = 'styles1.css';
                  break;
    default: $estilos = 'styles1.css';
             break;
  }
  $estilos = $estilos = 'styles.css';
?>
  <link rel='stylesheet' href='css/<?php echo $estilos ?>'>
  <!-- jQuery (Bootstrap JS plugins depend on it) -->
  <script src='js/jquery-3.2.1.min.js'></script>
  <script src='js/bootstrap.min.js'></script>
  <script src='js/script.js' type="text/javaScript"></script> 
</head>
<?php 
require_once('..\..\fpdf\fpdf.php'); 
require_once('data/baseMysql.php');
?>
