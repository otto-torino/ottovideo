<?php

class Db {

	public static function openConnection() {

		static $hDB;

		if(isset($hDB)) return $hDB;

		$hDB = mysql_connect(DBHOST, DBUSER, DBPASSWD) or die("Failure connecting to DB");	
		$db_charset = mysql_query( "SHOW VARIABLES LIKE 'character_set_database'" );
		$charset_row = mysql_fetch_assoc( $db_charset );
		mysql_query( "SET NAMES '" . $charset_row['Value'] . "'" );
		unset( $db_charset, $charset_row );
		@mysql_select_db(DBNAME, $hDB) OR die("Failure selecting DB");
		return $hDB;

	}
	
	public static function closeConnection() {
		mysql_close();
	}
	
	public static function actionQuery($qry) {
		// insert, update, delete
		
		self::openConnection();
		$res = mysql_query($qry);
		if(!$res) return false;
		else return true;

	}
	
	public static function selectQuery($qry) {

		self::openConnection();
		$res = mysql_query($qry);
		if(!$res) {
			return false;
		} else {
			$dbresults = array();
			if(mysql_num_rows($res) > 0)
				while($rows=mysql_fetch_assoc($res)) $dbresults[]=$rows;
			mysql_free_result($res);
			return $dbresults;
		}
	}

	public function getFieldFromId($table, $field, $id) {
		
		$query = "SELECT $field FROM $table WHERE id='$id'";
		$a = $this->selectquery($query);
		if(!$a) return null;
		else {
			foreach($a as $b) {
				return $b[$field];
			}
		}
	}


}

?>
