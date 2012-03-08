<?php

include('config.php');
include('paths.php');
include('const.php');
include(ABS_INCLUDE.S.'include.php');

session_name(SESSION_NAME);
session_start();

include('language.php');  // sets SESSION['lng']

if(!Auth::checkAuth()) header('Location: login.php');

$buffer = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
$buffer .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"it_IT\" xml:lang=\"it_IT\">\n";
$buffer .= "<head>\n";
$buffer .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";		
$buffer .= "<title>".TITLE." "._("Amministrazione")."</title>\n";
$buffer .= Css::adminCss();
$buffer .= Javascript::mootoolsLib();
$buffer .= Javascript::ajaxLib();
$buffer .= Javascript::abiToolsLib();
$buffer .= Javascript::abidiMenuLib();
$buffer .= Javascript::abiCanvasShadow();
$buffer .= Javascript::formLib();
$buffer .= Javascript::translationLib();
$buffer .= "<script type=\"text/javascript\">
		var r = (Browser.Engine.gecko)?15:5;
		window.addEvent('domready',function(){
        		// 'shadow' is the ID of the element
        		$('mainContainer').AbiCanvasShadow({
                		opacity: 0.2,
               	 		size: 8,
                		radius: r,
       			        color: '#000000'
        		});     

			// tooltips
			var myTips = new Tips('[class$=tooltip]', {className: 'tips'});
		});
		function updateTooltip() {var myTips = new Tips('[class$=tooltip]', {className: 'tips'});}
	   </script>";
$buffer .= "</head>\n";

$buffer .= "<body>\n";

$mng = (isset($_GET['mng']))? (string) cleanVar($_GET['mng']):null;
$type = (isset($_GET['type']))? (string) cleanVar($_GET['type']):null;
$item = (isset($_GET['item']))? (string) cleanVar($_GET['item']):null;

$sm = null;
if($mng && $mng == 'logout') {$_SESSION=array();session_destroy();header('Location: login.php');}
elseif($mng && $mng == 'ctg') {$contents = CategoryInterfaceVideo::manage(); $sm = 'm2';}
elseif($mng && $mng == 'spotctg') {$contents = SpotCategoryInterfaceVideo::manage(); $sm = 'm24';}
elseif($mng && $mng == 'item') {$contents = VideoInterface::manage(); $sm = 'm3';}
elseif($mng && $mng == 'prg') {$contents = ScheduleInterfaceVideo::manage(); $sm = 'm4';}
elseif($mng && $mng == 'com') {$contents = CommentInterface::manage(); $sm = 'm25';}
elseif($mng && $mng == 'rep') {$contents = ReportInterface::manage(); $sm = 'm26';}
elseif($mng && $mng == 'layout') {
    $contents = LayoutInterface::manage();
    if($type=='ondemand') $sm = 'm6';
    elseif($type=='onair') $sm = 'm7';
    elseif($type=='onairbox') $sm = 'm17';
    elseif($type=='live') $sm = 'm22';
}
elseif($mng && $mng == 'conf') {
    $contents = Configuration::manage(); 
    if($item=='server') $sm = 'm9';
    elseif($item=='player') $sm = 'm10';
    elseif($item=='parent') $sm = 'm20';
    elseif($item=='live') $sm = 'm21';
    else $sm = 'm11';
}
elseif($mng && $mng == 'ondemand') {$contents = OnDemand::manage(); $sm = 'm13';}
elseif($mng && $mng == 'onair' && $type=='onairbox') {$contents = OnAir::manage(); $sm = 'm18';}
elseif($mng && $mng == 'live') {$contents = Live::manage(); $sm = 'm23';}
elseif($mng && $mng == 'onair') {$contents = OnAir::manage(); $sm = 'm14';}
elseif($mng && $mng == 'usr') {$contents = UserInterfaceVideo::manage(); $sm = 'm15';}
elseif($mng && $mng == 'lng') {$contents = LanguageInterface::manage(); $sm = 'm19';}
else $contents = GENERAL_INFO;

$buffer .= "<div id=\"mainContainer\">";
$buffer .= "<div id=\"container\">";

$buffer .= "<div id=\"topBar\">";
$buffer .= "<div id=\"logoContainer\" onclick=\"location.href='admin.php'\" title=\""._("amministrazione")."\" class=\"tooltip\" style=\"cursor:pointer;\"></div>"; 
$options = "{";
$options .= "fmode: 'horizontal',";
$options .= "initShowIcon: true,";
$options .= "clickEvent: false,";
$options .= "selectVoiceSnake: true";
$options .= "}";
$buffer .= "<script type=\"text/javascript\">\n";
$buffer .= "if(Browser.Engine.webkit){
		window.addEvent('domready',function() {var myMenu = new AbidiMenu('adminMenu', $options);}.delay(200,this));
	    }
	    else {
		window.addEvent('domready',function() {var myMenu = new AbidiMenu('adminMenu', $options);});
	    }";
