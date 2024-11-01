<?php
/*
Plugin Name: WordTour
Plugin URI: http://www.wordtour.com
Description: WordTour is a comprehensive WordPress plug-in for performing artists and their agents. WordTour offers you a no-fuss time-saving solution to events management and a whole lot more!
Author: Gil Noy
Version: 1.2.5.0
Author URI: http://www.wordtour.com

Copyright 2010  Gil Noy  (email : wordtourplugin@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
global $wpdb,$_wt_options,$_wt_countries,$_wt_states,$_wt_time;

$_wt_countries = array("US" => "United States", "CA" => "Canada","AF"=>"Afghanistan","AX"=>"Aland Islands","AL"=>"Albania","DZ" =>"Algeria","AS"=>"American Samoa","AD"=>"Andorra","AO"=>"Angola","AI"=>"Anguilla","AQ" =>"Antarctica","AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria","AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo (DR)", "CK" => "Cook Islands", "CR" => "Costa Rica", "CI" => "Cote D'Ivoire", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard and McDonald Islands", "VA" => "Holy See (Vatican City State)", "HN" => "Honduras", "HK" => "Hong Kong","HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy","JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KP" => "Korea (North)", "KR" => "Korea (South)","KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Laos", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein","LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macau", "MK" => "Macedonia", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia", "MD" => "Moldova", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau","PS" => "Palestinian Territory (Occupied)", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn","PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RE" => "Reunion", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda","BL" => "Saint Barthelemy", "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin (French)", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and South Sandwich Islands", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland","SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syria", "TW" => "Taiwan", "TJ" => "Tajikistan", "TZ" => "Tanzania", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo","TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu","UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "UK" => "United Kingdom", "UM" => "United States Minor Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan","VU" => "Vanuatu", "VE" => "Venezuela", "VN" => "Vietnam", "VG" => "Virgin Islands (British)", "VI" => "Virgin Islands (US)", "WF" => "Wallis and Futuna", "EH" => "Western Sahara","YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe");
$_wt_states = array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois",'IN'=>"Indiana",'IA'=>"Iowa",'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland",'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma",'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming");
$_wt_currency = array("USD" => "U.S. Dollars ($)","EUR" => "Euros","GBP" => "Pounds Sterling","AUD" => "Australian Dollars","CAD" => "Canadian Dollars","JPY" => "Yen","CZK" => "Czech Koruna","DKK" => "Danish Krone","HKD" => "Hong Kong Dollar","HUF" => "Hungarian Forint","NZD" => "New Zealand Dollar","NOK" => "Norwegian Krone","PLN" => "Polish Zloty","SGD" => "Singapore Dollar","SEK" => "Swedish Krona","CHF" => "Swiss Franc","ILS" => "Israeli Shekels","MXN" => "Mexican Peso","BRL" => "Brazilian Real","MYR" => "Malaysian Ringgits","PHP" => "Philippine Pesos","TWD" => "Taiwan New Dollars","THB" => "Thai baht");


function wordtour_time_list(){
	global $_wt_time;
	$_wt_time = array();
	for($i = 1 ; $i <= 12 ; $i++) {
		$time_format = get_option("time_format");
		$_wt_time[] =array("term"  => ($i<10 ? "0"+$i : $i).":00:00","value" => mysql2date($time_format,($i<10 ? "0"+$i : $i).":00:00"));
		$_wt_time[] = array("term"  => ($i<10 ? "0"+$i : $i).":30:00","value" => mysql2date($time_format,($i<10 ? "0"+$i : $i).":30:00"));
		$_wt_time[] = array("term"  => (($i+12) == 24 ? "00" : ($i+12)).":00:00","value"  => mysql2date($time_format,(($i+12) == 24 ? "00" : ($i+12)).":00:00"));
		$_wt_time[] = array("term"  => (($i+12) == 24 ? "00" : ($i+12)).":30:00","value"  => mysql2date($time_format,(($i+12) == 24 ? "00" : ($i+12)).":30:00")); 
	};	
}

wordtour_time_list();

define("WORDTOUR_VERSION","1.2.5.0");
define("WORDTOUR_EVENTS",$wpdb->prefix.'wtr_events');
define("WORDTOUR_EVENTS_META",$wpdb->prefix.'wtr_events_meta');
define("WORDTOUR_VENUES",$wpdb->prefix.'wtr_venues');
define("WORDTOUR_ARTISTS",$wpdb->prefix.'wtr_artists');
define("WORDTOUR_TOUR",$wpdb->prefix.'wtr_tour');
define("WORDTOUR_COMMENTS",$wpdb->prefix.'wtr_comments');
define("WORDTOUR_RSVP",$wpdb->prefix.'wtr_rsvp');
define("WORDTOUR_ATTENDING",$wpdb->prefix.'wtr_attending');
define("WORDTOUR_ATTACHMENT",$wpdb->prefix.'wtr_attachment');
define("WORDTOUR_GALLERY",$wpdb->prefix.'wtr_gallery');
define("WORDTOUR_SOCIAL",$wpdb->prefix.'wtr_social');
define("WORDTOUR_ALBUMS",$wpdb->prefix.'wtr_albums');
define("WORDTOUR_TRACKS",$wpdb->prefix.'wtr_tracks');
define("WT_PLUGIN_URL",WP_PLUGIN_URL.'/wordtour');
define("WT_EXTENSION_URL",WP_PLUGIN_URL.'/wordtour/extension/');
define("WT_PLUGIN_PATH",WP_CONTENT_DIR.'/plugins/wordtour/');
define("WT_PLUGIN_CLASS_PATH",WT_PLUGIN_PATH.'class/');
define("WT_ADMIN_URL",admin_url('admin.php?'));
define("WT_THEME_URL",WT_PLUGIN_URL.'/theme/');
define("WT_THEME_PATH",WP_CONTENT_DIR.'/plugins/wordtour/theme/');
define("WT_PLUGIN_NAME",'wordtour');

require(WT_PLUGIN_PATH."class/class.options.php");
$_wt_options = new WT_Options();

require(WT_PLUGIN_PATH."schema.php");
require(WT_PLUGIN_PATH."admin/handlers.php");
require(WT_PLUGIN_PATH."upgrade.php");
require(WT_PLUGIN_PATH."class/class.renderer.php");
require(WT_PLUGIN_PATH."navigation.php");
require(WT_PLUGIN_PATH."registerer.php");
 
require(WT_PLUGIN_PATH."widget.php");
wt_register();


function wordtour_init_method() {
	wordtour_register_rewrite_rules();
	wt_enqueue_script();
	wt_enqueue_style();
	wordtour_tinymce_addbuttons();
}

function wordtour_shortcode($atts,$content=null,$code="") {
	return do_shortcode($content);	
}

function wordtour_events_shortcode($atts,$content=null,$code="") {
	$renderer = new WT_Renderer();
	return $renderer->events($atts,$content,$code);		
}

function wordtour_event_shortcode($atts,$content=null,$code="") {
	$renderer = new WT_Renderer();
	return $renderer->event($atts,$content,$code);		
}

function wordtour_artists_shortcode($atts,$content=null,$code="") {
	$renderer = new WT_Renderer();
	return $renderer->artists($atts,$content,$code);		
}

function wordtour_tours_shortcode($atts,$content=null,$code="") {
	$renderer = new WT_Renderer();
	return $renderer->tours($atts,$content,$code);		
}


function wordtour_venues_shortcode($atts,$content=null,$code="") {
	$renderer = new WT_Renderer();
	return $renderer->venues($atts,$content,$code);		
}


function wordtour_albums_shortcode($atts,$content=null,$code="") {
	$renderer = new WT_Renderer();
	return $renderer->albums($atts,$content,$code);		
}

function wordtour_bio_shortcode($atts,$content=null,$code="") {
	$renderer = new WT_Renderer();
	return $renderer->bio($atts,$content,$code);		
}

function wordtour_videos_shortcode($atts,$content=null,$code="") {
	$renderer = new WT_Renderer();
	return $renderer->videos($atts,$content,$code);		
}

/* REWRITE RULES */
function wordtour_rewrite_rules(){
	return array(
		'(artist|event|tour|venue|album)/(\d*)$'      => 'index.php?wtpage=$matches[1]&id=$matches[2]',
		'(artist|event|tour|venue|album)/(.+)/(\d*)$' => 'index.php?wtpage=$matches[1]&id=$matches[3]',
		'([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/event/(\d*)$' => 'index.php?wtpage=event&id=$matches[4]',
		'([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(.+)/event/(\d*)$' => 'index.php?wtpage=event&id=$matches[5]'
	);
};

