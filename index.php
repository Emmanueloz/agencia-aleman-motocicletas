<?php
session_start();
require_once 'SpynTPL.php';


$html = new SpynTPL('views/');
$html->Fichero('index.html');

if (isset($_SESSION['user'])) {
  $html->Asigna('display-login','d-none');
  $html->Asigna('display','d-flex');
} else {
  $html->Asigna('display-login','d-flex');
  $html->Asigna('display','d-none');
}


echo $html->Muestra();