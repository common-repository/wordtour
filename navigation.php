<?php
function wt_admin_menu(){
	global $_wt_options;
	$add = "New Event";;
	$events = "Events";
	$venues = "Venues";
	$artists = "Artists";
	$tour = "Tour";
	$comments = "Comments";
	$rsvp = "RSVP";
	$gallery = "Gallery";
	$albums = "Albums";
	$tracks = "Tracks";
	$settings = "Settings";
	$capabilitie = $_wt_options->options("user_role");
	
	
	add_menu_page("My Plugin &rsaquo; $add","WordTour",$capabilitie,__FILE__,"wt_nav_edit_event");
	
	$edit_page = add_submenu_page(__FILE__, WT_PLUGIN_NAME." &rsaquo; $events", $events,$capabilitie,__FILE__,"wt_nav_edit_event");

	add_action('admin_print_scripts-' . $edit_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $edit_page, 'wt_admin_style');
	
	$new_page = add_submenu_page(__FILE__,WT_PLUGIN_NAME." &rsaquo; $add",$add,$capabilitie,"wt_new_event","wt_nav_new_event");
	add_action('admin_print_scripts-' . $new_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $new_page, 'wt_admin_style');
	
	$venues_page = add_submenu_page(__FILE__, WT_PLUGIN_NAME." &rsaquo; $venues",$venues, $capabilitie, "wt_venues", "wt_nav_venues");
	add_action('admin_print_scripts-' . $venues_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $venues_page, 'wt_admin_style');
	
	$artists_page = add_submenu_page(__FILE__,WT_PLUGIN_NAME." &rsaquo; $artists",$artists,$capabilitie, "wt_artists", "wt_nav_artists");
	add_action('admin_print_scripts-' . $artists_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $artists_page, 'wt_admin_style');
	
	$tour_page = add_submenu_page(__FILE__, WT_PLUGIN_NAME." &rsaquo; $tour", $tour,$capabilitie, "wt_tour", "wt_nav_tour");
	add_action('admin_print_scripts-' . $tour_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $tour_page, 'wt_admin_style');
	
	$albums_page = add_submenu_page(__FILE__, WT_PLUGIN_NAME." &rsaquo; $albums", $albums,$capabilitie, "wt_albums", "wt_nav_albums");
	add_action('admin_print_scripts-' . $albums_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $albums_page, 'wt_admin_style');
	
	$tracks_page = add_submenu_page(__FILE__, WT_PLUGIN_NAME." &rsaquo; $tracks", $tracks,$capabilitie, "wt_tracks", "wt_nav_tracks");
	add_action('admin_print_scripts-' . $tracks_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $tracks_page, 'wt_admin_style');
	
	
	$comments_page = add_submenu_page(__FILE__,WT_PLUGIN_NAME." &rsaquo; $comments", $comments,$capabilitie, "wt_comments", "wt_nav_comments");
	add_action('admin_print_scripts-' . $comments_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $comments_page, 'wt_admin_style');
	
	$gallery_page = add_submenu_page(__FILE__,WT_PLUGIN_NAME." &rsaquo; $gallery", $gallery,$capabilitie, "wt_gallery", "wt_nav_gallery");
	add_action('admin_print_scripts-' . $gallery_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $gallery_page, 'wt_admin_style');
	
	$settings_page = add_submenu_page(__FILE__,WT_PLUGIN_NAME." &rsaquo; $settings", $settings,"manage_options", "wt_settings", "wt_nav_settings");
	add_action('admin_print_scripts-' . $settings_page, 'wt_admin_script');
	add_action('admin_print_styles-' . $settings_page, 'wt_admin_style');
	
	add_action('admin_print_scripts-wordtour', 'wt_admin_script');
	add_action('admin_print_styles-wordtour', 'wt_admin_style');
}

function wt_navigation($page){
	global $wpdb,$_wt_options,$_wt_countries;
	
	require_once("admin/handlers.php");
	require_once("admin/template.php");
	require_once("admin/Class.ListGenerator.php");
	
	$wpdb->show_errors();
	switch($page) {
		case "edit":
			include("admin/edit.php");
		break;
		case "new":
			include("admin/new_event.php");
			include("admin/event.php");
		break;
		case "venues":
			include("admin/venues.php");
		break;
		case "artists":
			include("admin/artists.php");
		break;
		case "tour":
			include("admin/tours.php");
		break;
		case "comments":
			include("admin/comments.php");
		break;
		case "settings":
			include("admin/settings.php");
		break;
		case "gallery":
			include("admin/gallery.php");
		break;
		case "albums":
			include("admin/albums.php");
		break;
		case "tracks":
			include("admin/tracks.php");
		break;
	};
}

function wt_nav_new_event() {
	wt_navigation("new");
}

function wt_nav_edit_event() {
	wt_navigation("edit");
}

function wt_nav_venues() {
	wt_navigation("venues");
}

function wt_nav_artists() {
	wt_navigation("artists");
}

function wt_nav_tour() {
	wt_navigation("tour");
}

function wt_nav_comments() {
	wt_navigation("comments");
}

function wt_nav_settings() {
	wt_navigation("settings");
}

function wt_nav_gallery() {
	wt_navigation("gallery");
}

function wt_nav_albums() {
	wt_navigation("albums");
}
function wt_nav_tracks() {
	wt_navigation("tracks");
}

?>