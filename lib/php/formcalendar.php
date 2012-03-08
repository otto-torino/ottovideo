<?php
/**
 * INSTRUCTIONS 
 * settings
 * 	- include the file formcalendar.css in main.css
 * 	- include the file formcalendar.js in javascript.php
 *  - upload the calendar icon
 *  - set the options provided by the class in the mod_formcalendar.php file
 * 
 * when using a form calendar appearing after a click on an input field:
 *  - simply call the GinoForm function cinput_date, and all will work.
 *  - decide in the input_date function if attach the onclick event also at the input field besides at the calendar icon 
 *  - the calendar will be drawn in a div container called calendarLayer
 *  - this div container appears with animations. 
 *  - No more than one div container is allowed at a time (opening one causes the closure of the other, if present)
 */
class formcalendar {

	private $_className;	
	private $_home;

	private $_input_field;
	
	private $_day;
	private $_month;
	private $_year;
	
	private $days;
	
	private $_month31 = array(1,3,5,7,8,10,12);
	private $_month30 = array(4,6,9,11);
	private $_monthfeb = array(2);
	
	private $_month_array;
	private $_year_array;
	
	private $_holidays_sign;
	private $_holidays;
	private $_check_sat_sun;
	private $_date_divider;
	
	private $_close_button;
	
	function __construct() {

		$this->_className = get_class($this);
		$this->_home = 'methodPointer.php';

		// OPTIONS
		$this->_holidays_sign = true;			// option to highlight holydays (true/false) 
		$this->_date_divider = "/";			// symbol used to separate day month and year
		$this->_check_sat_sun = true;			// option to highlight saturdays and sundays (true/false)
		// END 
		 
		$this->_holidays = array("01/01" => _("Capodanno"),
					 "06/01" => _("Epifania"),
					 "25/04" => _("Liberazione dell' Italia"),
					 "01/05" => _("Festa del lavoro"),
					 "02/06" => _("Festa della Repubblica Italiana"),
					 "15/08" => _("Assunzione"),
					 "01/11" => _("Ognissanti"),
					 "08/12" => _("Immaccolata Concezione"),
					 "25/12" => _("Natale"),
					 "26/12" => _("S. Stefano")
					);
								 
		// today date variables
		$this->_day = date("j"); 
		$this->_month = date("n"); 
		$this->_year = date("Y");
		
		$this->_days = array(_("Lu"), _("Ma"), _("Me"), _("Gi"), _("Ve"), _("Sa"), _("Do"));
		$title_close = _("chiudi");

		$this->_close_button = "<span class=\"cal_control\" title=\"".$title_close."\">X</span>";//"<img src=\"../img/close_button.jpg / alt=\"close\" title=\"close\">\"";
	
		$this->_month_array = array(1 => _("Gennaio"),
									2 => _("Febbraio"),
									3 => _("Marzo"),
									4 => _("Aprile"),
									5 => _("Maggio"),
									6 => _("Giugno"),
									7 => _("Luglio"),
									8 => _("Agosto"),
									9 => _("Settembre"),
									10 => _("Ottobre"),
									11 => _("Novembre"),
									12 => _("Dicembre")
		);
		
		for($i=1902;$i<2038;$i++) {
			$this->_year_array[$i] = $i;
		}
	
	}

	public function printCalendar() {
		
		$CALENDAR = '';
		
		$this->_input_field = (string) cleanVar($_POST['input_field']);
		if($this->_input_field=='null') {
			echo _("Calendario <br/> Errore tecnico");exit;
		}
		
		$month = isset($_POST['month']) ? (int) cleanVar($_POST['month']) : null;
		$year = isset($_POST['year']) ? (int) cleanVar($_POST['year']) : null;
		// if month paramether is passed
		if(!empty($month)) {
			if(in_array($month, $this->_monthfeb))	$CALENDAR .= $this->printMonthFeb($year);
			else $CALENDAR .= $this->printMonth($month, $year);
		}
		// otherwise it takes the current month
		else {
			$CALENDAR .= "<div class=\"bkg_calendar\">\n";
			$CALENDAR .= $this->printTop($this->_month, $this->_year);
			$CALENDAR .= "<div id=\"calendar_table\">";
			if(in_array($this->_month, $this->_monthfeb))	$CALENDAR .= $this->printMonthFeb($this->_year);
			else $CALENDAR .= $this->printMonth($this->_month, $this->_year);
			$CALENDAR .= "</div>\n";
			$CALENDAR .= "</div>\n";
		}
		
		echo $CALENDAR;
		exit();
	}
	
