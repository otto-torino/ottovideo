<?php

class OnAir {
	
	public static function render($container) {

		$contents = '';

		$l = new Layout('onair');
		$options = array('viewType'=>'onair');
		$p = new Player($options);

		$pb = "<div id=\"$container\" style=\"width:".$l->pWidth."px;height:".$l->pHeight."px;\"></div>";
		$pb .= $p->render('player');

		$lb = self::renderList($l);
		
		list($width, $height) = self::getDimensions($l);
		
		if(preg_match("#pt#i", $l->layout)) $contents .= $pb.$lb;
		elseif(preg_match("#pb#i", $l->layout)) $contents .= $lb.$pb;
		elseif(preg_match("#pl#i", $l->layout)) {
			$contents .= "<div style=\"float:left;width:".$l->pWidth."px\">".$pb."</div>";			
			$contents .= "<div style=\"float:left;width:".$l->lWidth."px\">".$lb."</div>";
			$contents .= CLEAR;
		}
		elseif(preg_match("#pr#i", $l->layout)) {
			$contents .= "<div style=\"float:left;width:".$l->lWidth."px\">".$lb."</div>";			
			$contents .= "<div style=\"float:left;width:".$l->lWidth."px\">".$pb."</div>";
			$contents .= CLEAR;
		}
		$buffer = "<div id=\"onAirContainer\" style=\"text-align:left;width:".$width."px;height:".$height."px;\">";
		$buffer .= $contents;
		$buffer .= "</div>\n";

		return $buffer;

	}
	
	public static function renderList($l) {
		
		date_default_timezone_set("Europe/Rome");
		$date = date("Y-m-d");
		
		$buffer = self::jsPolling('onair');
		$buffer .= "<div id=\"jsCode\" style=\"height:0px;\"></div>";
		$buffer .= self::scheduleList();
		
		$buffer .= "<script>window.vList = new vScrollingList('scheduleList', 10, ".($l->lHeight).", ".(Configuration::getValue('imgHeightAir')+20).", {list_width: null, tr_duration: ".$l->lSpeed."})</script>";

		return $buffer;
				
	}
	
	public static function scheduleList() {
		
		$date = date("Y-m-d");
		$seconds = timeToSeconds(date("H:i:s"));
		$s = new Schedule($date);
		$items = $s->getItemsAfterTime($seconds);
		
		if(count($items)>0) {
			$buffer = "<ul id=\"scheduleList\">\n";
			$prev_block = 0;
			$class1 = "block_odd";$class2 = "block_even";
			$act_class = '';
			foreach($items as $item) {
				$line = ($prev_block!=$item->block)?"border-top:1px solid #dddddd":'';

				if($prev_block==0) $class = $class1;
				elseif($line) $class = ($act_class==$class1)?$class2:$class1;

				$buffer .= "<li>".self::itemCard($item)."</li>";
				$prev_block = $item->block;
				$act_class = $class;				
				
			}
			$buffer .= "</ul>";
		}
		else {
			$item = $s->getNextItemDateIndependent();
			if(!$item) {
				$buffer = "<ul id=\"scheduleList\">\n";
				$buffer .= "<li><div class=\"itemContainer\"><div class=\"videoTitle\">"._("Nessun contenuto in palinsesto")."</div></div></li>";
				$buffer .= "</ul>";
			}
			else {
				$buffer = "<ul id=\"scheduleList\">\n";
				$buffer .= "<li>".self::itemCard($item)."</li>";
				$buffer .= "</ul>";
					
			}
		}

		return $buffer;
		
	}

	private static function itemCard($item) {

		date_default_timezone_set('Europe/Rome');

		$video = new Video($item->itemId, TBL_VIDEO);
		$buffer = "<div class=\"itemContainer\" style=\"height:".(Configuration::getValue('imgHeightAir')+20)."px\">";

		$buffer .= "<table style=\"width:100%\">";
		$buffer .= "<tr>";
		$buffer .= "<td style=\"width:".(Configuration::getValue('imgWidthAir')+10)."px\">";
		$buffer .= "<div title=\"".htmlChars($video->ml('description'))."\" class=\"videoImg tooltip\" style=\"background-image:url('".REL_UP_IMG."/".$video->image."'); background-position:center center;background-repeat:no-repeat; width:".(Configuration::getValue('imgWidthAir')+10)."px;height:".(Configuration::getValue('imgHeightAir')+20)."px;\" />";
		$buffer .= "</div>\n";
		$buffer .= "</td>";
		$buffer .= "<td>";
		$buffer .= "<div class=\"videoStartTime\">";
		if($item->date != date("Y-m-d")) $buffer .= dbDateToDate($item->date, "/")." ";
		$buffer .= secondsToTime($item->initTime);
		$buffer .= "</div>";
		$buffer .= "<div class=\"videoTitle\">".htmlChars($video->ml('title'))."</div>";
		$buffer .= "</td>";
		$buffer .= "</tr>";
		$buffer .= "</table>";
		$buffer .= "</div>";

		return $buffer;

	}

