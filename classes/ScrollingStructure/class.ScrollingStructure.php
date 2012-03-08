<?php
class ScrollingStructure {

	private $_grip_sx, $_grip_dx;
	
	private $_name, $_cont_width, $_cont_height, $_item_width, $_item_height, $_item_margin, $_item_bkg, $_scrolling_speed, $_obj;  
	
	function __construct($name, $width_container, $height_container, $width_item, $height_item, $margin, $speed, $obj) {
		
		
		$this->setProperties($name, $width_container, $height_container, $width_item, $height_item, $margin, $speed, $obj);
				
		$this->_grip_sx = REL_IMG."/bgrip_sx.gif";
		$this->_grip_dx = REL_IMG."/bgrip_dx.gif";
		
	}
	
	private function setProperties($name, $width_container, $height_container, $width_item, $height_item, $margin, $speed, $obj) {
		
		$this->_name = $name;
		$this->_cont_width = $width_container;
		$this->_cont_height = $height_container;
		$this->_item_width = $width_item;
		$this->_item_height = $height_item;
		$this->_item_margin = $margin;
		$this->_item_bkg = $item_bkg;
		$this->_scrolling_speed = $speed;
		$this->_obj = $obj;
	}
	
	/*
	 * INPUT
	 * - string $query			the query used to get scrollable items content (used to get item numbers and name paramether to set)
	 * - string $name_field		field used to set is value as name paramether of item DOM object
	 * - string $function		JS functon to call back after clicking on an item, without parenthesis
	 * - string $startEvent 	the JS event on arrow buttons that causes the scrolling to start
	 * - string $stopEvent 		the JS event arrow buttons that causes the scrolling to stop
	 * 
	 * OUTPUT
	 * - html   the whole scrolling structure
	 */
	public function createScrollingStructure($query, $name_field, $function, $startEvent, $stopEvent) {
		
		$db = new Db;
		$a = $db->selectquery($query);
		$totItem = sizeof($a);
		
		$scroll_ctrl_margin = 5;
		$slider_width = ($totItem * ($this->_item_width + $this->_item_margin) - $this->_item_margin + ($this->_cont_width-$this->_item_width));  
		
		$buffer .= "<script>";
		$buffer .= "window.addEvent('domready', function(){
					window.videoScrolling = new ScrollingStructure('".$this->_name."', '".$this->_name."scroll_sx', '".$this->_name."scroll_dx', {duration:".($totItem*$this->_scrolling_speed).", startEvent:'$startEvent', stopEvent:'$stopEvent'});
					window.videoScrolling.start($function);
				  })";
		$buffer .= "</script>";
			
		$buffer .= "<div class=\"hListContainer\" style=\"height: ".($this->_cont_height)."px;width:".($this->_cont_width+14*2+$scroll_ctrl_margin*4)."px;\">\n";
		// left scroller
		$buffer .= "<div id=\"".$this->_name."scroll_sx\" style=\"cursor:pointer;position:relative;float:left;top:50%;margin-top:-16px;margin-right:".$scroll_ctrl_margin."px;margin-left:".$scroll_ctrl_margin."px;width:14px;height:32px;background:url('".$this->_grip_sx."');\">";
		$buffer .= "</div>\n";
				
		// container
		$buffer .= "<div id=\"".$this->_name."\" style=\"float:left;width:".$this->_cont_width."px;height: ".$this->_cont_height."px;overflow:auto;margin: 0 auto;margin-top:0px;overflow-x: hidden;overflow-y: hidden;\">\n"; // margin-top=1px if in IE the scrolling causes a movement along y axes => and then $this->_cont_height+2 for hListContainer height.
		
		// slider
		$buffer .= "<div style=\"width:".$slider_width."px;margin-top:".(($this->_cont_height - $this->_item_height)/2)."px;margin-left:".(($this->_cont_width-$this->_item_width)/2)."px;\">\n";
		
		$i=1;
		foreach($a as $b) {
			if($i==$totItem) $margin_right = 0;
			else $margin_right = 0;
			if($i==1) $margin_left = 0;
			else $margin_left = $this->_item_margin;
			
			$id = $b['id'];
			$field = $b[$name_field];
			// item
			$buffer .= "<div class=\"itemContainer\" id=\"".$this->_name."item$id\" name=\"".$field."\" style=\"float:left;width:".$this->_item_width."px;height:".$this->_item_height."px; margin-left:".$margin_left."px;margin-right:".$margin_right."px;\">\n";
			$buffer .= $this->_obj->printSliderItemContent($id);
			$buffer .= "</div>\n";
			$i++;
		}
		$buffer .= "<span class=\"null\"></span>\n";
		$buffer .= "</div>\n"; 
		
		$buffer .= "</div>\n";
		
		$buffer .= "<div id=\"".$this->_name."scroll_dx\" style=\"cursor:pointer;position:relative;float:left;top:50%;margin-top:-16px;margin-left:".$scroll_ctrl_margin."px;margin-right:".$scroll_ctrl_margin."px;width:14px;height:32px;background:url('".$this->_grip_dx."');\">";
		$buffer .= "</div>\n";
		
		$buffer .= "<div class=\"null\"></div>\n";
		
		$buffer .= "</div>\n";
		
		return $buffer;
	}
	
	
}
?>
