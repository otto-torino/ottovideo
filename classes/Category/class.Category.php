<?php

class Category extends propertyObject {

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
		else return array('id'=>null, 'name'=>null, 'parent'=>null, 'description'=>null, 'public'=>null);
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
				$res[] = new Category($b['id'], $this->_tbl_data);
			}
		}

		return $res;

	}

	public function printTree($parent, $link, $varname='id') {

		$db = new db;
		
		$GINO = '';

		$query = "SELECT id, name FROM ".$this->_tbl_data." WHERE parent='$parent' ORDER BY name";
		$a = $db->selectquery($query);

		if(sizeof($a)>0) {
			$GINO = ($parent!=0)?"<ul>\n":"<ul id=\"ctgTree\">\n"; 
			foreach($a as $b) {
				$id = htmlChars($b['id']);
				$name = htmlChars($b['name']);
				$class = ($this->id==$id)?"selectedVoice":"";
				$GINO .= "<li class=\"$class\"><a href=\"".$link."$varname=$id\">".$name."</a>";
				$GINO .= $this->printTree($id, $link, $varname);
				$GINO .= "</li>\n";
			}
			$GINO .= "</ul>\n"; 
		}
		else if($parent==0) $GINO = "<div><p>"._("Nessuna categoria registrata")."</p></div>";

		return $GINO;
	}

	public function getChildren($all=false) {

		$children = array();

		$query = "SELECT id FROM ".$this->_tbl_data." WHERE parent='{$this->_p['id']}' ORDER BY name";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$ctg = new Category($b['id'], $this->_tbl_data);
				$children[$b['id']] = $ctg;
				if($all) $children = array_merge($children, $ctg->getChildren($all));
			}
		}

		return $children;
	}
	
	public function inputTreeArray($query, $options=null) {
		
		$short = $this->getOpt($options, 'short');
		$db = new db;
		$ctg_ordered = array();
		
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$i=0;
				$id = htmlChars($b['id']);
				$aCtg = new Category($id, $this->_tbl_data);
				$value_init = htmlInput($aCtg->name);
				$value = "";
				$ctg_parent_tree = $aCtg->ctgParentTree();
				foreach($ctg_parent_tree as $pCtg) {
					if($short && count($ctg_parent_tree)>1 && $i==0) $value .= htmlInput($pCtg->name)." ... ";
					elseif($short && $i>0) $value .= '';
					else $value .= htmlInput($pCtg->name)." - ";
					$i++;
				}
				$ctg_ordered[$id] = $value.$value_init;
			}
		}
		
		asort($ctg_ordered);

		return $ctg_ordered;
	}
	
	private function ctgParentTree() {

		$db = new db;
		$ctg_parent_tree = array();
		$parent = $this->parent;

		while($parent!=0) {
			$pCtg = new Category($parent, $this->_tbl_data);
			$ctg_parent_tree[] = $pCtg;
			$parent = $pCtg->parent;
		}
		$ctg_parent_tree = array_reverse($ctg_parent_tree);

		return $ctg_parent_tree;
	}

	public function getEndTreeCategories() {
	
		$results = array();

		$db = new db;
		$query = "SELECT id FROM ".$this->_tbl_data." WHERE id NOT IN (SELECT parent FROM ".$this->_tbl_data.")";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				$results[$b['id']] = new Category($b['id']);
			}
		}

		return $results;

	}

}

?>
