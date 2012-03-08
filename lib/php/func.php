<?php

function extension($filename) {

	$filename_a = explode(".", $filename);
	return end($filename_a);

}

function searchFolderFiles($dir){

	$filenames = array();
	$dp = opendir($dir);
	while($file = readdir($dp))
	{
		if($file != "." AND $file != "..")
			$filenames[] = $file;
	}
	return $filenames;
}

function fileName($filename, $folder) {

	if($folder[strlen($folder)-1]!="/") $folder .= "/";
	$fName = str_replace(" ", "_", $filename);
	$f = $fName;
	$run = true;
	$i=1;
	while($run) {
		if(!in_array($f, searchFolderFiles($folder))) {$run = false;}
		else $f = $i++."_".$fName;
	}
	return $f;
}

function upload($file_tmp, $file_name, $uploaddir){
		
	if($uploaddir[strlen($uploaddir)-1]!="/") $uploaddir .= "/";
	$uploadfile = $uploaddir.$file_name;
	if(move_uploaded_file($file_tmp, $uploadfile)) return true;
	else return false;
}

function share($site, $url, $title=null, $description=null) {
	if($site=='facebook') {
		$buffer = "<a target=\"_blank\" name=\"fb_share\" href=\"http://www.facebook.com/sharer.php?u=".urlencode($url)."&t=title\"><img src=\"".REL_IMG."/share_facebook.png\" alt=\"Share on Facebook\" title=\"Share on facebook\"></a>";
		
	}
	elseif($site=='twitter') {
		$buffer = "<a target=\"_blank\" href=\"http://twitter.com/home?status=Watching%20video ".urlencode($url)."\" title=\""._("condividi su Twitter")."\"><img src=\"".REL_IMG."/share_twitter.png\" alt=\"Share on Twitter\" title=\"Share on twitter\"></a>";
	}

	return $buffer;

}

