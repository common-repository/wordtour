<?php
Class WT_Eventbrite {
	public $is_init = 0;
	public $user_key = 0;
	public $app_key = 0;
	public $response = null;
	
	public function __construct(){
		global $_wt_options;
		$this->user_key = $_wt_options->options("eventbrite_user_key");
		$this->app_key = $_wt_options->options("eventbrite_app_key");
	}
	/* Check if eventbrite was configured */
	public function is_init(){
		global $_wt_options;
		if($this->user_key || !empty($this->user_key) && $this->app_key || !empty($this->app_key)) return 1;
		return 0;
	}
	/* Check if need to update eventbrite each time component saved*/
	public function is_update(){
		global $_wt_options;
		if($this->is_init() && $_wt_options->options("eventbrite_auto_update") ==1) return 1;
		return 0;
	}
	
	private function is_valid_response($response = null){
		if(!$response) $response =  '{"error": {"error_type": "Connection Error", "error_message": "Can\"t connect to Eventbrite servers"}}'; 
		$this->response = json_decode($response,true);
		if($this->response["error"]) return 0;
		return 1;
	}
	
	public function add_organizer_id(){
		try {
			global $_wt_options;
			$org = wt_file_get_contents("http://www.eventbrite.com/json/organizer_new?app_key=$this->app_key&user_key=$this->user_key&name=wordtour");
			if($org) {
				if($this->is_valid_response($org)) {
					$id = $this->response["process"]["id"];
					if($id) $_wt_options->update(array("eventbrite_organizer_id"=>$id));	
					return $id;			
				} else {
					$org = wt_file_get_contents("http://www.eventbrite.com/json/user_list_organizers?app_key=$this->app_key&user_key=$this->user_key");
					if($org) {
						if($this->is_valid_response($org)) {
							foreach((array) $this->response["organizers"] as $orgs) {
								if($orgs["organizer"]["name"] == "wordtour") {
									return $orgs["organizer"]["id"]; 
								}
							}	
						}
					} 
					return 0;
				}
			} else {
			   return 0;
			}	
		} catch(Exception $e){}
		return 0;		
	}
	
	public function get_organizer_id(){
		try {
			global $_wt_options;
			if($this->is_init() && !$_wt_options->options("eventbrite_organizer_id")) {
				return $this->add_organizer_id();	
			}
			if($_wt_options->options("eventbrite_organizer_id")) return $_wt_options->options("eventbrite_organizer_id");
			
			return 0;
	
		} catch(Exception $e){}
		return 0;		
	}
	
	public function update_event($event_id,$data = array()){
		if($this->is_update() && $event_id) {
			$data = array(
				"eventbrite_event_id" => $event_id, 
				"eventbrite_title" => !empty($data["event_title"]) ? $data["event_title"] : null,
			  	"eventbrite_description" => !empty($data["event_notes"]) ? $data["event_notes"] : null,
				"eventbrite_status"      => $data["event_status"] == "cancelled" ? "canceled" : null 
			);
			return $this->save_event($data);
		}	
		return 0;
	}
	
	public function save_event($data = array()){
		$event_id = $data["eventbrite_event_id"];
		$event = new WT_Event($event_id);
		$event->retrieve();
		
		if($this->is_init() && $event->data) {
			$social = new WT_Social();
			// check if event social type is exist
			$social_row = $social->get_by_event($data[eventbrite_event_id],"ebevent");
			// if exist get eventbrite event id
			$eb_event_id = $social_row ? $social_row["social_ref_id"] : 0;
			// check if event exist - by id
			$r = wt_file_get_contents("http://www.eventbrite.com/json/event_get?app_key=$this->app_key&user_key=$this->user_key&id=$eb_event_id");
			// event exist in system - need to update
			if($this->is_valid_response($r)) {
				$is_event = 1;	
			} else {
				try {
					// event doesnt exist in system - need to add new
					if($this->response["error"]["error_type"] == "Not Found") {
						$is_event = 0;
					} else {
						return 0;	
					}
				} catch(Exception $e){
					return 0;
				}
			}
			
			$param = array(
				"title" => $data["eventbrite_title"],
				"start_date" => $event->data["event_start_date"]." ".$event->data["event_start_time"], 
				"end_date" => $event->data["event_end_date"]." ".($event->data["event_end_time"] == "00:00:01" ? $event->data["event_start_time"] : $event->data["event_end_time"]),
				"timezone"  => "GMT".(get_option("gmt_offset") > 0 ? "+".get_option("gmt_offset") : get_option("gmt_offset")),
				"privacy"  => $data["eventbrite_privacy"],
				"personalized_url" => $data["personalized_url"],
				"status"  => $data["eventbrite_status"],
				"description"  => $data["eventbrite_description"],
				"organizer_id" => $this->get_organizer_id()
			);
			
			$venue_id =  $this->get_venue_id($event->data["event_venue_id"]);
			if($venue_id) {
				$param["venue_id"] = $venue_id;
				if(!$is_event) {
					$action = "event_new";
				} else {
					$action = "event_update";
					$param["event_id"] = $eb_event_id;
				}
				$r = wt_file_get_contents("http://www.eventbrite.com/json/$action?app_key=$this->app_key&user_key=$this->user_key&".http_build_query($param));
				if($this->is_valid_response($r)) {
					if(!$social_row) {
						$eb_event_id = $this->response["process"]["id"];
						$social->insert(wp_create_nonce(WT_SOCIAL::NONCE_INSERT),"ebevent",$event_id,$eb_event_id,"event");
						return $eb_event_id;
					} else {
						$social->update(wp_create_nonce(WT_SOCIAL::NONCE_INSERT),$social_row["social_id"],"ebevent",$event_id,$eb_event_id,"event");
						return $eb_event_id;
					}
				} else {
					if($this->response["error"]["error_type"] == "Not Found") {
						global $wpdb;
						$wpdb->query("DELETE FROM ".WORDTOUR_SOCIAL." WHERE social_type='ebevent' AND social_parent_id=$event_id AND social_parent_type='event' AND social_ref_id=$eb_event_id");
					}	
				}
			}
			return 0;
		}
	}

	public function get_venue_id($venue_id = 0){
		if($venue_id) {
			$venue = new WT_Venue($venue_id);
			$venue->retrieve();
			return 	$this->save_venue($venue->db_out(null,0),0);
		}
		
		return 0;
	}
	
	public function save_venue($data = array(),$update=1){
		if($this->is_init()) {
			try {
				$social = new WT_Social();
				// check if venue social type is exist
				$social_row = $social->get_by_venue($data[venue_id],"ebvenue");
				// if exist get eventbrite venue id
				$eb_venue_id = $social_row ? $social_row["social_ref_id"] : 0;
				// check if venue exist - by name
				$r = wt_file_get_contents("http://www.eventbrite.com/json/user_list_venues?app_key=$this->app_key&user_key=$this->user_key");
				if($this->is_valid_response($r)) {
					$is_venue = 0;
					if(!$this->response["venues"]) throw new Exception("Connection Error");
					foreach($this->response["venues"] as $v) {
						// if id already exist, update venue
						if($v["venue"]["id"] == $eb_venue_id) $is_venue =1;
						// if theres is a venue with the same name, replace venue id, because by sending event with the same name it will invoke error 404
						if($v["venue"]["name"] == $data["venue_name"]) {
							$is_venue =1;
							$eb_venue_id = $v["venue"]["id"]; 				
						} 
					}
					
					if(!$update && $is_venue) return $eb_venue_id;
					// prepere params
					$param = array(
						"venue"        => $data["venue_name"],
						"adress"       => $data["venue_address"],
						"city"         => $data["venue_city"],
						"region"       => $data["venue_state_code"],
						"postal_code"  => $data["venue_zip"],
						"country_code" => $data["venue_country_code"]	
					);	
					// decide if insert or update venue
					if($is_venue) {
						$action = "venue_update";
						$param["id"] = $eb_venue_id;
					} else {
						$action = "venue_new";
						$param["organizer_id"] = $this->get_organizer_id();	
					}
					$r = wt_file_get_contents("http://www.eventbrite.com/json/$action?app_key=$this->app_key&user_key=$this->user_key&".http_build_query($param));
					if($this->is_valid_response($r)) {
						if(!$social_row) {
							$eb_venue_id = $this->response["process"]["id"];
							$social->insert(wp_create_nonce(WT_SOCIAL::NONCE_INSERT),"ebvenue",$data["venue_id"],$eb_venue_id,"venue");
							return $eb_venue_id;
						} else {
							$social->update(wp_create_nonce(WT_SOCIAL::NONCE_INSERT),$social_row["social_id"],"ebvenue",$data["venue_id"],$eb_venue_id,"venue");
							return $eb_venue_id;
						}
					} 
				} 
			} catch(Exception $e){
				$this->is_valid_response(0);
			}
		}
		return 0;		
	}
	
	public function import($artist_id = 0){
		$results = array();
		if(!$artist_id || empty($artist_id)) {
			$this->is_valid_response('{"error": {"error_type": "Artist Missing", "error_message": "Can\"t import events without an artist"}}');	
		} else {
			// get list of all venues
			$venues = wt_file_get_contents("http://www.eventbrite.com/json/user_list_venues?app_key=$this->app_key&user_key=$this->user_key");
			$venues_parents = array();
			if($this->is_valid_response($venues)) {	
				$social = new WT_Social();
				try {
					if(!$this->response["venues"]) throw new Exception("Connection Error");
					foreach($this->response["venues"] as $v) {
						$venue_id = 0;
						$social_regist = $social->get_by_ref_id($v["venue"]["id"],"venue","ebvenue");
						if(!$social_regist) { 
							$venue_params = array(
								"_nonce"               => wp_create_nonce(WT_Venue::NONCE_INSERT),
								"venue_name"           => $v["venue"]["name"],
								"venue_city"           => $v["venue"]["city"],
								"venue_country"        => get_country_by_code($v["venue"]["country_code"]), 
								"venue_state"          => get_state_by_code($v["venue"]["region"]),
								"venue_zip"            => $v["venue"]["postal_code"],
								"venue_address"        => $v["venue"]["address"],
								"venue_tour_status"    => 1,
								"venue_gallery_status" => 1,
								"venue_video_status"   => 1,
								"venue_flickr_status"  => 1,
								"venue_post_status"    => 1
							);
							
							$venue = new WT_Venue();
							$venue->insert($venue_params,0);
							if($venue->data) {
								$venue_id = $venue->data["venue_id"];
								$social->insert(wp_create_nonce(WT_SOCIAL::NONCE_INSERT),"ebvenue",$venue->data["venue_id"],$v["venue"]["id"],"venue");
							} else {
								$results["venues"][] = array(
									"venue" =>$v["venue"],
									"msg"  =>$venue->db_response() 
								);
							}
						} else {
							$venue_id = $social_regist["social_parent_id"];
						}
						if($venue_id) $venues_parents[$v["venue"]["id"]] = $venue_id;
					}
					
					$events = wt_file_get_contents("http://www.eventbrite.com/json/user_list_events?app_key=$this->app_key&user_key=$this->user_key");
					if($this->is_valid_response($events)) {	
						if(!$this->response["events"]) throw new Exception("Connection Error");
						foreach($this->response["events"] as $e) {
							if($e["event"]["status"] != "Canceled") { 
								$social_regist = $social->get_by_ref_id($e["event"]["id"],"event","ebevent");
								if(!$social_regist) {
									$event_params = array(
										"_nonce"               => wp_create_nonce(WT_Event::NONCE_INSERT),
										"event_title"		   => $e["event"]["title"],
										"event_notes"		   => $e["event"]["description"],
										"event_start_date"     => mysql2date("Y-m-d",$e["event"]["start_date"]),
										"event_start_time"     => mysql2date("H:i:s",$e["event"]["start_date"]),
										"event_end_date"       => mysql2date("Y-m-d",$e["event"]["end_date"]),
										"event_end_time"       => mysql2date("H:i:s",$e["event"]["end_date"]),
										"event_venue_id"       => $venues_parents[$e["event"]["venue"]["id"]],
										"event_artist_id"      => $artist_id,
										"event_published"      => 1,
										'event_status'         => $e["event"]["status"] == "Canceled" ? "cancelled" : "active",
										"tkts_url"             => $e["event"]["url"],
										"tkts_price"           => $e["ticket"]["price"].$e["ticket"]["currency"],
										'comment_status'       => 1,
										'rsvp_status'          => 1,
										'gallery_status'       => 1,
										'flickr_status'        => 1,
										'post_status'          => 1,
										'video_status'         => 1
									); 
									$event = new WT_Event();
									$event->insert($event_params,0);
									if($event->data) {
										$event_id = $event->data["event_id"];
										$social->insert(wp_create_nonce(WT_SOCIAL::NONCE_INSERT),"ebevent",$event->data["event_id"],$e["event"]["id"],"event");
									} else {
										$results["events"][] = array(
											"event" => $e["event"],
											"msg"   => $event->db_response()
										);
									}
								}
							}
						}
						$this->response = array("type"=>"success","log"=>$results);
						return 1;
					}
				} catch(Exception $e){
					$this->is_valid_response(0);
				}
			} 
		}
		$this->response = array("type"=>"error","log"=>$this->response); 
	}
}