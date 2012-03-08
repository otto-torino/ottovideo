<?php

class PageList{

	public $rangeNumber;

	private $_db;
	private $_actual;
	private $_last;
	private $_first;
	private $_start;
	private $_tot;
	private $_items_for_page;
	private $_more;
	private $_less;
	
	/*
	 * Numero di pagine adiacenti a quella corrente visualizzate come link (escluse la prima e l'ultima)
	 */
	private $_vpage_num;
	
	private $_filename;
	private $_url;
	private $_symbol;

	private $_ico_less, $_ico_more;

	// parametri chiamate ajax
	private $_ajax, $_postvar, $_ref_id, $_load_id, $_script;
	
	function __construct($items_for_page, $data) {
		
		$start = $this->start();
		$this->_items_for_page = $items_for_page;
		$this->rangeNumber = $this->_items_for_page;

		$this->_db = new DB;
		
		$this->_vpage_num = 5;
		$this->_more = 0;
		$this->_less = 0;
		$this->_first = 1;
		
		$this->_start = $start+1;
		$this->_actual = ceil($this->_start / $this->_items_for_page);
		
		if(is_array($data)) {
			$this->_tot = sizeof($data);
			$this->_last = ceil($this->_tot/$this->_items_for_page);
		}
		else {
			$a = $this->_db->selectQuery($data);
			if(!$a) {
				$this->_last = 1;
				$this->_tot = 0;
			}
			else
			{
				$this->_tot = sizeof($a);
				$this->_last = ceil($this->_tot / $this->_items_for_page);
			}
		}

		$this->_ico_more = _("succ.");
		$this->_ico_less = _("prec.");

	}
	
	public function start()
	{
		$start = isset($_REQUEST['start']) ? (int) cleanVar($_REQUEST['start']) : 0;
		
		return $start<0?0:$start;
	}
	
	
	public function reassumedPrint()
	{
		$printTBL = '';

		if($this->_tot > 0)
		{
			$end = $this->_start+$this->_items_for_page - 1;
			if($end > $this->_tot) $end = $this->_tot;
			$printTBL .= $this->_start.' - '.$end.' '._("di").' '.$this->_tot."\n";
		}
		
		return $printTBL;
	}

	private function pageLink($label, $params, $link=true) {

		if(!$this->_ajax && $link) return "<a href=\"".$this->_url.$this->_symbol."$params\">".$label."</a>";
		elseif(!$this->_ajax && !$link) return $label;
		else {
			if(!$link) return $label;
			else {
				if($this->_postvar) $params = $this->_postvar."&".$params;
				$onclick = "ajaxRequest('post', '".$this->_url."', '$params', '$this->_ref_id', {'load':'$this->_load_id', 'js':$this->_script)";
				$GINO = "<span class=\"link\" onclick=\"$onclick\">".$label."</span>";
				return $GINO;
			}	
		}
	}

	public function listReferenceGINO($variables, $ajax=false, $postvar='', $ref_id='', $load_id='', $script=false) {
		
		$this->_filename = basename($_SERVER['PHP_SELF']);
		$this->_url = $this->_filename."?".$variables;
		if($variables) $this->_symbol = '&';
		else $this->_symbol = '';
		
		$this->_ajax = $ajax;
		$this->_postvar = $postvar;
		$this->_ref_id = $ref_id;
		$this->_load_id = $load_id;
		if($script) $this->_script = 'true';
		else $this->_script = 'false';

		$BUFFER = "";
		$LOWPART = "";
		$HIGHPART = "";
		
		$BUFFER = "<div class=\"pagination\">\n";
		
		if($this->_last == 1) return "";
				
		for($i=$this->_actual; $i>1; $i--) {
			if($i == $this->_last) $LOWPART .= "";
			elseif($i == $this->_actual) $LOWPART = "<span class=\"pagelist_selected\">".$this->pageLink($i, "start=".($i-1)*$this->_items_for_page, false)."</span>".$LOWPART;
			elseif($i>$this->_actual - $this->_vpage_num - 1) $LOWPART = "<span class=\"pagelist\">".$this->pageLink($i, "start=".($i-1)*$this->_items_for_page)."</span>".$LOWPART;
			else $this->_less = 1;
		}
		if($this->_less) $LOWPART = "<span class=\"pagelistdots\">...</span>".$LOWPART;
		
		for($i=$this->_actual+1; $i<$this->_last; $i++) {
			if($i<$this->_actual + $this->_vpage_num +1) $HIGHPART .= "<span class=\"pagelist\">".$this->pageLink($i, "start=".($i-1)*$this->_items_for_page)."</span>";
			else $this->_more = 1;
		}
		if($this->_more) $HIGHPART .= "<span class=\"pagelistdots\">...</span>";
		
		$BUFFER .= _("Pag. &nbsp;");
		$BUFFER .= ($this->_actual == $this->_first)? "":"<span class=\"pagelistarrow\">".$this->pageLink($this->_ico_less, "start=".($this->_actual-2)*$this->_items_for_page)."</span>";
		$class_first = ($this->_actual == $this->_first)? "pagelist_selected" : "pagelist";
		$link_first = ($this->_actual == $this->_first)? false:true;
		$BUFFER .= "<span class=\"$class_first\">".$this->pageLink($this->_first, "start=0", $link_first)."</span>";
		$BUFFER .= $LOWPART.$HIGHPART;
		$class_last = ($this->_actual == $this->_last)? "pagelist_selected" : "pagelist";
		$link_last = ($this->_actual == $this->_last)? false:true; 
		$BUFFER .= "<span class=\"$class_last\">".$this->pageLink($this->_last, "start=".($this->_last-1)*$this->_items_for_page, $link_last)."</span>";
		$BUFFER .= ($this->_actual == $this->_last)? "":"<span class=\"pagelistarrow\">".$this->pageLink($this->_ico_more, "start=".($this->_actual*$this->_items_for_page))."</span>";
		
		$BUFFER .= "</div>\n";
		
		return $BUFFER;
	}
}

?>
