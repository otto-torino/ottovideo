<?php

class LayoutInterface {
	
	public static $img_hlt_pb, $img_pt_hlb, $img_vlt_pb, $img_pt_vlb, $img_vll_pr, $img_pl_vlr;

	public static function manage() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
	    self::$img_hlt_pb = "<img src=\"".REL_IMG."/hlt_pb.png\" alt=\""._("lista orizzontale sopra, player sotto")."\" title=\""._("lista orizzontale sopra, player sotto")."\" class=\"tooltip\"/>\n";
	    self::$img_pt_hlb = "<img src=\"".REL_IMG."/pt_hlb.png\" alt=\""._("player sopra, lista orizzontale sotto")."\" title=\""._("player sopra, lista orizzontale sotto")."\" class=\"tooltip\"/>\n";
	    self::$img_vlt_pb = "<img src=\"".REL_IMG."/vlt_pb.png\" alt=\""._("lista verticale sopra, player sotto")."\" title=\""._("lista verticale sopra, player sotto")."\" class=\"tooltip\"/>\n";
	    self::$img_pt_vlb = "<img src=\"".REL_IMG."/pt_vlb.png\" alt=\""._("player sopra, lista verticale sotto")."\" title=\""._("player sopra, lista verticale sotto")."\" class=\"tooltip\"/>\n";
	    self::$img_vll_pr = "<img src=\"".REL_IMG."/vll_pr.png\" alt=\""._("lista verticale sinistra, player destra")."\" title=\""._("lista verticale sinistra, player destra")."\" class=\"tooltip\"/>\n";
	    self::$img_pl_vlr = "<img src=\"".REL_IMG."/pl_vlr.png\" alt=\""._("player sinistra, lista verticale destra")."\" title=\""._("player sinistra, lista verticale destra")."\" class=\"tooltip\"/>\n";
		
