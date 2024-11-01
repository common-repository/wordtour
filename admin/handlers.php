<?php
require_once(WT_PLUGIN_PATH."dwoo/dwooAutoload.php");
require_once (WT_PLUGIN_CLASS_PATH."class.theme.php");
require_once (WT_PLUGIN_CLASS_PATH."class.component.php");
require_once (WT_PLUGIN_CLASS_PATH."class.dbprepere.php");
require_once (WT_PLUGIN_CLASS_PATH."class.object.php");
require_once (WT_PLUGIN_CLASS_PATH."class.event.php");
require_once (WT_PLUGIN_CLASS_PATH."class.artist.php");
require_once (WT_PLUGIN_CLASS_PATH."class.track.php");
require_once (WT_PLUGIN_CLASS_PATH."class.album.php");
require_once (WT_PLUGIN_CLASS_PATH."class.gallery.php");
require_once (WT_PLUGIN_CLASS_PATH."class.venue.php");
require_once (WT_PLUGIN_CLASS_PATH."class.tour.php");
require_once (WT_PLUGIN_CLASS_PATH."class.comment.php");
require_once (WT_PLUGIN_CLASS_PATH."class.rsvp.php");
require_once (WT_PLUGIN_CLASS_PATH."class.social.php");
require_once (WT_PLUGIN_CLASS_PATH."class.attachment.php");
require_once (WT_PLUGIN_CLASS_PATH."class.post.php");
require_once (WT_PLUGIN_CLASS_PATH."class.eventbrite.php");
require_once ("panels.php");


//NOTE: you must use a trailing slash to identify a directory
function wt_is_writable($path) {
	if ($path{strlen($path)-1}=='/')
	    return wt_is_writable($path.uniqid(mt_rand()).'.tmp');
	
	if (file_exists($path)) {
	    if (!($f = @fopen($path, 'r+')))
	        return false;
	    fclose($f);
	    return true;
	}
	
	if (!($f = @fopen($path, 'w')))
	    return false;
	fclose($f);
	unlink($path);
	return true;
}

function wt_script_js($file = ""){
	global $_wt_options;
	if(!empty($file)) {
		include "$file.js.php";
	}
}

function getQueryString($key = '') {
	if(isset($_GET[$key])) {
		return $_GET[$key] ;
	}
	return '' ;	
}
			
function wt_get_map_str($values = array()){
	$address = array();
	$country = "";
	if(!empty($values["venue_address"])) $address[] = $values["venue_address"];
	if(!empty($values["venue_city"])) $address[] = $values["venue_city"];
	if(!empty($values["venue_state"])) $address[] = get_state_by_name($values["venue_state"]);
	if(!empty($values["venue_country"])) $country = get_country_by_name($values["venue_country"]);
	
	return array("address"=>implode($address,","),"country"=>$country); 
}

// exclude $_get params added by wordtour
function wt_get_cleanurl($qs = array()){
	$url = get_bloginfo("url");
	$params = array();
	$exclude = array("wt_page");
	if(count($_GET)) {
		foreach($_GET as $key=>$value) {
			if(!in_array($key,$exclude)) $params[] = "$key=$value";
		}	
	}
	$qs = array_merge($params,$qs);
	if(count($qs)) return $url."?".implode("&",$qs);
	return $url; 
}


function get_countries() {
	global $_wt_countries;
	return $_wt_countries;
}

function get_country_by_code($code='') {
	if(empty($code)) return "";
	$c = get_countries() ;
	return ($c[$code]) ? ($c[$code]) : "";
}

function get_country_by_name($country='') {
	$c = get_countries() ;
	foreach($c as $code=>$name){
		if(strtolower($country) === strtolower($name)) {
			return $code ;
			break;	
		}
	}	
	return ""; 
}

function get_states() {
	global $_wt_states;
	return $_wt_states;
}

function get_state_by_code($code='') {
	if(empty($code)) return "";
	$s = get_states() ;
	return ($s[$code]) ? ($s[$code]) : "";
}

function get_state_by_name($state='') {
	$c = get_states() ;
	foreach($c as $code=>$name){
		if(strtolower($state) === strtolower($name)) {
			return $code ;
			break;	
		}
	}
	return ""; 
}

function get_all_status() {
	return array("active"=>"Active","onsale"=>"On Sale","soldout"=>"Sold Out","cancelled"=>"Cancelled","postponed"=>"Postponed");
}


/* Event Type */
function get_all_event_type() {
	$event_type = get_option("wordtour_event_type");
	if(!$event_type) {
		$event_type = array("event","competition","concert","conference","jam session","festival","television special","session","premiere","party","recital");
		update_option("wordtour_event_type",$event_type); 
	}
	return $event_type;
}

