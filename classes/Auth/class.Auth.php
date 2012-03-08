<?php

class Auth {

	public static function checkAuth() {
	
		return isset($_SESSION['abv_userid']);
		
	}
	
	public static function authRole() {
	
		return (isset($_SESSION['abv_userRole']))? $_SESSION['abv_userRole']:null;
		
	}


}

?>
