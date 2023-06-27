<?php
/********************************************************************************/
/*		NOMBRE:		SpynTPL (Clase)												*/
/*		AUTOR:		Andr�s Nieto Porras											*/
/*		FECHA:		4/6/05														*/
/*		DESCRIPCION:															*/
/*			Clase que implementa un motor de templates con el cual		  		*/
/*		podemos separar el codigo del dise�o									*/
/*		MODIFICACIONES:															*/
/*			- 4/5/05		Creacion del Fichero								*/		
/*			- 28/7/05		Mejora en sistema de bloques (vacios)				*/		
/*		FUNCIONAMIENTO:															*/
/*			fichero.php															*/
/*				include('class-tpl.php');										*/
/*				$html = new SpynTPL($directorio_templates);						*/
/*				$html->Fichero("fichero.html");									*/
/*				$html->Asigna("VARIABLE", $valor);								*/
/*				echo $html->Muestra();											*/
/*			fichero.html														*/
/*				<head>															*/
/*				<title>[VARIABLE]</title>										*/
/*				</head>															*/
/********************************************************************************/

class SpynTPL {

    var $output;						// -- Template a mostrar
	var $izq = '[';						// -- Delimitador izquierdo
	var $der = ']';						// -- Delimitador derecho

	var $tpl_dir;						// -- Directorio de los templates

	var $cache = false;					// -- Flag q activa la cache
	var $cache_dir;						// -- Directorio de la cache
	var $cache_time = 3600;				// -- Tiempo de la cache
	var $cache_file;					// -- Nombre del fichero generado

	var $variables = array();			// -- Variables
	var $blkvar = array();
	var $pos = 0;
	

	//SpynTPL()
	//Funcion:		Constructor de la clase.
	//Par�metros:	$dir	-> Directorio donde estan los templates.
	//				$cache	-> Flag q activa o desactiva el sistema de cache.
    function __construct($dir = '.',$cache = false)
	{ 
		(is_dir($dir))?$this->tpl_dir = $dir:die("No existe el directorio : $dir");
		if ($cache)
			{
			(is_dir($dir."cache/"))?$this->cache_dir = $dir."cache/":die("No existe el directorio para la cache: ".$dir."cache/");
			$this->cache = $cache;
			$this->cache_file = md5($_SERVER['REMOTE_ADDR']);
			}
		//$this->Asigna('_tDir',$dir);
	}

	// -- Agregamos un fichero al template -- //
    function Fichero($file)
	{
 	  if(!$salida=$this->LeeCache() && !$this->cache)
	  {
	  	(file_exists($this->tpl_dir.$file))?$salida=file_get_contents($this->tpl_dir.$file):die('No se encuentra el fichero :'.$this->tpl_dir.$file);
       }
	   $this->output .= $salida;
    }
	
	// -- Asignamos una variable -- //
	function Asigna ($nombre, $valor='')
	{
		if (is_array($nombre))
			$this->AsignaArray($nombre);
		else
			$this->variables[$nombre] = $valor;
	}

	// -- Asignamos un array de variables -- //
	function AsignaArray($array)
	{
		$this->variables = array_merge($this->variables,$array);
	}

	// -- Tratamos el template -- //
    function TrataTemplate()
	{
      if(count($this->variables)>0)
	  {
          foreach($this->variables as $nom=>$val)
		  {
	
			error_reporting(0);
			$val=(file_exists($this->tpl_dir.$val) && !getimagesize($this->tpl_dir.$val))?$this->CargaFile($this->tpl_dir.$val):$val;
			error_reporting(1);
			$this->output=str_replace($this->izq.$nom.$this->der,$val,$this->output);
           }
      if ($this->cache)
		   $this->EscribeCache($this->output);
	  }	
	  else   
	  	die('No hay variables asignadas');
    }

	// -- Cargamos fichero si lo encontramos definido en el template -- //
    function CargaFile($file)
	{
	  ob_start();
      include($file);
      $content=ob_get_contents();
      ob_end_clean();
      return $content;
    }

	// -- Escribe el fichero en la cache -- //
    function EscribeCache($content)
	{
      $fp=fopen($this->cache_dir.$this->cache_file,'w');
      fwrite($fp,$content);
      fclose($fp);
    }

	// -- Lee el fichero de la cache -- //
    function LeeCache()
	{
      if(file_exists($this->cache_dir.$this->cache_file)&&filemtime($this->cache_dir.$this->cache_file)>(time()-$this->cache_time))
	  {
           return file_get_contents($this->cache_dir.$this->cache_file);
      }
      return false;
    }

	// -- Muestra el template -- //
    function Muestra()
	{
	  $this->TrataTemplate();
		foreach ($this->blkvar as $nom=>$var)
		{
			$bloque = $this->CreaBloque($nom);
			if ($var[0] != '')
				$bloque = $this->RepiteBloque($nom,$bloque);
			else
				$bloque = '';
			$this->output = $this->CambiaBloque($nom,$bloque);
		}
	  return $this->output;
    }
	
	function CambiaBloque($nom, $content)
	{
		$blockName = $this->izq."block: ".$nom.$this->der;
		$blockEndName = $this->izq."/block: ".$nom.$this->der;
		$ini = strpos($this->output,$blockName);
		$fin = strpos($this->output,$blockEndName)+strlen($blockEndName);
		$bloq = substr($this->output,$ini,($fin-$ini));
		return str_replace ($bloq, $content, $this->output);
	}
	
	function CreaBloque($nom)
	{
		$blockName = $this->izq."block: ".$nom.$this->der;
		$blockEndName = $this->izq."/block: ".$nom.$this->der;
		$ini = strpos($this->output,$blockName)+strlen($blockName);
		$fin = strpos($this->output,$blockEndName);
		return substr($this->output,$ini,($fin-$ini));

	}
	
	function RepiteBloque($nom,$content)
	{
		$fin ='';
		foreach($this->blkvar[$nom] as $v1) 
		{
		$tmp = $content;
		  if ($v1 != '')
		  	{
				foreach ($v1 as $nom=>$val) 
			  	{
		  		$nom = $this->izq.$nom.$this->der;
				$tmp =str_replace($nom,$val,$tmp);
				}
			}
		$fin .= $tmp;
		}
		return $fin;
	}
	function AsignaBloque($nom, $var)
	{
		if (!array_key_exists($nom,$this->blkvar))
			$this->blkvar[$nom] = array();
		$seg = count($this->blkvar[$nom]) + 1;
		$this->blkvar[$nom] = array_merge($this->blkvar[$nom],array($seg=>$var));
	}
}
?>