function wordtour_add_event_type($type = "") {
	try{
		$event_type = get_all_event_type();
		if(!empty($type) && !in_array($type,$event_type)) {
   			$event_type[] = $type;
   			update_option("wordtour_event_type",$event_type);	
		};
	} catch (Exception $e){};
}
/* Genre */
function wordtour_add_genre($genre = "") {
	try{
		$genres = wordtour_get_all_genre();
		$genre = strtolower($genre);
		if(!empty($genre) && !in_array($genre,$genres)) {
   			$genres[] = $genre;
   			update_option("wordtour_genre",serialize($genres));	
		};
	} catch (Exception $e){};
}

function wordtour_get_all_genre() {
	$genres = get_option("wordtour_genre");
	if(!$genres) {
		$genres = serialize(array());
		update_option("wordtour_genre",$genres); 
	}
	return unserialize($genres);
}

/* --------------------- */

function array_associate_val_to_key($array = array(),$key,$value) {
	if(!is_array($array)) return array();
	$a = array();
	foreach($array as $v) {
		$a[$v[$key]] = $v[$value] ;	
	};
	return $a;
}
/* html rendering */
function generate_select_html($id='',$name='',$options,$current_value='',$first_option = array(),$required=0) {
	$req = $required ? "required='true'" : "" ;
	$html = "<select $req id='$id' name='$name'>";
	if(count($first_option)>0) $html.="<option value='".$first_option["value"]."'>".$first_option["text"]."</option>";     
	foreach($options as $value=>$text) { 
		$selected = ($value == $current_value) ? "selected" : "";
		$html.="<option $selected value=$value>$text</option>";	
	} 
	$html .= "</select>" ;
	
	return $html ;
}

function is_checked_html($value,$trueValue) {
	if($value === $trueValue) 
		return "checked=\"true\"" ;
		return "" ;
}

// THEME
function get_current_user_info() {
	$user = wp_get_current_user();
	if(0 != $user->ID) {
    	return $user;
	}
	return false;
}

function get_attachment_data($id=0,$size = "thumbnail") {
	if(!$id) return array();
	$image = wp_get_attachment_image_src($id,$size) ;//get_post( $id,"OBJECT");
	$meta = get_post($id); 
	return array(
		"id"     => $id,
		"url"    => $image[0],
		"width"  => $image[1],
		"height" => $image[2],
		"title"  => $meta->post_title,
		"content"  => $meta->post_content,
		"excerpt" => $meta->post_excerpt	
	);
	 
}

function get_user() {
	global $current_user;
    get_currentuserinfo();
    return array(
    	"user_id"       => $current_user->ID,
    	"user_nickname" => $current_user->user_nicename,
    	"user_level"    => $current_user->user_level  
    );
}


function is_valid_url($url) {
	try {
		return filter_var($url, FILTER_VALIDATE_URL);
	} catch(Exception $e) {
		return preg_match('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $url);	
	}
	
}

function is_valid_email($email){
	return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email);
}


