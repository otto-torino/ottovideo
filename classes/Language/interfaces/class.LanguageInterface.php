<?php

class LanguageInterface {

	public static function manage() {
		
		if(!Auth::checkAuth()) header('Location: login.php');
		
		$urlParams = array('a', 'id');
		foreach($urlParams as $p) {
			$$p = null;
		}
		foreach($_GET as $k=>$v) {
			if(in_array($k,$urlParams)) $$k=cleanVar($v);
		}
		$lng = new Language((int) $id);
		
		if($a=='save' || ($a=='save' && $lng->id)) self::actionLanguage($lng);
		elseif($a=='delete') self::actionDelete($lng);

		$link_insert = "<a class=\"icon\" href=\"".$_SERVER['PHP_SELF']."?mng=lng&a=new\">".Icon::insert(_("nuova"))."</a>";
		$buffer = "<div class=\"area_title\">\n";
		$buffer .= "<div class=\"area_title_sx\">"._("Gestione lingue")."</div>\n";
		$buffer .= "<div class=\"area_title_dx\">$link_insert ".BACK."</div>\n";
		$buffer .= CLEAR;
		$buffer .= "</div>\n";
		$buffer .= "<div class=\"nav_left\">";
		$buffer .= self::lngList($lng);
		$buffer .= "</div>";
		$buffer .= "<div class=\"nav_right\">";
		if((string) $a=='new' || $lng->id) $buffer .= self::formLanguage($lng);
		else $buffer .= self::info();
		if($lng->id) $buffer .= self::formDelLanguage($lng);

		$buffer .= "</div>";
		$buffer .= CLEAR;

		return $buffer;

	}
	
	private static function lngList($lng) {
		
		$buffer = "<div>";
		$lngs = $lng->getAll();
		if(count($lngs)){
			$buffer .= "<ul>";
			foreach($lngs as $l) {
				$buffer .= "<li><a href=\"".$_SERVER['PHP_SELF']."?mng=lng&id=$l->id\">".$l->label."</a>".($l->active ? " &#160;<span class=\"tooltip\" title=\""._("attiva")."\">A</span>":"").($l->main ? " &#160;<span class=\"tooltip\" title=\""._("principale")."\">*</span>":"")."</li>";
			}
			$buffer .= "</ul>";
		}
		else $buffer .= "<p>"._("non risultano lingue registrate")."</p>";
		$buffer .= "</div>";

		return $buffer;

	}
	
	private static function formLanguage($lng) {
		
		$buffer = "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Modifica")."</legend>";
		$buffer .= "<form id=\"formlng\" name=\"formlng\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=lng&id=$lng->id&a=save\" onsubmit=\"return ValidateForm('formlng')\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formlng')."\"/>";
		
		$buffer .= "<div class=\"label\"><label for=\"label\" class=\"req\">"._("Etichetta")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"text\" name=\"label\" value=\"".htmlInput($lng->label)."\" /></div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"lng_code\" class=\"req\">"._("Lingua")." ".STAR."</label></div>";
		$codes = explode("_", $lng->code);
		$buffer .= "<div class=\"input\">".inputSelect('lng_code', self::lngCodes(), $codes[0], '', array(""=>""))."</div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"country_code\" class=\"req\">"._("Stato")." ".STAR."</label></div>";
		$buffer .= "<div class=\"input\">".inputSelect('country_code', self::countryCodes(), $codes[1], '', array(""=>""))."</div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"main\" class=\"req\">"._("Principale")." ".STAR."</label></div>";
		$p_a = array("1"=>_("si"), "0"=>_("no"));
		$buffer .= "<div class=\"input\">".inputSelect('main', $p_a, $lng->main, '', array(""=>""))."</div>".CLEAR;
		$buffer .= "<div class=\"label\"><label for=\"active\" class=\"req\">"._("Attiva")." ".STAR."</label></div>";
		$a_a = array("1"=>_("si"), "0"=>_("no"));
		$buffer .= "<div class=\"input\">".inputSelect('active', $a_a, $lng->active, '', array(""=>""))."</div>".CLEAR;
			
