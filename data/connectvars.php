<?php
if(!isset($_SESSION)) 
  {
  //Reanudamos la sesión:
  session_start(); 
}
/*!
  @file connectvars.php
  @brief Archivo que contiene las constantes predefinidas para la conexión. \n
  Entre ellas están DB_HOST, DB_USER, DB_PASSWORD, y DB_NAME.
  @version v.1.0.
  @author Juan Martín Ortega
 */

//Definicion de las constantes para la conexion con la base de datos:
/*!
  @param DB_HOST Constante que indica el host donde se encuentra la base de datos.
*/
define('DB_HOST', 'localhost');
/**
  @param DB_USER Constante con el usuario por defecto que se conectará a la base de datos.
*/
define('DB_USER', 'root');
/**
  @param DB_PASSWORD Constante con la contraseña del usuario por defecto que se conectará a la base de datos.
*/
define('DB_PASSWORD', 'jmpp');
/**
  \param DB_NAME Constante que indica la base de datos del host a la cual conectarse.
*/
define('DB_NAME', 'controlstock');

/**
  \param DURACION Constante que indica el tiempo de sesión permitido sin actividad (en segundos).
*/
define('DURACION', 300);

?>