		$urlParams = array('a', 'type');
		foreach($urlParams as $p) {
			$$p = null;
		}
		foreach($_GET as $k=>$v) {
			if(in_array($k,$urlParams)) $$k=cleanVar($v);
		}
		if($a=='save') self::actionLayout($type);
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Layout")." $type</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav\">";
		if($type=='onairbox') $buffer .= self::boxLayout();
		elseif($type=='live') $buffer .= self::liveLayout();
		else $buffer .= self::selectLayout($type);
		$buffer .= "</div>";
		return $buffer;

	}

	private static function selectLayout($type) {
	
		$layoutObj = new Layout($type);
		$layout = $layoutObj->layout;

		$bkg_css = "background-color:#cc3333";

		$buffer = self::jsLib($type);
		$buffer .= "<p>"._("Selezionare il layout desiderato e settare le relative opzioni a fondo pagina.");
		if($type=='ondemand') $buffer .= _(" L'altezza della lista comprende anche il modulo di filtraggio delle categorie.");
		$buffer .= "</p>";
		
		$buffer .= "<form id=\"formlayout\" name=\"formlayout\" method=\"post\" action=\"admin.php?mng=layout&amp;type=$type&amp;a=save\" onsubmit=\"ValidateForm('formlayout')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formlayout')."\"/>";

		if($type=='ondemand') {
			$css = ($layout == 'hlt_pb')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"hlt_pb\" style=\"float:left;padding:5px;cursor:pointer;margin-right:20px;$css\">\n";
			$buffer .= self::$img_hlt_pb;
			$buffer .= "</div>\n";
		
			$css = ($layout == 'pt_hlb')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"pt_hlb\" style=\"float:left;padding:5px;cursor:pointer;".$css."\">\n";
			$buffer .= self::$img_pt_hlb;
			$buffer .= "</div>\n";
		
			$css = ($layout == 'vlt_pb')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"vlt_pb\" style=\"float:right;padding:5px;cursor:pointer;margin-left:20px;$css\">\n";
			$buffer .= self::$img_vlt_pb;
			$buffer .= "</div>\n";
		
			$css = ($layout == 'pt_vlb')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"pt_vlb\" style=\"float:right;padding:5px;cursor:pointer;$css\">\n";
			$buffer .= self::$img_pt_vlb;
			$buffer .= "</div>\n";
		
			$buffer .= CLEAR;
		
			$buffer .= "<div style=\"margin-top:30px;\">\n";
		
			$css = ($layout == 'vll_pr')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"vll_pr\" style=\"float:left;padding:5px;cursor:pointer;$css\">\n";
			$buffer .= self::$img_vll_pr;
			$buffer .= "</div>\n";
		
			$css = ($layout == 'pl_vlr')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"pl_vlr\" style=\"float:right;padding:5px;cursor:pointer;$css\">\n";
			$buffer .= self::$img_pl_vlr;
			$buffer .= "</div>\n";	
		
			$buffer .= CLEAR; 
		}
		elseif($type=='onair') {
				
			$buffer .= "<div style=\"float:left;\">";
			$css = ($layout == 'vlt_pb')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"vlt_pb\" style=\"float:left;padding:5px;cursor:pointer;border-right:2px solid #000;$css\">\n";
			$buffer .= self::$img_vlt_pb;
			$buffer .= "</div>\n";
		
			$css = ($layout == 'pt_vlb')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"pt_vlb\" style=\"float:right;padding:5px;cursor:pointer;$css\">\n";
			$buffer .= self::$img_pt_vlb;
			$buffer .= "</div>\n";
		
			$buffer .= CLEAR; 
			$buffer .= "</div>\n";
		
			$buffer .= "<div style=\"float:right\">\n";
		
			$css = ($layout == 'vll_pr')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"vll_pr\" style=\"padding:5px;cursor:pointer;border-bottom:2px solid #000;$css\">\n";
			$buffer .= self::$img_vll_pr;
			$buffer .= "</div>\n";
		
			$css = ($layout == 'pl_vlr')?$bkg_css:'';
			$buffer .= "<div class=\"layout_type\" name=\"pl_vlr\" style=\"padding:5px;cursor:pointer;$css\">\n";
			$buffer .= self::$img_pl_vlr;
			$buffer .= "</div>\n";	
		
			$buffer .= "</div>\n";
			$buffer .= CLEAR; 

		}
		$buffer .= "<div id=\"mngDimensions\" style=\"margin-top:20px;height:200px;\">";
		$buffer .= self::manageDimensions($layout, $type);
		$buffer .= "</div>";
		$buffer .= "</form>";

		return $buffer;	
	}
	
	public static function manageDimensions($layout='', $type='') {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		if(!$layout) $layout = (string) cleanVar($_POST['layout']);
		if(!$type) $type = (string) cleanVar($_POST['type']);

		if(!$layout || !$type) return '';

		$layoutObj = new Layout($type);
		
		$buffer = "<input type=\"hidden\" name=\"layout\" value=\"$layout\"/>";

		$buffer .= "<div style=\"float:left;width:49%;\">";
		$buffer .= "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Player")."</legend>";
		
		$buffer .= "<div class=\"label\"><label for=\"pWidth\" class=\"req\">"._("Larghezza (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"pWidth\" name=\"pWidth\" value=\"".$layoutObj->pWidth."\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"pHeight\" class=\"req\">"._("Altezza (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"pHeight\" name=\"pHeight\" value=\"".$layoutObj->pHeight."\" /></div>".CLEAR;

		$buffer .= "</fieldset>";
		$buffer .= "</div>";
		$buffer .= "</div>";
	
		$buffer .= "<div style=\"float:right;width:49%;\">";
		$buffer .= "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Lista")."</legend>";
		
		$buffer .= "<div class=\"label\"><label for=\"lWidth\" class=\"req\">"._("Larghezza (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"lWidth\" name=\"lWidth\" value=\"".$layoutObj->lWidth."\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"lHeight\" class=\"req\">"._("Altezza (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"lHeight\" name=\"lHeight\" value=\"".$layoutObj->lHeight."\" /></div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"lSpeed\">"._("Durata scorrimento (ms)")." &#160;</label></div>";
		$buffer .= "<div class=\"input\"><input class=\"tooltip\" title=\""._("consigliato da 500 (molto veloce) a 2000 (molto lento), 0: nessuno scorrimento")."\" type=\"text\" style=\"width:50px;\" id=\"lSpeed\" name=\"lSpeed\" value=\"".$layoutObj->lSpeed."\" /></div>".CLEAR;
		if(($layout == 'vlt_pb' || $layout == 'pt_vlb' || $layout == 'vll_pr' || $layout == 'pl_vlr') && $type=='ondemand') { 
			$buffer .= "<div class=\"label\"><label for=\"lChars\" class=\"req\">"._("Caratteri descrizione")." ".STAR."</label></div>";
			$buffer .= "<div class=\"input\"><input class=\"tooltip\" title=\""._("numero di caratteri mostrati nella lista seguiti dal 'leggi tutto'")."\" type=\"text\" style=\"width:50px;\" id=\"lChars\" name=\"lChars\" value=\"".$layoutObj->lChars."\" /></div>".CLEAR;
		}

		$buffer .= "</fieldset>";
		$buffer .= "</div>";
		$buffer .= "</div>";
		$buffer .= CLEAR;
		$buffer .= "<p style=\"border-top:1px solid #666;text-align:center;padding-top:20px;\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" /></p>";

		return $buffer;
	
	}

	private static function jsLib($type) {
	
		$buffer = "<script type=\"text/javascript\">\n";
		$buffer .= "window.addEvent('domready', function() {
			$$('.layout_type').addEvent('click', function(event) {
				var url = 'methodPointer.php?pt[LayoutInterface-manageDimensions]';
				var data = 'layout='+$(this).getProperty('name')+'&type=$type';
				$$('.layout_type').setStyle('background-color', '#eee');
				$(this).setStyle('background-color', '#0099cc');
				sendPost(url, data, 'mngDimensions', 'mngDimensions', true, updateTooltip);
			});
		}.delay(200, this));\n";
		$buffer .= "</script>\n";	
		
		return $buffer;
	
	}

	private function actionLayout($type) {

		$layoutObj = new Layout($type);

		if (!verifyFormToken('formlayout')) {
  			die('CSRF Attack detected.');
		}
		
		$layoutObj->layout = (string) cleanVar($_POST['layout']);
		$layoutObj->pWidth = (int) cleanVar($_POST['pWidth']);
		$layoutObj->pHeight = (int) cleanVar($_POST['pHeight']);
		$layoutObj->lWidth = (int) cleanVar($_POST['lWidth']);
		$layoutObj->lHeight = (int) cleanVar($_POST['lHeight']);
		$layoutObj->lSpeed = isset($_POST['lSpeed'])? (int) cleanVar($_POST['lSpeed']):null;
		$layoutObj->lChars = (int) cleanVar($_POST['lChars']);
		$layoutObj->bWidth = (int) cleanVar($_POST['bWidth']);
		$layoutObj->bHeight = (int) cleanVar($_POST['bHeight']);
		$layoutObj->bSpeed = (int) cleanVar($_POST['bSpeed']);

		if((($type=='onair' || $type=='ondemand') && !($layoutObj->layout && $layoutObj->pWidth && $layoutObj->pHeight && $layoutObj->lWidth && $layoutObj->lHeight))
			||
		($type=='onairbox' && !($layoutObj->bWidth && $layoutObj->bHeight && $layoutObj->bSpeed))
			||
		($type=='live' && !($layoutObj->pWidth && $layoutObj->pHeight))) {
			header('Location: admin.php?mng=layout&type='.$type.'&error=10');exit;
		}

		$layoutObj->updateDbData();
		
		header('Location: admin.php?mng=layout&type='.$type);

	}

	private static function boxLayout() {
		
		$type = 'onairbox';
		$layoutObj = new Layout($type);

		$buffer = "<fieldset>";
		$buffer .= "<legend>"._("Box informazioni On Air")."</legend>";
		$buffer .= "<form id=\"formlayout\" name=\"formlayout\" method=\"post\" action=\"admin.php?mng=layout&amp;type=$type&amp;a=save\" onsubmit=\"ValidateForm('formlayout')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formlayout')."\"/>";
		
		$buffer .= "<div class=\"label\"><label for=\"bWidth\" class=\"req\">"._("Larghezza (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"bWidth\" name=\"bWidth\" value=\"".$layoutObj->bWidth."\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"bHeight\" class=\"req\">"._("Altezza (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"bHeight\" name=\"bHeight\" value=\"".$layoutObj->bHeight."\" /></div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"bSpeed\" class=\"req\">"._("Durata scorrimento (ms)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"bSpeed\" name=\"bSpeed\" value=\"".$layoutObj->bSpeed."\" /></div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";

		return $buffer;

	}

	private static function liveLayout() {
		
		$type = 'live';
		$layoutObj = new Layout($type);

		$buffer = "<fieldset>";
		$buffer .= "<legend>"._("Live streaming")."</legend>";
		$buffer .= "<form id=\"formlayout\" name=\"formlayout\" method=\"post\" action=\"admin.php?mng=layout&amp;type=$type&amp;a=save\" onsubmit=\"ValidateForm('formlayout')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formlayout')."\"/>";
		
		$buffer .= "<div class=\"label\"><label for=\"pWidth\" class=\"req\">"._("Larghezza player (px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"pWidth\" name=\"pWidth\" value=\"".$layoutObj->pWidth."\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"pHeight\" class=\"req\">"._("Altezza player(px)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" style=\"width:50px;\" id=\"pHeight\" name=\"pHeight\" value=\"".$layoutObj->pHeight."\" /></div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";

		return $buffer;

	}

}

?>
