<?php

class Video extends propertyObject {

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
		else return array('id'=>null, 'type'=>null, 'category'=>null, 'spot_category'=>null, 'name'=>null, 'name_html5'=>null, 'duration'=>null, 'ratio'=>null, 'title'=>null, 'description'=>null, 'image'=>null, 'new'=>null, 'ondemand'=>null, 'date'=>null, 'notes'=>null, 'bind_spot'=>null, 'spot_active'=>null, 'spot_max_view'=>null, 'spot_url'=>null, 'views'=>null);
	}

	public static function getItems($query, $tbl) {

		$results = array();
		$db = new Db;
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$results[] = new Video($b['id'], $tbl);
			}
		}

		return $results;

	}

	public function types() {
		
		return array(
			1 => "video",
			2 => "spot"
		);

	}

}

?>