function get_comment_total_by_event($event_id) {
	global $wpdb ;
	if($event_id) {
		$total =  $wpdb->get_results($wpdb->prepare("SELECT 
			COUNT(*) as total, (SELECT COUNT(*) FROM ".WORDTOUR_COMMENTS." WHERE comment_approved=2) as total_pending    
			FROM ".WORDTOUR_COMMENTS." AS c 
			WHERE c.comment_event_id = $event_id"),"ARRAY_A");
		
		if(count($total)) {
			return $total[0];
		} else {
			$total = array("total"=>0,"total_pending"=>0);	
		}
		
	} 	
}
# FACEBOOK
function prepare_facbook_time($time) {
	// Create a date string from Unix timestamp
    $dateString = date("r",$time);
    // Create new date object from $dateString
    $datetime = new DateTime($dateString);
    // Create Los Angeles timezone
    $la_time = new DateTimeZone('America/Los_Angeles');
    // Set LA timezone to the date object
    $datetime->setTimezone($la_time);
    // Calculate the timezone offset (DST included during calculations)
    $offset = $datetime->getOffset();
    // Facebook adds its timezone offset to the received timestamp
    // Cheat facebook by adding the offset he is going to subtract
    $offset = $offset*(-1);
    $datetime->modify($offset." seconds");
    // Return Unix timestamp
    return $datetime->format("U");
}

function wt_update_facebook($type = "event",$event_id = 0,$facebook_id = 0){
	if(!$event_id && !$facebook_id) return false;
	$facebook_events = get_option("wt_facebook") ;
	$is_exist = $facebook_events;	
	if(!$facebook_events) {
		$facebook_events = array();
	} else {
		$facebook_events = unserialize($facebook_events);
	}
	
	if(!isset($facebook_events[$event_id])) $facebook_events[$event_id] = array();
	
	if($type=="event"){
		$facebook_events[$event_id]["facebook_event_id"] = $facebook_id; 	
	}
	
	if($type=="status"){
		$facebook_events[$event_id]["status_last_update"] = current_time("timestamp"); 	
	}
		
	if(!$is_exist) {
		add_option("wt_facebook",serialize($facebook_events));
	} else {
		update_option("wt_facebook",serialize($facebook_events));
	}
}



function get_event_title($event_id = 0) {
	if($event_id) {
		//$event = new wt_event($event_id);
		//return $event["title"] || $event["start_date"];
	} 
	return "";
}

function wt_set_event_more_artists(&$events = array()){
	$events_rows = array();
	foreach($events as $event) {
		if(!$events_rows["e".$event["event_id"]]) {
			$events_rows["e".$event["event_id"]] = $event;
			$events_rows["e".$event["event_id"]]["event_more_artists"] = array();
			if(!$event["event_is_headline"]) {
				$events_rows["e".$event["event_id"]]["artist_name"] = "";
			}
		}
		
		if($event["event_is_headline"]) {
			$events_rows["e".$event["event_id"]]["artist_name"] = $event["artist_name"];
		} else {
			$events_rows["e".$event["event_id"]]["event_more_artists"][] = array("name"=>$event["artist_name"],"id"=>$event["artist_id"]);
		}
	}
	$events = $events_rows;
	return $events;
}


function findexts($filename) { 
 $filename = strtolower($filename) ; 
 $exts = split("[/\\.]", $filename) ; 
 $n = count($exts)-1; 
 $exts = $exts[$n]; 
 return $exts; 
} 


function wt_group_artists_tour_json(){
	global $wpdb;
	$json = array(); 
	$artists = WT_Artist::all();
	foreach($artists as $artist) {
		$artist_id = $artist["artist_id"];
		$artist_name = $artist["artist_name"];
		$tours = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables("tour_id as id,tour_name as name",array("meta"=>0,"venue"=>0,"artists"=>0,"tour"=>1))." AND e.event_artist_id=$artist_id GROUP BY e.event_tour_id ASC"),"ARRAY_A");	
		$json[] = array(
					"artist" => array(
						"id"=>$artist_id,
						"name"=>$artist_name
					),
					"tour" => $tours
				 );
	}
	return json_encode($json);
}

function wt_get_themes($root = 0) {
	global $_wt_options;
	$root = $root ? $root : wt_get_theme_path();
	$themes = array(); 
	if(is_dir($root)) {
		if($handle = opendir($root)) {
			while (false !== ($file = readdir($handle))) {
				if($file!="." && $file!="..") {
					if(is_dir($root.$file)) {
	          			$themes[] = $file;
	          		}
	          	}           
			}
			closedir($handle);
		}
	}
	return $themes;	
}

function wt_get_theme_path(){
	global $_wt_options;
	$path = $_wt_options->options("theme_path") ? ABSPATH.$_wt_options->options("theme_path") : WT_THEME_PATH;
	return $path;  
}

function wt_get_default_theme_path(){
	global $_wt_options;
	$path = wt_get_theme_path();
	return realpath($path.$_wt_options->options("default_theme"));   
}

function wt_get_theme_url(){
	global $_wt_options;
	$path = $_wt_options->options("theme_path") ? get_bloginfo("wpurl")."/".$_wt_options->options("theme_path") : WT_THEME_URL;
	return str_replace('\\','/',$path);   
}

function wt_get_default_theme_url(){
	global $_wt_options;
	$path = $_wt_options->options("theme_path") ? get_bloginfo("wpurl")."/".$_wt_options->options("theme_path") : WT_THEME_URL;
	return str_replace('\\','/',$path.$_wt_options->options("default_theme"));   
}

function wt_file_get_contents($url) {
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
      curl_setopt($ch, CURLOPT_URL, $url);
      $data = curl_exec($ch);
      
      curl_close($ch);
      return $data;
}

/* Eventbrite */


function wt_sanitize_with_dashes($toClean) {
    $toClean     =     str_replace('&', '-and-', $toClean);
    $toClean     =    trim(preg_replace('/[^\w\d_ -]/si', '', $toClean));//remove all illegal chars
    $toClean     =     str_replace(' ', '-', $toClean);
    $toClean     =     str_replace('--', '-', $toClean);
   
    return $toClean;
}

function wt_get_permalink($page,$id,$data=array()){
	global $wp_rewrite,$_wt_options;
	if($wp_rewrite->using_permalinks() && $_wt_options->options("permalinks")==1){
		$data["%id%"] = $id;
		if($data["%name%"]) $data["%name%"] = wt_sanitize_with_dashes(strtolower($data["%name%"]));
		if($data["%date%"]) $data["%date%"] = mysql2date("Y/m/d",$data["%date%"]); 
		
		$structure = $_wt_options->options("permalinks_".$page);
		$url = strtr($structure,$data);	
	} else {
		$url = get_bloginfo("url")."/?wtpage=$page&id=$id";
	}
	
	return $url;
}


