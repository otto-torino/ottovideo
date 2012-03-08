<?php

class VideoList {

	private $_imgItemHeight, $_imgItemWidth, $_hlItemWidth;
	private $_layout, $_play_img;

	function __construct($layout=null, $vid=null) {

		$this->_layout = $layout;

		$this->_vid = $vid;
		
		$this->_imgItemHeight = Configuration::getValue('imgHeightDemand');//(eregi("hl",$this->_layout->layout))? $this->_hlItemHeight-30:100;
		$this->_imgItemWidth = Configuration::getValue('imgWidthDemand');//(eregi("hl",$this->_layout->layout))? $this->_imgItemHeight*1.5:150;
		
		$this->_hlItemWidth = $this->_imgItemWidth+30;

		$this->_play_img = "<img src=\"".REL_IMG."/play_bkg.png\" alt=\"play\" title=\"Play\" class=\"tooltip\" style=\"margin-left:".($this->_imgItemWidth/2+5)."px;margin-top:".($this->_imgItemHeight/2+5)."px;left:-22px;top:-22px;position:relative;visibility:hidden;cursor:pointer;\"/>";

	}

	public function render() {

		$db = new Db;
		$query = "SELECT id, name FROM ".TBL_VIDEO." WHERE ondemand='yes' ORDER BY date DESC, new";
		$a = $db->selectquery($query);
		$totItem = sizeof($a);

		//$buffer = $this->sequentialCtgList();
		$buffer = "<div id=\"itemsList\" style=\"width:".$this->_layout->lWidth."px\">";
		if(preg_match("#hl#i", $this->_layout->layout)) $buffer .= $this->listVideo($query, $totItem, 'h');
		elseif(preg_match("#vl#i", $this->_layout->layout)) $buffer .= $this->listVideo($query, $totItem, 'v'); 
		$buffer .= "</div>";
		
		return $buffer;


	}

	public function update() {
		
		$buffer = '';
		$ctg = isset($_POST['ctg'])? (int) cleanVar($_POST['ctg']):0;
		$prev = isset($_POST['prev'])? (int) cleanVar($_POST['prev']):0;
		$new = isset($_POST['new'])? (int) cleanVar($_POST['new']):0;

		$desc = isset($_POST['desc'])? (string) cleanVar($_POST['desc']):null;
	
		$where = "WHERE ondemand='yes'";
		if($new) $where .= " AND new='yes'";
		if($desc) $where .= " AND (description LIKE '%$desc%' OR title LIKE '%$desc%')";
		if($ctg || $prev) {
			$c = ($ctg)? new Category($ctg, TBL_CTG):new Category($prev, TBL_CTG);
			$ctg_children = $c->getChildren(true);
			$where .= " AND (category='$ctg'";
			foreach($ctg_children as $child) {
				$where .= " OR category='$child->id'";
			}
			$where .= ")";
		}
		
		$db = new Db;
		$query = "SELECT id, name FROM ".TBL_VIDEO." $where ORDER BY date DESC, new";
		$a = $db->selectquery($query);
		$totItem = sizeof($a);
		
		if(eregi("hl", $this->_layout->layout)) $buffer .= $this->listVideo($query, $totItem, 'h');
		elseif(eregi("vl", $this->_layout->layout)) $buffer .= $this->listVideo($query, $totItem, 'v'); 
		
		return $buffer;
		
	}
	
	private function listVideo($query, $totItem, $type) {

		$db = new Db;
		$a = $db->selectquery($query);
		$buffer = $this->jsLib($type);
		$buffer .= "<ul id=\"listVideo\">";
		$selected = null;
		if(sizeof($a)>0) {
			$i = 1;
			foreach($a as $b) {
				$id = $b['id'];
				if($id==$this->_vid) $selected = $i;
				$name = $b['name'];
				$buffer .= "<li>".($type=='h' ? $this->printSliderItemContentH($id, $i):$this->printSliderItemContentV($id, $i))."</li>";
				$i++;
			}
		}
		else $buffer .= "<li><div class=\"videoTitle\">"._("La ricerca non ha prodotto risultati")."</div></li>";
		$buffer .= "</ul>";

		if($type=='h') 
			$buffer .= "<script>window.vList = new hScrollingList('listVideo', 10, ".$this->_layout->lWidth.", $this->_hlItemWidth, {list_height: ".($this->_layout->lHeight-70).", tr_duration: ".$this->_layout->lSpeed."});window.vList.setSelected('$selected', showInfo, '$this->_vid');</script>";
		else 
			$buffer .= "<script>window.vList = new vScrollingList('listVideo', 10, ".($this->_layout->lHeight).", ".($this->_imgItemHeight+20).", {list_width: null, tr_duration: ".$this->_layout->lSpeed."});window.vList.setSelected('$selected', showInfo, '$this->_vid');</script>";

		return $buffer;

	}

