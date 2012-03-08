<?php

class Comment extends propertyObject {

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
		else return array('id'=>null, 'video'=>null, 'email'=>null, 'text'=>null, 'date'=>null);
	}

	public static function getItems($query, $tbl) {

		$results = array();
		$db = new Db;
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$results[] = new Comment($b['id'], $tbl);
			}
		}

		return $results;

	}

}

?>
