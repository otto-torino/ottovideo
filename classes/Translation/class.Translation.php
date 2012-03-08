<?php

class Translation {

	private $_tbl, $_id;

	function __construct($tbl=null, $id=null) {

		$this->_tbl = $tbl;
		$this->_id = $id;
	}

	public function trnsl($field, $dft) {

		$db = new Db();
		$lng = new Language(null);
		$mls = $lng->getAll(array("main"=>1));
		$ml = $mls[0];
		if($_SESSION['lng']==$ml->code) return $dft;
		else {
			$query = "SELECT text FROM ".TBL_TRANSLATIONS." WHERE tbl='$this->_tbl' AND id='$this->_id' AND field='$field' AND language='".$_SESSION['lng']."'";
			$a = $db->selectquery($query);
			return count($a) ? $a[0]['text'] : $dft;
		}
	
	}

	public function link($label, $field, $type, $opt=null) {

		if(!$this->_id) return null;

		$opt_param = '';
		if(count($opt)) foreach($opt as $k=>$v) $opt_param .= "&$k=$v";
		$url = 'methodPointer.php?pt[Translation-formTranslation]&tbl='.$this->_tbl.'&id='.$this->_id."&field=$field&type=$type".$opt_param;
		$onclick = "if(!window.translationWin || !window.translationWin.showing) {window.translationWin = new layerWindow({'title':'"._("Traduzione campo")." \'$label\'', 'url':'$url', 'width':600, 'maxHeight':350, 'closeButtonUrl':'img/icons/ico_close_small.gif', 'bodyId':'trnsl".$field."_body', 'destroyOnClose':true});window.translationWin.display($(this), {'left':getViewport().cX-600/2, 'top':getViewport().cY-400/2});}";

		$buffer = "<span class=\"link translation\" onclick=\"$onclick\">".Icon::translation()."</span>";

		
		return $buffer;
	
	}

	public function formTranslation() {
	
		if(!Auth::checkAuth()) exit();

		$db = new Db();

		$this->_tbl = (string) cleanVar($_GET['tbl']);
		$this->_id = (int) cleanVar($_GET['id']);
		$field = (string) cleanVar($_GET['field']);
		$type = (string) cleanVar($_GET['type']);
		$size = isset($_GET['size']) ? (int) cleanVar($_GET['size']) : null;
		$maxlength = isset($_GET['maxlength']) ? (int) cleanVar($_GET['maxlength']) : null;
		$cols = isset($_GET['cols']) ? (int) cleanVar($_GET['cols']) : null;
		$rows = isset($_GET['rows']) ? (int) cleanVar($_GET['rows']) : null;
		
		$buffer = "<div id=\"translationForm".$field."_$this->_tbl\" style=\"display:block\">";
		$buffer .= "<input type=\"hidden\" name=\"table\" value=\"$this->_tbl\">";
		$buffer .= "<input type=\"hidden\" name=\"field\" value=\"$field\">";
		$buffer .= "<input type=\"hidden\" name=\"id\" value=\"$this->_id\">";

		$lng = new Language(null);
		$data = "tbl=$this->_tbl&id=$this->_id&field=$field";
		$par_lngs = array();
		foreach($lng->getAll(array("active"=>1, "main"=>0)) as $l) {
			$buffer .= "<div class=\"label\"><label for=\"".$l->code."\" >".htmlChars($l->label)."</label></div>";
			$buffer .= "<div class=\"input\">";
			$query = "SELECT text FROM ".TBL_TRANSLATIONS." WHERE tbl='$this->_tbl' AND field='$field' AND id='$this->_id' AND language='$l->code'";
			$a =  $db->selectquery($query);
			$value = count($a) ? $a[0]['text'] : null;
			if($type=='text') 
				$buffer .= $this->inputText($l->code, $value, array("size"=>$size, "maxlength"=>$maxlength));
			elseif($type=='textarea') 
				$buffer .= $this->textarea($l->code, $value, array("rows"=>$rows, "cols"=>$cols));

			$buffer .= "</div>".CLEAR;
			$data .= "&".$l->code."='+$$('#trnsl".$field."_body *[name=".$l->code."]')[0].value+'";
			$par_lngs[] = $l->code;
		}
		$data = "'".$data."&lngs=".implode(",",$par_lngs)."'";

		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$onclick = "ajaxRequest('post', 'methodPointer.php?pt[Translation-actionTranslation]', $data, null, {callback:closeTrslWin})";
		$buffer .= "<div class=\"input\"><input type=\"button\" name=\"submit\" onclick=\"$onclick\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</div>";

		return $buffer;
	
	}

	private function inputText($field, $value, $opt) {
	
		return "<input type=\"text\" id=\"$field\" name=\"$field\" value=\"".htmlInput($value)."\" ".($opt['size'] ? "size=\"".$opt['size']."\"":"")." ".($opt['maxlength'] ? "maxlength=\"".$opt['maxlength']."\"":"")."/>";

	}

	private function textarea($field, $value, $opt) {

		return "<textarea id=\"$field\" name=\"$field\" ".($opt['rows'] ? "rows=\"".$opt['rows']."\"":"")." ".($opt['cols'] ? "cols=\"".$opt['cols']."\"":"").">".htmlInput($value)."</textarea>";

	}

	public function actionTranslation() {
	
		if(!Auth::checkAuth()) exit();

		$db = new Db;

		$tbl = (string) cleanVar($_POST['tbl']);
		$field = (string) cleanVar($_POST['field']);
		$id = (int) cleanVar($_POST['id']);
		$lngs = (string) cleanVar($_POST['lngs']);
		foreach(explode(",", $lngs) as $lngcode) {
		
			$query_chk = "SELECT id FROM ".TBL_TRANSLATIONS." WHERE tbl='$tbl' AND field='$field' AND id='$id' AND language='$lngcode'";
			$a = $db->selectquery($query_chk);
			if(count($a)) {
				$query = empty($_POST[$lngcode])
					? "DELETE FROM ".TBL_TRANSLATIONS." WHERE tbl='$tbl' AND field='$field' AND id='$id' AND language='$lngcode'"
					: "UPDATE ".TBL_TRANSLATIONS." SET text='".cleanVar($_POST[$lngcode])."' WHERE tbl='$tbl' AND field='$field' AND id='$id' AND language='$lngcode'";
				$db->actionquery($query);
			}
			elseif(!empty($_POST[$lngcode])) {
				$query = "INSERT INTO ".TBL_TRANSLATIONS." (tbl, id, field, language, text) VALUES ('$tbl', '$id', '$field', '$lngcode', '".cleanVar($_POST[$lngcode])."')";
				$db->actionquery($query);
			}
			echo $query;
		
		}
		exit;

	}


}

?>