$buffer .= "</script>\n"; 
$buffer .= "<ul id=\"adminMenu\" class=\"mainmenu\">";
$buffer .= "<li id=\"m1\" class=\"".(($sm=='m1')?"selectedVoice":"unselectedVoice")."\"><a>"._("Gestione file")."</a><ul>";
$buffer .= "<li id=\"m2\" class=\"".(($sm=='m2')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=ctg\">"._("Categorie")."</a></li>";
$buffer .= "<li id=\"m24\" class=\"".(($sm=='m24')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=spotctg\">"._("Categorie Spot")."</a></li>";
$buffer .= "<li id=\"m3\" class=\"".(($sm=='m3')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=item\">"._("Video/Spot")."</a></li>";
$buffer .= "</ul></li>";
$buffer .= "<li id=\"m25\" class=\"".(($sm=='m25')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=com\">"._("Commenti")."</a></li>";
$buffer .= "<li id=\"m26\" class=\"".(($sm=='m26')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=rep\">"._("Report")."</a></li>";
$buffer .= "<li id=\"m4\" class=\"".(($sm=='m4')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=prg\">"._("Palinsesto")."</a></li>";
$buffer .= "<li id=\"m5\" class=\"".(($sm=='m5')?"selectedVoice":"unselectedVoice")."\"><a>"._("Layout")."</a><ul>";
$buffer .= "<li id=\"m6\" class=\"".(($sm=='m6')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=layout&type=ondemand\">"._("On demand")."</a></li>";
$buffer .= "<li id=\"m7\" class=\"".(($sm=='m7')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=layout&type=onair\">"._("On air")."</a></li>";
$buffer .= "<li id=\"m7\" class=\"".(($sm=='m17')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=layout&type=onairbox\">"._("On air Box")."</a></li>";
$buffer .= "<li id=\"m22\" class=\"".(($sm=='m22')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=layout&type=live\">"._("Live")."</a></li>";
$buffer .= "</ul></li>";
$buffer .= "<li id=\"m8\" class=\"".(($sm=='m8')?"selectedVoice":"unselectedVoice")."\"><a>"._("Configurazione")."</a><ul>";
$buffer .= "<li id=\"m20\" class=\"".(($sm=='m20')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=conf&item=parent\">"._("Sito ospitante")."</a></li>";
$buffer .= "<li id=\"m21\" class=\"".(($sm=='m21')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=conf&item=live\">"._("Live streaming")."</a></li>";
$buffer .= "<li id=\"m9\" class=\"".(($sm=='m9')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=conf&item=server\">"._("Server")."</a></li>";
$buffer .= "<li id=\"m10\" class=\"".(($sm=='m10')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=conf&item=player\">"._("Player")."</a></li>";
$buffer .= "<li id=\"m11\" class=\"".(($sm=='m11')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=conf&item=list\">"._("Lista")."</a></li>";
$buffer .= "</ul></li>";
$buffer .= "<li id=\"m12\" class=\"".(($sm=='m12')?"selectedVoice":"unselectedVoice")."\"><a>"._("Pubblica")."</a><ul>";
$buffer .= "<li id=\"m13\" class=\"".(($sm=='m13')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=ondemand\">"._("On Demand")."</a></li>";
$buffer .= "<li id=\"m14\" class=\"".(($sm=='m14')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=onair\">"._("On Air")."</a></li>";
$buffer .= "<li id=\"m14\" class=\"".(($sm=='m18')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=onair&type=onairbox\">"._("On Air Box")."</a></li>";
$buffer .= "<li id=\"m23\" class=\"".(($sm=='m23')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=live\">"._("Live")."</a></li>";
$buffer .= "</ul></li>";
$buffer .= "<li id=\"m15\" class=\"".(($sm=='m15')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=usr\">"._("Utenti")."</a></li>";
$buffer .= "<li id=\"m19\" class=\"".(($sm=='m19')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=lng\">"._("Lingue")."</a></li>";
if(Auth::checkAuth()) $buffer .= "<li id=\"m16\" class=\"".(($sm=='m16')?"selectedVoice":"unselectedVoice")."\"><a href=\"".$_SERVER["PHP_SELF"]."?mng=logout\">"._("Logout")."</a></li>";
$buffer .= "</ul>";
$buffer .= CLEAR;
$buffer .= "</div>";

$buffer .= "<div class=\"contents\">";

$buffer .= $contents;

$buffer .= "</div>";

$buffer .= "<div id=\"footer_credits\">";
$buffer .= "Realizzato da :::  <a class=\"tooltip\" title=\""._("Otto multimedia")."\" href=\"http://www.otto.to.it\" target=\"_blank\">Otto srl</a>";
$buffer .= "</div>";

$buffer .= "</div>";
$buffer .= "<div id=\"error\">".$errorMessage."</div>";
$buffer .= "</div>";

$buffer .= "</body>";
$buffer .= "</html>";

echo $buffer;


?>
