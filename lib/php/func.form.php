<?php

function generateFormToken($formName)
{
  	$token = md5(uniqid(microtime(), true));
  	$_SESSION[$formName.'_token'] = $token;
  	return $token;
}

function verifyFormToken($formName)
{
  $index = $formName.'_token';
  // There must be a token in the session
  if (!isset($_SESSION[$index])) return false;
  // There must be a token in the form
  if (!isset($_POST['token'])) return false;
  // The token must be identical
  if ($_SESSION[$index] !== $_POST['token']) return false;
  return true;
}

function verifyTokenGet($formName)
{
  $index = $formName.'_token';
  // There must be a token in the session
  if (!isset($_SESSION[$index])) return false;
  // There must be a token in the form
  if (!isset($_GET['token'])) return false;
  // The token must be identical
  if ($_SESSION[$index] !== $_GET['token']) return false;
  return true;
}

function inputSelect($name, $data, $selected, $more, $first=array()) {
	
	if(is_array($data) && count($data)==0) return null;

	$other = empty($more)? "": "$more";
	$select_output = "<select id=\"$name\" name=\"$name\" $other>\n";
	if(count($first)==1) $select_output .= "<option value=\"".key($first)."\">".current($first)."</option>\n";
	
	if(is_array($data)) {
		foreach($data as $k=>$v) {
			$select_output .= "<option value=\"$k\" ".(($k==$selected)?"selected=\"selected\"":"").">".htmlInput($v)."</option>\n";
		}	
	}
	else {
		$result = mysql_query($data);
		if(mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				list($val1, $val2) = $row;
				$select_output .= "<option value=\"$val1\" ".(($val1==$selected)?"selected=\"selected\"":"").">".htmlInput($val2)."</option>\n";
			}
		}
		else return null;
	}

	$select_output .= "</select>\n";

	return $select_output;
}

function inputRadio($name, $data, $selected, $js, $mode='h') {

	if(is_array($data) && count($data)==0) return null;

	$radio_output = '';
	$spacing = ($mode=='v')?"<br/>":" &#160; ";

	if(is_array($data)) {
		foreach($data as $k=>$v) {
			$radio_output .= "<input type=\"radio\" name=\"$name\" id=\"$name\" value=\"$k\" ".(($k==$selected)?"checked=\"checked\"":"")." $js/> ".htmlInput($v).$spacing."\n";
		}	
	}

	return $radio_output;

}

function inputDate($name, $value, $input_click=false) {

	$other = ($input_click)? "onclick=\"printCalendar($(this).getParent().getNext().getChildren()[0], this)\" readonly=\"readonly\"":"";
	$output = "<div style=\"float:left\">";
	$output .= "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" size=\"10\" maxlength=\"10\" style=\"width:auto;\" $other/>";
	$output .= "</div>";
	$output .= "<div style=\"float:left;padding-top:0px;margin-left:2px\">";
	$output .= Icon::calendar($name);
	$output .= "</div>";
	$output .= CLEAR;

	return $output;
}
?>
