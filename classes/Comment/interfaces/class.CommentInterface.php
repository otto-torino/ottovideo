<?php

class CommentInterface {

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
		$buffer .= "<div class=\"area_title_sx\">"._("Gestione commenti")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav_left\">";
		$buffer .= self::listComments();
		$buffer .= "</div>";
		$buffer .= "<div class=\"nav_right\">";
		if($a && $id) {
			self::deleteComment($id);
		}
		elseif($id) {
			$buffer .= self::view($id);
		}
		else {
			$buffer .= self::info();
		}
		$buffer .= "</div>";
		$buffer .= CLEAR;

		return $buffer;

	}

	private static function info() {
		
		$buffer = "<div><p><b>"._("Informazioni")."</b></p>"._("I commenti ai video sono automaticamente pubblicati. In questa sezione è possibile visualizzarli ed eventualmente rimuoverli.")."</div>";
		return $buffer;
	}

	private static function listComments() {
		
		$query = "SELECT id FROM ".TBL_VIDEO_COMMENT." ORDER BY date DESC";
		$comments = Comment::getItems($query, TBL_VIDEO_COMMENT);
		if(count($comments)) {
			$buffer = "<ul>";
			foreach($comments as $c) {
				$buffer .= "<li>";
				$buffer .= "<a href=\"admin.php?mng=com&id=".$c->id."\">".dbDatetimeToDate($c->date, '/').' '.substr(dbDatetimeToTime($c->date), 0, 5)." - ".htmlChars($c->author)."</a>";
				$buffer .= "</li>";
			}
			$buffer .= "</ul>";
		}
		else {
			$buffer = "<p>"._("non risultano commenti pubblicati")."</p>";
		}

		return $buffer;
	}

	private static function view($id) {
		
		$c = new Comment($id, TBL_VIDEO_COMMENT);
		$v = new Video($c->video, TBL_VIDEO);

		$buffer = "<p><b>"._("Data")."</b>: ".htmlChars(dbDatetimeToDate($c->date, '/').' '.substr(dbDatetimeToTime($c->date), 0, 5))."</p>";
		$buffer .= "<p><b>"._("Autore")."</b>: ".htmlChars($c->author)."</p>";
		$buffer .= "<p><b>"._("Email")."</b>: ".htmlChars($c->email)."</p>";
		$buffer .= "<p><b>"._("Video")."</b>: ".htmlChars($v->title)."</p>";
		$buffer .= "<p><b>"._("Testo")."</b>: ".htmlChars($c->text)."</p>";

		$onclick = "if(confirm('".jsVar("Sicuro di voler eliminare il commento?")."')) location.href='admin.php?mng=com&id=".$c->id."&a=delete';";
		$buffer .= "<p><input type=\"button\" value=\"elimina\" onclick=\"".$onclick."\" /></p>";

		return $buffer;
	}

	private static function deleteComment($id) {

		$c = new Comment($id, TBL_VIDEO_COMMENT);

		$c->deleteDbData();

		header("Location: admin.php?mng=com");

		exit();

	}


}

?>
