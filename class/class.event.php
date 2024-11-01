<?php
class WT_Event extends WT_Object {
	const NONCE_UPDATE = 'wt-event-update';  
	const NONCE_INSERT = 'wt-event-insert';
	const NONCE_DELETE = 'wt-event-delete';
	const NONCE_UNPUBLISH = 'wt-event-unpublish';
	const NONCE_PUBLISH = 'wt-event-publish';
	const NONCE_TRASH = 'wt-event-trash';
	const NONCE_RESTORE = 'wt-event-restore';
	
	public function retrieve() {
		global $wpdb,$_wt_options;
		$id = $this->id;
		$data = null;
		if($id) {
			$data = $wpdb->get_results($wpdb->prepare("SELECT 
				*  
				FROM ".WORDTOUR_EVENTS." AS e 
				LEFT JOIN ".WORDTOUR_VENUES." AS v 
				ON e.event_venue_id = v.venue_id 
				LEFT JOIN ".WORDTOUR_ARTISTS." AS a
				ON e.event_artist_id = a.artist_id 
				LEFT JOIN ".WORDTOUR_TOUR." AS t
				ON e.event_tour_id = t.tour_id 
				LEFT JOIN ".WORDTOUR_EVENTS_META." AS m ON e.event_meta_id = m.meta_id
				WHERE e.event_id = $id ORDER BY id ASC"),"ARRAY_A");
			if(is_array($data)) {
				$event = $data[0];
				$event["event_more_artists"] = array();
				foreach($data as $e) {
					if(!$e["event_is_headline"]) $event["event_more_artists"][] = array("id"=>$e["artist_id"],"name"=>$e["artist_name"]);
				}
				$event_wpdb = clone $wpdb;
				$this->meta_id = $event["event_meta_id"];
				$social = new WT_Social();
				$fbstatus = $social->get_by_event($this->id,"fbstatus");
				$ebstatus = $social->get_by_event($this->id,"ebevent");
				$fbevent  = $social->get_by_event($this->id,"fbevent");
				$twitter  = $social->get_by_event($this->id,"twitter");
				$post     = $social->get_by_event($this->id,"post");
				$data = array_merge($data,array("post_ref_id"=>($post ?  admin_url("post.php?action=edit&post=$post[social_ref_id]") : 0),
									  			"facebook_event_id"=>($fbevent ? $fbevent["social_ref_id"] : 0),
									  			"twitter_status_date"=>($twitter ? $twitter["social_publish_time"] : 0),
												"facebook_status_date"=>($fbstatus ? $fbstatus["social_publish_time"] : 0),
												"eventbrite_status_date"=>($ebstatus ? $ebstatus["social_publish_time"] : 0),
												"eventbrite_event_id"=>  ($ebstatus ? $ebstatus["social_ref_id"] : 0)
										)
						);
				
				
				$event["event_thumbnail"] =  get_attachment_data($this->get_thumbnail("event"));
				$event["event_videos"]    =  $this->get_videos("event");
				$event["event_gallery"]   =  $this->get_gallery("event");
				$event["event_category"]  =  $this->get_category("event");
				$event["event_genre"]     =  $this->get_genre("event");
				
			} 	 
		}
		//$this->db_result("error",$event_wpdb,array("msg"=>"Can't retrieve event(<i>Missing event id</i>)"));
		return parent::retrieve($event);		
	}
	
	public function query($query = "") {
		global $wpdb; 
 		parse_str($query,$params);
 		$sql = array();
 		foreach($params as $column=>$value) {
 			$value = is_numeric($value) ? $value : "'$value'";
 			if($column!="order" && $column!="limit" && $column!="direction") $sql[] = "$column=$value";	
 		}
 		
 		$direction = isset($params["direction"]) ? $params["direction"] : "DESC";
 		$order = isset($params["order"]) ? "ORDER BY $params[order] $direction" : "";
 		$limit = (isset($params["limit"]) ? "LIMIT $params[limit]" : "");
 		$where = (count($sql)) ? "WHERE ".implode(" AND ",$sql) : "";
 	 	//print_r("SELECT SQL_CALC_FOUND_ROWS * FROM $table $where $order $limit");
 		return $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WORDTOUR_EVENTS." AS e 
	 		LEFT JOIN ".WORDTOUR_VENUES." AS v 
			ON e.event_venue_id = v.venue_id 
			LEFT JOIN ".WORDTOUR_ARTISTS." AS a
			ON e.event_artist_id = a.artist_id 
			LEFT JOIN ".WORDTOUR_TOUR." AS t
			ON e.event_tour_id = t.tour_id 
			LEFT JOIN ".WORDTOUR_EVENTS_META." AS m ON e.event_meta_id = m.meta_id
			$where $order $limit"),"ARRAY_A");
	}
	
	public function defaults() {
		global $wpdb,$_wt_options;
		$options = $_wt_options->options();
		parent::defaults(array(WORDTOUR_EVENTS,WORDTOUR_EVENTS_META));
		
		if(!empty($options["default_artist"]) && empty($this->data["event_artist_id"])) {
			$id = $options["default_artist"] ;
			$artist = new WT_Artist($id);
			$data = $artist->retrieve();
			if($data) {
				$this->data["event_artist_id"] = $id;
				$this->data["artist_name"] = $data["artist_name"];	
			};	
		}
		if(!empty($options["default_tour"]) && empty($this->data["event_tour_id"])) {
			$id = $options["default_tour"];
			$tour = new WT_Tour($id);
			$data = $tour->retrieve();
			if($tour) {
				$this->data["event_tour_id"] = $id;
				$this->data["tour_name"] = $data["tour_name"];	
			};	
		}
		
		if(!empty($options["default_venue"]) && empty($this->data["event_venue_id"])) {
			$id = $options["default_venue"];
			$venue = new WT_Venue($id);
			$data = $venue->retrieve();
			if($data) {
				$this->data["event_venue_id"] = $id;
				$this->data["venue_name"] = $data["venue_name"];	
			};	
		}
		
		$this->data = array_merge($this->data,array(
			"event_thumbnail" => "",
			"event_videos"  => array(),
			"event_gallery" => array(),
			"event_category"=> array(),
			"event_genre"=> array(),
			"comment_status" => $options["allow_comments"] ? $options["allow_comments"] : $this->data["comment_status"],
			"rsvp_status" => $options["allow_rsvp"] ? $options["allow_rsvp"] : $this->data["rsvp_status"],
			"gallery_status" => $options["show_gallery"] ? $options["show_gallery"] : $this->data["gallery_status"],
			"flickr_status" => $options["show_flickr"] ? $options["show_flickr"] : $this->data["flickr_status"],
			"video_status" => $options["show_videos"] ? $options["show_videos"] : $this->data["video_status"],
			"post_status" => $options["show_posts"] ? $options["show_posts"] : $this->data["post_status"],
			"event_more_artists" => array()
		));
		return $this->data;
	} 

	protected function validate(&$data,$nonce_name) {
		# check in parent class if nonce is legal
		global $wpdb;
		$is_valid = parent::validate($data,$nonce_name);
		if($is_valid && is_array($data)) {
			$is_valid = true ;
			// Check if date is empty
			if(empty($data["event_start_date"])) {
				$is_valid = false ;
				$this->add_db_result("event_start_date","required","Start Date is missing");
			}
			// Check if date format is valid
			else if($data["event_start_date"] === null) {
				$is_valid = false ;
				$this->add_db_result("event_start_date","field","Start Date format is not valid");
			}
			// Multi Day Event
			// Check if date format is valid
			if($data["event_end_date"] === null) {
				$is_valid = false ;
				$this->add_db_result("event_end_date","field","End Date is missing");
			}
			// Check if start date is lower\equel to end date 
			if(!empty($data["event_start_date"]) && !empty($data["event_end_date"])) {
				if(strtotime($data["event_start_date"]) > strtotime($data["event_end_date"])) {
					$is_valid = false ;
					$this->add_db_result("event_end_date","required","Event can't end before it starts");
				}
			} 
			
			if(isset($data["event_tour_id"])) {
				if(!is_numeric($data["event_tour_id"])) {
					$is_valid = false;
					$this->add_db_result("event_tour_id","required","Tour information is invalid");	
				} 
			} else {
				$tour_name = trim($data["tour_name"]); 
				if(!empty($tour_name) && !is_numeric($tour_name)) {
					$is_tour = $wpdb->get_row("SELECT tour_id FROM ".WORDTOUR_TOUR." WHERE UPPER(tour_name)='".trim(strtoupper($data["tour_name"])."'"),"ARRAY_A");
					if($is_tour) {
						$data["event_tour_id"] = $is_tour["tour_id"];
					} else {
						$is_valid = false ;
						$this->add_db_result("tour_name","required","Tour '$data[tour_name]' doesn't exist");
					}
				}	
			}
		
			if(isset($data["event_venue_id"])) {
				if(!is_numeric($data["event_venue_id"])) {
					$is_valid = false;
					$this->add_db_result("event_venue_id","required","Venue information is invalid");	
				} 
			} else {
				if(empty($data["venue_name"])) {
					$is_valid = false ;
					$this->add_db_result("venue_name","required","Venue is missing");
				} else if(!is_numeric($data["venue_name"])) {
					$is_venue = $wpdb->get_row("SELECT venue_id FROM ".WORDTOUR_VENUES." WHERE UPPER(venue_name)='".trim(strtoupper($data["venue_name"])."'"),"ARRAY_A");
					if($is_venue) {
						$data["event_venue_id"] = $is_venue["venue_id"];
					} else {
						$is_valid = false ;
						$this->add_db_result("venue_name","required","Venue '$data[venue_name]' doesn't exist");
					}
				}	
			}
			
			if(isset($data["event_artist_id"])) {
				if(!is_numeric($data["event_artist_id"])) {
					$is_valid = false;
					$this->add_db_result("event_artist_id","required","Artist information is invalid");	
				} 
			} else {
				if(empty($data["artist_name"])) {
					$is_valid = false ;
					$this->add_db_result("artist_name","required","Artist is missing");
				} else if(!is_numeric($data["artist_name"])) {
					$is_artist = $wpdb->get_row("SELECT artist_id FROM ".WORDTOUR_ARTISTS." WHERE UPPER(artist_name)='".trim(strtoupper($data["artist_name"])."'"),"ARRAY_A");
					if($is_artist) {
						$data["event_artist_id"] = $is_artist["artist_id"];
					} else {
						$is_valid = false ;
						$this->add_db_result("artist_name","required","Artist '$data[artist_name]' doesn't exist");
					}
				}	
			}
			
			if(isset($data["event_more_artists"])) {
				$more_artists = json_decode(stripslashes($data["event_more_artists"]));
				$more_artists_exist_error = array();
				$more_artists_error = array();
				$more_artists_is_valid = 1;
				$more_artists_id = array();
				if(is_array($more_artists)) {
					foreach($more_artists as $artist_name) {
						$name = addslashes(trim(strtoupper($artist_name)));
						if(!empty($name)) {
							// check if artist exist in the system
							$is_artist = $wpdb->get_row($wpdb->prepare("SELECT artist_id FROM ".WORDTOUR_ARTISTS." WHERE UPPER(artist_name)='".$name."'"),"ARRAY_A");
							//print_r($wpdb);
							if(!$is_artist) {
								$is_valid = false ;
								$more_artists_is_valid = 0;
								$more_artists_exist_error[] = "<i>'$artist_name'</i>";	
							} else {
								if($name == trim(strtoupper($data["artist_name"]))) {
									$is_valid = false ;
									$more_artists_is_valid = 0;
									$more_artists_error[] = "Additional Artist <i>'$artist_name'</i> is already assigned";
								} else {
									$more_artists_id[] = $is_artist["artist_id"]; 
								}	
							}
						}
					}
					
					if(!$more_artists_is_valid) {
						$more_artists_msg = ""; 
						if(count($more_artists_exist_error)>0) {
							$more_artists_msg.= "Additional Artist ".implode(", ",$more_artists_exist_error)." doesn't exist, ";	
						}
						if(count($more_artists_error)>0) {
							$more_artists_msg.= implode(", ",$more_artists_error);
						}
						$this->add_db_result("event_more_artists","required",$more_artists_msg);	
					} else {
						$data["event_more_artists"] = array_unique($more_artists_id);
					} 
				}
				
			}
			
			if(!empty($data["tkts_url"]) && !is_valid_url($data["tkts_url"])) {
				$is_valid = false ;
				$this->add_db_result("tkts_url","required","Buy Tickt url in not valid, the required format is http://your_website_url");	
			}
			
			if(!$is_valid) $this->db_result("error",null,array("data"=>$this->db_response_msg));		
		}
		return $is_valid;
	}
	
	public function db_in($post=null) {
		global $_wt_options,$current_user;
		$data = array();
		if($post) {
			$db = $this->dbprepere;
			if(empty($post["event_end_date"])) $post["event_end_date"] = $post["event_start_date"];
			if($post["event_status"] !="onsale") $post["event_on_sale"] = ""; 
			$data = array_merge($data,array(
				"event_id" => $db->int_in($post["event_id"]),
				"meta_id"  => $db->int_in($post["event_meta_id"]),
				"artists"  => $post["event_more_artists"],
				"common" => array(
					'event_title'        => $db->str_in($post["event_title"]),
					'event_opening_act'  => $db->str_in($post["event_opening_act"]),	
					'event_type'        =>  $db->str_in(strtolower($post["event_type"])),
					'event_start_date'   => $db->date_in($post["event_start_date"]),
					'event_start_time'   => $db->time_in($post["event_start_time"]),
					'event_end_date'     => $db->date_in($post["event_end_date"]),
					'event_end_time'     => $db->time_in($post["event_end_time"]),
					'event_opening_act'  => $db->str_in($post["event_opening_act"]),
					'event_venue_id'     => $db->int_in($post["event_venue_id"]),
					'event_artist_id'    => $db->int_in($post["event_artist_id"]),
					'event_is_headline'  => 1,
					'event_tour_id'      => $db->int_in($post["event_tour_id"]),
				    'event_status'       => $db->str_in($post["event_status"]),
					'event_on_sale'      => $db->date_in($post["event_on_sale"]),
					'event_notes'        => $db->html_in($post["event_notes"]),
					'comment_status'     => $db->int_in($post["comment_status"]),
					'rsvp_status'        => $db->int_in($post["rsvp_status"]),
					'gallery_status'     => $db->int_in($post["gallery_status"]),
					'flickr_status'      => $db->int_in($post["flickr_status"]),
					'post_status'        => $db->int_in($post["post_status"]),
					'video_status'       => $db->int_in($post["video_status"]),
					"event_publish_date" => current_time("mysql",0),
					"event_author"       => $current_user->ID
				),
				"meta" => array(
					"tkts_url" => $db->link_in($post["tkts_url"]),
					"tkts_price" => $db->str_in($post["tkts_price"]),
					"tkts_phone" => $db->str_in($post["tkts_phone"])
				)
			));
		}
		return parent::db_in($data);
	}
	
	public function db_out($data = null,$is_htmlspecial=1) {
		global $_wt_options;
		if(!$data) $data = $this->data;
		$options = $_wt_options->options();
		if($data) {
			$db = $this->dbprepere;
			
			if(is_array($data["event_more_artists"])) {
				foreach($data["event_more_artists"] as $more) {
					$more["name"] = $db->str_out($more["name"],$is_htmlspecial); 
				}
			}
			
			$data = array_merge($data,array(
				"event_notes" => $db->str_out($data["event_notes"],$is_htmlspecial),
				"event_opening_act" => $db->str_out($data["event_opening_act"],$is_htmlspecial),
				"event_more_artists" =>$data["event_more_artists"],
				"event_thumbnail_id"       => $data["event_thumbnail"]["id"],
				"tkts_url" => $db->link_out($data["tkts_url"]),
				"tkts_phone" => $db->str_out($data["tkts_phone"],$is_htmlspecial),
				"tkts_price" => $db->str_out($data["tkts_price"],$is_htmlspecial),
				"event_start_time" => $db->time_out($data["event_start_time"]),
				"event_publish_date" => $db->datetime_short_out($data["event_publish_date"]),
				"event_start_date" => $db->admin_date_out($data["event_start_date"]),
				"event_start_sql"  => $data["event_start_date"],
				"event_end_date" => $db->admin_date_out($data["event_end_date"]),
				"event_end_time" => $db->time_out($data["event_end_time"]),
				"event_artist_id" => $db->int_out($data["event_artist_id"]),
				"event_tour_id" => $db->int_out($data["event_tour_id"]),
				"event_title" => $db->str_out($data["event_title"],$is_htmlspecial),
				"event_type" => $db->str_out(ucwords($data["event_type"]),$is_htmlspecial),
				'event_status'  => $db->str_out($data["event_status"],$is_htmlspecial),
				"event_on_sale" => $db->admin_date_out($data["event_on_sale"]),
				"facebook_status_date" => $db-> datetime_short_out($data["facebook_status_date"]),
				"facebook_event_id" => $data["facebook_event_id"] ? "http://www.facebook.com/event.php?eid=$data[facebook_event_id]" : "",
				"eventbrite_event_id" => $data["eventbrite_event_id"] ? "http://www.eventbrite.com/myevent?eid=$data[eventbrite_event_id]" : "", 
				"twitter_status_date" => $db->datetime_short_out($data["twitter_status_date"]),
				"eventbrite_status_date" => $db->datetime_short_out($data["eventbrite_status_date"]),
				'comment_status'     => $db->int_out($data["comment_status"]),
				'rsvp_status'        => $db->int_out($data["rsvp_status"]),
				'gallery_status'     => $db->int_out($data["gallery_status"]),
				'video_status'      => $db->int_out($data["video_status"]),
				'flickr_status'      => $db->int_out($data["flickr_status"]),
				'post_status'        => $db->int_out($data["post_status"]),
				"permalink"          => $data["event_id"] >0 ? $db->link_out(wt_get_permalink("event",$data["event_id"],array("%date%"=>$data["event_start_date"],"%name%"=>$data["venue_name"]))):"", 
				"_nonce" => empty($data["event_id"]) ? wp_create_nonce(WT_Event::NONCE_INSERT) : wp_create_nonce(WT_Event::NONCE_UPDATE)
			));
			if($data["event_start_date"] == $data["event_end_date"] && empty($data["event_end_time"])) $data["event_end_date"] = "";
			return $data;
		}
		return array();	
	}
	
	public function quick_update($post) {
		$this->retrieve();
		if($this->data) {
			$values = array_merge($this->db_out($this->data,0),array(
				"event_start_date"=> $post["event_start_date"],
				"event_end_date"  => $post["event_end_date"],
				"event_start_time"=> $post["event_start_time"],
				"event_end_time"  => $post["event_end_time"],
				"event_title"     => $post["event_title"],
				"artist_name"     => $post["artist_name"],
				"venue_name"      => $post["venue_name"],
				"tour_name"       => $post["tour_name"],
				"comment_status"  => $post["comment_status"],
				"rsvp_status"     => $post["rsvp_status"],
				"gallery_status"  => $post["gallery_status"],
				"video_status"    => $post["video_status"],
				"flickr_status"   => $post["flickr_status"],
				"post_status"     => $post["post_status"],
				"_nonce" 		  => $post["_nonce"]
			));
			
			$artists = array();
			foreach($this->data["event_more_artists"] as $a) {
				$artists[] = $a["name"];	
			}
			
			$values["event_more_artists"] = json_encode($artists);
			
			unset($values["event_artist_id"]);
			unset($values["event_tour_id"]);
			unset($values["event_venue_id"]);
				
			return $this->update($values);
		}
		$this->db_result("error",array(),array("msg"=>"Error updating Event, please try again"));
	}
	
	public function get_rsvp_users($event_id=0) {
		$event_id = $event_id ? $event_id : $this->id;
		if($event_id) {
			$rsvp = new WT_RSVP();
			$users = $rsvp->get_users_by_event($event_id,0,50);
			$result = array();
			foreach($users["users"] as $user){
				$user_data = get_userdata($user["rsvp_user"]);
				$result[] = array("id"=>$user["rsvp_id"],"nickname"=>$user_data->nickname);	
			}
			return $result;
		}
		
		return array();
	}
	private function _insert(&$common,&$meta) {
		global $wpdb ;
		$wpdb->insert(WORDTOUR_EVENTS_META,$meta);
		if($wpdb->result && $wpdb->insert_id) {
			$meta_id = $wpdb->insert_id;
			$common["event_meta_id"] = $meta_id ;
			$wpdb->insert(WORDTOUR_EVENTS,$common);	
			if($wpdb->result && $wpdb->insert_id) {
				return $wpdb->insert_id ;
			} 
		} 
		if($meta_id) $wpdb->query("DELETE FROM ".WORDTOUR_EVENTS_META." WHERE meta_id = $meta_id");
		return 0; 	
	}
	
	public function insert($values){
		global $wpdb ;
		if($this->validate($values,self::NONCE_INSERT)) {
			$data = $this->db_in($values);
			# create event id
			$this->id = $wpdb->get_var("SELECT max(id)+1 FROM ".WORDTOUR_EVENTS);
			if(!$this->id) $this->id = 1;
			if($this->id) {
				$data["common"]["event_id"] = $this->id;
				# insert primary event
				$insert_id = $this->_insert($data["common"],$data["meta"]);
				if($insert_id) {
					$insert_wpdb = clone $wpdb;
					#insert multiple artists
					try {
						if(is_array($data["artists"])) {
							foreach($data["artists"] as $artist_id) {
								$data["common"]["event_artist_id"] = $artist_id;
								$data["common"]["event_is_headline"] = 0;
								$wpdb->insert(WORDTOUR_EVENTS,$data["common"]);
							}							
						};
					} catch(Exception $e){}
					
					if(!empty($values["event_thumbnail_id"]) && is_string($values["event_thumbnail_id"])) $this->update_thumbnail($values["event_thumbnail_id"],"event");
					if(!empty($values["event_category"])) $this->update_category($this->dbprepere->json_in($values["event_category"]),"event");
					if(!empty($values["event_videos"])) $this->update_videos($this->dbprepere->json_in($values["event_videos"]),"event");
					if(!empty($values["event_gallery"])) $this->update_gallery($this->dbprepere->json_in($values["event_gallery"]),"event");
					if(!empty($values["event_genre"]) && is_string($values["event_genre"])) $this->update_genre($this->dbprepere->json_in($values["event_genre"]),"event");
				
					$this->retrieve();
					wordtour_add_event_type($this->data["event_type"]); 
					$this->db_result("success",$insert_wpdb,array("result"=>$this->db_out($this->data,0),"html"=>$this->admin_html("get_event_row_html")));
					return true ; 
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error adding new Event, please try again(<i>".$wpdb->last_error."</i>)"));
				}
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error adding new Event, please try again(<i>".$wpdb->last_error."</i>)"));
			}
		}
		return false;	
	}
	
	private function _update($event_id,$meta_id,&$common,&$meta) {
		global $wpdb;
		$wpdb->update(WORDTOUR_EVENTS,$common,array('event_id'=>$event_id,"event_is_headline"=>1)); 	
		if($wpdb->result) {
			$wpdb->update(WORDTOUR_EVENTS_META,$meta,array('meta_id'=>$meta_id)); 
			if($wpdb->result) {
				return true ;
			}
		};
		return false ;
	}
	
	public function update($values){
		global $wpdb ;
		$event_id = $this->id;
		if($this->validate($values,self::NONCE_UPDATE)) {
			$data = $this->db_in($values);
			
			$update = $this->_update($data["event_id"],$data["meta_id"],$data["common"],$data["meta"]);
			if($update) {
				$update_wpdb = clone $wpdb;
				// delete more artist and add new rows
				try {
					$wpdb->query("DELETE FROM ".WORDTOUR_EVENTS." WHERE event_id=$event_id AND event_is_headline=0");
					if(is_array($data["artists"])) {
						foreach($data["artists"] as $artist_id) {
							$data["common"]["event_id"] = $event_id;
							$data["common"]["event_meta_id"] = $data["meta_id"];
							$data["common"]["event_artist_id"] = $artist_id;
							$data["common"]["event_is_headline"] = 0;
							$wpdb->insert(WORDTOUR_EVENTS,$data["common"]);
						}							
					};
				} catch(Exception $e){}
				
				
				if(!empty($values["event_thumbnail_id"]) && is_string($values["event_thumbnail_id"])) {
					$this->update_thumbnail($values["event_thumbnail_id"],"event");
						
				}
				if(!empty($values["event_category"]) && is_string($values["event_category"])) $this->update_category($this->dbprepere->json_in($values["event_category"]),"event");
				if(!empty($values["event_videos"]) && is_string($values["event_videos"])) $this->update_videos($this->dbprepere->json_in($values["event_videos"]),"event");
				if(!empty($values["event_gallery"]) && is_string($values["event_gallery"])) $this->update_gallery($this->dbprepere->json_in($values["event_gallery"]),"event");
				if(!empty($values["event_genre"]) && is_string($values["event_genre"])) $this->update_genre($this->dbprepere->json_in($values["event_genre"]),"event");
				
				$this->retrieve();
				wordtour_add_event_type($this->data["event_type"]);
				
				$result = array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_event_row_html"));
				/* Eventbrite */
				if(!empty($this->data["eventbrite_event_id"])) {
					$eb = new WT_Eventbrite();
					$eb->update_event($this->id,$this->db_out(null,0));
					if($eb->response) $result["eventbrite"] = $eb->response;
				}	
				
				$this->db_result("success",$update_wpdb,$result);
				return true ;
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error updating Event, please try again(<i>".$wpdb->last_error."</i>)"));
				return false;
			}
		}
		return false;	
	}
	
	public function delete($nonce=0) {
		global $wpdb;
		$event_id = $this->id;
		if(parent::validate($nonce,self::NONCE_DELETE)) {
			$this->retrieve();
			if($this->data) {
				$event_id = $this->id;
				$meta_id = $this->data["event_meta_id"];
				$wpdb->query("DELETE FROM ".WORDTOUR_EVENTS." WHERE event_id = $event_id AND event_meta_id = $meta_id");
				if($wpdb->result) {
					$wpdb->query("DELETE FROM ".WORDTOUR_EVENTS_META." WHERE meta_id = $meta_id");
					if($wpdb->result) {
						$wpdb->query("DELETE FROM ".WORDTOUR_COMMENTS." WHERE comment_event_id = $event_id");
						$wpdb->query("DELETE FROM ".WORDTOUR_ATTENDING." WHERE rsvp_event_id = $event_id");
						$wpdb->query("DELETE FROM ".WORDTOUR_SOCIAL." WHERE social_parent_id = $event_id AND social_parent_type = 'event'");
						$attachments = new WT_Attachment();
						parent::delete_attachments($event_id,"event");	
						$this->db_result("success",$wpdb,array("msg"=>"Event permanently deleted."));
						return true;		
					}
				}
				$this->db_result("error",$wpdb,array("msg"=>"Can't delete event, Error as occured, please try again(<i>".$wpdb->last_error."</i>)")); 
			}
			$this->db_result("error",$wpdb,array("msg"=>"Can't delete event, Event doesn't exist")); 
		}
		return false;
	}
	
	public function delete_all($id = array(),$nonce=0) {
		if($this->validate($nonce,self::NONCE_DELETE)) {
			if(is_array($id)) {
				$result = array();
				foreach($id as $event_id) {
					$this->id = $event_id;
					$this->retrieve();
					$this->delete($nonce);
					$result[$event_id] = $this->db_result; 
				}
				$this->db_result = $result;
				return 1;	
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error deleting events")); 	
			}
		}
		return 0;
	}
	
	public function restore($event_id = 0,$nonce=0) {
		global $wpdb;
		if($event_id) $event_id = $this->id;
		if(parent::validate($nonce,self::NONCE_RESTORE)) { 
			$wpdb->update(WORDTOUR_EVENTS,array("event_published"=>1),array("event_id"=>$event_id),array("%d"),array("%d"));
			if($wpdb->result) {
				if($wpdb->rows_affected > 0) {
					$this->db_result("success",$wpdb,array("msg"=>"Event restored from the trash.","result"=>$this->db_out(),"html"=>$this->admin_html("get_event_row_html")));
					return true;	
				}
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error as occured, please try again (<i>".$wpdb->last_error."</i>)"));
				return false;
			}
		}
		return false;	
	}
	
	public function unpublish($nonce=0) {
		global $wpdb ; 
		$event_id = $this->id;
		if(parent::validate($nonce,self::NONCE_UNPUBLISH)) {
			$wpdb->update(WORDTOUR_EVENTS,array("event_published"=>0),array("event_id"=>$event_id),array("%d"),array("%d"));
			if($wpdb->result) {
				$this->retrieve();
				$this->db_result("success",null,array("result"=>$this->db_out(),"html"=>$this->admin_html("get_event_row_html")));	
			} else {
				$this->db_result("error",null,array("msg"=>"Error as occured, please try again (<i>".$wpdb->last_error."</i>)"));
			}
		}
		return false;
	}
	
	public function publish($nonce=0) {
		global $wpdb ; 
		$event_id = $this->id;
		if(parent::validate($nonce,self::NONCE_PUBLISH)) {
			$wpdb->update(WORDTOUR_EVENTS,array("event_published"=>1),array("event_id"=>$event_id),array("%d"),array("%d"));
			if($wpdb->result) {
				$this->retrieve();
				$this->db_result("success",null,array("result"=>$this->db_out(),"html"=>$this->admin_html("get_event_row_html")));	
			} else {
				$this->db_result("error",null,array("msg"=>"Error as occured, please try again (<i>".$wpdb->last_error."</i>)"));
			}
		}
		return false;
	}
		
	public function template($data=null,$show_poster = 0,$show_artist_poster = 0) {
		global $_wt_options;
		if(!$data) $data = $this->data;
		$db = $this->dbprepere;
		// get artist info
		$artist = new WT_Artist($data["event_artist_id"]);
		$artist_data = $artist->template($data);
		// get venue info
		$venue = new WT_Venue($data["event_venue_id"]);
		$venue_data = $venue->template($data);
		// get tour info
		if($data["event_tour_id"] >0) {
			$tour = new WT_Tour($data["event_tour_id"]);
			$tour_data = $tour->template($data);
		}
		
		$event_poster_id = $data["event_thumbnail"] ? $data["event_thumbnail"]["id"] : $this->get_thumbnail("event");
		$event_poster = $db->media_out($event_poster_id);
		
		if($data["event_on_sale"] == "0000-00-00") {
			$onsale = "";	
		} else {
			$onsale_date =  strtotime($data["event_on_sale"]);
			if($onsale_date < time()) {
				$onsale = "";	
			} else {
				$onsale = $data["event_on_sale"];	
			}
		}
		
		$opening = array();
		$opening_array = array();
		if(is_array($data["event_more_artists"])) {
			foreach($data["event_more_artists"] as $a) {
				$opening[] = ucwords($a["name"]);
				$a["url"] = $db->link_out(wt_get_permalink("artist",$a["id"],array("%name%"=>$a["name"]))); 
				$opening_array[] = $a;	
			}
			$opening = implode(", ",$opening);
		}
		
		$genre  = $this->get_genre_tpl("event");	
		
		$google_map_str = wt_get_map_str($data);
		$tpl = array(
			"show_comments" => $db->int_out($data["comment_status"]),
			"show_rsvp"     => $db->int_out($data["rsvp_status"]),
			"show_gallery"  => $db->int_out($data["gallery_status"]),
			"show_flickr"   => $db->int_out($data["flickr_status"]),
			"date"          => $db->date_display($data["event_start_date"]),
			"date_raw"      => $data["event_start_date"],
			"genre"         => implode(", ",$genre),
			"genre_array"   => $genre,
			"artists"       => $db->str_out($opening,1),
			"artists_array" => $opening_array,
			"opening"       => $db->str_out($data["event_opening_act"],1),
			"onsale"        => $onsale,
			"time"          =>  $db->time_out($data["event_start_time"]),
			"end_date_raw"  => $data["event_end_date"],
			"end_date"      => $db->date_display($data["event_end_date"]),
			"end_time"      => $db->time_out($data["event_end_time"]),
			"status"        => $data["event_status"],
			"description"   => $db->html_out($data["event_notes"]),
			"short_description" => $db->html_teaser_out($data["event_notes"]),
			"title"         => $db->str_out($data["event_title"],1),
			"type"          => !empty($data["event_type"]) ? $db->str_out(ucwords($data["event_type"]),1) : "",
			"artist"       => $artist_data,
			"tour"         => $tour_data,
			"venue"        => $venue_data,
			"poster"       => $event_poster,
			"tickets"      => $db->link_out($data["tkts_url"]),
			"phone"        => $db->str_out($data["tkts_phone"]),
			"admission"    => $db->str_out($data["tkts_price"]),
			"comments"     => $db->int_out($data["comment_count"]),
			"rsvp"         => $db->int_out($data["rsvp_count"]),
			"url"          => $db->link_out(wt_get_permalink("event",$data["event_id"],array("%date%"=>$data["event_start_date"],"%name%"=>$data["venue_name"]))),
			"machinetag"   => $machinetag
		);
		
		return $tpl;
	}
	
	
	
	public function comments() {
		global $wpdb;
		if(!$this->id) return;
		$comment = new WT_Comment();
		return $comment->get_by_event($this->id);
		 
	}
	
	public function is_published() {
		global $_wt_options;
		if($this->data["event_published"] == "1") return 1;
		return current_user_can($_wt_options->options("user_role"));
	}
	
	public function is_rsvp() {
		return $this->data["rsvp_status"] ? true : false ;
	}
	
	public function is_comments() {
		global $_wt_options;
		$show = $this->data["comment_status"] ? true : false ;
		if($_wt_options->options("comment_show_after_event") == "1") {
			if(strtotime($this->data["event_start_date"]) >time()) {
				$show = false;			
			}
		}
		
		return $show ;
	}
	
	public function is_gallery() {
		return $this->data["gallery_status"] ? true : false ;
	}
	
	public function is_flickr() {
		return $this->data["flickr_status"] ? true : false ;
	}
	
	public function is_video() {
		return $this->data["video_status"] ? true : false ;
	}
	
	public function is_post() {
		return $this->data["post_status"] ? true : false ;
	}
	
	public static function all($sql = "",$order = "DESC",$limit) {
		global $wpdb ;
		$limit = ($limit) ? "LIMIT 0,$limit" : "";
		$events = $wpdb->get_results($wpdb->prepare("SELECT * 
			FROM ".WORDTOUR_EVENTS." AS e LEFT JOIN ".WORDTOUR_VENUES." AS v 
			ON e.event_venue_id = v.venue_id 
			LEFT JOIN ".WORDTOUR_EVENTS_META." AS m 
			ON e.event_meta_id = m.meta_id
			LEFT JOIN ".WORDTOUR_ARTISTS." AS a
			ON e.event_artist_id = a.artist_id
			LEFT JOIN ".WORDTOUR_TOUR." AS t
			ON e.event_tour_id = t.tour_id 
			WHERE e.event_published = 1 $sql          
			ORDER BY event_start_date $order"),"ARRAY_A"
		);
		return $events ;
	}
	
	public static function get_by($sql="",$type="",$data = array()) {
		global $wpdb ;
		
		extract($data);
		switch($type) {
			case "artists":
				$sql = "WHERE e.event_artist_id=$artist_id ORDER BY e.event_artist_id,e.event_tour_id,e.event_start_date";
			break;
			case "tour":
				$sql = "WHERE e.event_tour_id=$tour_id ORDER BY e.event_tour_id,e.event_artist_id,e.event_start_date";
			break;		
		}
		
		if(!empty($sql)) {
			$events = $wpdb->get_results($wpdb->prepare("SELECT 
				*  
				FROM ".WORDTOUR_EVENTS." AS e 
				LEFT JOIN ".WORDTOUR_VENUES." AS v 
				ON e.event_venue_id = v.venue_id 
				LEFT JOIN ".WORDTOUR_ARTISTS." AS a
				ON e.event_artist_id = a.artist_id 
				LEFT JOIN ".WORDTOUR_TOUR." AS t
				ON e.event_tour_id = t.tour_id 
				LEFT JOIN ".WORDTOUR_EVENTS_META." AS m ON e.event_meta_id = m.meta_id 
				$sql"),"ARRAY_A");
			
			return $events ;
		} 
	
		return array();
	}
	
	public static function sql_all_tables($select = "*",$join = array()){
		$tables = array("venue"=>1,"meta"=>1,"artists"=>1,"tour"=>1);
		$join = array_merge($tables,$join);
		extract($join);
		$join_venue = $venue ? "LEFT JOIN ".WORDTOUR_VENUES." AS v ON e.event_venue_id = v.venue_id" : "";
		$join_meta = $meta ? "LEFT JOIN ".WORDTOUR_EVENTS_META." AS m ON e.event_meta_id = m.meta_id" : "";
		$join_artists = $artists ? "LEFT JOIN ".WORDTOUR_ARTISTS." AS a ON e.event_artist_id = a.artist_id" : "";
		$join_tour = $tour ? "LEFT JOIN ".WORDTOUR_TOUR." AS t ON e.event_tour_id = t.tour_id" : "";
		return "SELECT $select FROM ".WORDTOUR_EVENTS." AS e $join_meta $join_venue $join_artists $join_tour WHERE e.event_published = 1";
	}
	
	public static function sql($select = '*'){
		return "SELECT $select FROM ".WORDTOUR_EVENTS." AS e WHERE e.event_published = 1 ";
	}
	
}