	private function printTop($month, $year) {
		
		$div = "calendar_table";
		$url = $this->_home."?pt[".$this->_className."-printCalendar]";
		
		$CALENDAR = '';
		$CALENDAR .= "<div id=\"super_top\" style=\"margin-bottom:5px;\">\n";
		$data = "month='+$(this).getProperty('value')+'&year='+$('sel_year').value+'&input_field=".$this->_input_field;
		$onchange = "onchange=\"sendPost('$url', '$data', '$div')\"";
		$CALENDAR .= inputSelect('sel_month', $this->_month_array, $month, $onchange);
		$data = "month='+$('sel_month').value+'&year='+$(this).getProperty('value')+'&input_field=".$this->_input_field;
		$onchange = "onchange=\"sendPost('$url', '$data', '$div')\"";
		$CALENDAR .= inputSelect('sel_year', $this->_year_array, $year, $onchange);
		$CALENDAR .= "</div>\n";
			
		return $CALENDAR;
	}
	
	private function printMonth($month, $year) {
		
		if(in_array($month, $this->_month31)) $type = 31; else $type = 30;
		
		$CALENDAR = '';
		
		$en_month = $this->getEnglishMonth($month);
	
		// get the month's first day 
		$first_day = date('D', strtotime("01 ".$en_month." ".$year.""));
				
		$CALENDAR .= "<table class=\"cal\">\n";
		$CALENDAR .= "<tr class=\"top\">\n";
		$CALENDAR .= "<td>".$this->_days[0]."</td><td>".$this->_days[1]."</td><td>".$this->_days[2]."</td><td>".$this->_days[3]."</td><td>".$this->_days[4]."</td><td>".$this->_days[5]."</td><td>".$this->_days[6]."</td>";
		$CALENDAR .= "</tr>\n";
		$first_row = $this->firstWeek($first_day, $month, $year);
		$CALENDAR .= $first_row[0];
		$last = $first_row[1];
		$second_row = $this->otherWeeks($last+1, $month, $year);
		$CALENDAR .= $second_row[0];
		$last = $second_row[1];
		$third_row = $this->otherWeeks($last+1, $month, $year);
		$CALENDAR .= $third_row[0];
		$last = $third_row[1];
		$fourth_row = $this->otherWeeks($last+1, $month, $year);
		$CALENDAR .= $fourth_row[0];
		$last = $fourth_row[1];
		$fifth_row = $this->otherWeeks($last+1, $month, $year);
		$CALENDAR .= $fifth_row[0];
		$last = $fifth_row[1];
		if($last<$type) {
			$fifth_row = $this->otherWeeks($last+1, $month, $year);
			$CALENDAR .= $fifth_row[0];
		}
		$CALENDAR .= "</table>\n";
		
		return $CALENDAR;
	}
	
	private function printMonthFeb($year) {
		
		$month = 2;
		
		$CALENDAR = '';
		
		$en_month = $this->getEnglishMonth($month);
				
		// get the month's first day
		$first_day = date('D', strtotime("01 ".$en_month." ".$year.""));
				
		$CALENDAR .= "<table class=\"cal\">\n";
		$CALENDAR .= "<tr class=\"top\">\n";
		$CALENDAR .= "<td>".$this->_days[0]."</td><td>".$this->_days[1]."</td><td>".$this->_days[2]."</td><td>".$this->_days[3]."</td><td>".$this->_days[4]."</td><td>".$this->_days[5]."</td><td>".$this->_days[6]."</td>";
		$CALENDAR .= "</tr>\n";
		$first_row = $this->firstWeek($first_day, $month, $year);
		$CALENDAR .= $first_row[0];
		$last = $first_row[1];
		$second_row = $this->otherWeeks($last+1, $month, $year);
		$CALENDAR .= $second_row[0];
		$last = $second_row[1];
		$third_row = $this->otherWeeks($last+1, $month, $year);
		$CALENDAR .= $third_row[0];
		$last = $third_row[1];
		$fourth_row = $this->otherWeeks($last+1, $month, $year);
		$CALENDAR .= $fourth_row[0];
		$last = $fourth_row[1];
		$fifth_row = $this->otherWeeks($last+1, $month, $year);
		$CALENDAR .= $fifth_row[0];
		$last = $fifth_row[1];
		
		$CALENDAR .= "</table>\n";
		
		return $CALENDAR;
	}
	
