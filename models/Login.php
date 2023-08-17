<?php
class Login
{
  private $usuario;
  private $nombre;
  private $pass;

  private static $bd;

  public function __construct($usuario, $nombre, $pass)
  {
    $this->usuario = $usuario;
    $this->nombre = $nombre;
    $this->pass = $pass;
  }

  public static function init($bd)
  {
    self::$bd = $bd;
  }

  public function name()
  {
    return $this->nombre;
  }

  public function user()
  {
    return $this->usuario;
  }

  public function password()
  {
    return $this->pass;
  }

  public static function login($usuario, $pass)
  {
    $myLogin = null;
    $nombre = '';
    $consulta = self::$bd->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password=sha(?)");
    $consulta->bind_param('ss', $usuario, $pass);
    $consulta->execute();
    $consulta->bind_result($usuario, $nombre, $pass);
    if ($consulta->fetch()) {
      $myLogin = new Login($usuario, $nombre, $pass);
    }
    return $myLogin;
  }
}

if (isset($argc) && $argc == 2) {
  $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
  Login::init($mysqli);

  switch ($argv[1]) {
    case 'login':
      print_r(Login::login('admin', 'qwerty'));
      break;
  }
}