	public function printSliderItemContentH($id, $i) {

		$video = new Video($id, TBL_VIDEO);
		$title = cutHtmlText($video->ml('title'), 40, '...', true, false, true);	


		$onclick = $this->onclickAction($video, $i);
		
		$onmouseover = "onmouseover=\"$(this).getChildren('img')[0].setStyle('visibility', 'visible');\"";
		$onmouseout = "onmouseout=\"$(this).getChildren('img')[0].setStyle('visibility', 'hidden');\"";

		$play = "<img $onclick src=\"".REL_IMG."/play_bkg.png\" alt=\"play\" title=\"Play\" class=\"tooltip\" style=\"margin-left:".($this->_imgItemWidth/2+5)."px;margin-top:".($this->_imgItemHeight/2+5)."px;left:-22px;top:-22px;position:relative;visibility:hidden;cursor:pointer;\"/>";
		$buffer = "<div $onmouseout $onmouseover class=\"videoImg himg\" style=\"background-image:url('".REL_UP_IMG."/".$video->image."'); background-position:center center;background-repeat:no-repeat; width:".($this->_imgItemWidth+10)."px;height:".($this->_imgItemHeight+10)."px;\" />".$play;
		$buffer .= $video->new=='yes'? "<span class=\"videoNew hnew\">"._("new")."</span>":"";
		$onclick_info = "onclick=\"showInfo('$video->id')\"";
		$buffer .= "<span $onclick_info class=\"videoInfo hinfo\">"._("info")."</span>";
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"itemTitle\" style=\"width:".($this->_imgItemWidth)."px;padding:5px;cursor:pointer;\" $onclick />".htmlChars($title)."</div>\n";
		
		return $buffer;

	}
	
	public function printSliderItemContentV($id, $i) {

		$video = new Video($id, TBL_VIDEO);

		$onclick = $this->onclickAction($video, $i);

		$onmouseover = "onmouseover=\"$(this).getChildren('img')[0].setStyle('visibility', 'visible');\"";
		$onmouseout = "onmouseout=\"$(this).getChildren('img')[0].setStyle('visibility', 'hidden');\"";

		$play = "<img $onclick src=\"".REL_IMG."/play_bkg.png\" alt=\"play\" title=\"Play\" class=\"tooltip\" style=\"margin-left:".($this->_imgItemWidth/2+5)."px;margin-top:".($this->_imgItemHeight/2+5)."px;left:-22px;top:-22px;position:relative;visibility:hidden;cursor:pointer;\"/>";
		$buffer = "<div class=\"itemContainer\" style=\"height:".($this->_imgItemHeight+20)."px\">";

		$query = "SELECT id FROM ".TBL_VIDEO_COMMENT." WHERE video='$id' ORDER BY date DESC";
		$comments = Comment::getItems($query, TBL_VIDEO_COMMENT);
		$num_comments = count($comments);

		$buffer .= "<table style=\"width:100%\">";
		$buffer .= "<tr>";
		$buffer .= "<td style=\"width:".($this->_imgItemWidth+10)."px\">";
		$buffer .= "<div $onmouseout $onmouseover class=\"videoImg\" style=\"background-image:url('".REL_UP_IMG."/".$video->image."'); background-position:center center;background-repeat:no-repeat; width:".($this->_imgItemWidth+10)."px;height:".($this->_imgItemHeight+20)."px;\" />".$play;
		$buffer .= $video->new=='yes'? "<span class=\"videoNew vnew\">"._("new")."</span>":"";
		$buffer .= "<span class=\"videoDuration vdur\">".$video->duration."</span>";
		$buffer .= "<div class=\"videoComments tooltip\" title=\"per condividere e visualizzare i commenti cliccare sul titolo del video\">$num_comments</div><div class=\"videoCommentsCollout\"></div>";
		$buffer .= "</div>\n";
		$buffer .= "</td>";
		$buffer .= "<td>";
		$buffer .= "<div class=\"videoTitle\" style=\"cursor:pointer;\" $onclick>".htmlChars($video->ml('title'))."</div>";
		$onclick_info = "onclick=\"showInfo('$video->id')\"";
		$ending = "... <span class=\"link\" $onclick_info>"._("leggi tutto")."</span>";
		$buffer .= "<div class=\"videoDescription\">".cutHtmlText(htmlChars($video->ml('description')), $this->_layout->lChars, $ending, false, false, true, array('endingPosition'=>'in'))."</div>";
		$buffer .= "</td>";
		$buffer .= "</tr>";
		$buffer .= "</table>";
		$buffer .= "</div>";
		
		return $buffer;

	}

