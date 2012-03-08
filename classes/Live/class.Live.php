<?php

class Live {

	public static function render($container) {

		$contents = '';

		$l = new Layout('live');
		$options = array('viewType'=>'live');
		$p = new Player($options);

		$pb = "<div id=\"$container\" style=\"width:".$l->pWidth."px;height:".$l->pHeight."px;outline=none;\"></div>";
		$pb .= $p->render('player');

		list($width, $height) = array($l->pWidth, $l->pHeight);
		$contents = $pb;

		$buffer = "<div id=\"liveContainer\" style=\"text-align:left;width:".$width."px;height:".$height."px;\">";
		$buffer .= $contents;
		$buffer .= "</div>\n";

		return $buffer;

	}

	public static function manage() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Pubblica - Live stream")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav\">";
		$buffer .= "<p><a href=\"iframelive.php\" rel=\"external\">"._("Preview")."</a></p>";
		$buffer .= "<p>"._("Di seguito il codice da copiare ed incollare per visualizzare i contenuti nel tuo sito.")."</p>";
		$buffer .= self::showIframe();
		$buffer .= "</div>";
		
		return $buffer;
		
	}

	private static function showIframe() {
				
		$l = new Layout('live');
		list($width, $height) = array($l->pWidth, $l->pHeight);
		
		$address = "http://".$_SERVER['SERVER_NAME'].(str_replace("/admin.php", "/iframelive.php",$_SERVER['PHP_SELF']));
		$buffer = "<div>";
		$buffer .= "<textarea style=\"width:100%;height:80px;\"><iframe src=\"$address\" width=\"".($width)."px\" height=\"".($height)."px\" frameborder=\"0\">\n<p>Your browser does not support iframes</p>\n</iframe></textarea>";
		$buffer .= "</div>";
		
		return $buffer;
		
	}
}

?>
