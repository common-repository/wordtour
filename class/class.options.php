<?php
class WT_Options {
	private $options;
	function __construct(){
		$options = get_option('wordtour_settings');
		$default_options = array(
			"version"                => WORDTOUR_VERSION,
			"js_minimized"           => "mini",
			"date_format"            => get_option("date_format"),
			"comment_registration"   => get_option("comment_registration"),
			"moderation_notify"      => get_option("moderation_notify"),
			"show_tour_poster"       => 1,
			"eventbrite_auto_update" => 1,
			"admin_date_format"      => "m/d/y",
			"user_role"              => "manage_options",
			"default_theme"          => "jqueryui" ,
			"theme_path"             => str_replace(ABSPATH,"",WT_THEME_PATH),
			"flickr_namespace"       => "wordtour",
			"facebook_status_template" => "%artist% will be performing on the %date% at the %venue%",
			"twitter_template"       => "%artist% will be performing on the %date% at the %venue%",
			"default_tour"           => 0,
			"default_venue"          => 0,
			"default_artist"         => 0,
			"allow_comments"         => 1,
			"allow_rsvp"             => 1,
			"allow_gallery"          => 1,
			"allow_posts"            => 1,
			"allow_videos"           => 1
		);
		//if($options) delete_option('wordtour_settings');
		if(!$options) {
			update_option("wordtour_settings",$default_options);
			$options = get_option('wordtour_settings');	
		} 
		
		$this->options = $options ;	
	}
	
	public function options($name = ""){
		if(empty($name)) {
			return $this->options ;
		} else {
			return $this->options[$name];
		} 
	}
	
	public function update($option = array()){
		$options = $this->options();
		if(count($option)>0) {
			foreach($option as $name=>$value){
				$options[$name] = $value; 
			}
			update_option('wordtour_settings',$options);
			$this->options = get_option('wordtour_settings');
		}
	}
}