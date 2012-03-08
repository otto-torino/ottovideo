<?php

class SpotCategory extends propertyObject {

	protected $_tbl_data;

	function __construct($id, $tbl) {
		
		$this->_tbl_data = $tbl;
		parent::__construct($this->initP($id));
	
	}
	
	private function initP($id) {
	
		$db = new Db;
		$query = "SELECT * FROM ".$this->_tbl_data." WHERE id='$id'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) return $a[0]; 
		else return array('id'=>null, 'name'=>null, 'description'=>null);
	}

	private function getOpt($options, $opt) {
	
		return isset($options[$opt]) ? $options[$opt] : null;
	
	}
	
	public function getFromQuery($query) {

		$res = array();
		$db = new Db();
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$res[] = new SpotCategory($b['id'], $this->_tbl_data);
			}
		}

		return $res;

	}

	public function inputSelect() {
		
		$res = array();

		$db = new Db;
		$query = "SELECT id, name FROM ".$this->_tbl_data." ORDER BY name";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$res[$b['id']] = htmlChars($b['name']);
			}
		}

		return $res;
	}

}

?>
