<?php
session_start();
require_once './models/Login.php';
require_once './models/elementos.php';
require_once 'SpynTPL.php';


$html = new SpynTPL('views/');
$html->Fichero('index.html');

if (isset($_SESSION['user'])) {
  $nav = navBar('', true);
} else {
  $nav = navBar('', false);
}

$html->Asigna('nav-bar', $nav);

echo $html->Muestra();
