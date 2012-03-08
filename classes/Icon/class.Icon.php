<?php

class Icon {

	public static function insert($title=null) {
		if(!$title) $title = _("nuovo");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_insert.gif\" />";
	}
	
	public static function delete($title=null) {
		if(!$title) $title = _("elimina");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_trash.gif\" />";
	}
	
	public static function close($title=null) {
		if(!$title) $title = _("chiudi");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_close.gif\" />";
	}
	
	public static function closeSmall($title=null) {
		if(!$title) $title = _("chiudi");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_close_small.gif\" />";
	}

	public static function expand($title=null) {
		if(!$title) $title = _("espandi");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_expand.png\" />";
	}

	public static function calendar($name, $title=null) {
		if(!$title) $title = _("calendario");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_calendar.png\" id=\"cal_button_$name\" style=\"cursor:pointer;\" onclick=\"printCalendar(this, $(this).getParent().getPrevious().getChildren()[0])\"/>";
	}
	
	public static function info($title=null) {
		if(!$title) $title = _("informazioni");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_info.gif\" />";
	}
	
	public static function translation($title=null) {
		if(!$title) $title = _("traduzioni");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_translation.gif\" />";
	}

	public static function copy($title=null) {
		if(!$title) $title = _("copia");
		return "<img class=\"tooltip\" alt=\"".$title."\" title=\"".$title."\" src=\"".REL_ICONS."/ico_copy.gif\" />";
	}

}

?>
