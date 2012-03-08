<?php

$error = isset($_GET['error'])? (int) cleanVar($_GET['error']):null;

if($error) {
	$errorMessage = "<script type=\"text/javascript\">";

	if($error==10) $msg = _("non sono stati compilati tutti i campi obbligatori"); 
	elseif($error==11) $msg = _("Il file supera le dimensioni massime, upload fallito"); 
	elseif($error==12) $msg = _("Il tipo di file non è supportato"); 
	elseif($error==20) $msg = _("La fascia oraria non è disponibile"); 
	elseif($error==30) $msg = _("Le password non coincidono"); 

	$errorMessage .= "alert('".$msg."')";
	$errorMessage .= "</script>";
}
else $errorMessage = null;

?>
