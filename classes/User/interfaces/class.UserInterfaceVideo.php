<?php

class UserInterfaceVideo {

	public static function manage() {

		if(!Auth::checkAuth()) header('Location: login.php');
		
		$urlParams = array('a', 'id');
		foreach($urlParams as $p) {
			$$p = null;
		}
		foreach($_GET as $k=>$v) {
			if(in_array($k,$urlParams)) $$k=cleanVar($v);
		}
		$user = new User((int) $id);
		$title = ($user->id)?_("Modifica"):_("Inserimento");

		if($a=='save' && $user->id && (Auth::authRole()==1 || $user->id==$_SESSION['abv_userid'])) self::actionEditUser($user);
		elseif($a=='save' && Auth::authRole()==1) self::actionNewUser();
		elseif($a=='delete' && Auth::authRole()==1) self::actionDelUser($user);

		$link_insert = (Auth::authRole()==1)?"<a class=\"icon\" href=\"".$_SERVER['PHP_SELF']."?mng=usr&a=new\">".Icon::insert()."</a>":"";
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Utenti")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">$link_insert ".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
	
		$buffer .= "<div class=\"nav_left\">";
		$buffer .= self::usersList($user);
		$buffer .= "</div>";
		$buffer .= "<div class=\"nav_right\">";
		if($user->id && (Auth::authRole()==1 || $user->id==$_SESSION['abv_userid'])) $buffer .= self::formEditUser($user);
		elseif((string) $a=='new' && Auth::authRole()==1) $buffer .= self::formNewUser();
		else $buffer .= self::info();
		if(Auth::authRole()==1 && $user->id) $buffer .= self::formDelUser($user);
		$buffer .= "</div>";
		$buffer .= CLEAR;

		return $buffer;

	}

	private static function usersList($user) {
		
		$buffer = "<div>";
		$users = $user->getUsers(Auth::authRole()==1);
		if(count($users)){
			$buffer .= "<ul>";
			foreach($users as $u) {
				$buffer .= "<li><a href=\"".$_SERVER['PHP_SELF']."?mng=usr&id=$u->id\">".$u->username."</a></li>";
			}
			$buffer .= "</ul>";
		}
		$buffer .= "</div>";
		return $buffer;

	}

	private static function formEditUser($user) {

		$buffer = "<div class=\"form\">";
		if(Auth::authRole()==1) {
			$buffer .= "<fieldset>";
			$buffer .= "<legend>"._("Modifica")."</legend>";
			$buffer .= "<form id=\"formusr\" name=\"formusr\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=usr&id=$user->id&a=save\" onsubmit=\"return ValidateForm('formusr')\">";
			$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formusr')."\"/>";
			
			$buffer .= "<div class=\"label\"><label for=\"username\" class=\"req\">"._("Username")." ".STAR."</label></div>";
			$buffer .= "<div class=\"input\"><input type=\"text\" id=\"username\" name=\"username\" value=\"".htmlInput($user->username)."\" /></div>".CLEAR;
			$buffer .= "<div class=\"label\"><label for=\"role\">"._("Ruolo")."</label></div>";
			$role_a = array("1"=>_("amministratore"), "2"=>_("utente"));
			$buffer .= "<div class=\"input\">".inputSelect('role', $role_a, $user->role, '', array(""=>""))."</div>".CLEAR;
			$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
			$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;


			$buffer .= "</form>";
			$buffer .= "</fieldset>";
		}
		
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Modifica password")."</legend>";
		$buffer .= "<form id=\"formpusr\" name=\"formpusr\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=usr&id=$user->id&a=save\" onsubmit=\"return ValidateForm('formpusr')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formpusr')."\"/>";
		$buffer .= "<input type=\"hidden\" name=\"chgpwd\" value=\"1\"/>";
		
		$buffer .= "<div class=\"label\"><label for=\"password\" class=\"req\">"._("Password")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"password\" id=\"password\" name=\"password\" value=\"\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"cpassword\" class=\"req\">"._("Conferma password")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"password\" id=\"cpassword\" name=\"cpassword\" value=\"\" /></div>".CLEAR;

		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;


		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		return $buffer;
	}

