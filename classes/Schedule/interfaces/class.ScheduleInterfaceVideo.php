<?php

class ScheduleInterfaceVideo {
	
	private static $sessionDate;

	private static function initSession() {
	
		if(isset($_SESSION['sessionDate']) && $_SESSION['sessionDate']) self::$sessionDate = $_SESSION['sessionDate'];
		elseif(isset($_GET['date'])) self::$sessionDate = (string) cleanVar($_GET['date']);
	        else self::$sessionDate = date("d/m/Y");
	
	}

	private static function setSessionDate($date) {

		$_SESSION['sessionDate'] = $date;
		self::initSession('sessionDate');

	}

	public static function manage() {

		if(!Auth::checkAuth()) header('Location: login.php');
		
		$date_set = (isset($_GET['date']))? (string) cleanVar($_GET['date']):null;
		if($date_set) self::setSessionDate($date_set);

		self::initSession();

		$id = null;
		$urlParams = array('a', 'act', 'ctgid', 'date');
		foreach($urlParams as $p) {
			$$p = null;
		}
		foreach($_GET as $k=>$v) {
			if(in_array($k,$urlParams)) $$k=cleanVar($v);
		}
	
		$schedule = new Schedule(dateToDbDate(self::$sessionDate, "/"));
		if($a=='remove') self::actionRemoveContent($schedule);
		elseif($a=='cp') self::actionCopySchedule();
		elseif($a=='save' && $act=='new') self::actionContent($schedule);
		elseif($a=='save' && $act=='newblock') self::actionBlockContent($schedule);
		elseif((int) $id && $a=='delete') self::actionDelItem($item);

		$buffer = "<script>";
		$buffer .= "function callbackCloseCalendar(inputFieldID) {
				if(inputFieldID=='date') location.href = 'admin.php?mng=prg&date='+$('date').value; 
			}";
		$buffer .= "</script>";

		$buffer .= "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\"><div style=\"float:left;margin-right:10px;\">"._("Palinsesto")."</div>".inputDate('date', self::$sessionDate, true)."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav_left\">";
		$buffer .= self::insertItem();
		$buffer .= self::insertBlock($schedule);
		$buffer .= self::copySchedule($schedule);
		$buffer .= self::removeContent($schedule);
		$buffer .= "</div>";
		$buffer .= "<div class=\"nav_right\" style=\"min-height:700px;\">";
		$buffer .= self::viewSchedule($schedule);
		$buffer .= "</div>";
		$buffer .= CLEAR;

