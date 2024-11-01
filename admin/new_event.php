<?php
global$_wt_options;
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));

$event_id = null ;
$action = "new";
/* Set default values for form */
function get_event_defaults() {
	return array(
		"event_start_date" => urlEncode(date("d/m/Y")),
		"event_status"     => "active",
		"comment_status"   => "1",
		"rsvp_status"      => "1",
		"gallery_status"   => "1" 	
	);	
}



//print_r($_SERVER['QUERY_STRING']);
//$_SERVER['QUERY_STRING'].="&rrr=dddd";


//echo "<script>
//		window.location=\"http://www.ynet.com\";
//	 </script>"; 

?>