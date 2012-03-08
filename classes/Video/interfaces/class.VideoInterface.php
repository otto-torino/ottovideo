<?php

class VideoInterface {

	private static $_img_ext = array('jpg', 'png');
	private static $_vfp = 25;

	public static function manage() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$id = null;
		$urlParams = array('a', 'ctgid', 'id');
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

		$item = new Video((int) $id, TBL_VIDEO);
		$ctg = new Category((int) $ctgid, TBL_CTG);
		if($a=='save') self::actionItem($item);
		elseif((int) $id && $a=='delete') self::actionDelItem($item);

		$link_insert = "<a class=\"icon\" href=\"".$_SERVER['PHP_SELF']."?mng=item&a=new\">".Icon::insert()."</a>";
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Gestione file - Video/Spot")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">$link_insert ".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav_left\">";
		$buffer .= "<ul><li><a href=\"admin.php?mng=item&ctgid=0\">"._("Tutte le categorie")."</a></li></ul>";
		$buffer .= "<p style=\"border-top:1px solid #aaa;\"></p>";

		$buffer .= $ctg->printTree(0, 'admin.php?mng=item&', 'ctgid');
		$buffer .= "</div>";
		$buffer .= "<div class=\"nav_right\">";
		if((int) $id || $a=='new') {
			$buffer .= self::formItem($item, $title);
			if((int) $id) $buffer .= self::formDelItem($item);
		}
		elseif((int) $ctgid || $ctgid==0) {
			$buffer .= self::ctgVideoList($ctg);
		}
		else $buffer .= self::info();
		$buffer .= "</div>";
		$buffer .= CLEAR;

