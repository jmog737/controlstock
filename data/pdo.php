<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 

try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=controlstock;charset=utf8',$_SESSION['username'], $_SESSION['username']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    //print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}
