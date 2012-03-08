<?php

class Javascript {

	public static function flowplayerLib() {

		$buffer = "<script type=\"text/javascript\" src=\"".REL_FLOWPLAYER."/js/flowplayer-3.2.6.min.js\"></script>\n";
		$buffer .= "<script type=\"text/javascript\" src=\"".REL_FLOWPLAYER."/js/flowplayer.ipad-3.2.2.min.js\"></script>\n";

		return $buffer;

	}
	
	public static function mootoolsLib() {

		$buffer = "<script type=\"text/javascript\" src=\"".REL_MOOTOOLS."/mootools-1.3-yc.js\"></script>";
		return $buffer;

	}

	public static function jQueryLib() {

		$buffer = "<script src=\"http://cdn.jquerytools.org/1.2.5/jquery.tools.min.js\"></script>\n";
		return $buffer;

	}
	
	public static function abiToolsLib() {

		return "<script type=\"text/javascript\" src=\"".REL_ABITOOLS."/abitools.js\"></script>\n";

	}
	
	public static function ajaxLib() {

		return "<script type=\"text/javascript\" src=\"".REL_AJAX."/ajax.js\"></script>\n";

	}
	
	public static function abiCanvasShadow() {

		$buffer = "<script type=\"text/javascript\" src=\"".REL_MCS."/moocanvas.js\"></script>\n";
		$buffer .= "<script type=\"text/javascript\" src=\"".REL_MCS."/abiCanvasShadow.js\"></script>";
		return $buffer;

	}

	public static function abidiMenuLib() {

		return "<script type=\"text/javascript\" src=\"".REL_ABIDIMENU."/abidiMenu.js\"></script>\n";

	}
	
	public static function formLib() {

		$buffer = "<script type=\"text/javascript\" src=\"".REL_FORM."/form.js\"></script>\n";
		$buffer .= "<script type=\"text/javascript\" src=\"".REL_FORM."/formcalendar.js\"></script>\n";

		return $buffer;
	}
	
	public static function scrollingLib() {

		return "<script type=\"text/javascript\" src=\"".REL_SCROLLING."/scrolling.js\"></script>\n";

	}
	
	public static function html5Lib() {
		
		$buffer = "<script type=\"text/javascript\" src=\"".REL_HTML5."/html5.js\"></script>\n";

		return $buffer;

	}
	
	public static function translationLib() {

		return "<script type=\"text/javascript\" src=\"".REL_TRANSLATION."/translation.js\"></script>\n";

	}



}

?>
