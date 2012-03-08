<?php

function cleanVar($var)
{
	if(is_array($var)) {
		foreach($var AS $k=>$v) {
			$var[$k] = clean_sequence($v, '');
		}
	}
	else $var = clean_sequence($var, '');

	return $var;
}

function clean_sequence($text, $strip_tags){
	
	Db::openConnection();
	$text = trim($text);
	if(get_magic_quotes_gpc()) $text = stripslashes($text);	// magic_quotes_gpc = On
	// Strip
	if(isset($strip_tags) && !empty($strip_tags)) $text = strip_selected_tags($text, $strip_tags, false);
	$text = strip_invisible_tags($text);
	$text = strip_tags_attributes($text, true, true);
	
	// Replace
	$text = replaceChar($text);
	$text = mysql_real_escape_string($text);
	//$text = utf8_decode($text);  // uncomment with latin1 db
	
	return $text;
}

/**
* strip_selected_tags ( string str [, string strip_tags[, strip_content flag]] )
* ---------------------------------------------------------------------
* Like strip_tags() but inverse; the strip_tags tags will be stripped, not kept.
* strip_tags: string with tags to strip, ex: "<a><p><quote>" etc.
* strip_content flag: TRUE will also strip everything between open and closed tag
*/
function strip_selected_tags($str, $tags = '', $stripContent = false)
{
	preg_match_all("/<([^>]+)>/i", $tags, $allTags, PREG_PATTERN_ORDER);
	foreach ($allTags[1] as $tag){
		if($stripContent){
			$str = preg_replace("/<".$tag."[^>]*>.*<\/".$tag.">/iU", "", $str);
		}
		$str = preg_replace("/<\/?".$tag."[^>]*>/iU", "", $str);
	}
	return $str;
}

function strip_invisible_tags($text)
{
	// This function will remove scripts, styles, and other unwanted
	// invisible text between tags.
	$text = preg_replace(
		array(
			'@<head[^>]*?>.*?</head>@siu',
			'@<style[^>]*?>.*?</style>@siu',
			'@<script[^>]*?.*?</script>@siu',
			'@<object[^>]*?.*?</object>@siu',
			'@<embed[^>]*?.*?</embed>@siu',
			'@<applet[^>]*?.*?</applet>@siu',
			'@<noframes[^>]*?.*?</noframes>@siu',
			'@<noscript[^>]*?.*?</noscript>@siu',
			'@<noembed[^>]*?.*?</noembed>@siu'
		),
		array(
			' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
		),
		$text);

	return $text;
}

function strip_tags_attributes($text, $strip_js, $strip_attributes)
{
	if($strip_js)
	{
		$js_attributes = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		
		$text = preg_replace('/\s(' . implode('|', $js_attributes) . ').*?([\s\>])/', '\\2', preg_replace('/<(.*?)>/ie', "'<' . preg_replace(array('/javascript:[^\"\']*/i', '/(" . implode('|', $js_attributes) . ")[ \\t\\n]*=[ \\t\\n]*[\"\'][^\"\']*[\"\']/i', '/\s+/'), array('', '', ' '), stripslashes('\\1')) . '>'", $text));
	}
	
	if($strip_attributes) $text = remove_attributes($text);
	
	return $text;
}

function remove_attributes($text)
{
	// rimuove 'style'
	$text = preg_replace("'\\s(style)=\"(.*?)\"'i", '', $text);
	
	// => problemi
	$strip_attrib = 
"/(font\-size|color|font\-family|line\-height|text\-indent):\\s(\\d+(\\x2E\\d+\\w+|\\W)|\\w+)(;|)(\\s|)/i";
	$text = preg_replace($strip_attrib, '', $text);
	
	// rimuove 'class' quando non assume un valore
	$text = str_replace(" class=\"\"", '', $text);
	
	return $text;
}

function replaceChar($text)
{
	$find = array("’", "‘", "`");
	$text = str_replace($find, "'", $text);
	$find = array("“", "”");
	$text = str_replace($find, "\"", $text);
	$text = str_replace("…", "...", $text);
	$text = str_replace("–", "-", $text);
	return $text;
}

function htmlChars($string, $id='')
{
	$string = trim($string);
	$string = stripslashes($string);

	$string = str_replace ('&euro;', '€', $string);
	$string = str_replace ('&', '&amp;', $string);	// CSS2
	$string = str_replace ('\'', '&#039;', $string);
	
	//$string = utf8_encode($string); // uncomment with latin1 db

	return $string;
}

function htmlInput($string)
{
	$string = trim($string);
	$string = stripslashes($string);
	$string = replaceChar($string);
	//$string = utf8_encode($string); // uncomment with latin1 db
	
	return $string;
}

function dateToDbDate($date, $s) {

	$date_array = explode($s, $date);
	return $date_array[2].'-'.$date_array[1].'-'.$date_array[0];

}

function dbDateToDate($db_date, $s) {
	if(empty($db_date) || $db_date=='0000-00-00') return '';
	$date_array = explode('-', $db_date);
	return $date_array[2].$s.$date_array[1].$s.$date_array[0];
}

function dbDatetimeToDate($datetime, $s) {
	$datetime_array = explode(" ", $datetime);
	return dbDateToDate($datetime_array[0], $s);
}

function dbDatetimeToTime($datetime) {
	$datetime_array = explode(" ", $datetime);
	return $datetime_array[1];
}

function timeToSeconds($time) {
		
	$time_array = explode(":", $time);
	$hours = $time_array[0];
	$minutes = $time_array[1];
	$seconds = $time_array[2];
		
	$timeseconds = $seconds + 60*$minutes + 3600*$hours;
		
	return $timeseconds;

}
	
function secondsToTime($seconds) {
		
	$hours = floor($seconds/3600);
	$minutes = floor(fmod($seconds,3600) / 60);
	$seconds = fmod(fmod($seconds,3600), 60);
		
	if($hours<10) $hours = '0'.$hours;
	if($minutes<10) $minutes = '0'.$minutes;
	if($seconds<10) $seconds = '0'.$seconds;
		
	$time = $hours.":".$minutes.":".$seconds; 
		
	return $time;
		
}

function jsVar($string) {
	preg_replace("#'#", "\'", $string);

	return $string;
}

?>