	public function printSliderItemDescription() {
		
		$id = (int) cleanVar($_POST['id']);
		$close = (int) cleanVar($_POST['close']);
		$video = new Video($id, TBL_VIDEO);
		
		$buffer = '';
		
		if($close) {
			$onclick = "onclick=\"$('itemdLayer').dispose();window.vList.updateCtrl();\"";
			$buffer .= "<div $onclick style=\"position:absolute;right:5px;top:5px;cursor:pointer;background: #c9cc01;height:17px;padding: 0 4px; color: #000; font-size: 0.9em;line-height:17px;\">"._("chiudi")."</div>";

			$onclick = "onclick=\"
			ajaxRequest('post', 'methodPointer.php?pt[VideoList-printSliderItemComment]', 'id=$id&close=1', $('itemdLayer'), {'script':true});
\"";
			$buffer .= "<div $onclick style=\"position:absolute;right:46px;top:5px;cursor:pointer;background: #c9cc01;height:17px;padding: 0 4px; color: #000; font-size: 0.9em;line-height:17px;\">"._("commenti")."</div>";
		}
		
		$onclick = $this->onclickAction($video, null);

		$onmouseover = "onmouseover=\"$(this).getChildren('img')[0].setStyle('visibility', 'visible');\"";
		$onmouseout = "onmouseout=\"$(this).getChildren('img')[0].setStyle('visibility', 'hidden');\"";

		$play = "<img $onclick src=\"".REL_IMG."/play_bkg.png\" alt=\"play\" title=\"Play\" class=\"tooltip\" style=\"margin-left:".($this->_imgItemWidth/2)."px;margin-top:".($this->_imgItemHeight/2)."px;left:-22px;top:-22px;position:relative;visibility:hidden;cursor:pointer;\"/>";
		$buffer .= "<table style=\"margin-top:20px;\">";
		$buffer .= "<tr>";
		$buffer .= "<td>";
		$buffer .= "<div $onmouseover $onmouseout class=\"videoImg\" style=\"position:relative;background-image:url('".REL_UP_IMG."/".$video->image."'); background-position:center center;background-repeat:no-repeat; width:".($this->_imgItemWidth+10)."px;height:".($this->_imgItemHeight+20)."px;\">";
		$buffer .= $play;
		$buffer .= $video->new=='yes'? "<span class=\"videoNew dnew\">"._("new")."</span>":"";
		$buffer .= "<span class=\"videoDuration ddur\">".$video->duration."</span>";
		$buffer .= "</div>\n";
		if($video->name_html5) {
			$buffer .= "<div class=\"html5 left\"></div>";
			$buffer .= "<div class=\"html5_txt left\">"._("disponibile in<br />versione mobile")."</div>".CLEAR;
		}
		$buffer .= "</td>";
		$buffer .= "<td>";
		$buffer .= "<div class=\"videoTitle\">";
		$buffer .= htmlChars($video->ml('title'));
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"videoDescription\">";
		$buffer .= htmlChars($video->ml('description'));
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"videoInsertionDate\">";
		$buffer .= _("Inserito il ").dbDatetimeToDate($video->date, "/");
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"videoSocial\">";
		$s = preg_match("#\?#", Configuration::getValue('parent_url')) ? "&" : "?";
		$buffer.= share("facebook", Configuration::getValue('parent_url').$s."vid=$video->id", htmlChars($video->ml('title')));
		$buffer .= " &#160;".share("twitter", Configuration::getValue('parent_url').$s."vid=$video->id");
		$buffer .= "</div>";
		$buffer .= "</td>";
		$buffer .= "</tr>";
		$buffer .= "</table>";
				
		echo $buffer;
		exit;
		
	}

