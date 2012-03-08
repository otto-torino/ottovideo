<?php

class ScheduleItem extends propertyObject {
	
	protected $_tbl_data;

	function __construct($id) {
		
		$this->_tbl_data = TBL_SCHEDULE;
		parent::__construct($this->initP($id));

	}		
	
	private function initP($id) {
	
		$db = new Db;
		$query = "SELECT * FROM ".$this->_tbl_data." WHERE id='$id'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) return $a[0]; 
		else return array('id'=>null, 'date'=>null, 'initTime'=>null, 'endTime'=>null, 'itemId'=>null, 'block'=>null);
	}

	public function deleteItem() {

		$db = new Db;
		$video = new Video($this->itemId, TBL_VIDEO);
		$duration_sec = timeToSeconds($video->duration);
		$query = "UPDATE ".TBL_SCHEDULE." SET initTime=(initTime-".$duration_sec."), endTime=(endTime-".$duration_sec.") WHERE block='$this->block' AND date='$this->date' AND initTime>'$this->initTime'";
		$result = $db->actionquery($query);
		if($result) return $this->deleteDbData();
		else return false;

	}

}

?>
