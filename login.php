<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Login.php';

$msg = '';
if (isset($_GET['msg'])) {
  $content  = $_GET['msg'];
  $msg = "<div class='border alert alert-danger alert-dismissible fade show'
    role='alert'><button type='button' class='btn-close' data-bs-dismiss='alert'
    aria-label='Close'></button>$content</div>";
}

if (isset($_POST['usuario'])) {

  $mysqli = new mysqli($servidor, $usuario, $password, $bd);
  Login::init($mysqli);

  $usuario = $_POST['usuario'];
  $pass = $_POST['pass'];
  session_start(); # Si existe la inicia y si ya existe la abre
  if ($user = Login::login($usuario, $pass)) {
    $_SESSION['user'] = serialize($user); # Permite convertir un objeto a una cadena de caracteres
    header('Location: vista_empleados.php');
  } else {
    unset($_SESSION['user']); # Liberar memoria que tenga guardada en la variable que se coloca.
    header('Location: login.php?msg=Usuario o Contraseña');
  }
}

$html = new SpynTPL('views/');
$html->Fichero('login.html');
$html->Asigna('msg', $msg);

echo $html->Muestra();