/**
* Truncates html text.
*
* Cuts a string to the length of $length and replaces the last characters
* with the ending if the text is longer than length. 
*Can strip tags or controlo their closure
*
* @param string  $html Html string to truncate.
* @param integer $length Length of returned string, including ellipsis.
* @param string  $ending Ending to be appended to the trimmed string.
* @param boolean $strip_tags If true, html tags are replaced by nothing
* @param boolean $cut_words If false, returned string will not be cut mid-word
* @return string Trimmed string.
*/
function cutHtmlText($html, $length, $ending, $strip_tags, $cut_words, $cut_images) {
	
	/*
		regular expressions to intercept tags
	*/
	$opened_tag = "<\w+\s*([^>]*[^\/>]){0,1}>";  // i.e. <p> <b> ...
	$closed_tag = "<\/\w+\s*[^>]*>";				// i.e. </p> </b> ...
	$openended_tag = "<\w+\s*[^>]*\/>";			// i.e. <br/> <img /> ...	
	$cutten_tag = "<\w+\s*[^>]*$";				// i.e. <img src="" 
	$reg_expr_img = "/<img\s*[^>]*\/>/is";      
	/* 
		Check: if text is shorter than length (tags excluded) return $html
		with or without tags
	*/
	$reg_expr = "/$opened_tag|$closed_tag|$openended_tag/is";
	$text = preg_replace($reg_expr, '', $html);
	if (strlen($text) <= $length) {
		if(!$strip_tags) {
			if($cut_images) {
				$html = preg_replace($reg_expr_img, "", $html);
			}
			return $html;
		}
		else return $text;
	}
	
	/*
		else if $strip_tags s false...
	*/
	if(!$strip_tags) {
	
		// splits all html-tags to scanable lines
		$reg_expr = "/(<\/?\w+\s*[^>]*\/?>)?([^<>]*)/is";
 		preg_match_all($reg_expr, $html, $lines, PREG_SET_ORDER);
 		/*
 			now 
 			- in $lines[$i] are listed all the matches with the regular expression:
 			  $lines[0]: first match
 			  $lines[1]: second match ...
 			  
 			- $lines[$i][0] contains the wide matching string
 			- $lines[$i][1] contains the matching with (<\/?\w+\s*[^>]*\/?>), that is opened or    
 			  closed ore openclosed tags
 			- $lines[$i][2]contains the matching with ([^<>]*) that is the text inside the tag
 			  or between a tag and another
 		*/
 		$total_length = 0;
 		$tags_opened = array();
  		$partial_html = '';
 		
 		foreach ($lines as $line_matchings) {
    		/*
    			$line_matchings[1] contains tags
    			$line_matchings[2] contains text contained in tags
    			
    			Check: what kind of tag is? open, close, openclose?
    		*/
   			if (!empty($line_matchings[1])) {
   				$strip_this_tag = 0;
   				$reg_expr_oc = "/".$openended_tag."$/is";
   				$reg_expr_o = "/<(\w+)\s*([^>]*[^\/>]){0,1}>$/is";
   				$reg_expr_c = "/<\/(\w+)>$/is";
   				// search img tags
   				if(preg_match($reg_expr_img, $line_matchings[1]) && $cut_images) {
                	$strip_this_tag = 1;
                }
                // search openended tags
                elseif (preg_match($reg_expr_oc, $line_matchings[1])) {
                	// nothing: doesn't encrease the count of characters
                	// and doesn't need a closure
                }
                // search opened tags
                elseif(preg_match($reg_expr_o, $line_matchings[1], $tag_matchings)) {
                	// open tag
                	// add tag to the beginning of $open_tags list
 					array_unshift($tags_opened, strtolower($tag_matchings[1]));
                }
                // search closed tags
                elseif(preg_match($reg_expr_c, $line_matchings[1], $tag_matchings)) {
                	// close tag
                	// delete tag from $open_tags list (as it has been already closed)
                	$pos = array_search($tag_matchings[1], $tags_opened);
  					if ($pos !== false) {
  						unset($tags_opened[$pos]);
  					}
                }
                // add html-tag to $truncate'd text
				if(!$strip_this_tag) $partial_html .= $line_matchings[1];
   				
   			}
   			/*
   				Calculate the lenght of the text inside tags and replace considering html entities one size characters
   			*/
   			$reg_exp_entities = '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i';
   			$content_length = strlen(preg_replace($reg_exp_entities, ' ', $line_matchings[2]));
   			
   			if ($total_length+$content_length> $length) {
   			
   				$left = $length - $total_length;
   				$entities_length = 0;
   				
   				// search for html entities (l'entities conta come un carattere, ma nell'html ne uccupa di più, quindi dobbiamo fare in modo di includere completament l'entities, cioè il suo codice e contarlo interamente come un singolo carattere: scaliamo uno da $left ed aggiungiamo $entities_length all alunghezza della substring)
				if(preg_match_all($reg_exp_entities, $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						}
						else {
							// no more characters left
							break;
						}
					}
				}
				
				$partial_html .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
  				break;
				  			
   			}
   			else {
				$partial_html .= $line_matchings[2];
  				$total_length += $content_length;
			}
   			
   			// if the maximum length is reached, get off the loop
			if($total_length>= $length) break;

		}
	}
	else {
		// considero solamente il testo puro
     	$partial_html = substr($text, 0, $length);
	}
	
	// if the words shouldn't be cut in the middle...
    if (!$cut_words) {
       //search the last occurance of a space or an end tag
       $spacepos = strrpos($partial_html, ' ');
       $endtagpos = strrpos($partial_html, '>');
       if(isset($spacepos) || isset($endtagpos)) {
       		//cut the text in this position
       		$cutpos = ($spacepos<$endtagpos)? ($endtagpos+1) : $spacepos;
       		$partial_html = substr($partial_html, 0, $cutpos);
       }
    }
	
	// add the ending characters to the partial text
	$partial_html .= $ending;
	
	/*
		Se non ho strippato i tag devo chiudere tutti quelli rimasti aperti
	*/
	if(!$strip_tags) {
    	// close all unclosed html tags
    	foreach ($tags_opened as $tag) {
    		$partial_html .= '</' . $tag . '>';
    	}
    }
             
    return $partial_html;	

}

?>
