<?php

require_once('class.ScheduleItem.php');

class Schedule {

	private $_items, $_date;

	function __construct($date) {
		
		$this->_items = $this->initItems($date);
		$this->_date = $date;

	}		
	
	public function getItems() {

		return $this->_items;

	}

	private function initItems($date) {
	
		$results = array();
		$db = new Db;
		$query = "SELECT id FROM ".TBL_SCHEDULE." WHERE date='$date' ORDER BY initTime ASC";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$id = (int) $b['id'];
				$results[$id] = new ScheduleItem($id);
			}
		}

		return $results;
	}
	
	public function getItemsAfterTime($seconds) {

		$results = array();
		$db = new Db;		
		$query = "SELECT * FROM ".TBL_SCHEDULE." WHERE date='".$this->_date."' AND endTime>='".$seconds."' ORDER BY initTime";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$id = (int) $b['id'];
				$results[$id] = new ScheduleItem($id);
			}
		}

		return $results;
	}
	
	public function getNextItemDateIndependent() {

		$result = null;
		$db = new Db;		
		$query = "SELECT * FROM ".TBL_SCHEDULE." WHERE date>'".$this->_date."' ORDER BY initTime ASC LIMIT 0,1";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$id = (int) $b['id'];
				$result = new ScheduleItem($id);
			}
		}

		return $result;
	}

	public function getNextFirstItem($seconds) {
		
		$result = null;
		$db = new Db;		
		$query = "SELECT id FROM ".TBL_SCHEDULE." WHERE date='$this->_date' AND initTime>='$seconds' ORDER BY initTime ASC LIMIT 0,1";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$id = (int) $b['id'];
				$result = new ScheduleItem($id);
			}
		}

		return $result;
	}
	
	public function getOnAirItem($seconds) {
		
		$result = null;
		$db = new Db;		
		$query = "SELECT id FROM ".TBL_SCHEDULE." WHERE date='$this->_date' AND initTime<='$seconds' AND endTime>='$seconds' LIMIT 0,1";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$id = (int) $b['id'];
				$result = new ScheduleItem($id);
			}
		}

		return $result;
	}
	
	public function getBlocks() {

		$results = array();
		$db = new Db;
		$query = "SELECT DISTINCT(block) FROM ".TBL_SCHEDULE." WHERE date='$this->_date' ORDER BY block ASC";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$results[$b['block']] = $b['block'];
			}
		}

		return $results;
	}

	public function getBlockItems($block)	{
		
		$results = array();
		$db = new Db;
		$query = "SELECT id FROM ".TBL_SCHEDULE." WHERE date='$this->_date' and block='$block' ORDER BY initTime ASC";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$results[$b['id']] = new ScheduleItem($b['id']);
			}
		}

		return $results;

	}

	public function getBlockTimeOrder() {

		$block_ordered = array();
		$db = new Db;
		$query = "SELECT DISTINCT(block), initTime FROM ".TBL_SCHEDULE." WHERE date='$this->_date' ORDER BY initTime ASC";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			$block_c = 0;
			foreach($a as $b) {
				$block = htmlChars($b['block']);
				if($block != $block_c) $i=0;
				if($i==0) $block_ordered[] = $block;
				$block_c = $block;
				$i++;
			}
		}
		return $block_ordered;
	}

	public function getLastBlock() {

		$last_block = 0;
		
		$db = new Db;
		$query = "SELECT max(block) as last_block FROM ".TBL_SCHEDULE." WHERE date='$this->_date'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$last_block = $b['last_block'];
			}
		}
		
		return $last_block;


	}

	public function getBlockLimits($block) {

		$limits = array();
		
		$db = new Db;
		$query = "SELECT initTime FROM ".TBL_SCHEDULE." WHERE block='$block' AND date='$this->_date' ORDER BY initTime ASC LIMIT 0,1";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			$limits['start'] = $a[0]['initTime'];
		}
		
		$query = "SELECT endTime FROM ".TBL_SCHEDULE." WHERE block='$block' AND date='$this->_date' ORDER BY initTime DESC LIMIT 0,1";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			$limits['end'] = $a[0]['endTime'];
		}

		return $limits;

	}
	
	public function removeBlock($block) {
	
		$db = new Db;
		$query = "DELETE FROM ".TBL_SCHEDULE." WHERE date='$this->_date' AND block='$block'";
		return  $db->actionquery($query);

	}

	public function remove() {
		
		$db = new Db;
		$query = "DELETE FROM ".TBL_SCHEDULE." WHERE date='$this->_date'";
		return  $db->actionquery($query);
	
	}

	public function checkAvailableSpace($init_time, $end_time) {

		$db = new Db;
		$query = "SELECT id FROM ".TBL_SCHEDULE." WHERE date='$this->_date' AND ((initTime<='$init_time' AND endTime>'$init_time') OR (initTime<'$end_time' AND endTime>='$end_time'))";
		$a = $db->selectquery($query);
		return !(sizeof($a)>0);

	}

	}

?>
