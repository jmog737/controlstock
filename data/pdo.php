<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 

try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=controlstock;charset=utf8',$_SESSION['username'], $_SESSION['username']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}
//echo $_SESSION['username']."<br>";
//$sql = "select fecha, hora, cantidad, tipo, estado from movimientos order by fecha desc limit 1000";
//$stmt = $pdo->query($sql);
//echo '<table>';
//while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
//  echo '<tr><td>'.$row['fecha'].'</td><td>'.$row['hora'].'</td><td>'.$row['cantidad'].'</td><td>'.$row['tipo'].'</td><td>'.$row['estado'].'</td></tr>';
//}
//echo '</table>';
?>