// Adding a new rule
function wordtour_insert_rewrite_rules($rules){
	return wordtour_rewrite_rules() + $rules;
	//return $rules;
}

function wordtour_insert_rewrite_query_vars($vars){
	$vars[] = 'id';
    $vars[] = 'wtpage';
    return $vars;
}

function wordtour_register_rewrite_rules(){
	global $wp_rewrite,$_wt_options;
	if($wp_rewrite->using_permalinks() && $_wt_options->options("permalinks")==1){
		$current_rules = $wp_rewrite->wp_rewrite_rules();
		$new_rules = wordtour_rewrite_rules();
		$flush = 0;
		foreach($new_rules as $rule=>$value) {
			if(!$current_rules[$rule]) $flush = 1;
		}
		add_filter('query_vars','wordtour_insert_rewrite_query_vars');
		if($flush) {
			add_filter('rewrite_rules_array','wordtour_insert_rewrite_rules');		
			$wp_rewrite->flush_rules();
		}
	}
}

function wt_render_single_page() {
	global $wp_query;
	//echo "wtpage".$wp_query->query_vars["wtpage"]."id=".$wp_query->query_vars["id"];
	$page = get_query_var("wtpage")? get_query_var("wtpage") : $_GET["wtpage"];
	$id= get_query_var("id")? get_query_var("id") : $_GET["id"];
	if($page && $id) {
		$r = new WT_Renderer();
		$r->single($page,$id); 
		exit();
	}	
}

