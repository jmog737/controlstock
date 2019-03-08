<?php
require_once("data/sesiones.php");
require_once ("connectvars.php");

if (isset($_SESSION["username"])){
  $userDB = $_SESSION["username"];
  $pwDB = $_SESSION['username'];
}
else {
  $userDB = DB_USER;
  $pwDB = DB_PASSWORD;
}

try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=controlstock;charset=utf8',$userDB, $pwDB);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}

$sql = "select fecha, hora, cantidad, tipo, estado from movimientos order by fecha desc limit 1000";
$stmt = $pdo->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  echo $row['fecha'].'-'.$row['hora'].'-'.$row['cantidad'].'-'.$row['tipo'].'-'.$row['estado'].'<br>';
}
?>

