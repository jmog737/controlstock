<?php
session_start();
require_once('data/escribirLog.php');
escribirLog('Finaliza sesión: '.$_SESSION['username']);
session_destroy();
setcookie('tiempo', time(), time()-1);
header('Location: index.php');
?>