function register_wordtour_settings() {
	register_setting('wordtour_settings_prefix','wordtour_settings');
}
//add_action('admin_init', 'wordtour_admin_init');
add_shortcode('wordtour', 'wordtour_shortcode');
add_shortcode('wordtour_events', 'wordtour_events_shortcode');
add_shortcode('wordtour_event', 'wordtour_event_shortcode');
add_shortcode('wordtour_tours', 'wordtour_tours_shortcode');
add_shortcode('wordtour_artists', 'wordtour_artists_shortcode');
add_shortcode('wordtour_venues', 'wordtour_venues_shortcode');
add_shortcode('wordtour_albums', 'wordtour_albums_shortcode');
add_shortcode('wordtour_bio', 'wordtour_bio_shortcode');
add_shortcode('wordtour_videos', 'wordtour_videos_shortcode');

add_action('init','wordtour_init_method');
add_action('template_redirect', 'wt_render_single_page');
add_action('admin_init', 'register_wordtour_settings');
add_action('admin_menu', 'wt_admin_menu',20);
register_activation_hook(__FILE__,'wordtour_install');
register_uninstall_hook(__FILE__, ' wordtour_uninstall');

// tinymce
function wordtour_tinymce_addbuttons() {
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "wordtour_tinymce_plugin");
     add_filter('mce_buttons', 'wordtour_tinymce_button');
   }
}
 
function wordtour_tinymce_button($buttons) {
   array_push($buttons,"wordtour");
   return $buttons;
}
 
function wordtour_tinymce_plugin($plugin_array) {
   $plugin_array['wordtour'] = WT_PLUGIN_URL.'/js/tinymce/editor_plugin.js';
   return $plugin_array;
}
//Favorite Actions
add_filter('favorite_actions', 'wordtour_fav');
function wordtour_fav($actions) {
	global $_wt_options;
	$capabilitie = $_wt_options->options("user_role");
	$actions['admin.php?page=wt_new_event'] = array('New Event',$capabilitie);
    return $actions;
}

add_filter('upgrader_pre_install', 'plugin_theme_backup', 10, 2);
add_filter('upgrader_post_install', 'plugin_theme_backup_recover', 10, 2);

