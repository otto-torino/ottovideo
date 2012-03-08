<?php

class Configuration {
	
	public static function manage() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$urlParams = array('a', 'item');
		foreach($urlParams as $p) {
			$$p = null;
		}
		foreach($_GET as $k=>$v) {
			if(in_array($k,$urlParams)) $$k=cleanVar($v);
		}
		if($a=='save' && $item=='parent') self::actionParent($item);
		elseif($a=='save' && $item=='server') self::actionServer($item);
		elseif($a=='save' && $item=='live') self::actionLive($item);
		elseif($a=='save' && $item=='player') self::actionPlayer($item);
		elseif($a=='save' && $item=='list') self::actionList($item);
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Configurazione")." ".(($item=='list')?_("lista"):$item)."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav\">";
		if($item=='parent') $buffer .= self::formParent();
		elseif($item=='server') $buffer .= self::formServer();
		elseif($item=='live') $buffer .= self::formLive();
		elseif($item=='player') $buffer .= self::formPlayer();
		elseif($item=='list') $buffer .= self::formList();
		$buffer .= "</div>";
		return $buffer;

	}

	public static function getValue($field) {
		
		$db = new Db;
		$query = "SELECT $field FROM ".TBL_CONFIG." WHERE id='1'";
		$a = $db->selectquery($query);
		return ($a)? $a[0][$field]:null;

	}
	
	private static function formParent() {

		$buffer = "<fieldset>";
		$buffer .= "<legend>"._("Sito ospitante")."</legend>";

		$buffer .= "<form id=\"formconfparent\" name=\"formconfparent\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=conf&a=save&item=parent\" onsubmit=\"return ValidateForm('formconfparent')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formconfparent')."\"/>";
	
		$buffer .= "<div class=\"label\"><label for=\"address\" class=\"req\">"._("Url della pagina ospitante ottovideo ondemand")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"parent_url\" name=\"parent_url\" value=\"".self::getValue('parent_url')."\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		
		return $buffer;
		
	}

	private static function formServer() {

		$buffer = "<form id=\"formconfserver\" name=\"formconfserver\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=conf&a=save&item=server\" onsubmit=\"return ValidateForm('formconfserver')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formconfserver')."\"/>";

		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Server streaming")."</legend>";

	
		$buffer .= "<div class=\"label\"><label for=\"address\" class=\"req\">"._("Indirizzo")." ".STAR."<br/><i>"._("Nell'indirizzo non includere lo schema e lo slash finale")."</i></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"address\" name=\"address\" value=\"".self::getValue('streamingAddress')."\" /></div>".CLEAR;
		
		$buffer .= "</fieldset>";

		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Server http")."</legend>";

	
		$buffer .= "<div class=\"label\"><label for=\"address_http\" class=\"\">"._("Indirizzo")."<br/><i>"._("Nell'indirizzo non includere lo schema e lo slash finale")."</i></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"address_http\" name=\"address_http\" value=\"".self::getValue('httpAddress')."\" /></div>".CLEAR;
		
		$buffer .= "</fieldset>";

		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;
		
		$buffer .= "</form>";

		return $buffer;
		
	}
	
	private static function actionParent($item) {

		if (!verifyFormToken('formconfparent')) {
  			die('CSRF Attack detected.');
		}

		$db = new Db;

		$parent_url = (string) cleanVar($_POST['parent_url']);

		if(!($item && $parent_url)) {header('Location: admin.php?mng=conf&item='.$item.'&error=10');exit;}

		if(self::getValue('id')!=1) $query = "INSERT INTO ".TBL_CONFIG." (parent_url) VALUES ('$parent_url')";
		else $query = "UPDATE ".TBL_CONFIG." SET parent_url='$parent_url' WHERE id='1'";
		$result = $db->actionquery($query);

		header('Location: admin.php?mng=conf&item='.$item);

	}

	private static function actionServer($item) {

		if (!verifyFormToken('formconfserver')) {
  			die('CSRF Attack detected.');
		}

		$db = new Db;

		$streamingAddress = (string) cleanVar($_POST['address']);
		$httpAddress = (string) cleanVar($_POST['address_http']);

		if(!($item && $streamingAddress)) {header('Location: admin.php?mng=conf&item='.$item.'&error=10');exit;}

		if(self::getValue('id')!=1) $query = "INSERT INTO ".TBL_CONFIG." (streamingAddress, httpAddress) VALUES ('$streamingAddress', '$httpAddress')";
		else $query = "UPDATE ".TBL_CONFIG." SET streamingAddress='$streamingAddress', httpAddress='$httpAddress' WHERE id='1'";
		$result = $db->actionquery($query);

		header('Location: admin.php?mng=conf&item='.$item);

	}
	
	private static function formPlayer() {

		$buffer = "<p>"._("Gli swf vanno caricati via ftp all'interno della cartella ")."'".REL_UP_SWF."'. Nei campi che seguono inserire solamente il nome del file.</p>";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Player")."</legend>";

		$buffer .= "<form id=\"formconfplayer\" name=\"formconfplayer\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=conf&a=save&item=player\" onsubmit=\"return ValidateForm('formconfplayer')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formconfplayer')."\"/>";
	
		$buffer .= "<div class=\"label\"><label for=\"onDemandSwf\" class=\"\">"._("Spash/Swf iniziale on demand")."<br/><i>"._("Immagine/filmatino in flash che compare al caricamento della pagina")."</i></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"onDemandSwf\" name=\"onDemandSwf\" value=\"".self::getValue('onDemandSwf')."\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"onAirSwf\" class=\"\">"._("Spash/Swf iniziale on air")."<br/><i>"._("Immagine/filmatino in flash che compare quando non ci sono contenuti in palinsesto")."</i></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"onAirSwf\" name=\"onAirSwf\" value=\"".self::getValue('onAirSwf')."\" /></div>".CLEAR;
	
		$buffer .= "<div class=\"label\"><label for=\"splashLive\" class=\"\">"._("Immagine splash live")."<br/><i>"._("Path relativo o assoluto dell'immagine visualizzata quando lo stream live è assente")."</i></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"splashLive\" name=\"splashLive\" value=\"".self::getValue('splashLive')."\" /></div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		
		return $buffer;
		
	}
	
	private static function actionPlayer($item) {

		if (!verifyFormToken('formconfplayer')) {
  			die('CSRF Attack detected.');
		}

		$db = new Db;

		$onDemandSwf = isset($_POST['onDemandSwf'])? (string) cleanVar($_POST['onDemandSwf']):null;
		$onAirSwf = isset($_POST['onAirSwf'])? (string) cleanVar($_POST['onAirSwf']):null;
		$splashLive = isset($_POST['splashLive'])? (string) cleanVar($_POST['splashLive']):null;
	
		if(!$item) {header('Location: admin.php?mng=conf&item='.$item.'&error=10');exit;}

		if(self::getValue('id')!=1) $query = "INSERT INTO ".TBL_CONFIG." (onDemandSwf, onAirSwf, splashLive) VALUES ('$onDemandSwf', '$onAirSwf', '$splashLive')";
		else $query = "UPDATE ".TBL_CONFIG." SET onDemandSwf='$onDemandSwf',  onAirSwf='$onAirSwf', splashLive='$splashLive' WHERE id='1'";
		$result = $db->actionquery($query);

		header('Location: admin.php?mng=conf&item='.$item);

	}
	
	private static function formList() {

		$buffer = "<form id=\"formconflist\" name=\"formconflist\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=conf&a=save&item=list\" onsubmit=\"return ValidateForm('formconflist')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formconflist')."\"/>";
		
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Lista on demand")."</legend>";

		$buffer .= "<div class=\"label\"><label for=\"imgWidthDemand\" class=\"req\">"._("Larghezza thumbs (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"imgWidthDemand\" name=\"imgWidthDemand\" value=\"".self::getValue('imgWidthDemand')."\" style=\"width:40px;\"/></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"imgHeightDemand\" class=\"req\">"._("Altezza thumbs (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"imgHeightDemand\" name=\"imgHeightDemand\" value=\"".self::getValue('imgHeightDemand')."\" style=\"width:40px;\"/></div>".CLEAR;
	
		$buffer .= "</fieldset>";
		
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Lista on air")."</legend>";
	
		$buffer .= "<div class=\"label\"><label for=\"imgWidthAir\" class=\"req\">"._("Larghezza thumbs (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"imgWidthAir\" name=\"imgWidthAir\" value=\"".self::getValue('imgWidthAir')."\" style=\"width:40px;\"/></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"imgHeightAir\" class=\"req\">"._("Altezza thumbs (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"imgHeightAir\" name=\"imgHeightAir\" value=\"".self::getValue('imgHeightAir')."\" style=\"width:40px;\"/></div>".CLEAR;
	
		$buffer .= "</fieldset>";
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;
		$buffer .= "</form>";
		
		return $buffer;
		
	}
	
	private static function actionList($item) {

		if (!verifyFormToken('formconflist')) {
  			die('CSRF Attack detected.');
		}

		$db = new Db;

		$imgWidthDemand = (int) cleanVar($_POST['imgWidthDemand']);
		$imgHeightDemand = (int) cleanVar($_POST['imgHeightDemand']);
		$imgWidthAir = (int) cleanVar($_POST['imgWidthAir']);
		$imgHeightAir = (int) cleanVar($_POST['imgHeightAir']);
	
		if(!$item) {header('Location: admin.php?mng=conf&item='.$item.'&error=10');exit;}

		if(self::getValue('id')!=1) $query = "INSERT INTO ".TBL_CONFIG." (imgWidthDemand, imgHeightDemand, imgWidthAir, imgHeightAir) VALUES ('$imgWidthDemand', '$imgHeightDemand', '$imgWidthAir', '$imgHeightAir')";
		else $query = "UPDATE ".TBL_CONFIG." SET imgWidthDemand='$imgWidthDemand',  imgHeightDemand='$imgHeightDemand', imgWidthAir='$imgWidthAir',  imgHeightAir='$imgHeightAir' WHERE id='1'";
		$result = $db->actionquery($query);

		header('Location: admin.php?mng=conf&item='.$item);

	}

	private static function formLive() {

		$buffer = "<fieldset>";
		$buffer .= "<legend>"._("Live streaming url")."</legend>";

		$buffer .= "<form id=\"formconflive\" name=\"formconflive\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=conf&a=save&item=live\" onsubmit=\"return ValidateForm('formconflive')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formconflive')."\"/>";
	
		$buffer .= "<div class=\"label\"><label for=\"address\" class=\"req\">"._("Indirizzo")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"address\" name=\"address\" value=\"".self::getValue('live_stream_url')."\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"stream_name\" class=\"req\">"._("Nome stream")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"stream_name\" name=\"stream_name\" value=\"".self::getValue('stream_name')."\" /></div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"address_mobile\" class=\"req\">"._("Indirizzo mobile")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"address_mobile\" name=\"address_mobile\" value=\"".self::getValue('live_stream_mobile_url')."\" /></div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		
		return $buffer;
		
	}

	private static function actionLive($item) {

		if (!verifyFormToken('formconflive')) {
  			die('CSRF Attack detected.');
		}

		$db = new Db;

		$live_stream_url = (string) cleanVar($_POST['address']);
		$stream_name = (string) cleanVar($_POST['stream_name']);
		$live_stream_mobile_url = (string) cleanVar($_POST['address_mobile']);

		if(!($item && $live_stream_url)) {header('Location: admin.php?mng=conf&item='.$item.'&error=10');exit;}

		if(self::getValue('id')!=1) $query = "INSERT INTO ".TBL_CONFIG." (live_stream_url, stream_name, live_stream_mobile_url) VALUES ('$live_stream_url', '$stream_name', '$live_stream_mobile_url')";
		else $query = "UPDATE ".TBL_CONFIG." SET live_stream_url='$live_stream_url', stream_name='$stream_name', live_stream_mobile_url='$live_stream_mobile_url' WHERE id='1'";
		$result = $db->actionquery($query);

		header('Location: admin.php?mng=conf&item='.$item);

	}


}

?>