	/* SCROLLING TEXT
	public static function onAirBox() {
		
		$layoutObj = new Layout('onairbox');
		
		$buffer = "<div id=\"jsCode\" style=\"height:0px;\"></div>";
		$buffer .= "<div id=\"onAirBoxContainer\" style=\"overflow:hidden;width:".$layoutObj->bWidth."px;height:".$layoutObj->bHeight."px;\">";
		$buffer .= "<div id=\"onAirBoxContent\" style=\"position:relative;left:10000em;\"></div>";
		$buffer .= "</div>";
		$buffer .= "<div style=\"position:relative;width:".$layoutObj->bWidth."px;overflow:hidden;\">";
		$buffer .= "<div style=\"position:relative;width:10000em;\">";
		$buffer .= "<div id=\"testOnAirBoxContent\" style=\"position:absolute;visibility:hidden;\"></div>";
		$buffer .= "</div>";
		$buffer .= "</div>";
		$buffer .= self::jsBox();
		$buffer .= self::jsPolling('onairbox');

		return $buffer;
				
	}
	*/
	public static function onAirBox() {
		
		$layoutObj = new Layout('onairbox');
		
		$buffer = "<div id=\"jsCode\" style=\"height:0px;\"></div>";
		$buffer .= "<div id=\"onAirBoxContainer\" style=\"overflow:hidden;width:".$layoutObj->bWidth."px;height:".$layoutObj->bHeight."px;\">";
		$buffer .= "<div id=\"onAirBoxContent\">";
		
		$buffer .= "</div>";
		$buffer .= self::jsPolling('onairbox');

		return $buffer;
				
	}

	private static function jsBox() {

		$layoutObj = new Layout('onairbox');

		$buffer = "<script>";	
		$buffer .= "window.fst_called = false;";
		$buffer .= "window.oabcontainerWidth =  $('onAirBoxContainer').getCoordinates().width;";
		$buffer .= "$('onAirBoxContent').setStyle('left', window.oabcontainerWidth+'px');";
		$buffer .= "myFx = new Fx.Tween($('onAirBoxContent'), {'property':'left', 'duration':".$layoutObj->bSpeed.", 'transition':'linear'});";
		$buffer .= "function scrollText() {";
		$buffer .= "window.fst_called = true;";
		$buffer .= "myFx.start('-'+window.oabcontentWidth+'px').chain(
				function() { this.set(window.oabcontainerWidth+'px'); scrollText();}
			);";
		$buffer .= "}";
		$buffer .= "</script>";	

