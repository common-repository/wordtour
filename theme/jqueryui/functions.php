<?php
function wordtour_artist_total($artists){
	global $wpdb;
	if($artists) {
		foreach($artists as &$artist) {
			$total = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_EVENTS." WHERE event_artist_id=".$artist["id"]." AND event_start_date >=CURDATE() AND event_published=1");
			$artist["total"] = $total;
		}
	}	
	return $artists;
}

function wordtour_tour_total($tours){
	global $wpdb;
	if($tours) {
		foreach($tours as &$tour) {
			$total = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_EVENTS." WHERE event_tour_id=".$tour["id"]." AND event_published=1");
			$tour["total"] = $total;
		}
	}	
	return $tours;
}

function wordtour_venues_total($venues){
	global $wpdb;
	if($venues) {
		foreach($venues as &$venue) {
			$total = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_EVENTS." WHERE event_venue_id=".$venue["id"]." AND event_start_date >=CURDATE() AND event_published=1");
			$venue["total"] = $total;
		}
	}	
	return $venues;
}

function wordtour_event_single_title($event){
	if(empty($event["title"])) {
		$event["title"] = "$event[date] - ".$event["artist"]["name"];
	}	
	return $event;
}

function wordtour_shows_params($data) {
	return array_merge($data,array("order"=>"asc"));
}

add_filter("event_single_template","wordtour_event_single_title");
add_filter("artists_template","wordtour_artist_total");
add_filter("tours_template","wordtour_tour_total");
add_filter("venues_template","wordtour_venues_total");
add_filter("venue_events_shortcode_params","wordtour_shows_params");
add_filter("artist_events_shortcode_params","wordtour_shows_params");
add_filter("tour_events_shortcode_params","wordtour_shows_params");





