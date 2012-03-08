<?php
include('config.php');
include('paths.php');
include('const.php');
include(ABS_INCLUDE.S.'include.php');

session_name(SESSION_NAME);
session_start();

if(Auth::checkAuth()) header("Location: admin.php");

if(isset($_POST['usr']) && isset($_POST['pwd'])) {

	$usr = (string) cleanVar($_POST['usr']);
	$pwd = (string) cleanVar($_POST['pwd']);
	if(strlen($usr)>30 || strlen($pwd)>30) die(HACK_MSG);
	Db::openConnection();
	$query = "SELECT id, role FROM ".TBL_USERS." WHERE username='".((string) cleanVar($_POST['usr']))."' AND password='".md5((string) cleanVar($_POST['pwd']))."'";
	$rows = Db::selectQuery($query);
	if(count($rows)) {
		foreach($rows as $r) {
			$id = $r['id'];
			$role = $r['role'];
			$error = 0;
			$_SESSION['abv_userid'] = $id;
			$_SESSION['abv_userRole'] = $role;
		}
		header("Location: admin.php");
	}
	else $error = 1;
	Db::closeConnection();
}

$buffer = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
$buffer .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"it_IT\" xml:lang=\"it_IT\">\n";
$buffer .= "<head>\n";
$buffer .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";		
$buffer .= "<title>".TITLE." "._("Login")."</title>\n";
$buffer .= Css::authCss();
$buffer .= "</head>\n";

$buffer .= "<body>\n";

$buffer .= "<div id=\"container\">";
$buffer .= "<div style=\"font-weight:bold;\">Otto Video</div>";
$buffer .= "<div id=\"logoContainer\"></div>\n";


$buffer .= "<div id=\"loginField\">";
$buffer .= "<fieldset>";
$buffer .= "<legend>Login</legend>";
$buffer .= "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">";
$buffer .= "<table>";
$buffer .= "<tr>";
$buffer .= "<td>Username</td>";
$buffer .= "<td><input type=\"text\" name=\"usr\" value=\"\" /></td>";
$buffer .= "</tr>";
$buffer .= "<tr>";
$buffer .= "<td>Password</td>";
$buffer .= "<td><input type=\"password\" name=\"pwd\" value=\"\" /></td>";
$buffer .= "</tr>";
$buffer .= "<tr>";
$buffer .= "<td></td>";
$buffer .= "<td style=\"text-align:left;\"><input type=\"submit\" name=\"submit\" value=\"entra\" /></td>";
$buffer .= "</tr>";
$buffer .= "</table>";
$buffer .= "</form>";
$buffer .= "</fieldset>";
if($error) $buffer .= "<div class=\"error\">"._("Errore di autenticazione")."</div>";
$buffer .= "</div>";

$buffer .= CLEAR;

$buffer .= "</div>";

$buffer .= "</body>";
$buffer .= "</html>";

echo $buffer;

?>