		return $buffer;

	}
	
	public static function viewSchedule($s) {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$block_i = (isset($_GET['block_i']))? (int) cleanVar($_GET['block_i']):-1;
		$buffer = "<script type=\"text/javascript\">";

		$buffer .= "function execAccordion(block_i) {
				var blockAccordion = new Accordion($('blockAccordion'), $$('div.toggler'), $$('div.element'), {alwaysHide:false,show:block_i});
			};";
		$buffer .= "</script>";

		$buffer .= "<p class=\"title\">"._("Programmazione del ").self::$sessionDate."</p>\n";

		if(count($s->getItems())) {
			$buffer .= "<table style=\"width:100%\">\n";
			$buffer .= "<tr style=\"background-color:#000;\">\n";
			$buffer .= "<th style=\"color:#eee;width:15%;padding:5px;\">"._("Inizio")."</th><th style=\"color:#eee;width:15%;padding:5px\">"._("Fine")."</th><th style=\"color:#eee;width:60%;padding:5px\" colspan=\"3\">"._("Video")."</th>\n";
			$buffer .= "</tr>\n";
			$buffer .= "</table>";

			$buffer .= "<div id=\"blockAccordion\">\n";
			$prev_block = 0;
			$odd = true;

			foreach($s->getItems() as $sItem) {
				$video = new Video($sItem->itemId, TBL_VIDEO);
				$blockLimits = $s->getBlockLimits($sItem->block);
				if($odd) $css_class = "odd";
				else $css_class = "even";
				$line = ($prev_block!=$sItem->block);
				if($line) {
					if($prev_block) $buffer .= "</table></div>";
					$buffer .= "<div class=\"toggler\" style=\"\"><span style=\"float:left;width:15%;text-align:left;margin-right:10px;\">".secondsToTime($sItem->initTime)."</span><span style=\"float:left;width:15%;text-align:left\">".secondsToTime($blockLimits['end'])."</span>"._("Blocco")." $sItem->block &nbsp;".Icon::expand().CLEAR."</div>";
					$buffer .= "<div class=\"element\">";
					$buffer .= "<table style=\"width:100%\">\n";
				}
				$buffer .= "<tr class=\"".$css_class."\">";
				$buffer .= "<td style=\"width:15%\">".secondsToTime($sItem->initTime)."</td>";
				$buffer .= "<td style=\"width:15%\">".secondsToTime($sItem->endTime)."</td>";
				$buffer .= "<td style=\"width:60%\" colspan=\"3\"><span style=\"float:left;width:85%\">".htmlChars($video->title)."</span><span style=\"float:right;width:13%;text-align:right;\"><a href=\"admin.php?mng=prg&amp;a=remove&amp;remove_type=video&amp;rem_video=$sItem->id&amp;token=".generateFormToken('remitem'.$sItem->id)."\">".Icon::delete()."</a></span>".CLEAR."</td>";
				$buffer .= "</tr>\n";
				$prev_block = $sItem->block;
				$odd = !$odd;

			}
			$buffer .= "</table></div>\n";
			$buffer .= "</div>";

		}
		
		$buffer .= "<script>execAccordion(".$block_i.");</script>";
		return $buffer; 

	}

	
	private static function insertBlock($s) {

		$buffer = "<fieldset>";
		$buffer .= "<legend>"._("Inserimento blocco")."</legend>";

		$buffer .= "<form id=\"formnewblock\" name=\"formnewitem\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=prg&a=save&act=newblock\" onsubmit=\"return ValidateForm('formnewblock')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formnewblock')."\"/>";
	
		$buffer .= "<div class=\"label\"><label for=\"block\" class=\"req\">"._("Blocco")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\">".inputSelect('block', $s->getBlocks(), '', '', array(""=>""))."</div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"position_type\" class=\"req\">"._("Posizione")." ".STAR."</label></div>";
		$ra = array("free"=>_("libero"), "bind"=>_("legato"));
		$onmouseup = "onmouseup=\"sendPost('methodPointer.php?pt[ScheduleInterfaceVideo-positionVideo]', 'position_type='+$(this).value, 'contentPositionBlock', '', true, updateTooltip)\"";
		$buffer .= "<div class=\"input\">".inputRadio('position_type', $ra, 'free', $onmouseup)."</div>".CLEAR;

		$buffer .= "<div id=\"contentPositionBlock\">";
		$buffer .= self::positionVideo();
		$buffer .= "</div>";
		
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		
		return $buffer;
		
	}
	
	private static function removeContent($s) {

		$buffer = "<fieldset>";
		$buffer .= "<legend>"._("Rimozione contenuti")."</legend>";

		$buffer .= "<form id=\"formremcontents\" name=\"formremcontents\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=prg&a=remove\" onsubmit=\"return ValidateForm('formremcontents');\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formremcontents')."\"/>";
	
		$buffer .= "<div class=\"label\"><label for=\"remove_type\" class=\"req\">"._("Contenuto")." ".STAR."</label></div>";
		$ra = array("all"=>_("palinsesto"), "block"=>_("blocco"));
		$onmouseup = "onmouseup=\"sendPost('methodPointer.php?pt[ScheduleInterfaceVideo-removeBlock]', 'remove_type='+$(this).value, 'contentRemoveBlock', '', true)\"";
		$buffer .= "<div class=\"input\">".inputRadio('remove_type', $ra, 'all', $onmouseup)."</div>".CLEAR;

		$buffer .= "<div id=\"contentRemoveBlock\">";
		$buffer .= "</div>";
		
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("rimuovi")."\" onclick=\"return confirmSubmit();\"></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		
		return $buffer;
		
	}

	public function removeBlock() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$remove_type = (isset($_POST['remove_type']))? (string) cleanVar($_POST['remove_type']):null;
		if($remove_type=='all') return '';
		if($remove_type) {self::initSession('sessionDate');}
		$s = new Schedule(dateToDbDate(self::$sessionDate, "/"));

		$buffer = "<div class=\"label\"><label for=\"rem_block\" class=\"req\">"._("Blocco")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\">".inputSelect('rem_block', $s->getBlocks(), 'req', '', array(""=>""))."</div>".CLEAR;

		return $buffer;

	}

	private static function insertItem() {

		
		$buffer = "<fieldset>";
		$buffer .= "<legend>"._("Inserimento video")."</legend>";
		$onclick = "if(!window.myWinInsert || !window.myWinInsert.showing) {window.myWinInsert = new layerWindow({'title':'"._("Inserimento video")."', 'url':'methodPointer.php?pt[ScheduleInterfaceVideo-formInsertVideo]', 'width':600, 'height':450, 'closeButtonUrl':'img/icons/ico_close_small.gif', 'destroyOnClose':true});window.myWinInsert.display($(this), {'left':getViewport().cX-600/2, 'top':getViewport().cY-400/2});}";
		$buffer .= "<div style=\"text-align:center\"><button onclick=\"$onclick\">"._("clicca per inserire")."</button></div>";
		$buffer .= "</fieldset>";
		
		return $buffer;
		
	}

	public function formInsertVideo() {
		
		if(!Auth::checkAuth()) exit();

		$buffer = "<form id=\"formnewitem\" name=\"formnewitem\" method=\"post\" action=\"admin.php?mng=prg&a=save&act=new\" onsubmit=\"return ValidateForm('formnewitem')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formnewitem')."\"/>";
	
		$ctg = new Category(null, TBL_CTG);
		$more = "onchange=\"sendPost('methodPointer.php?pt[ScheduleInterfaceVideo-selectVideo]', 'ctg='+$(this).value, 'videoSelection', '', true);$('vname').set('value', '');\"";
		$buffer .= "<div class=\"label\"><label for=\"category\">"._("Filtro categoria")."</label></div>";
		$query = "SELECT id FROM ".TBL_CTG." WHERE id NOT IN (SELECT parent FROM ".TBL_CTG.")";
		$ctg_ordered = $ctg->inputTreeArray($query, array('short'=>true));
		$buffer .= "<div class=\"input\">".inputSelect('cname', $ctg_ordered, '', $more, array("0"=>""))."</div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"video\" class=\"req\">"._("Video")." ".STAR."</label></div>";
		$onkeyup = "onkeyup=\"sendPost('methodPointer.php?pt[ScheduleInterfaceVideo-selectVideo]', 'vname='+$(this).value+'&ctg='+$(cname).value+'&vdesc='+($('vdesc').checked?'1':'0'), 'videoSelection', '', true)\"";
		$onclick = "onclick=\"sendPost('methodPointer.php?pt[ScheduleInterfaceVideo-selectVideo]', 'vname='+$('vname').value+'&ctg='+$(cname).value+'&vdesc='+($('vdesc').checked?'1':'0'), 'videoSelection', '', true)\"";
		$buffer .= "<div class=\"input tooltip\" title=\""._("filtro video")."\"><input alt=\""._("filtro video")."\" type=\"text\" id=\"vname\" name=\"vname\" style=\"width:80px;font-size:10px;\" $onkeyup/> &#160; <input type=\"checkbox\" $onclick name=\"vdesc\" id=\"vdesc\" value=\"1\" /> "._("cerca in note e descrizione")."</div>".CLEAR;
		$buffer .= "<div id=\"videoSelection\" style=\"width:400px;max-height:240px;background-color: #fff;overflow:auto;border:1px solid #aaa;margin: auto;margin-bottom:8px;\">".self::selectVideo()."</div>".CLEAR;
	
		$buffer .= "<div class=\"label\"><label for=\"position_type\" class=\"req\">"._("Posizione")." ".STAR."</label></div>";
		$ra = array("free"=>_("libero"), "bind"=>_("legato"));
		$onmouseup = "onmouseup=\"sendPost('methodPointer.php?pt[ScheduleInterfaceVideo-positionVideo]', 'position_type='+$(this).value, 'contentPosition', '', true, updateTooltip);\"";
		$buffer .= "<div class=\"input\">".inputRadio('position_type', $ra, 'free', $onmouseup)."</div>".CLEAR;

		$buffer .= "<div id=\"contentPosition\">";
		$buffer .= self::positionVideo();
		$buffer .= "</div>";

		
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";

		return $buffer;
	}

	public static function copySchedule($s) {
	
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$buffer = "<fieldset>";
		$buffer .= "<legend>"._("Copia palinsesto")."</legend>";

		$buffer .= "<form id=\"cpschedule\" name=\"cpschedule\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=prg&a=cp\" onsubmit=\"return ValidateForm('cpschedule')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('cpschedule')."\"/>";
		
		$buffer .= "<div class=\"label\"><label for=\"cp_date\" class=\"req\">"._("Copia da")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\">".inputDate('cp_date', '', true)."</div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("copia")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";

		return $buffer;

	}

	public static function selectVideo() {

		if(!Auth::checkAuth()) header('Location: login.php');

		self::initSession();
	
		$buffer = "<table style=\"width:100%;#width:90%\">";

		$ctg = (isset($_POST['ctg']))? (int) cleanVar($_POST['ctg']):null;
		$vname = (isset($_POST['vname']))? (string) cleanVar($_POST['vname']):null;
		$vdesc = (isset($_POST['vdesc']))? (int) cleanVar($_POST['vdesc']):0;
		$query = "SELECT id FROM ".TBL_VIDEO.(($ctg)?" WHERE category='$ctg'":"")." ORDER BY date DESC,title";
		$videos = Video::getItems($query, TBL_VIDEO);

		$c = 0;
		$odd = true;
		foreach($videos as $video) {
			if(!$vname || ($vname && preg_match("#$vname#", $video->title) || ($vname && $vdesc && preg_match("#$vname#", $video->description)) || ($vname && $vdesc && preg_match("#$vname#", $video->notes)))) {
				$class  = ($odd)?"odd":"even";
				$buffer .= "<tr class=\"$class\">";
				$buffer .= "<td style=\"width:62px;\">";
				if($video->image) $buffer .= "<img width=\"60px\" src=\"".REL_UP_IMG."/$video->image\" alt=\"$video->duration\" title=\"$video->duration\" class=\"tooltip\"/>";
				else $buffer .= $video->duration;
				$buffer .= "</td>";
				$buffer .= "<td>".htmlChars($video->title)."<br/>".$video->name."</td>";
				$buffer .= "<td><input type=\"radio\" value=\"$video->id\" name=\"video\" /></td>";
				$buffer .= "</tr>";
				$odd = !$odd;
				$c++;
			}
		}

		if(!$c) $buffer .= "<tr><td>"._("nessun video disponibile")."</td></tr>";
		$buffer .= "</table>";

		return $buffer;
	}

	public static function positionVideo() {

		if(!Auth::checkAuth()) header('Location: login.php');
		
		$position_type = (isset($_POST['position_type']))? (string) cleanVar($_POST['position_type']):null;
		if($position_type) {self::initSession('sessionDate');}
		$s = new Schedule(dateToDbDate(self::$sessionDate, "/"));

		$buffer = "";
		
		if(!$position_type || $position_type=='free') {

			$buffer .= "<div class=\"label\"><label for=\"position\" class=\"req\">"._("Ora")." ".STAR."</label></div>";
			$buffer .= "<div class=\"input\"><input class=\"tooltip\" title=\"hh:mm:ss\" type=\"text\" name=\"position\" value=\"\" style=\"width:60px;\"/></div>".CLEAR;
		}
		else {
			$buffer .= "<div class=\"label\"><label for=\"bind\" class=\"req\">"._("Lega")." ".STAR."</label></div>";
			$ra = array("before"=>_("inizio"), "after"=>_("fine"));
			$buffer .= "<div class=\"input\">".inputRadio('bind', $ra, 'after', '')."</div>".CLEAR;
			
			$buffer .= "<div class=\"label\"><label for=\"position\" class=\"req\">"._("Al blocco")." ".STAR."</label></div>";
			$buffer .= "<div class=\"input\">".inputSelect('position', $s->getBlocks(), '', '', array(""=>""))."</div>".CLEAR;

		}

		return $buffer;
	
	}

	private function info() {
		
		$buffer = "<div><p><b>"._("Informazioni")."</b></p>"._(".")."</div>";
		return $buffer;
	}

	private function actionBlockContent($s) {

		$error = null;
		$i = $end_time = $init_time = 0;

		if (!verifyFormToken('formnewblock')) {
  			die('CSRF Attack detected.');
		}

		$itemId = (int) cleanVar($_POST['block']);
		$position_type = (string) cleanVar($_POST['position_type']);
		$bind = (isset($_POST['bind']))? (string) cleanVar($_POST['bind']):null;
		$position = (string) cleanVar($_POST['position']);

		if(!($itemId && $position_type && $position)) {header('Location: admin.php?mng=prg&error=10');exit;}

		$bItems = $s->getBlockItems($itemId);

		if($position_type == 'free') {
			$newBlockItems = array();
			$block = $s->getLastBlock()+1;
			foreach($bItems as $bitem) {
				$video = new Video($bitem->itemId, TBL_VIDEO);
				$duration_sec = timeToSeconds($video->duration);
				$init_time = ($i)? $end_time:timeToSeconds($position);
				$end_time = $init_time + $duration_sec;
				if($s->checkAvailableSpace($init_time, $end_time)) {
					$newBlockItems[$i] = new ScheduleItem(null);
					$newBlockItems[$i]->block = $block;
					$newBlockItems[$i]->itemId = $video->id;
					$newBlockItems[$i]->date = dateToDbDate(self::$sessionDate, "/");
					$newBlockItems[$i]->initTime = $init_time;
					$newBlockItems[$i]->endTime = $end_time;
				}
				else {header('Location: admin.php?mng=prg&error=20');exit;}
				$i++;
			}
		}
		elseif($position_type == 'bind') {
			$block = $position;
			$limits = $s->getBlockLimits($block);
			if($bind == 'before') {
				$end_time_last = $limits['start'];
				foreach($bItems as $bitem) {
					$video = new Video($bitem->itemId, TBL_VIDEO);
					$duration_sec = timeToSeconds($video->duration);
					$end_time = ($i)? $init_time:$end_time_last;
					$init_time = $end_time-$duration_sec;
					if($s->checkAvailableSpace($init_time, $end_time)) {
						$newBlockItems[$i] = new ScheduleItem(null);
						$newBlockItems[$i]->block = $block;
						$newBlockItems[$i]->itemId = $video->id;
						$newBlockItems[$i]->date = dateToDbDate(self::$sessionDate, "/");
						$newBlockItems[$i]->initTime = $init_time;
						$newBlockItems[$i]->endTime = $end_time;
					}
					else {header('Location: admin.php?mng=prg&error=20');exit;}
					$i++;
				}
			}
			elseif($bind == 'after') {
				$init_time_first = $limits['end'];
				foreach($bItems as $bitem) {
					$video = new Video($bitem->itemId, TBL_VIDEO);
					$duration_sec = timeToSeconds($video->duration);
					$init_time = ($i)? $end_time:$init_time_first;
					$end_time = $init_time + $duration_sec;
					if($s->checkAvailableSpace($init_time, $end_time)) {
						$newBlockItems[$i] = new ScheduleItem(null);
						$newBlockItems[$i]->block = $block;
						$newBlockItems[$i]->itemId = $video->id;
						$newBlockItems[$i]->date = dateToDbDate(self::$sessionDate, "/");
						$newBlockItems[$i]->initTime = $init_time;
						$newBlockItems[$i]->endTime = $end_time;
					}
					else {header('Location: admin.php?mng=prg&error=20');exit;}
					$i++;
				}
			}
		}

		foreach($newBlockItems as $newItem) {
			$newItem->updateDbData();
		}
		
		header('Location: admin.php?mng=prg');
				
	}

	private function actionContent($s) {
	
		$error = null;

		if (!verifyFormToken('formnewitem')) {
  			die('CSRF Attack detected.');
		}

		$itemId = (int) cleanVar($_POST['video']);
		$position_type = (string) cleanVar($_POST['position_type']);
		$bind = (isset($_POST['bind']))? (string) cleanVar($_POST['bind']):null;
		$position = (string) cleanVar($_POST['position']);

		if(!($itemId && $position_type && $position)) {header('Location: admin.php?mng=prg&error=10');exit;}

		$video = new Video($itemId, TBL_VIDEO);
		$duration_sec = timeToSeconds($video->duration);

		if($position_type == 'free') {
			$init_time = timeToSeconds($position);
			$end_time = $init_time + $duration_sec;
			$block = $s->getLastBlock()+1;
		}
		elseif($position_type == 'bind') {
			$block = $position;
			$limits = $s->getBlockLimits($block);
			if($bind=='before') {
				$end_time = $limits['start'];
				$init_time = $end_time - $duration_sec;
			}
			elseif($bind=='after') {
				$init_time = $limits['end'];
				$end_time = $init_time + $duration_sec;
			}
		}
		
		if(!$s->checkAvailableSpace($init_time, $end_time))
			{header('Location: admin.php?mng=prg&error=20');exit;}

		$sItem = new ScheduleItem(null);
		$sItem->date = dateToDbDate(self::$sessionDate, "/");
		$sItem->initTime = $init_time;
		$sItem->endTime = $end_time;
		$sItem->itemId = $video->id;
		$sItem->block = $block;
		
		$sItem->updateDbData();
		
		$block_i = array_search($sItem->block, $s->getBlockTimeOrder());

		header('Location: admin.php?mng=prg&block_i='.$block_i);

	}
	
	private static function actionRemoveContent($s) {

		$remove_type = (string) cleanVar($_REQUEST['remove_type']);
		$rem_video = (isset($_GET['rem_video']))? (int) cleanVar($_GET['rem_video']):null;
		$rem_block = (isset($_POST['rem_block']))? (int) cleanVar($_POST['rem_block']):null;
		$block_i = -1;
	
		if($remove_type == 'video') {
			if(!verifyTokenGet('remitem'.$rem_video)) die("CSRF attack detected");
			$sItem = new ScheduleItem($rem_video);
			$block_i = array_search($sItem->block, $s->getBlockTimeOrder()); 
			$sItem->deleteItem();
		}
		elseif($remove_type == 'block') {
			if(!verifyFormToken('formremcontents')) die("CSRF attack detected");
			$s->removeBlock($rem_block);
		}
		elseif($remove_type == 'all') {
			if(!verifyFormToken('formremcontents')) die("CSRF attack detected");
			$s->remove();	
		}

		header('Location: admin.php?mng=prg&block_i='.$block_i);


	}

	private function actionCopySchedule() {

		if (!verifyFormToken('cpschedule')) {
  			die('CSRF Attack detected.');
		}

		$cp_date = (string) cleanVar($_POST['cp_date']);

		if(!$cp_date) {header('Location: admin.php?mng=prg&error=10');exit;}

		$s = new Schedule(dateToDbDate($cp_date, "/"));

		foreach($s->getItems() as $item) {

			$nItem = new ScheduleItem(null);
			$nItem->date = dateToDbDate(self::$sessionDate, "/");
			$nItem->initTime = $item->initTime;
			$nItem->endTime = $item->endTime;
			$nItem->itemId = $item->itemId;
			$nItem->block = $item->block;
			$nItem->updateDbData();
		} 

		header('Location: admin.php?mng=prg');

	}

	private static function timeToSeconds($time) {
		
		$time_array = explode(":", $time);
		$hours = $time_array[0];
		$minutes = $time_array[1];
		$seconds = $time_array[2];
		
		$timeseconds = $seconds + 60*$minutes + 3600*$hours;
		
		return $timeseconds;

	}
	
	private static function secondsToTime($seconds) {
		
		$hours = floor($seconds/3600);
		$minutes = floor(fmod($seconds,3600) / 60);
		$seconds = fmod(fmod($seconds,3600), 60);
		
		if($hours<10) $hours = '0'.$hours;
		if($minutes<10) $minutes = '0'.$minutes;
		if($seconds<10) $seconds = '0'.$seconds;
		
		$time = $hours.":".$minutes.":".$seconds; 
		
		return $time;
		
	}


}

?>