	private function firstWeek($first_day, $month, $year) {
		
		$first_row = array();
		
		// check if month contains current day
		if($month == $this->_month && $year == $this->_year)	$check_day = $this->_day;
		else $check_day = 32;
		
		$CALENDAR = "<tr>\n";
		
		if($first_day == 'Mon')	{
			for($i=1; $i<8; $i++) {
				if(!$this->checkHoliday($i, $month, $year))	$css = "";
				else $css = "class=\"signed\" title=\"".$this->checkHoliday($i, $month, $year)."\"";
				$date = $this->printInputForm($i,$month,$year);
				if($i == $check_day) {
					$CALENDAR .= "<td class=\"selected\">\n";
				}
				else {
					$CALENDAR .= "<td $css>\n"; 
				}
				$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">$i</span></td>\n";
			} 
			$last = 7;
		}
		elseif($first_day == 'Tue')	{
			$CALENDAR .= "<td></td>\n";
			for($i=1; $i<7; $i++) {
				if(!$this->checkHoliday($i, $month, $year))	$css = "";
				else $css = "class=\"signed\" title=\"".$this->checkHoliday($i, $month, $year)."\"";
				$date = $this->printInputForm($i,$month,$year);
				if($i == $check_day) {
					$CALENDAR .= "<td class=\"selected\">\n";
				}
				else {
					$CALENDAR .= "<td $css>\n"; 
				}
				$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">$i</span></td>\n";
			} 
			$last = 6;
		}
		elseif($first_day == 'Wed')	{
			$CALENDAR .= "<td></td><td></td>\n";
			for($i=1; $i<6; $i++) {
				if(!$this->checkHoliday($i, $month, $year))	$css = "";
				else $css = "class=\"signed\" title=\"".$this->checkHoliday($i, $month, $year)."\"";
				$date = $this->printInputForm($i,$month,$year);
				if($i == $check_day) {
					$CALENDAR .= "<td class=\"selected\">\n";
				}
				else {
					$CALENDAR .= "<td $css>\n"; 
				}
				$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">$i</span></td>\n";
			} 
			$last = 5;
		}
		elseif($first_day == 'Thu')	{
			$CALENDAR .= "<td></td><td></td><td></td>\n";
			for($i=1; $i<5; $i++) {
				if(!$this->checkHoliday($i, $month, $year))	$css = "";
				else $css = "class=\"signed\" title=\"".$this->checkHoliday($i, $month, $year)."\"";
				$date = $this->printInputForm($i,$month,$year);
				if($i == $check_day) {
					$CALENDAR .= "<td class=\"selected\">\n";
				}
				else {
					$CALENDAR .= "<td $css>\n"; 
				}
				$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">$i</span></td>\n";
			} 
			$last = 4;
		}
		elseif($first_day == 'Fri')	{
			$CALENDAR .= "<td></td><td></td><td></td><td></td>\n";
			for($i=1; $i<4; $i++) {
				if(!$this->checkHoliday($i, $month, $year))	$css = "";
				else $css = "class=\"signed\" title=\"".$this->checkHoliday($i, $month, $year)."\"";
				$date = $this->printInputForm($i,$month,$year);
				if($i == $check_day) {
					$CALENDAR .= "<td class=\"selected\">\n";
				}
				else {
					$CALENDAR .= "<td $css>\n"; 
				}
				$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">$i</span></td>\n";
			} 
			$last = 3;
		}
		elseif($first_day == 'Sat')	{
			$CALENDAR .= "<td></td><td></td><td></td><td></td><td></td>\n";
			for($i=1; $i<3; $i++) {
				if(!$this->checkHoliday($i, $month, $year))	$css = "";
				else $css = "class=\"signed\" title=\"".$this->checkHoliday($i, $month, $year)."\"";
				$date = $this->printInputForm($i,$month,$year);
				if($i == $check_day) {
					$CALENDAR .= "<td class=\"selected\">\n";
				}
				else {
					$CALENDAR .= "<td $css>\n"; 
				}
				$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">$i</span></td>\n";
			}
			$last = 2;
		}
		elseif($first_day == 'Sun')	{
			$CALENDAR .= "<td></td><td></td><td></td><td></td><td></td><td></td>\n";
			for($i=1; $i<2; $i++) {
				if(!$this->checkHoliday($i, $month, $year))	$css = "";
				else $css = "class=\"signed\" title=\"".$this->checkHoliday($i, $month, $year)."\"";
				$date = $this->printInputForm($i,$month,$year);
				if($i == $check_day) {
					$CALENDAR .= "<td class=\"selected\">\n";
				}
				else {
					$CALENDAR .= "<td $css>\n"; 
				}
				$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">$i</span></td>\n";
			}
			$last = 1;
		}
		
		$CALENDAR .= "</tr>\n";
		
		$first_row[0] = $CALENDAR;
		$first_row[1] = $last;
		
		return $first_row;
	}
	
