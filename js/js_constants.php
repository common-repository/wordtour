<?php 
	require_once("../../../../wp-load.php"); 
	global $_wt_options,$_wt_time;
	$date_format = $_wt_options->options("admin_date_format");
	$lastfm_key = $_wt_options->options("lastfm_key");
	$flickr_key = $_wt_options->options("flickr_key");
	$twitter_key = $_wt_options->options("twitter_api_key");
	$facebook_key = $_wt_options->options("facebook_app_id");
	
	
	$eventbrite = new WT_Eventbrite();
	$eventbrite_enabled = $eventbrite->is_init() ? "true" : "false";
?>

; var $CONSTANT = {
	"PLUGIN_AJAX":"<?php echo WP_PLUGIN_URL."/wordtour/admin/plugin-ajax.php"?>",
	"ADMIN_URL":"<?php echo WT_ADMIN_URL ;?>",
	"PAGE_EVENTS":"<?php echo WT_ADMIN_URL."page=wordtour/navigation.php";?>",
	"PAGE_NEW_EVENT":"<?php echo WT_ADMIN_URL."page=wt_new_event";?>",
	"PAGE_ARTISTS":"<?php echo WT_ADMIN_URL."page=wt_artists";?>",
	"PAGE_NEW_ARTIST":"<?php echo WT_ADMIN_URL."page=wt_artists&action=new";?>",
	"PAGE_TRACKS":"<?php echo WT_ADMIN_URL."page=wt_tracks";?>",
	"PAGE_NEW_TRACK":"<?php echo WT_ADMIN_URL."page=wt_tracks&action=new";?>",
	"PAGE_TOUR":"<?php echo WT_ADMIN_URL."page=wt_tour";?>",
	"PAGE_NEW_TOUR":"<?php echo WT_ADMIN_URL."page=wt_tour&action=new";?>",
	"PAGE_NEW_ALBUM":"<?php echo WT_ADMIN_URL."page=wt_albums&action=new";?>",
	"PAGE_VENUE":"<?php echo WT_ADMIN_URL."page=wt_venues";?>",
	"PAGE_NEW_VENUE":"<?php echo WT_ADMIN_URL."page=wt_venues&action=new";?>",                
	"THEME":"<?php echo WP_PLUGIN_URL."/wordtour/theme/"?>",
	"THEME_PATH":"<?php echo WT_PLUGIN_PATH."theme/"?>",
	"THEME_PATH_CURRENT":"<?php echo wt_get_theme_url(); ?>",
	"THEME_JS_URL":"<?php echo WP_PLUGIN_URL."/wordtour/theme/js.php"?>",
	"THEME_CSS_URL":"<?php echo WP_PLUGIN_URL."/wordtour/theme/css.php"?>",    
	"DIALOG":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php"?>",
	"DIALOG_ALL_VENUES":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=all_venues"?>",
	"DIALOG_ALL_COUNTRIES":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=all_countries"?>",
	"DIALOG_ALL_TYPE":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=all_type"?>",
	"DIALOG_ALL_GENRE":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=all_genre"?>",
	"DIALOG_ALL_STATES":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=all_states"?>",
	"DIALOG_ALL_ARTISTS":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=all_artists"?>",
	"DIALOG_ALL_TOUR":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=all_tour"?>",
	"DIALOG_EVENTS":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=events"?>",
	"DIALOG_VENUES":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=venues"?>",
	"DIALOG_ARTISTS":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=artists"?>",
	"DIALOG_ALBUMS":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=albums"?>",
	"DIALOG_TRACKS":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=tracks"?>",
	"DIALOG_COMMENT":"<?php echo WP_PLUGIN_URL."/wordtour/admin/dialog.php?page=comment"?>",
	"DIALOG_TOUR":"<?php echo WT_PLUGIN_URL.'/admin/dialog.php?page=tour'?>",
	"DIALOG_TWITTER":"<?php echo WT_PLUGIN_URL.'/admin/dialog.php?page=twitter'?>",
	"DIALOG_FACEBOOK":"<?php echo WT_PLUGIN_URL.'/admin/dialog.php?page=facebook'?>",
	"DIALOG_GALLERY":"<?php echo WT_PLUGIN_URL.'/admin/dialog.php?page=gallery'?>",
	"DIALOG_POST":"<?php echo WT_PLUGIN_URL.'/admin/dialog.php?page=post'?>",
	"DIALOG_EVENTBRITE":"<?php echo WT_PLUGIN_URL.'/admin/dialog.php?page=eventbrite'?>",
	"DIALOG_IMPORT_EVENTBRITE":"<?php echo WT_PLUGIN_URL.'/admin/dialog.php?page=import_eventbrite'?>",
	"DIALOG_IMPORT_ALBUM_INFO":"<?php echo WT_PLUGIN_URL.'/admin/dialog.php?page=import_album-info'?>",
	"AUTOCOMPLETE":"<?php echo WT_PLUGIN_URL.'/admin/autocomplete.php'?>",
	"AUTOCOMPLETE_COUNTRY":"<?php echo WT_PLUGIN_URL.'/admin/autocomplete.php?type=country&maxRows=10'?>",
	"AUTOCOMPLETE_CATEGORY":"<?php echo WT_PLUGIN_URL.'/admin/autocomplete.php?type=category&maxRows=10'?>",
	"AUTOCOMPLETE_EVENT_TYPE":"<?php echo WT_PLUGIN_URL.'/admin/autocomplete.php?type=event_type&maxRows=10'?>",
	"AUTOCOMPLETE_STATE":"<?php echo WP_PLUGIN_URL.'/wordtour/admin/autocomplete.php?type=state&maxRows=10'?>",
	"AUTOCOMPLETE_LASTFM":"<?php echo WP_PLUGIN_URL.'/wordtour/admin/autocomplete.php?type=lastfm&maxRows=10'?>",
	"AUTOCOMPLETE_YQL":"<?php echo WP_PLUGIN_URL.'/wordtour/admin/autocomplete.php?type=yql&maxRows=10'?>",
	"MEDIA_LIBRARY":"<?php echo bloginfo("wpurl").'/wp-admin/media-upload.php?type=image&tab=library'?>",
	"ADMIN_DATE_FORMAT": "<?php echo $date_format;?>",
	"LASTFM_API_KEY": "<?php echo $lastfm_key;?>",
	"FlICKR_API_KEY" : "<?php echo $flickr_key;?>",
	"FACEBOOK_API_KEY" : "<?php echo $facebook_key;?>",
	"TWITTER_API_KEY" : "<?php echo $twitter_key;?>",
	"EVENTBRITE_ENABLED" : <?php echo $eventbrite_enabled;?>,
	"TIME" :<?php echo json_encode($_wt_time); ?>
};
	
	