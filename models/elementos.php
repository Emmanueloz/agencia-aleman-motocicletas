<?php

function bntLimpiarFiltro($pagina)
{
  return "<a href='./$pagina' type='button' class='btn btn-outline-secondary'>
    <svg
  xmlns='http://www.w3.org/2000/svg'
  width='16'
  height='16'
  fill='currentColor'
  class='bi bi-x-circle'
  viewBox='0 0 16 16'
>
  <path
    d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'
  ></path>
  <path
    d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'
  ></path>
</svg>
</a>";
}

/**
 * Nos retorna la barra de navegación en html
 * @param string $pagina es la pagina en donde se mostrara la pagina
 */
function navBar($pagina, $session = true)
{

  require_once 'SpynTPL.php';
  $html = new SpynTPL('views/');
  $activePageEmpleados = $pagina == 'empleados' ? 'fw-bold' : '';
  $activePageClientes = $pagina == 'clientes' ? 'fw-bold' : '';
  $activePageProductos = $pagina == 'productos' ? 'fw-bold' : '';
  $activePageVentas = $pagina == 'ventas' ? 'fw-bold' : '';

  $html->Fichero('header.html');
  $html->Asigna('activePageEmpleados', $activePageEmpleados);
  $html->Asigna('activePageClientes', $activePageClientes);
  $html->Asigna('activePageProductos', $activePageProductos);
  $html->Asigna('activePageVentas', $activePageVentas);

  if ($session == true) {
    $html->Asigna('display-login', 'd-none');
    $html->Asigna('display', 'd-flex');
  } else {
    $html->Asigna('display-login', 'd-flex');
    $html->Asigna('display', 'd-none');
  }

  $nav = $html->Muestra();

  return $nav;
}
