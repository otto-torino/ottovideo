<?php

class Layout extends propertyObject {

	protected $_tbl_data;

	function __construct($type) {
		
		$this->_tbl_data = TBL_LAYOUT;
		parent::__construct($this->initP($type));
	
	}
	
	private function initP($type) {
	
		$db = new Db;
		$query = "SELECT * FROM ".$this->_tbl_data." WHERE id='$type'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) return $a[0]; 
		else return array('id'=>null, 'layout'=>null, 'pWidth'=>null, 'pHeight'=>null, 'lWidth'=>null, 'lHeight'=>null, 'lSpeed'=>null, 'lChars'=>null, 
			'bWidth'=>null, 'bHeight'=>null, 'bSpeed'=>null);
	}

}

?>
