<?php

$code = isset($_GET['lng']) ? (string) cleanVar($_GET['lng']) : '';

$lng = new Language(null);

$mls = $lng->getAll(array("main"=>1));

if(!isset($_SESSION['lng']) || $code) {
	$_SESSION['lng'] = count($lng->getAll(array("code"=>$code))) ? $code : $mls[0]->code;
}

?>
