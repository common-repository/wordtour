<?php
function wt_enqueue_script() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('wt-website');
	wp_enqueue_script('wt-google-maps');
}

function wt_enqueue_style() {
	wp_enqueue_style('wt-css');

}

function wt_admin_script() {
	wp_enqueue_script('wt-admin');
}

function wt_admin_style() {
	wp_enqueue_style('wt-jqueryui-css');
	wp_enqueue_style('wt-admin');
	//wp_enqueue_style('extjs');		
}

function wt_register(){
	global $_wt_options;
	wp_register_style('wt-admin',WT_PLUGIN_URL.'/css/admin/admin-css.php');
	wp_register_script('wt-admin',WT_PLUGIN_URL.'/js/admin-js.php');
	wp_register_script('wt-website',WT_PLUGIN_URL.'/js/website-js.php');
	wp_register_style('wt-css',wt_get_default_theme_url().'/css/theme.css');
	//wp_register_style('extjs',WT_PLUGIN_URL.'/js/extjs/resources/css/ext-all.css');
	wp_register_style('wt-jqueryui-css',WT_PLUGIN_URL.'/js/jquery-ui-1.8rc3/css/smoothness/jquery-ui-1.8.custom.css');	
	$google_map_key = $_wt_options->options("google_map_key");
	if($google_map_key) {
		wp_register_script('wt-google-maps','http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key='.$google_map_key,'>');
	}	
}
?>