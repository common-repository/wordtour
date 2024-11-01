<?php
class WT_Venue extends WT_Object {
	const NONCE_UPDATE = 'wt-venue-update';  
	const NONCE_INSERT = 'wt-venue-insert';
	const NONCE_DELETE = 'wt-venue-delete';
	const NONCE_DELETE_ALL = 'wt-venue-delete-all';
	
	public function retrieve() {
		global $wpdb,$_wt_options;
		$id = $this->id;
		$data = null;
		if($id) {
			$data = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_VENUES." WHERE venue_id=$id"),"ARRAY_A");
			if($data) {
				$data["is_default"] = $_wt_options->options("default_venue") == $id ? 1 : 0 ;
				$data["venue_thumbnail"] = get_attachment_data($this->get_thumbnail("venue"));
				$data["venue_videos"] =  $this->get_videos("venue");
				$data["venue_gallery"] = $this->get_gallery("venue");
				$data["venue_category"] = $this->get_category("venue");	
			} 
		} 
		
		return parent::retrieve($data); 		
	}
	
	public function defaults(){
		parent::defaults(WORDTOUR_VENUES);
		$this->data = array_merge($this->data,array(
			"is_default" =>  0,
			"venue_thumbnail" => "",
			"venue_videos"  => array(),
			"venue_gallery" => array(),
			"venue_category"=> array()
		));
		
		return $this->data;
	}
	
	protected function validate(&$data,$nonce_name) {
		# check in parent class if nonce is legal
		$is_valid = parent::validate($data,$nonce_name);
		if($is_valid && is_array($data)) {
			$is_valid = true ;
			if(empty($data["venue_name"])) {
				$is_valid = false ;
				$this->add_db_result("venue_name","required","Name is missing");	
			}
			
			if(empty($data["venue_country"])) {
				$is_valid = false ;
				$this->add_db_result("venue_country","required","Country is missing");
			} else {
				$country_code = get_country_by_name($data["venue_country"]);
				if(!$country_code) {
					$is_valid = false ;
					$this->add_db_result("venue_country","field","Country doesn't exist");	
				}
			}

			if(!empty($data["venue_state"]) && $country_code=="US") {
				$state_code = get_state_by_name($data["venue_state"]);
				if(!$state_code) $state_code = get_state_by_code(strtoupper($data["venue_state"]));
				if(!$state_code) {
					$is_valid = false ;
					$this->add_db_result("venue_state","field","The U.S State code is invalid.");	
				} 
			} else {
				if($country_code=="US") {
					$is_valid = false ;
					$this->add_db_result("venue_state","field","State is missing");	
				}
			}
			
			if(!empty($data["venue_url"]) && !is_valid_url($data["venue_url"])) {
				$is_valid = false ;
				$this->add_db_result("venue_url","required","Venue website in not valid, the required format is http://your_website_url");	
			}
			
			if(!$is_valid) $this->db_result("error",null,array("data"=>$this->db_response_msg));		
		}
		return $is_valid;
	}
	
	public function db_in($post = null) {
		global $current_user;
		if(!$post) $post = $this->data;
		if($post) {
			$db = $this->dbprepere;
			
			$state_code = $post["venue_state"]; 
			$country_code = get_country_by_name($post["venue_country"]);
			if(!empty($post["venue_state"]) && $country_code == "US") {
				$state_code = get_state_by_name($post["venue_state"]);
				if(!$state_code) $state_code = strtoupper($post["venue_state"]);
			} 
			
			$data = array(
				"venue_name"=>$db->str_in($post["venue_name"]),
				"venue_order"=> $db->int_in($post["venue_order"]),
				"venue_phone" =>$db->str_in($post["venue_phone"]),
				"venue_zip" => $db->str_in($post["venue_zip"]),
				"venue_address" => $db->str_in($post["venue_address"]),
				"venue_city" => $db->str_in($post["venue_city"]),
				"venue_state" => $state_code,
				"venue_country" => $country_code,
				"venue_info" => $db->html_in($post["venue_info"]),
				"venue_url" => $db->link_in($post["venue_url"]),
				"venue_publish_date" => current_time("mysql",0),
				"venue_tour_status" => $db->int_in($post["venue_tour_status"]),
				"venue_gallery_status" => $db->int_in($post["venue_gallery_status"]),
				"venue_video_status" => $db->int_in($post["venue_video_status"]),
				"venue_flickr_status"=> $db->int_in($post["venue_flickr_status"]),
				"venue_post_status" => $db->int_in($post["venue_post_status"]),
				"venue_author"       => $current_user->ID
			);
		}
		return parent::db_in($data);
	}
	
	
	public function db_out($data = null,$is_htmlspecial=1) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $this->dbprepere;
			
			$state =  get_state_by_code($data["venue_state"]);
			$data = array_merge($data,array(
				"venue_name" => $db->str_out($data["venue_name"],$is_htmlspecial),
				"venue_publish_date" => $db->datetime_short_out($data["venue_publish_date"]),
				"venue_thumbnail_id"       => $data["venue_thumbnail"]["id"],
				"venue_publish_date_raw" => $data["venue_publish_date"],
				"venue_address" => $db->str_out($data["venue_address"],$is_htmlspecial),
				"venue_city" => $db->str_out($data["venue_city"],$is_htmlspecial),
				"venue_zip" => $db->str_out($data["venue_zip"],$is_htmlspecial),
				"venue_state" => $state ? $state : $data["venue_state"],
				"venue_country" => get_country_by_code($data["venue_country"]),
				"venue_country_code" => $data["venue_country"],
				"venue_state_code" => $data["venue_state"],
				"venue_info" => $db->html_out($data["venue_info"]),
				"venue_url" => $db->link_out($data["venue_url"]),
				"permalink" => $data["venue_id"]>0 ? $db->link_out(wt_get_permalink("venue",$data["venue_id"],array("%name%"=>$data["venue_name"]))):"",
				"venue_phone" => $db->str_out($data["venue_phone"],$is_htmlspecial),
				"_nonce"=> empty($data["venue_id"]) ? wp_create_nonce(WT_Venue::NONCE_INSERT) : wp_create_nonce(WT_Venue::NONCE_UPDATE)
			));
			return $data;
		}
		return array();	
	}
	
	public function template($data=null) {
		global $wpdb;
		if(!$data) $data = $this->data;
		$db = $this->dbprepere;
		$venue_poster_id = $data["venue_thumbnail"] ? $data["venue_thumbnail"]["id"] : $this->get_thumbnail("venue");
		$venue_poster = $db->media_out($venue_poster_id);	
		$map = wt_get_map_str($data);
		return array(
			"id"           => $db->int_out($data["venue_id"]),
			"name"         => $db->str_out($data["venue_name"]),
			"description"  => $db->html_out($data["venue_info"]),
			"short_description" => $db->html_teaser_out($data["venue_info"]),
			"address"      => $db->str_out($data["venue_address"]),
			"city"         => $db->str_out($data["venue_city"]),
			"zip"          => $db->str_out($data["venue_zip"]),
			"state"        => get_state_by_code($data["venue_state"]),
			"country"      => get_country_by_code($data["venue_country"]),
			"country_code" => $data["venue_country"],
			"state_code"   => $data["venue_state"],
			"url"          => $db->link_out(wt_get_permalink("venue",$data["venue_id"],array("%name%"=>$data["venue_name"]))),
			"website"      => $db->link_out($data["venue_url"]),
			"phone"        => $db->str_out($data["venue_phone"]),
			"google_map"   => $map["address"],
			"poster"       => $venue_poster	
		);
	}
	
	public function admin_html($data=null) {
		return parent::admin_html("get_venue_row_html",$data);	
	}
	
	public function set_default($default=0){
		global $_wt_options;
		if($this->id) {
			if($default) {
				$_wt_options->update(array("default_venue"=>$this->id));	
			} else {
				$_wt_options->update(array("default_venue"=>0));
			}
			$this->retrieve($this->id);
			$this->db_result("success",$wpdb,array("result"=>$this->data,"html"=>$this->admin_html("get_venue_row_html")));
		}
	}
	
	public function events($options=array()){
		$r = new WT_Renderer();
		return $r->events(array_merge(array(
			"date_range"    => "upcoming",
			"venue"         => $this->id  			
		),$options),null,"wordtour_events");	
   }
	
	public function flickr() {
		return parent::flickr("venue"); 	
	}
	
	public function is_venue() {
		return $this->data;
	}
	
	public function is_gallery() {
		return $this->data["venue_gallery_status"] ? true : false ;
	}
	
	public function is_video() {
		return $this->data["venue_video_status"] ? true : false ;
	}
	
	public function is_post() {
		return $this->data["venue_post_status"] ? true : false ;
	}
	
	public function is_flickr() {
		return $this->data["venue_flickr_status"] ? true : false ;
	}
	
	public function is_tour() {
		return $this->data["venue_tour_status"] ? true : false ;
	}
	
	
	public function insert($values,$apply_add_on = 1) {
		global $wpdb;
		if($this->validate($values,self::NONCE_INSERT)) {
			$wpdb->insert(WORDTOUR_VENUES,$this->db_in($values));
			if($wpdb->result && $wpdb->insert_id) {
				$this->id = $wpdb->insert_id;	
				$insert_wpdb = clone $wpdb ;
				if(!empty($values["venue_thumbnail_id"]) && is_string($values["venue_thumbnail_id"])) $this->update_thumbnail($values["venue_thumbnail_id"],"venue");
				if(!empty($values["venue_category"]) && is_string($values["venue_category"])) $this->update_category($this->dbprepere->json_in($values["venue_category"]),"venue");
				if(!empty($values["venue_videos"]) && is_string($values["venue_videos"])) $this->update_videos($this->dbprepere->json_in($values["venue_videos"]),"venue");
				if(!empty($values["venue_gallery"]) && is_string($values["venue_gallery"])) $this->update_gallery($this->dbprepere->json_in($values["venue_gallery"]),"venue");			
				$this->retrieve();
				
				$result = array("result"=>$this->db_out(null,0),"html"=>$this->admin_html());
				if($apply_add_on) {
				/* Eventbrite */
				$eb = new WT_Eventbrite();
				$eb->save_venue($this->db_out(null,0));
				if($eb->response) $result["eventbrite"] = $eb->response;
				}
				/* */
				$this->db_result("success",$insert_wpdb,$result);
				return true; 		
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error adding new Venue, please try again(<i>".$wpdb->last_error."</i>)"));
				return false;	
			}			
		} 
		return false; 
	}
	
	public function update($values) {
		global $wpdb;
		$venue_id = $this->id;
		if($this->validate($values,self::NONCE_UPDATE)) {
			if($venue_id) {
				$wpdb->update(WORDTOUR_VENUES,$this->db_in($values),array("venue_id"=>$venue_id));
				if($wpdb->result) {
					$update_wpdb = clone $wpdb ;
					if(!empty($values["venue_thumbnail_id"]) && is_string($values["venue_thumbnail_id"])) $this->update_thumbnail($values["venue_thumbnail_id"],"venue");
					if(!empty($values["venue_category"]) && is_string($values["venue_category"])) $this->update_category($this->dbprepere->json_in($values["venue_category"]),"venue");
					if(!empty($values["venue_videos"]) && is_string($values["venue_videos"])) $this->update_videos($this->dbprepere->json_in($values["venue_videos"]),"venue");
					if(!empty($values["venue_gallery"]) && is_string($values["venue_gallery"])) $this->update_gallery($this->dbprepere->json_in($values["venue_gallery"]),"venue");
					$this->retrieve();
					
					$result = array("result"=>$this->db_out(null,0),"html"=>$this->admin_html());
					/* Eventbrite */
					$eb = new WT_Eventbrite();
					if($eb->is_update()) {
						$eb->save_venue($this->db_out(null,0));
						if($eb->response) $result["eventbrite"] = $eb->response;
					}
					/* */
					$this->db_result("success",$update_wpdb,$result);
					return true;
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error updating venue, please try again<br>".$wpdb->last_error));
					return false;
				}
			}
		}
		return false;
	}
	
	public function quick_update($post) {
		$this->retrieve();
		if($this->data) {
			$values = array_merge($this->db_out($this->data,0),array(
				"venue_name"     => $post["venue_name"],
				"venue_order"    => $post["venue_order"],
				"venue_address"    => $post["venue_address"],
				"venue_city"    => $post["venue_city"],
				"venue_country"    => $post["venue_country"],
				"venue_state"    => $post["venue_state"],
				"venue_gallery_status"  => $post["venue_gallery_status"],
				"venue_video_status"    => $post["venue_video_status"],
				"venue_flickr_status"   => $post["venue_flickr_status"],
				"venue_post_status"     => $post["venue_post_status"],
				"venue_tour_status"     => $post["venue_tour_status"],
				"_nonce" 		  => $post["_nonce"]
			));
			
			unset($values["venue_id"]);
			return $this->update($values);
		}
		$this->db_result("error",array(),array("msg"=>"Error updating Artist, please try again"));
	}
	
	
	public function delete($nonce="",$id = 0,$validate=1) {
		global $wpdb;
		# When using delete_all no need to check every time nonce. nd add id
		$is_valid = 1 ;
		if($validate) $is_valid = $this->validate($nonce,self::NONCE_DELETE);	
		if($is_valid) {
			$venue_id = $id ? $id : $this->id;
			$wpdb->get_row("SELECT event_id FROM ".WORDTOUR_EVENTS." WHERE event_venue_id=$venue_id","ARRAY_A");
			if(!$wpdb->num_rows) {
  				$wpdb->query($wpdb->prepare("DELETE FROM ".WORDTOUR_VENUES." WHERE venue_id = $venue_id"));
				if($wpdb->result) {
					$attachments = new WT_Attachment();
					$attachments->delete("attachment_target_id=$venue_id&attachment_target=venue");
					$wpdb->query("DELETE FROM ".WORDTOUR_SOCIAL." WHERE social_parent_id = $venue_id AND social_parent_type = 'venue'");
					$this->db_result("success",$wpdb);	
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error delete venue, please try again<br>".$wpdb->last_error));	
				}
				return $wpdb->result;
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error delete venue, already assigned to event<br>"));		
			}
		}
		return false;	
	}
	
	public function delete_all($venue_id=array(),$nonce="") {
		global $wpdb;
		if($this->validate($nonce,self::NONCE_DELETE)) {
			$result = array();
			foreach($venue_id as $id) {
				$this->delete(null,$id,0);
				$result[$id] = $this->db_result; 
			}
			$this->db_result = $result;	
		}
		return false;
	}
	
	public static function all() {
		global $wpdb ;
		//$venues = get_transient('event_venues');  
		//if(!$venues) {
			$result = $wpdb->get_results("SELECT * FROM " . WORDTOUR_VENUES . " AS v ORDER BY v.venue_name","ARRAY_A");
			if(!$result) $result = array(); 
			return $result;
			//set_transient("event_venues",$venues,5*60);
		//}	
		return $venues ;
	}
	
	public static function sql($columns = "*",$sql = "") {
		return "SELECT $columns FROM ".WORDTOUR_VENUES." $sql";
	}
	
}