		$buffer .= "<div class=\"label\"><label for=\"submit\"></label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("salva")."\" ></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		return $buffer;
	}
	
	private function actionLanguage($lng) {
		
		if (!verifyFormToken('formlng')) {
  			die('CSRF Attack detected.');
		}

		$lng->label = cleanVar($_POST['label']);
		$lng->code = cleanVar($_POST['lng_code'])."_".cleanVar($_POST['country_code']);
		$lng->main = cleanVar($_POST['main']);
		$lng->active = cleanVar($_POST['active']);

		if($lng->main)
			foreach($lng->getAll() as $l) {
				$l->main = 'no';
				$l->updateDbData();
			}
		$lng->updateDbData();

		header('Location: admin.php?mng=lng');

	}

	private static function formDelLanguage($lng) {
	
		$buffer = "<div class=\"form\">";
		$buffer .= "<fieldset>";
		$buffer .= "<legend>"._("Eliminazione")."</legend>";
		$buffer .= "<form name=\"formdellng\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?mng=lng&id=$lng->id&a=delete\">";
		$buffer .= "<input type=\"hidden\" name=\"token\" value=\"".generateFormToken('formdellng')."\"/>";

		$buffer .= "<div class=\"label\"><label for=\"submit\">"._("Attenzione l'aliminazione è definitiva!")."</label></div>";
		$buffer .= "<div class=\"input\"><input type=\"submit\" name=\"submit\" value=\""._("elimina")."\" onclick=\"return confirmSubmit();\"></div>".CLEAR;

		$buffer .= "</form>";
		$buffer .= "</fieldset>";
		$buffer .= "</div>";

		return $buffer;

	}
	
	private function actionDelete($lng) {

		$lng->deleteDbData();

		header('Location: admin.php?mng=lng');
	}

	private static function info() {
	
		$buffer = "<div><p><b>"._("Informazioni")."</b></p>"._("Una lingua è definita come principale (*). Tutti gli inserimenti vengono effettuati nella lingua principale. Tutte le lingue definite attive (A) sono disponibili per le traduzioni. Quando le traduzioni per una data lingua non sono disponibili viene mostrato il testo nella lingua principale.")."</div>";
		return $buffer;

	}

	private static function lngCodes(){

		return array(
		'aa'=>'Afar',
		'ab'=>'Abkhazian',
		'ae'=>'Avestan',
		'af'=>'Afrikaans',
		'am'=>'Amharic',
		'ar'=>'Arabic',
		'as'=>'Assamese',
		'ay'=>'Aymara',
		'az'=>'Azerbaijani',
		'ba'=>'Bashkir',
		'be'=>'Byelorussian; Belarusian',
		'bg'=>'Bulgarian',
		'bh'=>'Bihari',
		'bi'=>'Bislama',
		'bn'=>'Bengali; Bangla',
		'bo'=>'Tibetan',
		'br'=>'Breton',
		'bs'=>'Bosnian',
		'ca'=>'Catalan',
		'ce'=>'Chechen',
		'ch'=>'Chamorro',
		'co'=>'Corsican',
		'cs'=>'Czech',
		'cu'=>'Church Slavic',
		'cv'=>'Chuvash',
		'cy'=>'Welsh',
		'da'=>'Danish',
		'de'=>'German',
		'dz'=>'Dzongkha; Bhutani',
		'el'=>'Greek',
		'en'=>'English',
		'eo'=>'Esperanto',
		'es'=>'Spanish',
		'et'=>'Estonian',
		'eu'=>'Basque',
		'fa'=>'Persian',
		'fi'=>'Finnish',
		'fj'=>'Fijian; Fiji',
		'fo'=>'Faroese',
		'fr'=>'French',
		'fy'=>'Frisian',
		'ga'=>'Irish',
		'gd'=>'Scots; Gaelic',
		'gl'=>'Gallegan; Galician',
		'gn'=>'Guarani',
		'gu'=>'Gujarati',
		'gv'=>'Manx',
		'ha'=>'Hausa (?)',
		'he'=>'Hebrew (formerly iw)',
		'hi'=>'Hindi',
		'ho'=>'Hiri Motu',
		'hr'=>'Croatian',
		'hu'=>'Hungarian',
		'hy'=>'Armenian',
		'hz'=>'Herero',
		'ia'=>'Interlingua',
		'id'=>'Indonesian (formerly in)',
		'ie'=>'Interlingue',
		'ik'=>'Inupiak',
		'io'=>'Ido',
		'is'=>'Icelandic',
		'it'=>'Italian',
		'iu'=>'Inuktitut',
		'ja'=>'Japanese',
		'jv'=>'Javanese',
		'ka'=>'Georgian',
		'ki'=>'Kikuyu',
		'kj'=>'Kuanyama',
		'kk'=>'Kazakh',
		'kl'=>'Kalaallisut; Greenlandic',
		'km'=>'Khmer; Cambodian',
		'kn'=>'Kannada',
		'ko'=>'Korean',
		'ks'=>'Kashmiri',
		'ku'=>'Kurdish',
		'kv'=>'Komi',
		'kw'=>'Cornish',
		'ky'=>'Kirghiz',
		'la'=>'Latin',
		'lb'=>'Letzeburgesch',
		'ln'=>'Lingala',
		'lo'=>'Lao; Laotian',
		'lt'=>'Lithuanian',
		'lv'=>'Latvian; Lettish',
		'mg'=>'Malagasy',
		'mh'=>'Marshall',
		'mi'=>'Maori',
		'mk'=>'Macedonian',
		'ml'=>'Malayalam',
		'mn'=>'Mongolian',
		'mo'=>'Moldavian',
		'mr'=>'Marathi',
		'ms'=>'Malay',
		'mt'=>'Maltese',
		'my'=>'Burmese',
		'na'=>'Nauru',
		'nb'=>'Norwegian Bokmål',
		'nd'=>'Ndebele, North',
		'ne'=>'Nepali',
		'ng'=>'Ndonga',
		'nl'=>'Dutch',
		'nn'=>'Norwegian Nynorsk',
		'no'=>'Norwegian',
		'nr'=>'Ndebele, South',
		'nv'=>'Navajo',
		'ny'=>'Chichewa; Nyanja',
		'oc'=>'Occitan; Provençal',
		'om'=>'(Afan) Oromo',
		'or'=>'Oriya',
		'os'=>'Ossetian; Ossetic',
		'pa'=>'Panjabi; Punjabi',
		'pi'=>'Pali',
		'pl'=>'Polish',
		'ps'=>'Pashto, Pushto',
		'pt'=>'Portuguese',
		'qu'=>'Quechua',
		'rm'=>'Rhaeto-Romance',
		'rn'=>'Rundi; Kirundi',
		'ro'=>'Romanian',
		'ru'=>'Russian',
		'rw'=>'Kinyarwanda',
		'sa'=>'Sanskrit',
		'sc'=>'Sardinian',
		'sd'=>'Sindhi',
		'se'=>'Northern Sami',
		'sg'=>'Sango; Sangro',
		'si'=>'Sinhalese',
		'sk'=>'Slovak',
		'sl'=>'Slovenian',
		'sm'=>'Samoan',
		'sn'=>'Shona',
		'so'=>'Somali',
		'sq'=>'Albanian',
		'sr'=>'Serbian',
		'ss'=>'Swati; Siswati',
		'st'=>'Sesotho; Sotho, Southern',
		'su'=>'Sundanese',
		'sv'=>'Swedish',
		'sw'=>'Swahili',
		'ta'=>'Tamil',
		'te'=>'Telugu',
		'tg'=>'Tajik',
		'th'=>'Thai',
		'ti'=>'Tigrinya',
		'tk'=>'Turkmen',
		'tl'=>'Tagalog',
		'tn'=>'Tswana; Setswana',
		'to'=>'Tonga (?)',
		'tr'=>'Turkish',
		'ts'=>'Tsonga',
		'tt'=>'Tatar',
		'tw'=>'Twi',
		'ty'=>'Tahitian',
		'ug'=>'Uighur',
		'uk'=>'Ukrainian',
		'ur'=>'Urdu',
		'uz'=>'Uzbek',
		'vi'=>'Vietnamese',
		'vo'=>'Volap@"{u}k; Volapuk',
		'wa'=>'Walloon',
		'wo'=>'Wolof',
		'xh'=>'Xhosa',
		'yi'=>'Yiddish (formerly ji)',
		'yo'=>'Yoruba',
		'za'=>'Zhuang',
		'zh'=>'Chinese',
		'zu'=>'Zulu'
		);
	}

	private static function countryCodes(){

		return array(
		'AD'=>'Andorra',
		'AE'=>'United Arab Emirates',
		'AF'=>'Afghanistan',
		'AG'=>'Antigua and Barbuda',
		'AI'=>'Anguilla',
		'AL'=>'Albania',
		'AM'=>'Armenia',
		'AN'=>'Netherlands Antilles',
		'AO'=>'Angola',
		'AQ'=>'Antarctica',
		'AR'=>'Argentina',
		'AS'=>'Samoa (American)',
		'AT'=>'Austria',
		'AU'=>'Australia',
		'AW'=>'Aruba',
		'AZ'=>'Azerbaijan',
		'BA'=>'Bosnia and Herzegovina',
		'BB'=>'Barbados',
		'BD'=>'Bangladesh',
		'BE'=>'Belgium',
		'BF'=>'Burkina Faso',
		'BG'=>'Bulgaria',
		'BH'=>'Bahrain',
		'BI'=>'Burundi',
		'BJ'=>'Benin',
		'BM'=>'Bermuda',
		'BN'=>'Brunei',
		'BO'=>'Bolivia',
		'BR'=>'Brazil',
		'BS'=>'Bahamas',
		'BT'=>'Bhutan',
		'BV'=>'Bouvet Island',
		'BW'=>'Botswana',
		'BY'=>'Belarus',
		'BZ'=>'Belize',
		'CA'=>'Canada',
		'CC'=>'Cocos (Keeling) Islands',
		'CD'=>'Congo (DemRep.)',
		'CF'=>'Central African Rep.',
		'CG'=>'Congo (Rep.)',
		'CH'=>'Switzerland',
		'CI'=>'Cote d\'Ivoire',
		'CK'=>'Cook Islands',
		'CL'=>'Chile',
		'CM'=>'Cameroon',
		'CN'=>'China',
		'CO'=>'Colombia',
		'CR'=>'Costa Rica',
		'CU'=>'Cuba',
		'CV'=>'Cape Verde',
		'CX'=>'hristmas Island',
		'CY'=>'Cyprus',
		'CZ'=>'Czech Republic',
		'DE'=>'Germany',
		'DJ'=>'Djibouti',
		'DK'=>'Denmark',
		'DM'=>'Dominica',
		'DO'=>'Dominican Republic',
		'DZ'=>'Algeria',
		'EC'=>'Ecuador',
		'EE'=>'Estonia',
		'EG'=>'Egypt',
		'EH'=>'Western Sahara',
		'ER'=>'Eritrea',
		'ES'=>'Spain',
		'ET'=>'Ethiopia',
		'FI'=>'Finland',
		'FJ'=>'Fiji',
		'FK'=>'Falkland Islands',
		'FM'=>'Micronesia',
		'FO'=>'Faeroe Islands',
		'FR'=>'France',
		'GA'=>'Gabon',
		'GB'=>'Britain (UK)',
		'GD'=>'Grenada',
		'GE'=>'Georgia',
		'GF'=>'French Guiana',
		'GH'=>'Ghana',
		'GI'=>'Gibraltar',
		'GL'=>'Greenland',
		'GM'=>'Gambia',
		'GN'=>'Guinea',
		'GP'=>'Guadeloupe',
		'GQ'=>'Equatorial Guinea',
		'GR'=>'Greece',
		'GS'=>'South Georgia and the South Sandwich Islands',
		'GT'=>'Guatemala',
		'GU'=>'Guam',
		'GW'=>'Guinea-Bissau',
		'GY'=>'Guyana',
		'HK'=>'Hong Kong',
		'HM'=>'Heard Island and McDonald Islands',
		'HN'=>'Honduras',
		'HR'=>'Croatia',
		'HT'=>'Haiti',
		'HU'=>'Hungary',
		'ID'=>'Indonesia',
		'IE'=>'Ireland',
		'IL'=>'Israel',
		'IN'=>'India',
		'IO'=>'British Indian Ocean Territory',
		'IQ'=>'Iraq',
		'IR'=>'Iran',
		'IS'=>'Iceland',
		'IT'=>'Italy',
		'JM'=>'Jamaica',
		'JO'=>'Jordan',
		'JP'=>'Japan',
		'KE'=>'Kenya',
		'KG'=>'Kyrgyzstan',
		'KH'=>'Cambodia',
		'KI'=>'Kiribati',
		'KM'=>'Comoros',
		'KN'=>'St Kitts and Nevis',
		'KP'=>'Korea (North)',
		'KR'=>'Korea (South)',
		'KW'=>'Kuwait',
		'KY'=>'Cayman Islands',
		'KZ'=>'Kazakhstan',
		'LA'=>'Laos',
		'LB'=>'Lebanon',
		'LC'=>'St Lucia',
		'LI'=>'Liechtenstein',
		'LK'=>'Sri Lanka',
		'LR'=>'Liberia',
		'LS'=>'Lesotho',
		'LT'=>'Lithuania',
		'LU'=>'Luxembourg',
		'LV'=>'Latvia',
		'LY'=>'Libya',
		'MA'=>'Morocco',
		'MC'=>'Monaco',
		'MD'=>'Moldova',
		'MG'=>'Madagascar',
		'MH'=>'Marshall Islands',
		'MK'=>'Macedonia',
		'ML'=>'Mali',
		'MM'=>'Myanmar (Burma)',
		'MN'=>'Mongolia',
		'MO'=>'Macao',
		'MP'=>'Northern Mariana Islands',
		'MQ'=>'Martinique',
		'MR'=>'Mauritania',
		'MS'=>'Montserrat',
		'MT'=>'Malta',
		'MU'=>'Mauritius',
		'MV'=>'Maldives',
		'MW'=>'Malawi',
		'MX'=>'Mexico',
		'MY'=>'Malaysia',
		'MZ'=>'Mozambique',
		'NA'=>'Namibia',
		'NC'=>'New Caledonia',
		'NE'=>'Niger',
		'NF'=>'Norfolk Island',
		'NG'=>'Nigeria',
		'NI'=>'Nicaragua',
		'NL'=>'Netherlands',
		'NO'=>'Norway',
		'NP'=>'Nepal',
		'NR'=>'Nauru',
		'NU'=>'Niue',
		'NZ'=>'New Zealand',
		'OM'=>'Oman',
		'PA'=>'Panama',
		'PE'=>'Peru',
		'PF'=>'French Polynesia',
		'PG'=>'Papua New Guinea',
		'PH'=>'Philippines',
		'PK'=>'Pakistan',
		'PL'=>'Poland',
		'PM'=>'St Pierre and Miquelon',
		'PN'=>'Pitcairn',
		'PR'=>'Puerto Rico',
		'PS'=>'Palestine',
		'PT'=>'Portugal',
		'PW'=>'Palau',
		'PY'=>'Paraguay',
		'QA'=>'Qatar',
		'RE'=>'Reunion',
		'RO'=>'Romania',
		'RU'=>'Russia',
		'RW'=>'Rwanda',
		'SA'=>'Saudi Arabia',
		'SB'=>'Solomon Islands',
		'SC'=>'Seychelles',
		'SD'=>'Sudan',
		'SE'=>'Sweden',
		'SG'=>'Singapore',
		'SH'=>'St Helena',
		'SI'=>'Slovenia',
		'SJ'=>'Svalbard and Jan Mayen',
		'SK'=>'Slovakia',
		'SL'=>'Sierra Leone',
		'SM'=>'San Marino',
		'SN'=>'Senegal',
		'SO'=>'Somalia',
		'SR'=>'Suriname',
		'ST'=>'Sao Tome and Principe',
		'SV'=>'El Salvador',
		'SY'=>'Syria',
		'SZ'=>'Swaziland',
		'TC'=>'Turks and Caicos Is',
		'TD'=>'Chad',
		'TF'=>'French Southern and Antarctic Lands',
		'TG'=>'Togo',
		'TH'=>'Thailand',
		'TJ'=>'Tajikistan',
		'TK'=>'Tokelau',
		'TM'=>'Turkmenistan',
		'TN'=>'Tunisia',
		'TO'=>'Tonga',
		'TP'=>'East Timor',
		'TR'=>'Turkey',
		'TT'=>'Trinidad and Tobago',
		'TV'=>'Tuvalu',
		'TW'=>'Taiwan',
		'TZ'=>'Tanzania',
		'UA'=>'Ukraine',
		'UG'=>'Uganda',
		'UM'=>'US minor outlying islands',
		'US'=>'United States',
		'UY'=>'Uruguay',
		'UZ'=>'Uzbekistan',
		'VA'=>'Vatican City',
		'VC'=>'St Vincent',
		'VE'=>'Venezuela',
		'VG'=>'Virgin Islands (UK)',
		'VI'=>'Virgin Islands (US)',
		'VN'=>'Vietnam',
		'VU'=>'Vanuatu',
		'WF'=>'Wallis and Futuna',
		'WS'=>'Samoa (Western)',
		'YE'=>'Yemen',
		'YT'=>'Mayotte',
		'YU'=>'Yugoslavia',
		'ZA'=>'South Africa',
		'ZM'=>'Zambia',
		'ZW'=>'Zimbabwe'
		);
	}


}

?>
