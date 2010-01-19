<?php
namespace GeoPHP;

const Z_MASK = 0x80000000;
const M_MASK = 0x40000000;
const SRID_MASK = 0x20000000;

const DEFAULT_SRID = -1;

/**
 * Colon-separated list of folders to search.
 * 
 * Paths are relative to the folder containing this file.
 */
const LOAD_PATH = 'GeoPHP:GeoPHP/features:GeoPHP/parsers:GeoPHP/geocoders';

register_autoloader();

function register_autoloader()
{
  if (!$callbacks = spl_autoload_functions())
  {
    $callbacks = array();
  }
  foreach ($callbacks as $callback)
  {
    spl_autoload_unregister($callback);
  }
  spl_autoload_register(__NAMESPACE__.'\autoload');
  foreach ($callbacks as $callback)
  {
    spl_autoload_register($callback);
  }
}

function autoload($class)
{
	if (!preg_match('#^\\\\?'.__NAMESPACE__.'\\\\(.*)#', $class, $matches))
	{
    return;
  }
	$namespaced_class = $matches[1];
	
	foreach (explode(':', LOAD_PATH) as $path)
	{
	  $full_path = dirname(__FILE__) . '/' . $path . '/' . $namespaced_class . '.php';
	  if (file_exists($full_path))
	  {
	    require_once $full_path;
			return;
	  }
	}
}
?>
