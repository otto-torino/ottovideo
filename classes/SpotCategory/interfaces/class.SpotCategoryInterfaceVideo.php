<?php

class SpotCategoryInterfaceVideo {

	public static function manage() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$id = null;
		$urlParams = array('a', 'id');
		foreach($urlParams as $p) {
			$$p = null;
		}
		foreach($_GET as $k=>$v) {
			if(in_array($k,$urlParams)) $$k=cleanVar($v);
		}

		if($id) {
			$title = _("Modifica");
		}
		elseif($a=='new') {
			$title = _("Inserimento");
		}

		$ctg = new SpotCategory((int) $id, TBL_SPOT_CTG);
		if($a=='save') self::actionCtg($ctg);
		elseif((int) $id && $a=='delete') self::actionDelCtg($ctg);

		$link_insert = "<a class=\"icon\" href=\"".$_SERVER['PHP_SELF']."?mng=spotctg&a=new\">".Icon::insert()."</a>";
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Gestione file - Categorie")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">$link_insert ".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav_left\">";
		$buffer .= self::listCtg($ctg);
		$buffer .= "</div>";
		$buffer .= "<div class=\"nav_right\">";
		if((int) $id || $a=='new') {
			$buffer .= self::formCtg($ctg, $title);
			if((int) $id) $buffer .= self::formDelCtg($ctg);
		}
		else $buffer .= self::info();
		$buffer .= "</div>";
		$buffer .= CLEAR;

		return $buffer;

	}


	private static function listCtg($ctg) {

		$items = $ctg->getFromQuery("SELECT id FROM ".TBL_SPOT_CTG." ORDER BY name");

		if(count($items)) {
			$buffer = "<ul id=\"ctgTree\">";
			foreach($items as $item) {
				$buffer .= "<li><a href=\"admin.php?mng=spotctg&id=".$item->id."\">".htmlChars($item->name)."</a></li>";
			}
			$buffer .= "</ul>";
		}
		else {
			$buffer = "<p>"._("Non risultano categorie registrate.")."</p>";
		}		

		return $buffer;

	}

	private static function formCtg($ctg, $title) {

		$tr = new Translation(TBL_SPOT_CTG, $ctg->id);

		$buffer = "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>".$title."</legend>";
		$buffer .= "<form id=\"formctg\" name=\"formctg\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=spotctg&id=$ctg->id&a=save\" onsubmit=\"return ValidateForm('formctg')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formctg')."\"/>";
		
		$buffer .= "<div class=\"label\"><label for=\"name\" class=\"req\">"._("Nome")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"name\" name=\"name\" value=\"".htmlInput($ctg->name)."\" /> &#160;".$tr->link(_("Nome"), 'name', 'text')."</div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"description\">"._("Descrizione")."</label></div>";
		$buffer .= "<div class=\"input\"><textarea name=\"description\">".htmlInput($ctg->description)."</textarea> &#160;".$tr->link(_("Descrizione"), 'description', 'textarea')."</div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		return $buffer;

	}
	
	private function actionCtg($ctg) {
		
		if (!verifyFormToken('formctg')) {
  			die('CSRF Attack detected.');
		}

		$ctg->name = (string) cleanVar($_POST['name']);			
		$ctg->description = (string) cleanVar($_POST['description']);			
		if($ctg->name) $ctg->updateDbData();
		header('Location: admin.php?mng=spotctg');
	}	

	private static function formDelCtg($ctg) {
	
		$buffer = "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Eliminazione")."</legend>";
		$buffer .= "<form name=\"formdelctg\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=spotctg&id=$ctg->id&a=delete\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formdelctg')."\"/>";

		$buffer .= "<div class=\"label\"><label for=\"submit\">"._("Attenzione l'aliminazione è definitiva e determina l'eliminazione di tutti i contenuti della catogoria!")."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("elimina")."\" onclick=\"return confirmSubmit();\"></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		return $buffer;

	}

	private function actionDelCtg($ctg) {

		if (!verifyFormToken('formdelctg')) {
  			die('CSRF Attack detected.');
		}

		$ctg->deleteDbData();

		header('Location: admin.php?mng=spotctg');
	}

	private static function info() {
		
		$buffer = "<div><p><b>"._("Informazioni")."</b></p>"._("Le categorie spot servono per associare gli spot ai contenuti video. Sono categorie flat, senza annidamento.")."</div>";
		return $buffer;
	}


}

?>
