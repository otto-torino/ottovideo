<?php

class ReportInterface {

	private static $_vfp = 25;

	public static function manage() {

		if(!Auth::checkAuth()) header('Location: login.php');

		$id = null;
		$urlParams = array('a', 'id');
		foreach($urlParams as $p) {
			$$p = null;
		}
		foreach($_GET as $k=>$v) {
			if(in_array($k,$urlParams)) $$k=cleanVar($v);
		}


		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Visualizzazione video ondemand")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div>";
		$buffer .= self::listViews();
		$buffer .= "</div>";
		$buffer .= CLEAR;

		return $buffer;

	}

	private static function listViews() {

		$query = "SELECT id FROM ".TBL_VIDEO." ORDER BY views DESC,title";

		$items = Video::getItems($query, TBL_VIDEO);

		if(count($items)) {
			$pl = new PageList(self::$_vfp, $items);
			$buffer = "<table class=\"generic\" style=\"width:100%; margin-top: 10px;\">";
			$buffer .= "<tr><th></th><th>"._("Titolo")."</th><th>"._("Vis.")."</th><th>"._("Link")."</th></tr>";
			$end = $pl->start()+self::$_vfp > count($items) ? count($items) : $pl->start()+self::$_vfp;
			for($i=$pl->start(); $i<$end; $i++) {
				$item = $items[$i];
				$buffer .= "<tr>";
				$buffer .= "<td><img src=\"".REL_UP_IMG."/".$item->image."\" /></td>";
				$buffer .= "<td>";
				if($item->type == 1) {
					$buffer .= "<span class=\"icon video_icon\">"._("video")."</span>";
				}
				elseif($item->type == 2) {
					$buffer .= "<span class=\"icon spot_icon\">"._("spot")."</span>";
				}

				$buffer .= "<a href=\"admin.php?mng=item&id=".$item->id."\">".htmlChars($item->title)."</a>";

				$buffer .= "<br />";

				$buffer .= dbDatetimeToDate($item->date, "/")." ".$item->duration; 

				$buffer .= "<br />";

				if($item->new=='yes') {
					$buffer .= "<span class=\"icon new_icon\">new</span>";
				}
				if($item->ondemand=='yes') {
					$buffer .= "<span class=\"icon ondemand_icon\">ondemand</span>";
				}
				if($item->bind_spot) {
					$buffer .= "<span class=\"icon bind_spot_icon\">spot</span>";
				}
				$buffer .= "</td>";
				$buffer .= "<td>";
				if($item->type==1) $buffer .= htmlChars($item->views);
				elseif($item->type==2) {
					$onclick = "openInfoLayer('$item->id')";
					$buffer .= "<span id=\"info_views_".$item->id."\" class=\"link\" onclick=\"$onclick\">".htmlChars($item->views)."</span>";
				}
				$buffer .= "</td>";
				$url = preg_match("#\?.+#", Configuration::getValue('parent_url')) 
					? Configuration::getValue('parent_url')."&vid=".$item->id 
					: Configuration::getValue('parent_url')."?vid=".$item->id;
				$buffer .= "<td><span class=\"link\" onclick=\"copyToClipboard('".$url."');\">".Icon::copy()."</a></td>";
				$buffer .= "</tr>";
			}
			$buffer .= "</table>";
			$buffer .= "<div style=\"margin-left:15px;\">".$pl->listReferenceGINO("mng=rep")."</div>";
		}
		else {
			$buffer = "<p>"._("Non ci sono report da visualizzare")."</p>";
		}

		return $buffer;	

	}

}

?>
