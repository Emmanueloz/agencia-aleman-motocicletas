/**
 * Valida si los campos precio y existencia sea menor o igual a diez dígitos
 * @returns boolean si es falso no se manda el formulario
 */
function validarNumeroProductos() {
  // Se obtiene los inputs del html mediante el ID
  let precio = document.getElementById("precio");
  let existencias = document.getElementById("existencias");

  // Se valida la condición si el precio o existencia en su valor y su tamaño sea menor o igual a 10
  if (precio.value.length <= 10 && existencias.value.length <= 10) {
    return true;
  } else {
    alert(
      `El campo precio o existencias supera el valor maximo, solo coloca menos de 10 dígitos`
    );
    return false;
  }
}

/**
 * Valida el campo de salario sea menor o igual a diez dígitos
 * @returns boolean si es falso no se manda el formulario
 */
function validarNumeroEmpleados() {
  let salario = document.getElementById("salario");

  if (salario.value.length <= 10) {
    return true;
  } else {
    alert(
      `El campo salarios supera el valor maximo, solo coloca menos de 10 dígitos`
    );
    return false;
  }
}
