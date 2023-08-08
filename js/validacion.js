function validarNumero() {
  let precio = document.getElementById("precio");
  let existencias = document.getElementById("existencias");

  if (precio.value.length <= 10 && existencias.value.length <= 10) {
    return true;
  } else {
    alert(
      `El campo precio o existencias supera el valor maximo, solo coloca menos de 10 dígitos`
    );
    return false;
  }
}
