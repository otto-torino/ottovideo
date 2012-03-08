<?php

include('config.php');
include('paths.php');
include('const.php');
include(ABS_INCLUDE.S.'include.php');

session_name(SESSION_NAME);
session_start();

include('language.php');

header("Content-type: text/xml; charset=utf-8");

$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$buffer = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$buffer .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
$buffer .= "<channel>\n";
$buffer .= "<atom:link href=\"".$url."\" rel=\"self\" type=\"application/rss+xml\" />\n";
$buffer .= "<title>".TITLE." - "._("Commenti")."</title>\n";
$buffer .= "<link>".$url."</link>\n";
$buffer .= "<description></description>\n";
$buffer .= "<language>".$_SESSION['lng']."</language>\n";
$buffer .= "<copyright> Copyright 2009 Otto srl </copyright>\n";
$buffer .= "<docs>http://blogs.law.harvard.edu/tech/rss</docs>\n";

$query = "SELECT id FROM ".TBL_VIDEO_COMMENT." ORDER BY date DESC";
$db = new Db;
$a = $db->selectquery($query);
if(sizeof($a)>0) {
	foreach($a as $b) {
		$id = $b['id'];
		$comment = new Comment($id, TBL_VIDEO_COMMENT);
		$query2 = "SELECT id FROM ".TBL_VIDEO." WHERE id='".$comment->video."'";
		$a2 = $db->selectquery($query2);
		$video = new Video($a2[0]['id'], TBL_VIDEO);

		$buffer .= "<item>\n";
		$buffer .= "<title>".dbDatetimeToDate($comment->date, '/').' '.substr(dbDatetimeToTime($comment->date), 0, 5).' - '.htmlChars($comment->author).' - '._("commento al video").' "'.htmlChars($video->ml('title')).'"'."</title>\n";

		$s = preg_match("#\?#", Configuration::getValue('parent_url')) ? "&" : "?";
		$link = Configuration::getValue('parent_url').$s."vid=".$video->id;

		$buffer .= "<link>".preg_replace("#&#", "&amp;", $link)."</link>\n";
		$buffer .= "<description>\n";
		$buffer .= "<![CDATA[\n";
		$buffer .= htmlChars($comment->text)."\n";
		$buffer .= "]]>\n"; 
		$buffer .= "</description>\n";
		$buffer .= "<guid>".preg_replace("#&#", "&amp;", $link)."</guid>\n";
		$buffer .= "</item>\n";
	}
}

$buffer .= "</channel>\n";
$buffer .= "</rss>\n";

echo $buffer;

?>
