<?php

function bntLimpiarFiltro()
{
  return "<a href='./consultar_ventas.php' type='button' class='btn btn-outline-secondary'>
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
function navBar(string $pagina)
{
  $activePageEmpleados = $pagina == 'empleados' ? 'fw-bold' : '';
  $activePageClientes = $pagina == 'clientes' ? 'fw-bold' : '';
  $activePageProductos = $pagina == 'productos' ? 'fw-bold' : '';
  $activePageVentas = $pagina == 'ventas' ? 'fw-bold' : '';

  return "
  <nav class='navbar navbar-expand-lg bg-lightblue'>
        <div class='container-fluid'>
          <a href='./index.php' class='navbar-brand d-flex align-items-center'>
            <img
              src='./public/favicon.png'
              class='d-inline-block icon-navbar'
              alt='Inicio'
            />
            <h1 class='text-light fs-4 fw-bold'>Agencia Aleman</h1>
          </a>
          <button
            class='navbar-toggler d-lg-none'
            type='button'
            data-bs-toggle='collapse'
            data-bs-target='#collapsibleNavId'
            aria-controls='collapsibleNavId'
            aria-expanded='false'
            aria-label='Toggle navigation'
          >
            <span class='navbar-toggler-icon'></span>
          </button>
          <div
            class='collapse navbar-collapse w-20 justify-content-end'
            id='collapsibleNavId'
          >
            <ul
              class='navbar-nav mt-2 mt-sm-1 justify-content-end align-items-end align-items-lg-center'
            >
              <li class='nav-item dropdown mx-sm-1 mt-sm-0 mt-1'>
                <a
                  class='nav-link dropdown-toggle text-light $activePageEmpleados fs-5'
                  href='#'
                  role='button'
                  data-bs-toggle='dropdown'
                  aria-expanded='false'
                >
                  Empleados
                </a>
                <ul class='dropdown-menu bg-lightblue-secondary'>
                  <li>
                    <a
                      class='dropdown-item text-white'
                      href='./vista_empleados.php'
                      >Consultar</a
                    >
                  </li>
                  <li>
                    <a
                      class='dropdown-item text-white'
                      href='./agregar_empleados.php'
                    >
                      Agregar
                    </a>
                  </li>
                </ul>
              </li>
              <li class='nav-item dropdown mx-sm-1 mt-sm-0 mt-1'>
                <a
                  class='nav-link dropdown-toggle text-light $activePageClientes fs-5'
                  href='#'
                  role='button'
                  data-bs-toggle='dropdown'
                  aria-expanded='false'
                >
                  Clientes
                </a>
                <ul class='dropdown-menu bg-lightblue-secondary'>
                  <li>
                    <a
                      class='dropdown-item text-white'
                      href='./vista_clientes.php'
                    >
                      Consultar
                    </a>
                  </li>
                  <li>
                    <a
                      class='dropdown-item text-white'
                      href='./agregar_clientes.php'
                    >
                      Agregar
                    </a>
                  </li>
                </ul>
              </li>
              <li class='nav-item dropdown mx-sm-1 mt-sm-0 mt-1'>
                <a
                  class='nav-link dropdown-toggle text-light $activePageProductos fs-5'
                  href='#'
                  role='button'
                  data-bs-toggle='dropdown'
                  aria-expanded='false'
                >
                  Productos
                </a>
                <ul class='dropdown-menu bg-lightblue-secondary'>
                  <li>
                    <a
                      class='dropdown-item text-white'
                      href='./consulta_productos.php'
                    >
                      Consultar
                    </a>
                  </li>
                  <li>
                    <a
                      class='dropdown-item text-white'
                      href='./agregar_productos.php'
                    >
                      Agregar
                    </a>
                  </li>
                </ul>
              </li>
              <li class='nav-item dropdown mx-sm-1 mt-sm-0 mt-1'>
                <a
                  class='nav-link dropdown-toggle text-light fs-5 $activePageVentas'
                  href='#'
                  role='button'
                  data-bs-toggle='dropdown'
                  aria-expanded='false'
                >
                  Ventas
                </a>
                <ul class='dropdown-menu bg-lightblue-secondary'>
                  <li>
                    <a class='dropdown-item text-white' href='./consultar_ventas.php'>Consultar</a>
                  </li>
                  <li>
                    <a
                      class='dropdown-item text-white'
                      href='./agregar_venta.php'
                    >
                      Agregar
                    </a>
                  </li>
                </ul>
              </li>

              <li class='nav-item mx-sm-1 mt-sm-0 mt-1'>
                <a
                  class='btn btn-sm btn-danger'
                  href='./logout.php'
                  aria-current='page'
                >
                  Cerrar Sesión
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
  
  ";
}