function wt_get_themes_files() {
	$path = wt_get_default_theme_path();
	$files = array();
	if(is_dir($path)) {
		if($handle = opendir($path) ) {
			while (false !== ($file = readdir($handle))) {
				if($file!="." && $file!=".." && strpos($file,"tpl.php")) {
					if(is_file($path."/".$file)) {
						$files[] = $file;
	          		}
	          	}           
			}
			closedir($handle);
		}	
	}
	return $files;
		
}

function wt_load_script($file = null,$theme = null) {
	global $_wt_options;
	if(!$file) return "";
	$url = wt_get_default_theme_url()."/js/";
	$src = $url.$file.".js";
	return  "<script src='$src'></script>";
}

#############
# render events list
function wt_render_events($attr = array()) {
	$renderer = new WT_Renderer();
	echo $renderer->events($attr,null,"wordtour_events"); 	
}
# get all tour
# $artist_id - get only tour according to artist
# order display - name,order
function wt_get_tour($artist_id = 0,$order = "order"){
	$order = ($order == "order") ? "tour_order" : "tour_name";
	$data = array();
	if($artist_id) {
		$data = WT_Tour::all_by_artist($artist_id,$order);	
	} else {
		$data = WT_Tour::all($order);
	}
	
	foreach($data as $key=>$value) {
		$data[$key] = WT_Tour::tpl(WT_Tour::db_out($value)); 
	};
	
	return $data;
}

# get dates
function wt_group_by_month($start_date = "") {
	global $wpdb;
	$date_sql = "";
	if($start_date) $date_sql = "AND e.event_start_date >= '$start_date'";
	$sql = WT_Event::sql("COUNT(*) as total,MONTH(e.event_start_date) as month,YEAR(e.event_start_date) as year")." $date_sql GROUP BY year,month";
	$dates = $wpdb->get_results($wpdb->prepare($sql),"ARRAY_A");
	return $dates;	 	
}


# get all galleries
function wt_galleries(){
	global $wpdb;
	$data = WT_Gallery::all();
	return $data;
}

function wordtour_gallery_checklist() {
	foreach(wt_galleries() as $gallery) {
		$go = new WT_Gallery();
		$go->db_out($gallery);
		echo "<li><input type='text' value='$gallery[gallery_id]'>$gallery[gallery_name]</li>";
	}
}

function wordtour_category_checklist() {
	echo "<ul>";
	wp_category_checklist();
	echo "</ul>";
}

/* FLICKR */
function wt_get_machinetag($predicate,$value){
	global $_wt_options;
	return $_wt_options->options("flickr_namespace").":$predicate=".str_replace(' ','',strtolower($value));
}

function wt_flickr_by_machinetag($machinetag = null){
	if(!$machinetag) return array();
	global $_wt_options;
	require_once WT_PLUGIN_PATH.'phpflickr/phpFlickr.php';
	$flickr = new phpFlickr($_wt_options->options("flickr_key"));
	$photos = $flickr->photos_search(array("machine_tags"=>$machinetag,"page"=>1,"per_page"=>30,"media"=>"photos"));
	$photos["machinetag"] = $machinetag;
	foreach ((array)$photos['photo'] as $k=>$v) {
		$photos['photo'][$k] = array("thumb"=>$flickr->buildPhotoURL($v, "square"),
						"large"=>$flickr->buildPhotoURL($v, "large"),
						"title"=>$v[title],
						"href"=> "http://www.flickr.com/photos/$v[owner]/$v[id]");	
	}
	return $photos; 
}

function wordtour_update_panel_state($page,$data) {
	//delete_option("wordtour_panel_state");
	
	$option = get_option("wordtour_panel_state");
	$state = array();
	if($option) {
		$state = unserialize($option);
	};
	
	$state[$page] = (array) json_decode(stripslashes($data));
	$state = serialize($state);
	update_option("wordtour_panel_state",$state);
	return 1;
}

function wordtour_get_panel_state($page) {
	//delete_option("wordtour_panel_state");
	$option = get_option("wordtour_panel_state");
	if($option) {
		$data = unserialize($option);
		return $data[$page]; 	
	}
	return 0; 
}

//function wordtour_add_panel_state($page,$panel,$pos = "left") {
//	$data = wordtour_get_panel_state($page);
//	//print_r($data);
//	if($data && !in_array($panel,$data[$pos])) {
//		array_push($data[$pos],$panel);
//		wordtour_update_panel_state($page,json_encode($data)); 	
//	}
//	return 0; 
//}

