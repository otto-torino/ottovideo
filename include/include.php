<?php

// classes

function __autoload($class)
{
	if(preg_match("#Interface#i", $class)) {
		$folder = preg_replace("/Interface.*$/", "", $class);
   		include(ABS_CLASSES.S.$folder.S.'interfaces'.S.'class.'.$class.'.php');

	}
	else include(ABS_CLASSES.S.$class.S.'class.'.$class.'.php');
	
	// Check to see if the include declared the class
	if (!class_exists($class, false)) trigger_error("Unable to load class: $class", E_USER_WARNING);
}

// functions
include(ABS_PHP_LIB."/func.var.php");
include(ABS_PHP_LIB."/func.form.php");
include(ABS_PHP_LIB."/func.php");
include(ABS_PHP_LIB."/formcalendar.php");

// errors
include(ABS_ROOT.S."error.php");
?>
