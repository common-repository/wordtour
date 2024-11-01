<?php
require_once("../../../../wp-load.php");
if(!current_user_can($_wt_options->options("user_role"))) wp_die(__('Cheatin&#8217; uh?'));
require_once("handlers.php");
require_once("template.php");
function ajax_get_key() {
	$response = array();
	if(isset($_POST["state_name"])) {
		$state_code = get_state_by_name($_POST["state_name"]);
		$response["state_code"] = $state_code;
	}
	
	if(isset($_POST["country_name"])) {
		$country_code = get_country_by_name($_POST["country_name"]);
		$response["country_code"] = $country_code;
	}

	return $response;
}




switch($_POST["action"]) {
	// UPGRADE TO 1.2.5.0 
	case "upgrade":
		global $_wt_options;
		$upgrade = wordtour_upgrade_1_2_5_0();
		if($upgrade) $_wt_options->update(array("version"=>WORDTOUR_VERSION));	
	break;	
	//EVENTBRITE
	case "save_eventbrite_event":
		$eventbrite = new WT_Eventbrite();
		$eb_id = $eventbrite->save_event($_POST);
		try {
			$r = $eventbrite->response; 	
			if($r["error"]) {
				if($r["error"]["error_type"] == "Region error") $r["error"]["error_message"] = $r["error"]["error_message"]."<br/>Refer to the venue administration page, could be because venue is missing a state value";
				if($r["error"]["error_type"] == "Not Found") $r["error"]["error_message"] = $r["error"]["error_message"]."<br/>Event dosn't exist in Eventbrite, Please try again";
				echo json_encode(array("type"=>"error","msg"=>"Error publishing event to eventbrite:<p>".$r["error"]["error_message"]." (".$r["error"]["error_type"].")</p>"));
				exit();
			} else if($r["process"]){
				$social = new WT_Social();
				$social_row = $social->get_by_event($_POST["eventbrite_event_id"],"ebevent");
				 
				echo json_encode(array("type"=>"success",
										"url"=>"http://www.eventbrite.com/myevent?eid=$eb_id",
										"tickets" => "http://www.eventbrite.com/event/$eb_id", 
										"publish_date" => WT_DBPrepere::datetime_short_out($social_row["social_publish_time"]),
										"eventbrite"=>$r));
				exit();
			}
		} catch(Exception $e){
			
		}
		echo json_encode(array("type"=>"error","msg"=>"Error connecting to Eventbrite, Please try again later"));
	break;
	case "import-eventbrite-events":
		$eventbrite = new WT_Eventbrite();
		$eventbrite->import($_POST["artist_id"]);    
		echo json_encode($eventbrite->response);
	break;
	// GENERAL
	case "panel-state":
		if(isset($_POST["page"]) && isset($_POST["panels"])) {
			echo wordtour_update_panel_state($_POST["page"],$_POST["panels"]);
		}		
	break;
	case "theme_path":
		global $_wt_options;
		if(isset($_POST["path"]) && !empty($_POST["path"])) {
			$path = trim($_POST["path"]);
			$full_path = realpath(ABSPATH.$path);
			if(is_dir($full_path)) {
				$theme_select_markup = "<select id='theme_default_name' name='wordtour_settings[default_theme]'>";
				foreach(wt_get_themes($full_path."/") as $theme){
					$theme_name = strtoupper($theme);
					if($theme_name!="LIBRARY") {
						$selected_theme = $options["default_theme"] == $theme ? "selected='true'" : "";
						$theme_select_markup.="<option $selected_theme value='$theme'>$theme_name</option>";
					}	  
				}
				$theme_select_markup .= "</select>";
				
				$abspath = ABSPATH;
				if($pos = strpos($full_path,'\\')) {
					$abspath =  preg_replace('/\//','\\',$abspath);	
				}
				
				echo json_encode(array(
					"success" => 1,
					"path"    => str_replace($abspath,"",$full_path).($pos ? "\\" : "/"),
					"themes"  => $theme_select_markup
				));
			} else {
				echo json_encode(array(
					"error"  => 1,
					"msg"    => "Path '<i>$full_path</i>' doesnt exist"
				));				 
			}
		}
	break;
	case "map":
		echo json_encode(wt_get_map_str($_POST));
	break;
	// GENERAL
	case "get-key":
		echo json_encode(ajax_get_key());		
	break;
	// THUMBNAIL
	case "get-thumbnail":
		echo json_encode(get_attachment_data($_POST["attachment_id"]));		
	break;
	// TWITTER
	case "update_twitter":
		$social = new Wt_Social();
		$social->insert($_POST["_nonce"],$_POST["type"],$_POST["event_id"],$_POST["ref_id"]);
		$social->db_response("json");	
	break;
	case "update-facebook":
		$social = new Wt_Social();
		$social->insert($_POST["_nonce"],$_POST["type"],$_POST["event_id"],$_POST["ref_id"]);
		$social->db_response("json");	
	break;
	// POST
	case "add_post":
		$post = new WT_Post();
		$post->update($_POST);
		$post->db_response("json");	
	break;
	
	//COMMENTS
	case "get_comment":
		$comment = new WT_Comment($_POST["comment_id"]);
		if(!empty($_POST["comment_id"])) {
			$comment->retrieve();
		} 
		echo json_encode($comment->db_out(null,0));
	break;
	
	case "get_event_comments":
		if(!empty($_POST["event_id"])) {
			$comment = new WT_Comment();
			echo "<table id='the-comment-list' class='widefat'>";
				comments_rows($comment->query("comment_event_id=".$_POST["event_id"]));
			echo "</table>";
		}
	break;
	case "insert_comment":
		$comment = new WT_Comment();
		$insert = $comment->insert($_POST);
		if($insert) $comment->db_response("html");
	break;
	case "more_comments":	
		$comments = new WT_Comments($_POST["event_id"],$_POST["start_index"]);
		if($comments->is_comments()) {
			$comments->json();	
		}
	break;
	case "update_comment":	
		# Initialize comment, without retrieving it from db
		$comment = new WT_Comment($_POST["comment_id"]);
		# Update DB
		$comment->update($_POST);
		$comment->db_response("json");
	break;			
	case "approve_comment":
		$comment = new WT_Comment($_POST["comment_id"]); 
		$comment->approve($_POST["_nonce"],$_POST["event_id"]);
		$comment->db_response("json");	
	break;
	case "unapprove_comment":
		$comment = new WT_Comment($_POST["comment_id"]);
		$comment->unapprove($_POST["_nonce"],$_POST["event_id"]);
		$comment->db_response("json");
	break;
	case "delete_comment":	
		$comment = new WT_Comment($_POST["comment_id"]);
		$comment->delete($_POST["_nonce"],$_POST["event_id"]);
		$comment->db_response("json");
	break;
	case "delete_all_comments":
		unset($_POST["action"]);
		if($_POST["id"]) {
			$comment = new WT_Comment();
			$comment->delete_all(json_decode(stripslashes($_POST["id"])),$_POST["_nonce"]);
			$comment->db_response("json");
		}
	break;
	
	//RSVP
	case "not-attend":
		$rsvp = new WT_RSVP();
		$rsvp->notattend_event($_POST["event_id"]);
		$rsvp->panel_markup($_POST["event_id"]);
		
	break;
	case "attend":
		$rsvp = new WT_RSVP($_POST["event_id"]);
		$rsvp->attend_event($_POST["event_id"]);
		$rsvp->panel_markup($_POST["event_id"]);
	break;
	case "delete-rsvp":
		if($_POST["rsvp_id"]) {
			$rsvp = new WT_RSVP($_POST["rsvp_id"]);
			$rsvp->remove();
			$rsvp->db_response("json");	
			//echo json_encode(array("type"=>"success"));	
		}
		
	break;
	// EVENTS
	case "get_event":
		$event = new WT_Event($_POST["event_id"]);
		if(!empty($_POST["event_id"])) {
			$event->retrieve();
		} else {
			$event->defaults();
		}
		
		echo json_encode($event->db_out(null,0));
	break;
	case "insert_event":
		$event = new WT_Event();
		$event->insert($_POST);
		$event->db_response("json");	
	break;
	case "update_event":
		$event = new WT_Event($_POST["event_id"]);
		$event->update($_POST);
		$event->db_response("json");
	break;
	case "quickupdate_event":
		$event = new WT_Event($_POST["event_id"]);
		$event->quick_update($_POST);
		$event->db_response("json");
	break;
	case "delete_event":
		$event = new WT_Event($_POST["event_id"]);
		$event->delete($_POST["_nonce"]);
		$event->db_response("json");	
	break;
	case "delete_all_events":
		if($_POST["id"]) {
			$event = new WT_Event();
			$event->delete_all(json_decode(stripslashes($_POST["id"])),$_POST["_nonce"]);
			$event->db_response("json");
		}
	break;
	case "unpublish_event":
		$event = new WT_Event($_POST["event_id"]);
		$event->unpublish($_POST);
		$event->db_response("json");
	break;
	case "publish_event":
		$event = new WT_Event($_POST["event_id"]);
		$event->publish($_POST);
		$event->db_response("json");
	break;
	
	case "get_event_rsvp":
		if(!empty($_POST["event_id"])) {
			$event = new WT_Event($_POST["event_id"]);
			echo json_encode($event->get_rsvp_users());
		}
	break;
	// VENUE
	case "get_venue":
		$venue = new WT_Venue($_POST["venue_id"]);
		if(!empty($_POST["venue_id"])) {
			$venue->retrieve(); 
		} else {
			$venue->defaults();
		}
		echo json_encode($venue->db_out(null,0));
	break;
	case "update_venue":
		$venue = new WT_Venue($_POST["venue_id"]);
		$venue->update($_POST);
		$venue->db_response("json");	
	break;
	case "quickupdate_venue":
		$venue = new WT_Venue($_POST["venue_id"]);
		$venue->quick_update($_POST);
		$venue->db_response("json");	
	break;
	case "insert_venue":
		$venue = new WT_Venue();
		$venue->insert($_POST);
		$venue->db_response("json");	
	break;
	case "delete_venue":
		$venue = new WT_Venue($_POST["venue_id"]);
		$venue->delete($_POST["_nonce"]);
		$venue->db_response("json");	
	break;
	case "delete_all_venues":
		if($_POST["id"]) {
			$venue = new WT_Venue();
			$venue->delete_all(json_decode(stripslashes($_POST["id"])),$_POST["_nonce"]);
			$venue->db_response("json");
		}
	break;
	case "default_venue":
		$id = $_POST["venue_id"]; 
		if($id) {
			$venue = new WT_Venue($id);
			$venue->set_default(1);
			$venue->db_response("json");
		}
	break;
	case "remove_default_venue":
		$id = $_POST["venue_id"]; 
		if($id) {
			$venue = new WT_Venue($id);
			$venue->set_default("");
			$venue->db_response("json");
		}
	break;
	// ALBUM
	case "get_album":
		$album = new WT_Album($_POST["album_id"]);
		if(!empty($_POST["album_id"])) {
			$album->retrieve(); 
		} else {
			$album->defaults();
		}
		echo json_encode($album->db_out(null,0));
	break;
	case "insert_album":
		$album = new WT_Album();
		$album->insert($_POST);
		$album->db_response("json");	
	break;
	case "update_album":
		$album = new WT_Album($_POST["album_id"]);
		$album->update($_POST);
		$album->db_response("json");	
	break;
	case "quickupdate_album":
		$album = new WT_Album($_POST["album_id"]);
		$album->quick_update($_POST);
		$album->db_response("json");	
	break;
	case "delete_album":
		unset($_POST["action"]);
		$artist = new WT_Album($_POST["album_id"]);
		$artist->delete($_POST["_nonce"]);
		$artist->db_response("json");	
	break;
	case "delete_all_albums":
		unset($_POST["action"]);
		if($_POST["id"]) {
			$album = new WT_Album();
			$album->delete_all(json_decode(stripslashes($_POST["id"])),$_POST["_nonce"]);
			$album->db_response("json");
		}
	break;
	case "import_album_info":
		global $_wt_options;
		$api_key = $_wt_options->options("lastfm_key");
		$artist = $_POST['artist'];
		$album = $_POST['album']; 

		
		$lastfm_base_url = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=$api_key";
		$lastfm_query = "&artist=".urlencode($artist)."&album=".urlencode($album)."&format=json&autocorrect=1";
		$lastfm_query_url = $lastfm_base_url.$lastfm_query;
		$response = wt_file_get_contents($lastfm_query_url);
		$response = json_decode($response);
		 
		if($response->album) {
			$album = $response->album;
			$result = array(
				"release_date" => trim($album->releasedate),
				"title" => $album->name,
				"artist" => $album->artist,
				"about" => $album->wiki ? $album->wiki->content : "",
				"tracks" => $album->tracks ? $album->tracks->track : ""
			);	
		} else {
			$result = $response;	
		} 
		echo json_encode($result);
		exit();
	break;	
	// TRACK
	case "get_track":
		$track = new WT_Track($_POST["track_id"]);
		if(!empty($_POST["track_id"])) {
			$track->retrieve(); 
		} else {
			$track->defaults();
		}
		echo json_encode($track->db_out(null,0));
	break;
	case "insert_track":
		$track = new WT_Track();
		$track->insert($_POST);
		$track->db_response("json");	
	break;
	case "update_track":
		$track = new WT_Track($_POST["track_id"]);
		$track->update($_POST);
		$track->db_response("json");	
	break;
	case "quickupdate_track":
		$track = new WT_Track($_POST["track_id"]);
		$track->quick_update($_POST);
		$track->db_response("json");	
	break;
	case "delete_track":
		unset($_POST["action"]);
		$artist = new WT_Track($_POST["track_id"]);
		$artist->delete($_POST["_nonce"]);
		$artist->db_response("json");	
	break;
	case "delete_all_tracks":
		unset($_POST["action"]);
		if($_POST["id"]) {
			$track = new WT_Track();
			$track->delete_all(json_decode(stripslashes($_POST["id"])),$_POST["_nonce"]);
			$track->db_response("json");
		}
	break;
	// ARTIST
	case "get_artist":
		$artist = new WT_Artist($_POST["artist_id"]);
		if(!empty($_POST["artist_id"])) {
			$artist->retrieve(); 
		} else {
			$artist->defaults();
		}
		echo json_encode($artist->db_out(null,0));
	break;
	case "update_artist":
		$artist = new WT_Artist($_POST["artist_id"]);
		$artist->update($_POST);
		$artist->db_response("json");	
	break;
	case "quickupdate_artist":
		$artist = new WT_Artist($_POST["artist_id"]);
		$artist->quick_update($_POST);
		$artist->db_response("json");	
	break;
	case "insert_artist":
		$artist = new WT_Artist();
		$artist->insert($_POST);
		$artist->db_response("json");	
	break;
	case "delete_artist":
		unset($_POST["action"]);
		$artist = new WT_Artist($_POST["artist_id"]);
		$artist->delete($_POST["_nonce"]);
		$artist->db_response("json");	
	break;
	case "delete_all_artists":
		unset($_POST["action"]);
		if($_POST["id"]) {
			$artist = new WT_Artist();
			$artist->delete_all(json_decode(stripslashes($_POST["id"])),$_POST["_nonce"]);
			$artist->db_response("json");
		}
	break;
	case "default_artist":
		$id = $_POST["artist_id"]; 
		if($id) {
			$artist = new WT_Artist($id);
			$artist->set_default(1);
			$artist->db_response("json");
		}
	break;
	case "remove_default_artist":
		$id = $_POST["artist_id"]; 
		if($id) {
			$artist = new WT_Artist($id);
			$artist->set_default("");
			$artist->db_response("json");
		}
	break;
	// TOUR
	case "get_tour":
		$tour = new WT_Tour($_POST["tour_id"]);
		if(!empty($_POST["tour_id"])) {
			$tour->retrieve(); 
		} else {
			$tour->defaults();
		}
		echo json_encode($tour->db_out(null,0));
	break;
	case "update_tour":
		$tour = new WT_Tour($_POST["tour_id"]);
		$tour->update($_POST);
		$tour->db_response("json");	
	break;
	case "quickupdate_tour":
		$tour = new WT_Tour($_POST["tour_id"]);
		$tour->quick_update($_POST);
		$tour->db_response("json");	
	break;
	case "insert_tour":
		$tour = new WT_Tour();
		$tour->insert($_POST);
		$tour->db_response("json");	
	break;
	case "default_tour":
		$id = $_POST["tour_id"]; 
		if($id) {
			$tour = new WT_Tour($id);
			$tour->set_default(1);
			$tour->db_response("json");
		}
	break;
	case "remove_default_tour":
		$id = $_POST["tour_id"]; 
		if($id) {
			$tour = new WT_Tour($id);
			$tour->set_default("");
			$tour->db_response("json");
		}
	break;
	case "delete_tour":
		unset($_POST["action"]);
		$tour = new WT_Tour($_POST["tour_id"]);
		$tour->delete($_POST["_nonce"]);
		$tour->db_response("json");	
	break;
	case "delete_all_tour":
		unset($_POST["action"]);
		if($_POST["id"]) {
			$tour = new WT_Tour();
			$tour->delete_all(json_decode(stripslashes($_POST["id"])),$_POST["_nonce"]);
			$tour->db_response("json");
		}
	break;
	// GALLERY
	case "get_gallery":
		$gallery = new WT_Gallery($_POST["gallery_id"]);
		if(!empty($_POST["gallery_id"])) {
			$gallery->retrieve(); 
		} else {
			$gallery->defaults();
		}
		echo json_encode($gallery->db_out(null,0));	
	break;
	case "insert_gallery":
		$gallery = new WT_Gallery();
		$gallery->insert($_POST);
		$gallery->db_response("json");	
	break;
	case "update_gallery":
		$gallery = new WT_Gallery($_POST["gallery_id"]);
		$gallery->update($_POST);
		$gallery->db_response("json");	
	break;
	case "update_gallery-from-event":
		$gallery = new WT_Gallery($_POST["gallery_id"]);
		$gallery->update($_POST);
		return $gallery->data["gallery_name"];	
	break;
	case "delete_gallery":
		$gallery = new WT_Gallery($_POST["gallery_id"]);
		$gallery->delete($_POST["_nonce"]);
		$gallery->db_response("json");	
	break;
	case "delete_all_galleries":
		if($_POST["id"]) {
			$gallery = new WT_Gallery();
			$gallery->delete_all(json_decode(stripslashes($_POST["id"])),$_POST["_nonce"]);
			$gallery->db_response("json");
		}
	break;
}






?>