<?php 
class WT_DBPrepere {
	public function int_in($value = 0){
		if(!is_numeric($value)) return 0;
		return $value;
	}
	
	public function int_out($value = 0){
		if(!is_numeric($value)) return 0;
		return $value;
	}
	
	public function date_display($date = null) {
		global $_wt_options;
		$date_format = get_option("date_format");
		if(!date) return "";
		$date =  strtotime($date);
		$format = "Y/m/d";
		$current_date = date($format,$date);
		$today = strtotime(current_time("mysql",0));
		
		if($current_date == date($format,$today)) {
			return "Today";
		}
		
		$dateNextDay = mktime(0,0,0,date('m',$today), date('d',$today)+1, date('Y',$today));
		
		if($current_date == date($format,$dateNextDay)) {
			return "Tomorrow";
		}
		
		$datePrevDay = mktime(0,0,0,date('m',$today), date('d',$today)-1, date('Y',$today));
		
		if($current_date == date($format,$datePrevDay)) {
			return "Yesterday";
		}
		
		return date_i18n($date_format,$date);	
	}
	
	public function date_in($value = "0000-00-00"){
		if(empty($value)) $value = "0000-00-00";
		return $value;
	}
	
	public function time_in($value = "00:00:01"){
		if(empty($value)) $value = "00:00:01";
		return $value;
	}
	
	public function time_out($value = "00:00:01"){
		if($value == "00:00:01") $value = "";
		return mysql2date(get_option("time_format"),$value);
	}
	
	public function date_out($value = "0000-00-00"){
		if($value == "0000-00-00") return "";
		return mysql2date(get_option("date_format"),$value);
	}
	
	public function admin_date_out($value = "0000-00-00"){
		global $_wt_options;
		if($value == "0000-00-00") return "";
		return mysql2date($_wt_options->options("admin_date_format"),$value);
	}
	
	public function datetime_in($value = "0000-00-00 00:00:00"){
		return $value;
	}
	
	public function datetime_out($value = "0000-00-00 00:00:00"){
		if($value == "0000-00-00 00:00:00") return "";
		return mysql2date(get_option("date_format")." ".get_option("time_format"),$value);
	}
	
	public function datetime_short_out($value = "0000-00-00 00:00:00"){
		if($value == "0000-00-00 00:00:00") return "";
		return mysql2date("M d, Y"." ".get_option("time_format"),$value);
	}
	
	public function html_in($value = 0){
		$_WT_PLUGINS_ALLOWEDTAGS = array('a' => array('href' => array(),'title' => array(), 'target' => array()),
			'abbr' => array('title' => array()),'acronym' => array('title' => array()),
			'code' => array(), 'pre' => array(), 'em' => array(),'strong' => array(),
			'ul' => array(),'b'=> array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'br' => array());
		if($value) {
			return html_entity_decode(wp_kses(stripslashes(trim(wpautop($value))),$_WT_PLUGINS_ALLOWEDTAGS));
		}
	}
	
	public function html_form_in($value = 0){
		$_WT_PLUGINS_ALLOWEDTAGS = array('a' => array('href' => array(),'title' => array(), 'target' => array()),
			'abbr' => array('title' => array()),'acronym' => array('title' => array()),
			'code' => array(), 'pre' => array(), 'em' => array(),'strong' => array(),
			'ul' => array(),'b'=> array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'br' => array(),
			'form' => array('name' => array(),'value' => array(),'action' => array(),'method' => array()),
			'input' => array('type' => array(),'name' => array(),'value' => array(),'src' => array(),'border' => array(),'alt' => array()));
		if($value) {
			return html_entity_decode(wp_kses(stripslashes(trim($value)),$_WT_PLUGINS_ALLOWEDTAGS));
		}
	}
	
	public function str_in($value = 0){
		if($value) {
			//$kses = wp_kses($value,$_WT_PLUGINS_ALLOWEDTAGS);
			//return stripslashes(trim($value));
			return strip_tags(stripslashes(trim($value)));
		}
	}
	
	public function str_out($value = 0,$is_htmlspecial =1){
		if($value) {
			if(!$is_htmlspecial) return $value;
			return htmlspecialchars($value);
		}
		
		return "";
	}
	
	public function json_in($value = ""){
		if(!empty($value)) return json_decode(stripslashes($value));
		return "";
	}
	
	public function json_out($value = ""){
		if(!empty($value)) return unserialize($value);	
		return "";
	}
	
	public function link_in($value = 0){
		if($value) return urlencode($value);
		return "";
	}
	
	public function link_out($value = 0){
		if($value) return urldecode($value);
		return "";
	}
	
	
	public function html_out($value = 0){
		if($value) {
			return wp_kses_normalize_entities($value);
		}
	}
	/* Search for the <!-- more --> */	
	public function html_teaser_out($content = "",$length = 0){
		$output = "";
		if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
			$content = explode($matches[0], $content, 2);
			//print_r($content[0]);
			//if( !empty($matches[1])) $more_link_text = strip_tags(wp_kses_no_null(trim($matches[1])));
		} else {
			$content = array($content);
		}	
		return force_balance_tags($content[0]);
	}
	
	public function media_out($media_id = 0){
		$imgTag = "";
		$url = "";
		$height = 0;
		$width = 0;
		if($media_id > 0) {
			$media = get_attachment_data($media_id);
			$imgTag = "<img src='".$media["url"]."' width='".$media["width"]."' height='".$media["height"]."'/>";
			$url =  $media["url"];
			$height = $media["width"];
			$width = $media["height"];
		}
		return array(
			"imgTag" => $imgTag,
			"url"    => $url,
		    "width"  => $width,
		    "height"  => $height  
		);		
	}	
}
