<?php
include('config.php');
include('paths.php');
include('const.php');
include(ABS_INCLUDE.S.'include.php');

session_name(SESSION_NAME);
session_start();

$urlclasses = array('categoryInterfaceVideo', 'LayoutInterface', 'OnAir', 'OnDemand', 'ScheduleInterfaceVideo', 'UserInterfaceVideo', 'VideoInterface', 'VideoList', 'formcalendar', 'Translation');

if(isset($_REQUEST['pt'])) {
	$mypointer_array= $_REQUEST['pt'];
	$mypointer = key($mypointer_array);
	
	$pfc = explode('-', $mypointer);
	$MYCLASS = $pfc[0];
	$MYFUNCTION = $pfc[1];
	
	if(!in_array($MYCLASS, $urlclasses)) die(HACK_MSG);
	
	$MYOBJECT = new $MYCLASS;
	
	$buffer = $MYOBJECT->$MYFUNCTION();
	
	if(!empty($buffer)) echo $buffer;
	
	exit();
}
?>