	public function printSliderItemComment() {

		$id = (int) cleanVar($_POST['id']);
		$close = (int) cleanVar($_POST['close']);
		$video = new Video($id, TBL_VIDEO);
		
		$buffer = '';
		
		if($close) {
			$onclick = "onclick=\"$('itemdLayer').dispose();window.vList.updateCtrl();\"";
			$buffer .= "<div $onclick style=\"position:absolute;right:5px;top:5px;cursor:pointer;background: #c9cc01;height:17px;padding: 0 4px; color: #000; font-size: 0.9em;line-height:17px;\">"._("chiudi")."</div>";

			$onclick = "onclick=\"ajaxRequest('post', 'methodPointer.php?pt[VideoList-printSliderItemDescription]', 'id=$id&close=1', $('itemdLayer'), {'script':true});\"";
			$buffer .= "<div $onclick style=\"position:absolute;right:46px;top:5px;cursor:pointer;background: #c9cc01;height:17px;padding: 0 4px; color: #000; font-size: 0.9em;line-height:17px;\">"._("scheda")."</div>";
		}

		$buffer .= "<div style=\"margin: 20px 10px 10px;\">";

		$buffer .= "<h4>"._("Inserisci un commento")."</h4>";
		$buffer .= "<form name=\"comments\" action=\"\" method=\"post\">";
		$buffer .= "<input type=\"hidden\" name=\"video\" value=\"".$id."\" />";
		$buffer .= "<label for=\"author\">"._("nome")." *</label><br />";
		$buffer .= "<input id=\"author\" style=\"width: 250px;\" type=\"text\" name=\"author\" value=\"\" size=\"30\" maxlength=\"64\" /><br />";
		$buffer .= "<label for=\"email\">"._("email (non pubblicata)")." *</label><br />";
		$buffer .= "<input id=\"email\" style=\"width: 250px;\" type=\"text\" name=\"email\" value=\"\" size=\"30\" maxlength=\"64\" /><br />";
		$buffer .= "<label for=\"email\">"._("commento")." *</label><br />";
		$buffer .= "<textarea  id=\"comment\" style=\"width: 250px;\" name=\"comment\" cols=\"40\"></textarea>";
		$onclick = "if($('comment').value && $('email').value && $('author').value) {
					ajaxRequest('post', 'methodPointer.php?pt[VideoList-actionComment]', 'id=$id&author='+$('author').value+'&email='+$('email').value+'&comment='+$('comment').value, $('comment_response'), {'script':true});
				}
				else {
					alert('".jsVar(_("Compilare i campi obbligatori"))."');
				}";
		$buffer .= " <input type=\"button\" name=\"submit\" value=\"inserisci\" style=\"position:relative;bottom:2px;\" onclick=\"".$onclick."\"/>";
		$buffer .= "</form>";
		$buffer .= "<div id=\"comment_response\">";
		$buffer .= "</div>";

		$buffer .= "<div class=\"comment_list\">";
		$buffer .= "<h4>"._("Commenti pubblicati")."</h4>";
		$query = "SELECT id FROM ".TBL_VIDEO_COMMENT." WHERE video='$id' ORDER BY date DESC";
		$comments = Comment::getItems($query, TBL_VIDEO_COMMENT);
		if(count($comments)) {
			$buffer .= "<ul class=\"comments_list\">";
			foreach($comments as $c) {
				$buffer .= "<li>";
				$buffer .= "<span class=\"date\">".dbDatetimeToDate($c->date, '/').' '.substr(dbDatetimeToTime($c->date), 0, 5)."</span> - <span class=\"author\">".htmlChars($c->author)."</span><br />";
				$buffer .= "<div class=\"text\">".htmlChars($c->text)."</div>";
				$buffer .= "</li>";
				$buffer .= "<li class=\"callout\"><div class=\"callout\"></div></li>";
			}
			$buffer .= "</ul>";
		}
		else {
			$buffer .= "<p>"._("non risultano commenti pubblicati")."</p>";
		}
		$buffer .= "</div>";



		$buffer .= "</div>";
		
		
				