		return $buffer;

	}

	private static function ctgVideoList($ctg) {
		
		$txt_search = isset($_REQUEST['txt_search']) ? (string) cleanVar($_REQUEST['txt_search']) : '';
		$where_txt = "(title LIKE '%$txt_search%' OR description LIKE '%$txt_search%' OR notes LIKE '%$txt_search%')";

		if(!$ctg->id)
			$query = "SELECT id FROM ".TBL_VIDEO." ".($txt_search ? "WHERE $where_txt":"")." ORDER BY date DESC,title";
		else
			$query = "SELECT id FROM ".TBL_VIDEO." WHERE category='$ctg->id' ".($txt_search ? "AND $where_txt":"")." ORDER BY date DESC,title";

		$items = Video::getItems($query, TBL_VIDEO);

		if(!count($items)) 
			if($txt_search) return "<p>"._("La ricerca non ha prodotto risultati")."</p>";
			else return "<p>"._("La categoria non contiene video")."</p>";
		$buffer = "<form action=\"admin.php?mng=item&ctgid=$ctg->id\" method=\"post\">";
		$buffer .= "<p style=\"text-align:right\">";
		$buffer .= "<input type=\"text\" name=\"txt_search\" style=\"width:100px;\" value=\"$txt_search\"/>";
		$buffer .= " &#160; <input type=\"submit\" value=\""._("cerca")."\" />";
		$buffer .= "</p>";
		$buffer .= "</form>";
		$pl = new PageList(self::$_vfp, $items);
		$buffer .= "<table class=\"generic\" style=\"width:100%\">";
		$buffer .= "<tr><th></th><th>"._("Titolo")."</th><th>"._("Note")."</th><th>"._("Vis.")."</th><th>"._("Link")."</th></tr>";
		$end = $pl->start()+self::$_vfp > count($items) ? count($items) : $pl->start()+self::$_vfp;
		for($i=$pl->start(); $i<$end; $i++) {
			$item = $items[$i];
			$buffer .= "<tr>";
			$buffer .= "<td><img src=\"".REL_UP_IMG."/".$item->image."\" /></td>";
			$buffer .= "<td>";
			if($item->type == 1) {
				$buffer .= "<span class=\"icon video_icon\">"._("video")."</span>";
			}
			elseif($item->type == 2) {
				$buffer .= "<span class=\"icon spot_icon\">"._("spot")."</span>";
			}
			if($item->name_html5) {
				$buffer .= "<span class=\"icon html5_icon\">"._("html5")."</span>";
			}

			$buffer .= "<a href=\"admin.php?mng=item&id=".$item->id."\">".htmlChars($item->title)."</a>";

			$buffer .= "<br />";

			$buffer .= dbDatetimeToDate($item->date, "/")." ".$item->duration; 

			$buffer .= "<br />";

			if($item->new=='yes') {
				$buffer .= "<span class=\"icon new_icon\">new</span>";
			}
			if($item->ondemand=='yes') {
				$buffer .= "<span class=\"icon ondemand_icon\">ondemand</span>";
			}
			if($item->bind_spot) {
				$buffer .= "<span class=\"icon bind_spot_icon\">spot</span>";
			}
			$buffer .= "</td>";
			$buffer .= "<td>".htmlChars($item->notes)."</td>";
			$buffer .= "<td>";
			if($item->type==1) $buffer .= htmlChars($item->views);
			elseif($item->type==2) {
				$onclick = "openInfoLayer('$item->id')";
				$buffer .= "<span id=\"info_views_".$item->id."\" class=\"link\" onclick=\"$onclick\">".htmlChars($item->views)."</span>";
			}
			$buffer .= "</td>";
			$url = preg_match("#\?.+#", Configuration::getValue('parent_url')) 
				? Configuration::getValue('parent_url')."&vid=".$item->id 
				: Configuration::getValue('parent_url')."?vid=".$item->id;
			$buffer .= "<td><span class=\"link\" onclick=\"copyToClipboard('".$url."');\">".Icon::copy()."</a></td>";
			$buffer .= "</tr>";
		}
		$buffer .= "</table>";
		$buffer .= "<div style=\"margin-left:15px;\">".$pl->listReferenceGINO("mng=item&ctgid=$ctg->id&txt_search=$txt_search")."</div>";
		$buffer .= "<script type=\"text/javascript\" src=\"https://www.google.com/jsapi\"></script>";
		$buffer .= "<script type=\"text/javascript\">";
		$buffer .= "function openInfoLayer(id) {
			if(!window.myWinInfo || !window.myWinInfo.showing) {window.myWinInfo = new layerWindow({'title':'"._("Informazioni visualizzazioni")."', 'url':'methodPointer.php?pt[VideoInterface-viewInfo]&id='+id+'&ctgid=$ctg->id&start=".$pl->start()."', 'width':600, 'height':360, 'closeButtonUrl':'img/icons/ico_close_small.gif', 'destroyOnClose':true});window.myWinInfo.display($('info_views_'+id), {'left':getViewport().cX-600/2, 'top':getViewport().cY-360/2});}
		}";
		$buffer .= "</script>";

		return $buffer;
	}

	public static function viewInfo() {
		
		if(!Auth::checkAuth()) header('Location: login.php');

		$id = (int) cleanVar($_GET['id']);
		$ctgid = (int) cleanVar($_GET['ctgid']);
		$start = (int) cleanVar($_GET['start']);
		$spot = new Video($id, TBL_VIDEO);

		$visit_x = ceil(100*$spot->views/$spot->spot_max_view);

		$buffer = "<p>Numero di visualizzazioni: <b>$spot->views</b></p>";
		$buffer .= "<p>Numero massimo di visualizzazioni: <b>$spot->spot_max_view</b></p>";

		$buffer .= "<div style=\"text-align:left\">";
		$buffer .= "<img src=\"http://chart.apis.google.com/chart?
					chs=520x200
					&chd=t:".$visit_x.",".(100-$visit_x)."
					&cht=p3
					&chl=".$visit_x._("% visualizzazioni")."\" alt=\"google chart\"/>";
		$buffer .= "</div>";
		$buffer .= "<p><input type=\"button\" value=\"resetta contatore visualizzazioni\" onclick=\"
				if(confirm('"._("Sei proprio sicuro?")."')) { 
					location.href='methodPointer.php?pt[VideoInterface-resetViews]&id=$id&ctgid=$ctgid&start=$start';
				}
			\" /></p>";

		return $buffer;

	}

	public static function resetViews() {
		
		if(!Auth::checkAuth()) header('Location: login.php');

		$id = (int) cleanVar($_GET['id']);
		$ctgid = (int) cleanVar($_GET['ctgid']);
		$start = (int) cleanVar($_GET['start']);

		$spot = new Video($id, TBL_VIDEO);
		$spot->views = 0;
		$spot->updateDbData();

		header("Location: admin.php?mng=item&ctgid=$ctgid&start=$start");


	}

	private static function formItem($item, $title) {

		$tr = new Translation(TBL_VIDEO, $item->id);

		$ctg = new Category(null, TBL_CTG);
		$buffer = "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>".$title."</legend>";
		$buffer .= "<form id=\"formitem\" name=\"formitem\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=item&id=$item->id&a=save\" onsubmit=\"return ValidateForm('formitem')\" enctype=\"multipart/form-data\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formitem')."\"/>";
		$buffer .= "<input type=\"hidden\" name=\"old_image\" value=\"$item->image\"/>";
	
		$onchange = "onchange=\"changeType($(this).get('value'))\"";
		$buffer .= "<div class=\"label\"><label for=\"type\" class=\"req\">"._("Tipologia")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\">".inputSelect('type', $item->types(), $item->type, $onchange, array(''=>_('seleziona una tipologia')))."</div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"name\" class=\"req\">"._("Nome file")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"name\" name=\"name\" value=\"".htmlInput($item->name)."\" /> &#160;".$tr->link(_("Nome file"), 'name', 'text')."</div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"name_html5\" class=\"\">"._("Nome file html5")."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"name_html5\" name=\"name_html5\" value=\"".htmlInput($item->name_html5)."\" /> &#160;".$tr->link(_("Nome file html5"), 'name_html5', 'text')."</div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"category\" class=\"req\">"._("Categoria")." ".STAR."</label></div>";
		$query = "SELECT id FROM ".TBL_CTG." WHERE id NOT IN (SELECT parent FROM ".TBL_CTG.")";
		$ctg_ordered = $ctg->inputTreeArray($query);
		$buffer .= "<div class=\"input\">".inputSelect('category', $ctg_ordered, $item->category, '', array("0"=>""))."</div>".CLEAR;
	
		$buffer .= "<div class=\"label\"><label for=\"duration\" class=\"req\">"._("Durata (hh:mm:ss)")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"duration\" name=\"duration\" value=\"".htmlInput($item->duration)."\" /></div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"ratio\" class=\"req\">"._("Rapporto video (w:h)")."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"ratio\" name=\"ratio\" value=\"".htmlInput($item->ratio)."\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"title\" class=\"req\">"._("Titolo")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"title\" name=\"title\" value=\"".htmlInput($item->title)."\" /> &#160;".$tr->link(_("Titolo"), 'title', 'text')."</div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"description\">"._("Descrizione")."</label></div>";
		$buffer .= "<div class=\"input\"><textarea name=\"description\" rows=\"6\">".htmlInput($item->description)."</textarea> &#160;".$tr->link(_("Descrizione"), 'description', 'textarea', array("rows"=>6))."</div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"ondemand\" class=\"req\">"._("Disponibilità on demand")." ".STAR."</label></div>";
		$ra = array("yes"=>_("si"), "no"=>_("no"));
		$buffer .= "<div class=\"input\">".inputRadio('ondemand', $ra, $item->ondemand, '')."</div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"new\" class=\"req\">"._("Novità")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\">".inputRadio('new', $ra, $item->new, '')."</div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"image\" class=\"\">"._("Immagine (".implode(", ", self::$_img_ext).")")."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"file\" id=\"image\" name=\"image\" /></div>".CLEAR;
		
		if($item->image) {
			$buffer .= "<div class=\"label\"><label for=\"image\" class=\"\">"._("Immagine registrata")."</label></div>";
			$buffer .= "<div class=\"input\"><img class=\"tooltip\" src=\"".REL_UP_IMG."/$item->image\" width=\"100px\" alt=\"$item->image\" title=\"$item->image\" rel=\""._("una nuova immagine<br/>sovrascrve quella presente")."\" /></div>".CLEAR;
		}

		$buffer .= "<div class=\"label\"><label for=\"notes\">"._("Note")."</label></div>";
		$buffer .= "<div class=\"input\"><textarea name=\"notes\" rows=\"6\">".htmlInput($item->notes)."</textarea></div>".CLEAR;
		$buffer .= "<div id=\"form_extra\">";
		$buffer .= self::formExtra($item->type, $item->id);
		$buffer .= "</div>";

		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		$buffer .= "<script>";
		$buffer .= "function changeType(type) {
			if(type==1) {
				$$('#ondemand[value=no]')[0].removeProperty('checked');
				$$('#ondemand[value=yes]')[0].setProperty('checked', 'checked');
				
				$$('#new[value=no]')[0].removeProperty('checked');
				$$('#new[value=yes]')[0].setProperty('checked', 'checked');
			}
			else if(type==2) {
				$$('#ondemand[value=yes]')[0].removeProperty('checked');
				$$('#ondemand[value=no]')[0].setProperty('checked', 'checked');
				
				$$('#new[value=yes]')[0].removeProperty('checked');
				$$('#new[value=no]')[0].setProperty('checked', 'checked');
			}
			
			ajaxRequest('post', 'methodPointer.php?pt[VideoInterface-formExtra]', 'type='+type+'&id=$item->id', 'form_extra');
		}";
		$buffer .= "</script>";

		return $buffer;

	}

	public static function formExtra($type='post', $id='post') {

		if(!Auth::checkAuth()) header('Location: login.php');

		if($type==='post') $type = (int) cleanVar($_POST['type']);
		if($id==='post') $id = (int) cleanVar($_POST['id']);

		$item = new Video($id, TBL_VIDEO);
		$ctg = new SpotCategory(null, TBL_SPOT_CTG);

		$spot_ctgs = $ctg->inputSelect();

		$buffer = '';

		if($type==1) {
			$buffer .= "<div class=\"label\"><label for=\"bind_spot\" class=\"req\">"._("Attiva spot")." ".STAR."</label></div>";
			$buffer .= "<div class=\"input\">".inputRadio('bind_spot', array("1"=>_("si"), 0=>_("no")), $item->bind_spot, null)."</div>".CLEAR;
			$buffer .= "<div class=\"label\"><label for=\"spot_category\">"._("Categoria spot")."</label></div>";
			$buffer .= "<div class=\"input\">".inputSelect('spot_category', $spot_ctgs, $item->spot_category, '', array(''=>_('tutte le categorie')))."</div>".CLEAR;
		}
		else if($type==2) {
			$buffer .= "<div class=\"label\"><label for=\"spot_active\" class=\"req\">"._("Attivo")." ".STAR."</label></div>";
			$buffer .= "<div class=\"input\">".inputRadio('spot_active', array("1"=>_("si"), 0=>_("no")), $item->spot_active, null)."</div>".CLEAR;
			$buffer .= "<div class=\"label\"><label for=\"spot_max_view\" class=\"req\">"._("Numero massimo visualizzazioni")." ".STAR."</label></div>";
			$buffer .= "<div class=\"input\"><input type=\"text\" id=\"spot_max_view\" name=\"spot_max_view\" value=\"".htmlInput($item->spot_max_view)."\" /></div>".CLEAR;
			$buffer .= "<div class=\"label\"><label for=\"spot_url\">"._("Url")."</label></div>";
			$buffer .= "<div class=\"input\"><input type=\"text\" id=\"spot_url\" name=\"spot_url\" value=\"".htmlInput($item->spot_url)."\" /></div>".CLEAR;
			$buffer .= "<div class=\"label\"><label for=\"spot_category\" class=\"req\">"._("Categoria spot")." ".STAR."</label></div>";
			$buffer .= "<div class=\"input\">".inputSelect('spot_category', $spot_ctgs, $item->spot_category, '', array(''=>_('seleziona una categoria')))."</div>".CLEAR;
		}

		return $buffer;	

	}
	
	private function actionItem($item) {
		
		$error = null;

		if (!verifyFormToken('formitem')) {
  			die('CSRF Attack detected.');
		}

		$item->type = (int) cleanVar($_POST['type']);			
		$item->name = (string) cleanVar($_POST['name']);			
		$item->name_html5 = (string) cleanVar($_POST['name_html5']);			
		$item->category = (int) cleanVar($_POST['category']);			
		$item->spot_category = (int) cleanVar($_POST['spot_category']);			
		$item->duration = (string) cleanVar($_POST['duration']);			
		$item->ratio= (string) cleanVar($_POST['ratio']);			
		$item->title = (string) cleanVar($_POST['title']);			
		$item->description = (string) cleanVar($_POST['description']);			
		$item->new = (string) cleanVar($_POST['new']);			
		$item->ondemand = (string) cleanVar($_POST['ondemand']);			
		$item->notes = (string) cleanVar($_POST['notes']);			
		if(!$item->id) $item->date = date("Y-m-d H:i:s");			

		if($item->type==1) {
			$item->bind_spot = (int) cleanVar($_POST['bind_spot']);			
		}
		elseif($item->type==2) {
			$item->spot_active = (int) cleanVar($_POST['spot_active']);			
			$item->spot_max_view = (int) cleanVar($_POST['spot_max_view']);			
			$item->spot_url = (string) cleanVar($_POST['spot_url']);			
		}

		$old_image = (string) cleanVar($_POST['old_image']);
		$image_name = $_FILES['image']['name'];
		$image_size = $_FILES['image']['size'];
		$image_tmp = $_FILES['image']['tmp_name'];

		if($image_name) {
			if($image_size>MAX_FILE_SIZE) $error = 11;
			elseif(!in_array(extension($image_name), self::$_img_ext)) $error = 12;
		
			$item->image = fileName($image_name, ABS_UP_IMG);
			
			if(!upload($image_tmp, $item->image, ABS_UP_IMG)) $error=13;
		}
	
		if(!($item->name && $item->category && $item->duration && $item->ratio && $item->title && $item->new && $item->ondemand)) $error = 10;

		if($error) {header('Location: admin.php?mng=item&'.(($item->id)?"id=".$item->id:"a=new").'&error='.$error);exit;}

		$item->updateDbData();

		if($old_image && $image_name) @unlink(ABS_UP_IMG.S.$old_image);
		
		//header('Location: admin.php?mng=item&ctgid='.$item->category);
		header('Location: admin.php?mng=item&ctgid=0');
	}	

	private static function formDelItem($item) {
	
		$buffer = "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Eliminazione")."</legend>";
		$buffer .= "<form name=\"formdelitem\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=item&id=$item->id&a=delete\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formdelitem')."\"/>";

		$buffer .= "<div class=\"label\"><label for=\"submit\">"._("Attenzione l'eliminazione è definitiva")."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("elimina")."\" onclick=\"return confirmSubmit();\"></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		return $buffer;

	}

	private function actionDelItem($item) {

		if (!verifyFormToken('formdelitem')) {
  			die('CSRF Attack detected.');
		}

		if($item->image) @unlink(ABS_UP_IMG.S.$item->image);
		$item->deleteDbData();

		header('Location: admin.php?mng=item');
	}

	private function info() {
		
		$buffer = "<div><p><b>"._("Informazioni")."</b></p>"._("Selezionare una categoria per visualizzare e modificare i video presenti. utilizza l'icona '+' per inserire nuovi video.")."</div>";
		return $buffer;
	}

	public static function updateView($ids = null) {
		
		if(is_null($ids)) {
			$ids = (string) cleanVar($_POST['ids']);
		}
		foreach(explode('-', $ids) as $id) {
			$video = new Video($id, TBL_VIDEO);
			$video->views = $video->views + 1;
			$video->updateDbData();
		}

	}

}

?>
