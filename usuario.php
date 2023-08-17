<?php
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
  header('Location: ./index.php');
}

require_once 'models/elementos.php';
require_once('SpynTPL.php');
require_once('models/config.php');
$html = new SpynTPL('views/');
$html->Fichero('frmUsuario.html');
$html->Asigna('title', 'Modificar Usuario');

$nav = navBar('usuario');
$html->Asigna('nav-bar', $nav);
$html->Asigna('msg', '');
$html->Asigna('d-confirm', '');
$html->Asigna('d-modify', 'd-none');
$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Login::init($mysqli);
$usuario = unserialize($_SESSION['user']);
$user = $usuario->user();
$name = $usuario->name();
$title = 'Modificar Usuario';

if (isset($_POST['accion']) && $_POST['accion'] == 'modificar') {
  $newUser = new Login($user, $_POST['name'], $_POST['password']);
  $newUser->modificar();
  header('Location: logout.php');
} elseif (isset($_POST['accion']) && $_POST['accion'] == 'comprobar') {
  $password = $_POST['pass'];
  $userVerify = Login::login($user, $password);
  if (isset($userVerify)) {
    $html->Asigna('d-confirm', 'd-none');
    $html->Asigna('d-modify', '');

    $html->Asigna('user', $user);
    $html->Asigna('name', $name);
    $html->Asigna('password', $password);
    $html->Asigna('accion', 'agregar');
  } else {
    $content = "Contraseña Incorrecta";
    $msgError = "<div class='alert alert-danger alert-dismissible fade show'
    role='alert'><button type='button' class='btn-close'
    data-bs-dismiss='alert' aria-label='Close'></button>$content</div>";
    $html->Asigna('msg', $msgError);
  }
}
echo $html->Muestra();