		echo $buffer;
		exit();

	}

	public function actionComment() {
	
		$id = (int) cleanVar($_POST['id']);
		$author = (string) cleanVar($_POST['author']);
		$email = (string) cleanVar($_POST['email']);
		$comment = (string) cleanVar($_POST['comment']);

		$db = new Db;
		$query = "INSERT INTO ".TBL_VIDEO_COMMENT." (video, author, email, text, date) VALUES ('".$id."', '".$author."', '".$email."', '".$comment."', '".date("Y-m-d H:i:s")."');";
		$a = $db->actionquery($query);

		if($a) {
			$msg = _("il commento è stato inserito correttamente");
		}
		else {
			$msg = _("si è verificato un errore");
		}

		$buffer = "<script>";
		$buffer .= "alert('".jsVar($msg)."');";
		$buffer .= "ajaxRequest('post', 'methodPointer.php?pt[VideoList-printSliderItemComment]', 'id=$id&close=1', $('itemdLayer'), {'script':true});";
		$buffer .= "</script>";

		echo $buffer;

		exit();
	}

	private function onclickAction($video, $i) {

		$clips = '';
		$ids = '';
		$player_plugin_content = '';

		if($video->bind_spot) 
		{
			if($video->spot_category) 
			{
				$where_add = "AND spot_category='$video->spot_category'";
			}
			else 
			{
				$where_add = '';
			}

			$query = "SELECT id FROM ".TBL_VIDEO." WHERE type='2' AND spot_active='1' AND spot_max_view>views $where_add";

			$spots = $video->getItems($query, TBL_VIDEO);

			if(count($spots))
			{ 
				$spot = $spots[array_rand($spots)];
				$clips = "{url: '".$spot->ml('name')."', provider: 'rtmp'"
				       . ($spot->name_html5 
						? ", ipadUrl:'http://".Configuration::getValue('httpAddress')."/".$spot->ml('name_html5')."'" 
						: "")
				       . "},";
				$ids = $spot->id."-";

				$spot_html = "<p>".sprintf(_("Il contenuto pubblicitario durerà %s secondi"), substr($spot->duration, 6, 2))." - "
					. "<a target=\'blank\' href=\'".$spot->spot_url."\'><font color=\'#ffff00\'>"._("visita il sito dello sponsor")."</font></a></p>";

				$player_plugin_content =  "player.getPlugin('content').css({display:'block'});"
						       .  "player.getPlugin('content').setHtml('$spot_html');"
						       .  "player.getPlugin('content').animate({opacity: '0.9'}, 1000);";
			}
		}

		$clips .= "{url: '".$video->ml('name')."', provider: 'rtmp'".($video->name_html5 ? ", ipadUrl:'http://".Configuration::getValue('httpAddress')."/".$video->ml('name_html5')."'" : "")."}";	
		$ids .= $video->id;

		// play videos and update views
		$onclick = "onclick=\""
			 . "player.play([$clips]);"
			 . (!is_null($i) ? "window.vList.setSelected('$i', showInfo, '$video->id');" : "")
			 . "ajaxRequest('post', 'methodPointer.php?pt[VideoInterface-updateView]', 'ids=$ids', null, null);"
			 . "$player_plugin_content\"";

		return $onclick;


	}

	public function sequentialCtgList() {
				
		$c = new Category(null, TBL_CTG);

		$buffer = "<script type=\"text/javascript\">";
		$buffer .= "window.act_prev = 0;";
		$buffer .= "window.act_ctg_id = 0;";
		$buffer .= "function listCtgContents(prev, ctg_id, span_id) {

				window.act_prev = prev;
				window.act_ctg_id = ctg_id;

				var url = 'methodPointer.php?pt[OnDemand-updateList]';
				var data = 'prev='+prev+'&ctg='+ctg_id+'&desc='+$('fdesc').value+'&new='+($('fnew').checked ? '1':'0');
				sendPost(url, data, 'itemsList','itemsList', true);

				if(span_id) sendPost('methodPointer.php?pt[VideoList-sequentialCtgInput]', 'parent='+ctg_id, span_id);	
					
		}";
		
		$buffer .= "</script>\n";	
		
		list($width, $height) = OnDemand::getDimensions($this->_layout);
				
		//$buffer .= "<div id=\"icoFilter\"><fieldset><img src=\"".REL_IMG."/ico_search-mini.gif\" /></fieldset></div>";
		$buffer .= "<div id=\"descFilter\" style=\"width:".(220)."px;float:left;\">\n";
		$buffer .= "<fieldset>";
		$buffer .= "<legend><img id=\"icoFilter\" src=\"".REL_IMG."/ico_search-mini.gif\" /> "._("Descrizione")."</legend>";	
		$buffer .= "<input type=\"text\" name=\"desc\" id=\"fdesc\" onkeyup=\"listCtgContents(window.act_prev, window.act_ctg_id)\"/>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>\n";

		$buffer .= "<div id=\"newFilter\" style=\"width:".(90)."px;float:left;\">\n";
		$buffer .= "<fieldset>";
		$buffer .= "<legend><img id=\"icoFilter\" src=\"".REL_IMG."/ico_search-mini.gif\" /> "._("New")."</legend>";	
		$buffer .= "<div style=\"text-align:center;\">";
		$buffer .= "<input type=\"checkbox\" name=\"new\" id=\"fnew\" onchange=\"listCtgContents(window.act_prev, window.act_ctg_id)\"/>";
		$buffer .= "</div>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>\n";
		
		$buffer .= "<div id=\"ctgFilter\" style=\"width:".($width-220-90)."px;float:left;\">\n";
		$buffer .= "<fieldset>";
		$buffer .= "<legend><img id=\"icoFilter\" src=\"".REL_IMG."/ico_search-mini.gif\" /> "._("Categorie")."</legend>";		
		$buffer .= "<div id=\"seqCtg\" style=\"overflow:auto; margin:0px 0px;\">\n";
		$buffer .= "<span>\n";

		$ctgs = $c->getFromQuery("SELECT id, name FROM ".TBL_CTG." WHERE parent='0' AND public='1' ORDER BY name");
		$ctgs_a = array();
		foreach($ctgs as $ctg) $ctgs_a[$ctg->id] = htmlChars($ctg->ml('name'));
		$buffer .= inputSelect('ctg_level0', $ctgs_a, '', "onchange=\"listCtgContents(0, $(this).value, 'video_ctg_p0')\"", $first=array(""=>_("tutte")));	
		$buffer .= "</span>\n";
		$buffer .= "<span id=\"video_ctg_p0\" style=\"margin-left:10px;\"></span>\n";
		$buffer .= "</div>\n";
		$buffer .= "</fieldset>";
		$buffer .= "</div>\n";

		
		$buffer .= CLEAR;
		
		return $buffer; 
	
	}
	
	public function sequentialCtgInput() {
		
		$c = new Category(null, TBL_CTG);

		$parent = isset($_POST['parent'])? (int) cleanVar($_POST['parent']):0;
		$c = new Category($parent, TBL_CTG);
		if(count($c->getChildren())==0 || !$parent) return '';
		
		$ctgs = $c->getFromQuery("SELECT id, name FROM ".TBL_CTG." WHERE parent='$parent' AND public='1'");
		$ctgs_a = array();
		foreach($ctgs as $ctg) $ctgs_a[$ctg->id] = htmlChars($ctg->ml('name'));
		$buffer = inputSelect('ctg_parent'.$parent, $ctgs_a, '', "onchange=\"listCtgContents('$parent', $(this).value, 'video_ctg_p$parent')\"", $first=array(""=>_("tutte")));
		
		$buffer .= "<span id=\"video_ctg_p".$parent."\" style=\"margin-left:10px;\">";
		$buffer .= "</span>\n";
		
		return $buffer;
	}

	private function jsLib($type) {
	
		$buffer = "<script>";
		$buffer .= "function showInfo(id) {
			window.vList.deactivateCtrl();	
			var dLayer = new Element('div', {
				'id': 'itemdLayer',
				'styles': {
					'width': '".($type=='h' ? ($this->_layout->lWidth-52): $this->_layout->lWidth)."px',
					'height': '".($type=='h' ? ($this->_layout->lHeight-4):($this->_layout->lHeight-52))."px',
					'margin': '2px 0',
					'overflow': 'auto',
					'position':'absolute', 
					'z-index':'2',
					'".($type=='h' ? "left":"top")."': ((window.vList.vps-1)*".($type=='h' ? ($this->_layout->lWidth-52):($this->_layout->lHeight-52)).")+'px'
				},
				'class': 'dlayer'
			});
			var tr = new Fx.Tween(dLayer);
			tr.set('opacity', '0');
			ajaxRequest('post', 'methodPointer.php?pt[VideoList-printSliderItemDescription]', 'id='+id+'&close=1', dLayer, {'script':true});
			dLayer.inject($('listVideo'), 'before');
			tr.start('opacity', '0', '1');
		}";
		$buffer .= "</script>";

		return $buffer;
	}
	
}

?>