		return $buffer;
	}

	public function phpPolling() {
				
		$buffer = '';

		date_default_timezone_set("Europe/Rome");
		$client_time = timeToSeconds(date("H:i:s"));
		$date = date("Y-m-d");
		$s = new Schedule($date);
		$item = $s->getOnAirItem($client_time);
		if($item) {
			// get all videos in the same block
			$block_items = $s->getBlockItems($item->block);
			$playlist = array();
			if(count($block_items)) {
				foreach($block_items as $bi) {
					if($bi->initTime>$item->initTime) $playlist[$bi->itemId] = new Video($bi->itemId, TBL_VIDEO);
				}
			}
		}
		else {
			$ni = $s->getNextFirstItem($client_time);
			$nextTimeout = ($ni)? ($ni->initTime-$client_time)."000":"60000";
			$address = "http://".$_SERVER['SERVER_NAME'].(str_replace("/methodPointer.php", "",$_SERVER['PHP_SELF']));
			$buffer .= "<script type=\"text/javascript\">\n";  
			if(Configuration::getValue('onAirSwf') && preg_match("#\.swf$#", Configuration::getValue('onAirSwf'))) {
				$buffer .= "playerState = player.getState();";
				$buffer .= "if(playerState<=1) player.play({url:'$address/".REL_UP_SWF."/".Configuration::getValue('onAirSwf')."'});";
			}
			$buffer .= "setTimeout('polling()', '$nextTimeout')";		
			$buffer .= "</script>\n";
		}
		
		if($item){
			$video = new Video($item->itemId, TBL_VIDEO);
			$difference = $client_time-$item->initTime; 
			$timeblock = $item->endTime-$client_time;
			$clips = "{url: '".$video->ml('name')."', provider: 'rtmp',".($video->name_html5 ? " ipadUrl:'http://".Configuration::getValue('httpAddress')."/".$video->ml('name_html5')."'," : "")." start: $difference}";
			foreach($playlist as $key=>$value) {
				$clips .= ",{url:'".$value->ml('name')."', provider: 'rtmp'".($value->name_html5 ? ", ipdaUrl: 'http://".Configuration::getValue('httpAddress')."/".$value->ml('name_html5')."'" : "")."}";
				$timeblock = $timeblock + timeToSeconds($value->duration);
			}
			$nextTimeout = $timeblock.'000';
			$buffer .= "<script type=\"text/javascript\">";
			$buffer .= "playerState = player.getState();";
			$buffer .= "if(playerState<=1) {
							player.play([$clips]);
			                setTimeout('polling()', '$nextTimeout');
			            }
			           	else 
			            	setTimeout('polling()', 5000);";
			$buffer .= "</script>\n";
		
		}

		return $buffer;
		
	}
	
	public function phpPollingBox() {
				
		date_default_timezone_set("Europe/Rome");
		$client_time = timeToSeconds(date("H:i:s"));
		$date = date("Y-m-d");
		$s = new Schedule($date);
		$item = $s->getOnAirItem($client_time);
		if($item) {
			// get all videos in the same block
			$block_items = $s->getBlockItems($item->block);
			$playlist = array();
			if(count($block_items)) {
				foreach($block_items as $bi) {
					if($bi->initTime>$item->initTime) $playlist[$bi->itemId] = new Video($bi->itemId, TBL_VIDEO);
				}
			}
		}
		else {
			$ni = $s->getNextFirstItem($client_time);
			$nextTimeout = ($ni)? ($ni->initTime-$client_time)."000":"60000";
			$address = "http://".$_SERVER['SERVER_NAME'].(str_replace("/methodPointer.php", "",$_SERVER['PHP_SELF']));
			$buffer = "<script type=\"text/javascript\">\n";
		      	if($ni) {
				$nv = new Video($ni->itemId, TBL_VIDEO);
				$message = "<div class=\"time\">"._("next")."</div>";
				$message .= "<img src=\"".REL_UP_IMG."/".$nv->image."\" />";
				$message .= "<div class=\"title\">".htmlChars($nv->ml('title'))."</div>";
				$message .= "<div class=\"at_time\">@ ".substr(secondsToTime($ni->initTime), 0, 5)."</div>";
			}
			else {
				$message = _("TV program not available");	
			}
			$buffer .= "$('onAirBoxContent').innerHTML='$message';";
			$buffer .= "$('testOnAirBoxContent').innerHTML='$message';";
			$buffer .= "window.oabcontentWidth =  $('testOnAirBoxContent').getCoordinates().width;";
			$buffer .= "if(!window.fst_called) scrollText();";
			$buffer .= "setTimeout('polling()', '$nextTimeout')";		
			$buffer .= "</script>\n";
		}
		
		if($item){
			$video = new Video($item->itemId, TBL_VIDEO);
			$message = "<div class=\"time\">"._("now")."</div>";
			$message .= "<img src=\"".REL_UP_IMG."/".$video->image."\" />";
			$message .= "<div class=\"title\">".htmlChars($video->ml('title'))."</div>";
			$difference = $client_time-$item->initTime; 
			$timeblock = $item->endTime-$client_time;
			$i=0;
			if(count($playlist)) {
				foreach($playlist as $key=>$value) {
					if(!$i) {
						$message .= "<div class=\"time\">"._("next")."</div>";
						$message .= "<img src=\"".REL_UP_IMG."/".$value->image."\" />";
						$message .= "<div class=\"title\">".htmlChars($value->ml('title'))."</div>";
						$message .= "<div class=\"at_time\">@ ".substr(secondsToTime($item->endTime), 0, 5)."</div>";
					}
					$timeblock = $timeblock + timeToSeconds($value->duration);
					$i++;
				}
			}
			else {
				$ni = $s->getNextFirstItem($client_time);
				$nextTimeout = ($ni)? ($ni->initTime-$client_time)."000":"60000";
				if($ni) {
					$nv = new Video($ni->itemId, TBL_VIDEO);
					$message .= "<div class=\"time\">"._("next")."</div>";
					$message .= "<img src=\"".REL_UP_IMG."/".$nv->image."\" />";
					$message .= "<div class=\"title\">".htmlChars($nv->ml('title'))."</div>";
					$message .= "<div class=\"at_time\">@ ".substr(secondsToTime($ni->initTime), 0, 5)."</div>";
				}
			}
			$nextTimeout = $timeblock.'000';
			$buffer = "<script type=\"text/javascript\">";
			$buffer .= "$('onAirBoxContent').innerHTML='$message';";
			$buffer .= "$('testOnAirBoxContent').innerHTML='$message';";
			$buffer .= "window.oabcontentWidth =  $('testOnAirBoxContent').getCoordinates().width;";
			$buffer .= "if(!window.fst_called) scrollText();";
			$buffer .= "setTimeout('polling()', '".($item->endTime-$client_time)."000"."');";
			$buffer .= "</script>\n";
		
		}

		return $buffer;
		
	}

	private static function jsPolling($f) {
		
		$GINO = "<script type=\"text/javascript\">"; 
		
		$GINO .= "window.addEvent('domready', function() {
					setTimeout('polling()', '100');
				});
				
				function polling() {
			
					sendPost('methodPointer.php?pt[OnAir-".($f=='onair' ? 'phpPolling':'phpPollingBox')."]', '', 'jsCode','',true);
			
				}";
		
		$GINO .= "</script>";
		
		return $GINO; 
	}
	
	public static function manage() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		if(isset($_GET['type']) && cleanVar($_GET['type'])=='onairbox') return self::manageBox();

		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Pubblica - On Air")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav\">";
		$buffer .= "<p><a href=\"iframeAir.php\" rel=\"external\">"._("Preview")."</a></p>";
		$buffer .= "<p>"._("Di seguito il codice da copiare ed incollare per visualizzare i contenuti nel tuo sito.")."</p>";
		$buffer .= self::showIframe();
		$buffer .= "</div>";
		
		return $buffer;
		
	}
	
	private static function manageBox() {
		
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Pubblica - On Air Box")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav\">";
		$buffer .= "<p><a href=\"iframeAirBox.php\" rel=\"external\">"._("Preview")."</a></p>";
		$buffer .= "<p>"._("Di seguito il codice da copiare ed incollare per visualizzare i contenuti nel tuo sito.")."</p>";
		$buffer .= self::showIframeBox();
		$buffer .= "</div>";
		
		return $buffer;
		
	}

	private static function showIframe() {
				
		list($width, $height) = self::getDimensions(new Layout('onair'));
		
		$address = "http://".$_SERVER['SERVER_NAME'].(str_replace("/admin.php", "/iframeAir.php",$_SERVER['PHP_SELF']));
		$buffer = "<div>";
		$buffer .= "<textarea style=\"width:100%;height:80px;\"><iframe src=\"$address\" width=\"".($width)."px\" height=\"".($height)."px\" frameborder=\"0\">\n<p>Your browser does not support iframes</p>\n</iframe></textarea>";
		$buffer .= "</div>";
		
		return $buffer;
		
	}

	private static function showIframeBox() {
	
		$layoutObj = new Layout('onairbox');
		
		$address = "http://".$_SERVER['SERVER_NAME'].(str_replace("/admin.php", "/iframeAirBox.php",$_SERVER['PHP_SELF']));
		$buffer = "<div>";
		$buffer .= "<textarea style=\"width:100%;height:80px;\"><iframe src=\"$address\" width=\"".($layoutObj->bWidth)."px\" height=\"".($layoutObj->bHeight)."px\" frameborder=\"0\">\n<p>Your browser does not support iframes</p>\n</iframe></textarea>";
		$buffer .= "</div>";
		
		return $buffer;

	}

	private static function getDimensions($l) {
		
		if(preg_match("#pt|pb#i", $l->layout)) {
			$width = ($l->pWidth>$l->lWidth)?$l->pWidth:$l->lWidth;
			$height = $l->pHeight+$l->lHeight;
		}
		elseif(preg_match("#pl|pr#i", $l->layout)) {
			$width = $l->pWidth+$l->lWidth;
			$height = ($l->pHeight>$l->lHeight)?$l->pHeight:$l->lHeight;
		}
		
		return array($width, $height);
	} 
	
}

?>