	private function otherWeeks($begin_day, $month, $year) {
		
		// get month's number of days
		if(in_array($month, $this->_month31))	$type = 31;
		
		elseif(in_array($month, $this->_month30))	$type = 30;
		
		elseif(in_array($month, $this->_monthfeb)) {
			if(($year%4==0 && $year%100!=0) || $year%400==0) $type = 29;	
			elseif(!(($year%4==0 && $year%100!=0) || $year%400==0)) $type = 28;
		}
		
		// check if month contains current day
		if($month == $this->_month && $year == $this->_year)	$check_day = $this->_day;
		else $check_day = 32;
		
		$en_month = $this->getEnglishMonth($month);
		
		$CALENDAR = '';

		if($begin_day == ($type + 1)) return array('', '');
		
		$next1 = ($begin_day == $type) ? '': $begin_day+1;
		$next2 = ($next1 == $type || $next1 == '') ? '': $begin_day+2;
		$next3 = ($next2 == $type || $next2 == '') ? '': $begin_day+3;
		$next4 = ($next3 == $type || $next3 == '') ? '': $begin_day+4;
		$next5 = ($next4 == $type || $next4 == '') ? '': $begin_day+5;
		$next6 = ($next5 == $type || $next5 == '') ? '': $begin_day+6;
		
		$CALENDAR .= "<tr>\n";
		
		if(!$this->checkHoliday($begin_day, $month, $year))	$css = "";
		else $css = "class=\"signed\" title=\"".$this->checkHoliday($begin_day, $month, $year)."\"";		
		$date = $this->printInputForm($begin_day,$month,$year);
		if($begin_day == $check_day)	$CALENDAR .= "<td class=\"selected\">\n";
		else	$CALENDAR .= "<td $css>\n";
		$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">".$begin_day."</span></td>\n";
		
		if(!$this->checkHoliday($next1, $month, $year))	$css = "";
		else $css = "class=\"signed\" title=\"".$this->checkHoliday($next1, $month, $year)."\"";
		$date = $this->printInputForm($next1,$month,$year);
		if($next1 == $check_day)	$CALENDAR .= "<td class=\"selected\">\n";
		else	$CALENDAR .= "<td $css>\n";
		$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">".$next1."</span></td>\n";
		
		if(!$this->checkHoliday($next2, $month, $year))	$css = "";
		else $css = "class=\"signed\" title=\"".$this->checkHoliday($next2, $month, $year)."\"";
		$date = $this->printInputForm($next2,$month,$year);
		if($next2 == $check_day)	$CALENDAR .= "<td class=\"selected\">\n";
		else	$CALENDAR .= "<td $css>\n";
		$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">".$next2."</span></td>\n";
		
		if(!$this->checkHoliday($next3, $month, $year))	$css = "";
		else $css = "class=\"signed\" title=\"".$this->checkHoliday($next3, $month, $year)."\"";
		$date = $this->printInputForm($next3,$month,$year);
		if($next3 == $check_day)	$CALENDAR .= "<td class=\"selected\">\n";
		else	$CALENDAR .= "<td $css>\n";
		$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">".$next3."</span></td>\n";
		
		if(!$this->checkHoliday($next4, $month, $year))	$css = "";
		else $css = "class=\"signed\" title=\"".$this->checkHoliday($next4, $month, $year)."\"";
		$date = $this->printInputForm($next4,$month,$year);
		if($next4 == $check_day)	$CALENDAR .= "<td class=\"selected\">\n";
		else	$CALENDAR .= "<td $css>\n";
		$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">".$next4."</span></td>\n";
		
		if(!$this->checkHoliday($next5, $month, $year))	$css = "";
		else $css = "class=\"signed\" title=\"".$this->checkHoliday($next5, $month, $year)."\"";
		$date = $this->printInputForm($next5,$month,$year);
		if($next5 == $check_day)	$CALENDAR .= "<td class=\"selected\">\n";
		else	$CALENDAR .= "<td $css>\n";
		$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">".$next5."</span></td>\n";
		
		if(!$this->checkHoliday($next6, $month, $year))	$css = "";
		else $css = "class=\"signed\" title=\"".$this->checkHoliday($next6, $month, $year)."\"";
		$date = $this->printInputForm($next6,$month,$year);
		if($next6 == $check_day)	$CALENDAR .= "<td class=\"selected\">\n";
		else	$CALENDAR .= "<td $css>\n";
		$CALENDAR .= "<span class=\"link\" onclick=\"fillInputField('".$date."', '".$this->_input_field."')\">".$next6."</span></td>\n";
		
		$CALENDAR .= "</tr>\n";
		
		if($begin_day<$type-6) $last = $begin_day+6;
		else $last = $type;
		
		$other_row[0] = $CALENDAR;
		$other_row[1] = $last;
		
		return $other_row;
	}
	
