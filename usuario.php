<?php
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
  header('Location: ./index.php');
}

require_once 'models/elementos.php';
require_once('SpynTPL.php');
require_once('models/config.php');

$title = 'Modificar Usuario';
$target = 'usuario.php';


if (isset($_POST['user'])) {
  print_r($_POST);
} else {
  $html = new SpynTPL('views/');
  $html->Fichero('frmUsuario.html');
  $html->Asigna('title', $title);
  $html->Asigna('target', $target);

  $nav = navBar('usuario');
  $html->Asigna('nav-bar', $nav);

  $usuario = unserialize($_SESSION['user']);
  $html->Asigna('user', $usuario->user());
  $html->Asigna('name', $usuario->name());
  $html->Asigna('password', $usuario->password());
  echo $html->Muestra();
}
