<?php

class OnDemand {

	public static function render($container) {
		$contents = '';
		$vid = isset($_GET['vid']) ? cleanVar($_GET['vid']):0;
		$l = new Layout('ondemand');
		$options = array('viewType'=>'ondemand', 'initVideo'=>$vid ? $vid : null);
		$p = new Player($options);

		$pb = "<div id=\"$container\" style=\"width:".$l->pWidth."px;height:".$l->pHeight."px;\"></div>";
		$pb .= $p->render('player');

		$list = new VideoList($l, $vid);
		$lb = $list->render();

		list($width, $height) = self::getDimensions($l);
		
		if(preg_match("#pt#i", $l->layout)) $contents .= $pb.$lb;
		elseif(preg_match("#pb#i", $l->layout)) $contents .= $lb.$pb;
		elseif(preg_match("#pl#i", $l->layout)) {
			$contents .= "<div style=\"float:left;width:$l->pWidth\">".$pb."</div>";			
			$contents .= "<div style=\"float:left;width:$l->lWidth\">".$lb."</div>";
			$contents .= CLEAR;
		}
		elseif(preg_match("#pr#i", $l->layout)) {
			$contents .= "<div style=\"float:left;width:$l->lWidth\">".$lb."</div>";			
			$contents .= "<div style=\"float:left;width:$l->lWidth\">".$pb."</div>";
			$contents .= CLEAR;
		}
		$buffer = "<div id=\"onDemandContainer\" style=\"text-align:left;width:".$width."px;height:".$height."px;\">";
		$buffer .= $list->sequentialCtgList();
		$buffer .= $contents;
		$buffer .= "</div>\n";

		return $buffer;

	}
	
	public function updateList() {
			
		$l = new Layout('ondemand');
		$list = new VideoList($l);
		
		return $list->update();
		
	}
	
	public static function manage() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Pubblica - On Demand")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav\">";
		$buffer .= "<p><a href=\"iframeDemand.php\" rel=\"external\">"._("Preview")."</a></p>";
		$buffer .= "<p>"._("Di seguito il codice da copiare ed incollare per visualizzare i contenuti nel tuo sito.")."</p>";
		$buffer .= self::showIframe();
		$buffer .= "</div>";
		
		return $buffer;
		
	}
	
	private static function showIframe() {
				
		list($width, $height) = self::getDimensions(new Layout('ondemand'));
		
		$address = "http://".$_SERVER['SERVER_NAME'].(str_replace("/admin.php", "/iframeDemand.php",$_SERVER['PHP_SELF']));
		$buffer = "<div>";
		$buffer .= "<textarea style=\"width:100%;height:80px;\"><iframe src=\"$address\" width=\"".($width)."px\" height=\"".($height)."px\" frameborder=\"0\">\n<p>Your browser does not support iframes</p>\n</iframe></textarea>";
		$buffer .= "</div>";
		
		return $buffer;
		
	}

	public static function getDimensions($l) {
		
		if(preg_match("#pt|pb#", $l->layout)) {
			$width = ($l->pWidth>$l->lWidth)?$l->pWidth:$l->lWidth;
			$height = $l->pHeight+$l->lHeight;
		}
		elseif(preg_match("#pl|pr#", $l->layout)) {
			$width = $l->pWidth+$l->lWidth;
			$height = ($l->pHeight>$l->lHeight)?$l->pHeight:$l->lHeight;
		}
		
		return array($width, $height);
	} 

}

?>
