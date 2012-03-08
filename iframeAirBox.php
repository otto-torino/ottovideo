<?php

include('config.php');
include('paths.php');
include('const.php');
include(ABS_INCLUDE.S.'include.php');

session_name(SESSION_NAME);
session_start();

include('language.php');  // sets SESSION['lng']

$buffer = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
$buffer .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"it_IT\" xml:lang=\"it_IT\">\n";
$buffer .= "<head>\n";
$buffer .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";		
$buffer .= "<title>".TITLE."</title>\n";
$buffer .= Css::onAirBoxCss();
$buffer .= Javascript::flowplayerLib();
$buffer .= Javascript::mootoolsLib();
$buffer .= Javascript::ajaxLib();
$buffer .= "<script type=\"text/javascript\">
		window.addEvent('domready',function(){
			// tooltips
			var myTips = new Tips('.tooltip', {className: 'tips'});
		});
		function updateTooltip() {var myTips = new Tips('.tooltip', {className: 'tips'});}
	   </script>";

$buffer .= "</head>\n";

$buffer .= "<body>\n";

$buffer .= OnAir::onAirBox();

$buffer .= "</body>";
$buffer .= "</html>";

echo $buffer;

?>
