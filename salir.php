<?php
session_start();
session_destroy();
setcookie('tiempo', time(), time()-1);
header('Location: index.php');
?>

