<?php

class Css {
	
	public static function authCss() {
		$buffer = "<style type=\"text/css\">\n";
		$buffer .= "@import url(\"css/auth.css\");\n";
		$buffer .= "</style>\n";
		return $buffer;
	}
	
	public static function adminCss() {
		$buffer = "<style type=\"text/css\">\n";
		$buffer .= "@import url(\"css/admin.css\");\n";
		$buffer .= "@import url(\"css/menuAdmin.css\");\n";
		$buffer .= "@import url(\"css/formcalendar.css\");\n";
		$buffer .= "</style>\n";
		return $buffer;
	}
	
	public static function onDemandCss() {
		$buffer = "<style type=\"text/css\">\n";
		$buffer .= "@import url(\"css/onDemand.css\");\n";
		$buffer .= "</style>\n";
		return $buffer;
	}
	
	public static function onAirCss() {
		$buffer = "<style type=\"text/css\">\n";
		$buffer .= "@import url(\"css/onAir.css\");\n";
		$buffer .= "</style>\n";
		return $buffer;
	}

	public static function onAirBoxCss() {
		$buffer = "<style type=\"text/css\">\n";
		$buffer .= "@import url(\"css/onAirBox.css\");\n";
		$buffer .= "</style>\n";
		return $buffer;
	}

	public static function liveCss() {
		$buffer = "<style type=\"text/css\">\n";
		$buffer .= "@import url(\"css/live.css\");\n";
		$buffer .= "</style>\n";
		return $buffer;
	}

}
?>
