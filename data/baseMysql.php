<?php
session_start();
require_once("config.php");
require_once("sesiones.php");
require_once ("escribirLog.php");

/**
 * \brief Función usada para conectarse a la base de datos especificada.
 * @param string $servidor Especifica cual es el servidor al que debe conectarse.
 * @param string $usuario Indica el usuario con el que debe realizar la conexión.
 * @param string $pass Indica la contraseña del usuario.
 * @param string $base Determina la base de datos a la cual se hará la conexión.
 * @return object Devuelve el tipo de objeto mysqli que será usado para realizar las consultas.
 */
function crearConexion($servidor, $usuario, $pass, $base)
  {
  if (!isset($pass)) {
    $pass = 'jmpp';
    }
  if (!isset($pass)) {
    $usuario = 'root';
    } 

  $mysqli = new mysqli($servidor, $usuario, $pass, $base);
  if ($mysqli->connect_error)
      {
      die('Error de conexión (' . $mysqli->connect_errno.') '.$mysqli->connect_error);
      }
  /* cambiar el conjunto de caracteres a utf8 */
  if (!$mysqli->set_charset("utf8")) {
      printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
      exit();
  }   
  return $mysqli;
  }

function cerrarConexion($mysqli)
  {
  $mysqli->close();
  }

function consultarBD($consulta, $mysqli)
  {
  $resultado = $mysqli->query($consulta);
  if ($resultado)
      {
      $salida = $resultado;
      }
  else
    {
    $salida = $mysqli->error;
  }
  return $salida;
  }

function obtenerResultados($resultado)
  {
  $i = 1;
  while ($obj = $resultado->fetch_object())
      {
      $salida[$i] = $obj;
      $i++;
      }  
  $resultado->close();    
  return $salida;
  }
  
function obtenerResultadosArray($resultado)
  {
  $i = 1;
  while ($row = $resultado->fetch_array(MYSQLI_NUM))
      {
      $salida[$i] = $row;
      $i++;
      }
  return $salida;
  }  

function mostrarResultados($resultado)
  {
  $campos = $resultado->fetch_fields();
  $tam = count($campos);
  $i = 1;
  echo "<table><tr>";
  foreach ($campos as $campo)
      {
      echo "<td>$campo->name</td>";
      }
  $datos = $resultado->fetch_array(MYSQLI_NUM);
  foreach ($datos as $dato)
      {
      while ($i<=$tam)
          {
          echo "<td>$dato[$i]</td>";
          }
      }
  echo "</tr></table>";    
  }
?>