<?php
/*
 * Kulakov Sergey
 * mailto: kulakov.serg@gmail.com
 */
define('_module_path','module/');
define('_kernel_path','class/');
define('_class_ext','.class.php');
define('_config_path','config/');
define('_config_ext','.php');
define('_language_path','language/');
define('_language_ext','.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$output_index_error = true;

spl_autoload_register('autoload');
function autoload($class_name){
	global $output_index_error;
	if(strpos($class_name, 'PHPExcel') !== False)
		return;
	elseif (file_exists($class_file = _module_path.$class_name.'/'.$class_name._class_ext))
		require_once($class_file);
	elseif(file_exists($class_file = _kernel_path.$class_name._class_ext))
		require_once($class_file);
	elseif($output_index_error)
		echo 'index.php error: class "'.$class_name.' ('.$class_file.') " not found<br/>';
}

new app(isset($admin_mode)?$admin_mode:NULL);
?>