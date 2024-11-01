<?php 
$theme = new WT_Theme(); 
if($code == "wordtour_events") {
	if($group_by==="DATE") {
		$html.= $theme->events_by_date(array("attr"=>$settings,"tpl"=>$tpl),false);
	} else {
		$theme_attr = array("attr"=>$settings,"tpl"=>$tpl);
		if($navigation && $navigation_attr) $theme_attr["navigation"] = $theme->navigation($navigation_attr,false);
		$html.= $theme->events($theme_attr,false,$theme_file_name);
	}
	
	$html.= wt_load_script("all",$theme_name);
	$html.= wt_load_script("events",$theme_name);
}

if($code == "wordtour_artists") {
	$html.= $theme->artists($tpl,false,$theme_file_name);
	$html.= wt_load_script("all",$theme_name);
	$html.= wt_load_script("artists",$theme_name);
	
}

if($code == "wordtour_tours") {	
	$html.= $theme->tours($tpl,false,$theme_file_name);
	$html.= wt_load_script("all",$theme_name);
	$html.= wt_load_script("tours",$theme_name);
}

if($code == "wordtour_venues") {	
	$html.= $theme->venues($tpl,false,$theme_file_name);
	$html.= wt_load_script("all",$theme_name);
	$html.= wt_load_script("tours",$theme_name);
}

if($code == "wordtour_albums") {
	$html.= $theme->albums($tpl,false,$theme_file_name);
	$html.= wt_load_script("all",$theme_name);
	$html.= wt_load_script("albums",$theme_name);
}

if($code == "wordtour_bio") {
	$html.= $theme->bio($tpl,false,$theme_file_name);
	$html.= wt_load_script("all",$theme_name);
	$html.= wt_load_script("bio",$theme_name);
}

if($code == "wordtour_videos") {
	$html.= $theme->videos_shortcode($tpl,false,$theme_file_name); 
	$html.= wt_load_script("all",$theme_name);
	$html.= wt_load_script("videos",$theme_name);
}

?>