	private function actionEditUser($user) {
		
		if (!(verifyFormToken('formusr') || verifyFormToken('formpusr'))) {
  			die('CSRF Attack detected.');
		}
		if($user->id != $_SESSION['abv_userid'] && Auth::authRole()!=1) die(HACK_MSG);
		$chgpwd = isset($_POST['chgpwd'])? (int) cleanVar($_POST['chgpwd']):0;

		if(!$chgpwd && Auth::authRole()==1) {
			$user->username = (string) cleanVar($_POST['username']);
			$user->role = (int) cleanVar($_POST['role']);
			if(!$user->username || !$user->role) {header('Location: admin.php?mng=usr&id='.$user->id.'&error=10');exit;}
			$user->updateDbData();
		}
		else {
			$pwd = (string) cleanVar($_POST['password']);
			$cpwd = (string) cleanVar($_POST['cpassword']);
			if(!$pwd || !$cpwd) {header('Location: admin.php?mng=usr&id='.$user->id.'&error=10');exit;}
			if($pwd !== $cpwd) {header('Location: admin.php?mng=usr&id='.$user->id.'&error=30');exit;}
			$user->password = md5($pwd);
			$user->updateDbData();
		}
		
		header('Location: admin.php?mng=usr');

	}
	
	private static function formNewUser() {
		
		if(Auth::authRole()!=1) die(HACK_MSG);

		$buffer = "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Inserimento")."</legend>";
		$buffer .= "<form id=\"formusr\" name=\"formusr\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=usr&a=save\" onsubmit=\"return ValidateForm('formusr')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formusr')."\"/>";
		
		$buffer .= "<div class=\"label\"><label for=\"username\" class=\"req\">"._("Username")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" id=\"username\" name=\"username\" value=\"\" /></div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"role\">"._("Ruolo")."</label></div>";
		$role_a = array("1"=>_("amministratore"), "2"=>_("utente"));
		$buffer .= "<div class=\"input\">".inputSelect('role', $role_a, '', '', array(""=>""))."</div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"password\" class=\"req\">"._("Password")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"password\" id=\"password\" name=\"password\" value=\"\" /></div>".CLEAR;
		
		$buffer .= "<div class=\"label\"><label for=\"cpassword\" class=\"req\">"._("Conferma password")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"password\" id=\"cpassword\" name=\"cpassword\" value=\"\" /></div>".CLEAR;
	
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		return $buffer;
	}

	private function actionNewUser() {
		
		if (!verifyFormToken('formusr')) {
  			die('CSRF Attack detected.');
		}
		if(Auth::authRole()!=1) die(HACK_MSG);

		$user = new User(null);

		$pwd = (string) cleanVar($_POST['password']);
		$cpwd = (string) cleanVar($_POST['cpassword']);
		if($pwd !== $cpwd) {header('Location: admin.php?mng=usr&id='.$user->id.'&error=30');exit;}

		$user->username = (string) cleanVar($_POST['username']);
		$user->role = (int) cleanVar($_POST['role']);
		$user->password = md5($pwd);

		if(!$pwd || !$cpwd || !$user->username || !$user->role) {header('Location: admin.php?mng=usr&a=save&error=10');exit;}
		$user->updateDbData();
		
		header('Location: admin.php?mng=usr');

	}
	
	private static function formDelUser($user) {
	
		$buffer = "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Eliminazione")."</legend>";
		$buffer .= "<form name=\"formdeluser\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=usr&id=$user->id&a=delete\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formdeluser')."\"/>";

		$buffer .= "<div class=\"label\"><label for=\"submit\">"._("Attenzione l'aliminazione è definitiva!")."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("elimina")."\" onclick=\"return confirmSubmit();\"></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		return $buffer;

	}

	private function actionDelUser($user) {

		if(Auth::authRole()!=1) die(HACK_MSG);

		$user->deleteDbData();

		header('Location: admin.php?mng=usr');
	}

	private static function info() {
	
		$buffer = "<div><p><b>"._("Informazioni")."</b></p>"._("Tutti gli utenti non amministratori hanno gli stessi privilegi e possono amministrare tutte le funzionalità del sistema, escluse le funzionalità riguardanti le utenze che sono privilegio dei soli amministratori.")."</div>";
		return $buffer;

	}

}

?>
