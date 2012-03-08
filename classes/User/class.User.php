<?php

class User extends propertyObject {

	protected $_tbl_data;

	function __construct($id) {
		$this->_tbl_data = TBL_USERS;
		parent::__construct($this->initP($id));
	
	}
	
	private function initP($id) {
	
		$db = new Db;
		$query = "SELECT * FROM ".$this->_tbl_data." WHERE id='$id'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) return $a[0]; 
		else return array('id'=>null, 'username'=>null, 'password'=>null, 'role'=>null);
	}

	public function getUsers($other) {
	
		$users = array();
		if(!$other) return array($_SESSION['abv_userid']=>new User($_SESSION['abv_userid']));
		$db = new Db;
		$query = "SELECT id FROM $this->_tbl_data order by username";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$users[$b['id']] = new User($b['id']);
			}
		}
		return $users;
	}

}

?>
