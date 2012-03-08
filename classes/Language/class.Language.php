<?php

class Language extends propertyObject {

	protected $_tbl_data;

	function __construct($id) {
		$this->_tbl_data = TBL_LANGUAGES;
		parent::__construct($this->initP($id));
	
	}
	
	private function initP($id) {
	
		$db = new Db;
		$query = "SELECT * FROM ".$this->_tbl_data." WHERE id='$id'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) return $a[0]; 
		else return array('id'=>null, 'label'=>null, 'code'=>null, 'main'=>null, 'active'=>null);
	}

	public function getAll($opt=null) {

		$where = array();
		if(isset($opt['active'])) $where[] = "active='".$opt['active']."'";
		if(isset($opt['main'])) $where[] = "main='".$opt['main']."'";
		if(isset($opt['code'])) $where[] = "code='".$opt['code']."'";
		$where_q = count($where) ? "WHERE ".implode(" AND ", $where):"";
		$lngs = array();
		$query = "SELECT id FROM $this->_tbl_data $where_q ORDER BY label";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$lngs[] = new Language($b['id']);
			}
		}
		return $lngs;
	}


}

?>