	private function textualMonth($month) {
	
		switch($month) {
			case 1: return _("Gen");
			case 2: return _("Feb");
			case 3: return _("Mar");
			case 4: return _("Apr");
			case 5: return _("Mag");
			case 6: return _("Giu");
			case 7: return _("Lug");
			case 8: return _("Ago");
			case 9: return _("Set");
			case 10: return _("Ott");
			case 11: return _("Nov");
			case 12: return _("Dic");
		}
	}
	
	private function getEnglishMonth($month) {
	
		switch($month) {
			case 1: return "January";
			case 2: return "February";
			case 3: return "March";
			case 4: return "April";
			case 5: return "May";
			case 6: return "June";
			case 7: return "July";
			case 8: return "August";
			case 9: return "September";
			case 10: return "October";
			case 11: return "November";
			case 12: return "December";
		}
	}
	
	private function checkHoliday($day, $month, $year) {
		
		if(!$this->_holidays_sign) return false;
		
		if(empty($day)) return false;
		
		$en_month = $this->getEnglishMonth($month);
		
		$day = (strlen($day) == 1 && !empty($day))?"0".$day:$day;
		$month = (strlen($month) == 1 && !empty($month))?"0".$month:$month;
		
		// looking for holidays
		$date = $day."/".$month;
		if(array_key_exists($date, $this->_holidays)) return $this->_holidays[$date];
		
		if($this->_check_sat_sun) {
			$en_day = date('D', strtotime($day." ".$en_month." ".$year.""));
			if($en_day == 'Sat' || $en_day == 'Sun')	return " ";
			else return false;
		}
		else return false;
	}
	
	private function printInputForm($day, $month, $year) {
		
		$date = (strlen($day)==1)? "0".$day : $day;
		$date .= (strlen($month)==1)? $this->_date_divider."0".$month : $this->_date_divider.$month;
		$date .= $this->_date_divider.$year;
		
		return $date;
	}
}
?>
