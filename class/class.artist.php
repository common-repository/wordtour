<?php
class WT_Artist extends WT_Object {
	const NONCE_UPDATE = 'wt-artist-update';  
	const NONCE_INSERT = 'wt-artist-insert';
	const NONCE_DELETE = 'wt-artist-delete';
	const NONCE_DELETE_ALL = 'wt-artist-delete-all';
	
	public function retrieve() {
		global $wpdb,$_wt_options;
		$artist_id = $this->id;
		$artist = null;
		if($artist_id) {
			$artist = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_ARTISTS." WHERE artist_id=$artist_id"),"ARRAY_A");
			if($artist) {
				$artist["is_default"] = $_wt_options->options("default_artist") == $artist_id ? 1 : 0;
				$artist["artist_thumbnail"] = get_attachment_data($this->get_thumbnail("artist"));
				$artist["artist_videos"] =  $this->get_videos("artist");
				$artist["artist_gallery"] = $this->get_gallery("artist");
				$artist["artist_category"] = $this->get_category("artist");
				$artist["artist_genre"] = $this->get_genre("artist");
			}	 
		}
		return parent::retrieve($artist); 		
	}
	
	public function defaults(){
		parent::defaults(WORDTOUR_ARTISTS);
		$this->data = array_merge($this->data,array(
			"is_default" =>  0,
			"artist_thumbnail" => "",
			"artist_videos"  => array(),
			"artist_gallery" => array(),
			"artist_category"=> array(),
			"artist_genre"=> array()
		));
		
		return $this->data;
	}
	
	public function template($data=null) {
		global $wpdb;
		if(!$data) $data = $this->data;
		$db = $this->dbprepere;
		
		$artist_poster_id = $data["artist_thumbnail"] ? $data["artist_thumbnail"]["id"] : $this->get_thumbnail("artist");
		$artist_poster = $db->media_out($artist_poster_id);
		$genre  = $this->get_genre_tpl("artist");	
		$social_links = unserialize($data["artist_social_links"]); 
		return array(
			"id"         => $db->int_out($data["artist_id"]),
			"name"       => $db->str_out($data["artist_name"]),
			"bio"        => $db->html_out($data["artist_bio"]),
			"short_bio"  => $db->html_teaser_out($data["artist_bio"]),
			"genre"       => implode(", ",$genre),
			"genre_array" => $genre,
			"label"      => $db->str_out($data["artist_record_company"]),
			"url"        => $db->link_out(wt_get_permalink("artist",$data["artist_id"],array("%name%"=>$data["artist_name"]))),
			"poster"     => $artist_poster,
			"website"    => $db->link_out($data["artist_website_url"]),
			// deprecated - need to replace email with contact
			"email"      => $db->str_out($data["artist_email"],$is_htmlspecial),
			"contact"      => $db->str_out($data["artist_email"],$is_htmlspecial),
			"flickr"     => $db->link_out($social_links["artist_flickr"]),
			"youtube"    => $db->link_out($social_links["artist_youtube"]),
			"vimeo"      => $db->link_out($social_links["artist_vimeo"]),
			"facebook"   => $db->link_out($social_links["artist_facebook"]),
			"twitter"    => $db->link_out($social_links["artist_twitter"]),
			"lastfm"     => $db->link_out($social_links["artist_lastfm"]),
			"myspace"    => $db->link_out($social_links["artist_myspace"]),
			"bandcamp"    => $db->link_out($social_links["artist_bandcamp"]),
			"tumblr"    => $db->link_out($social_links["artist_tumblr"]),
			"reverbnation"    => $db->link_out($social_links["artist_reverbnation"]),		
						
		);
	}
	
	protected function validate(&$data,$nonce_name) {
		# check in parent class if nonce is legal
		$is_valid = parent::validate($data,$nonce_name);
		if($is_valid && is_array($data)) {
			$is_valid = true ;
			if(empty($data["artist_name"])) {
				$is_valid = false ;
				$this->add_db_result("artist_name","required","Name is missing");	
			}
			
			if(!empty($data["artist_website_url"]) && !is_valid_url($data["artist_website_url"])) {
				$is_valid = false ;
				$this->add_db_result("artist_website_url","required","Artist website url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_flickr"]) && !is_valid_url($data["artist_flickr"])) {
				$is_valid = false ;
				$this->add_db_result("artist_flickr","required","Flickr url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_youtube"]) && !is_valid_url($data["artist_youtube"])) {
				$is_valid = false ;
				$this->add_db_result("artist_youtube","required","YouTube url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_vimeo"]) && !is_valid_url($data["artist_vimeo"])) {
				$is_valid = false ;
				$this->add_db_result("artist_vimeo","required","Vimeo url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_facebook"]) && !is_valid_url($data["artist_facebook"])) {
				$is_valid = false ;
				$this->add_db_result("artist_facebook","required","Facebook url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_twitter"]) && !is_valid_url($data["artist_twitter"])) {
				$is_valid = false ;
				$this->add_db_result("artist_twitter","required","Twitter url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_myspace"]) && !is_valid_url($data["artist_myspace"])) {
				$is_valid = false ;
				$this->add_db_result("artist_myspace","required","MySpace url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_lastfm"]) && !is_valid_url($data["artist_lastfm"])) {
				$is_valid = false ;
				$this->add_db_result("artist_lastfm","required","Last.FM url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_reverbnation"]) && !is_valid_url($data["artist_reverbnation"])) {
				$is_valid = false ;
				$this->add_db_result("artist_reverbnation","required","ReverbNation url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_tumblr"]) && !is_valid_url($data["artist_tumblr"])) {
				$is_valid = false ;
				$this->add_db_result("artist_tumblr","required","Tumblr url in not valid, the required format is http://your_website_url");	
			}
			
			if(!empty($data["artist_bandcamp"]) && !is_valid_url($data["artist_bandcamp"])) {
				$is_valid = false ;
				$this->add_db_result("artist_bandcamp","required","BandCamp url in not valid, the required format is http://your_website_url");	
			}
			
//			if(!empty($data["artist_email"]) && !is_valid_email($data["artist_email"])) {
//				$is_valid = false ;
//				$this->add_db_result("artist_email","required","Email address is not valid, the required format is http://your_website_url");	
//			}
			if(!$is_valid) $this->db_result("error",null,array("data"=>$this->db_response_msg));	

		}
		return $is_valid;
	}
	
	public function db_in($post = null) {
		global $current_user;
		if(!$post) $post = $this->data;
		if($post) {
			$db = $this->dbprepere;
			$data = array(
				"artist_name"=>$db->str_in($post["artist_name"]),
				"artist_order"=> $db->int_in($post["artist_order"]),
				"artist_record_company" =>$db->str_in($post["artist_record_company"]),
				"artist_bio" => $db->html_in($post["artist_bio"]),
				"artist_website_url" => $db->link_in($post["artist_website_url"]),
				"artist_email" => $db->str_in($post["artist_email"]),
				"artist_publish_date" => current_time("mysql",0),
				"artist_tour_status" => $db->int_in($post["artist_tour_status"]),
				"artist_gallery_status" => $db->int_in($post["artist_gallery_status"]),
				"artist_video_status" => $db->int_in($post["artist_video_status"]),
				"artist_flickr_status"=> $db->int_in($post["artist_flickr_status"]),
				"artist_post_status" => $db->int_in($post["artist_post_status"]),
			    "artist_social_links" => serialize(array(
					"artist_flickr" => $db->link_in($post["artist_flickr"]),
			 		"artist_youtube" => $db->link_in($post["artist_youtube"]),
					"artist_vimeo" => $db->link_in($post["artist_vimeo"]),
					"artist_facebook" => $db->link_in($post["artist_facebook"]),
					"artist_twitter"  => $db->link_in($post["artist_twitter"]),
					"artist_lastfm"  => $db->link_in($post["artist_lastfm"]),
					"artist_myspace"  => $db->link_in($post["artist_myspace"]),
					"artist_bandcamp"  => $db->link_in($post["artist_bandcamp"]),
					"artist_tumblr"  => $db->link_in($post["artist_tumblr"]),
					"artist_reverbnation"  => $db->link_in($post["artist_reverbnation"])					
				)),
				"artist_author"       => $current_user->ID
			);
		}
		return parent::db_in($data);
	}
	
	public function db_out($data = null,$is_htmlspecial=1) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $this->dbprepere;
			$social_links = unserialize($data["artist_social_links"]); 
			$data = array_merge($data,array(
				"artist_name" => $db->str_out($data["artist_name"],$is_htmlspecial),
				"artist_record_company" => $db->str_out($data["artist_record_company"],$is_htmlspecial),
				"artist_order" => $db->int_out($data["artist_order"]),
				"artist_bio" => $db->html_out($data["artist_bio"]),
				"artist_thumbnail_id"       => $data["artist_thumbnail"]["id"],
				"artist_website_url" => $db->link_out($data["artist_website_url"]),
				"artist_email" => $db->str_out($data["artist_email"],$is_htmlspecial),
				"artist_publish_date" => $db->datetime_short_out($data["artist_publish_date"]),
				"artist_publish_raw" => $data["artist_publish_date"],
				"artist_flickr"  => $db->link_out($social_links["artist_flickr"]),
				"artist_youtube" => $db->link_out($social_links["artist_youtube"]),
				"artist_vimeo" => $db->link_out($social_links["artist_vimeo"]),
				"artist_facebook" => $db->link_out($social_links["artist_facebook"]),
				"artist_twitter"  => $db->link_out($social_links["artist_twitter"]),
				"artist_lastfm"  => $db->link_out($social_links["artist_lastfm"]),
				"artist_myspace"=> $db->link_out($social_links["artist_myspace"]),	
				"artist_bandcamp"=> $db->link_out($social_links["artist_bandcamp"]),
				"artist_tumblr"=> $db->link_out($social_links["artist_tumblr"]),
				"artist_reverbnation"=> $db->link_out($social_links["artist_reverbnation"]),
				"permalink"      => $data["artist_id"]>0 ? $db->link_out(wt_get_permalink("artist",$data["artist_id"],array("%name%"=>$data["artist_name"]))) : "",	
				"_nonce"=> empty($data["artist_id"]) ? wp_create_nonce(WT_Artist::NONCE_INSERT) : wp_create_nonce(WT_Artist::NONCE_UPDATE)
			));
			unset($data["artist_social_links"]);
			return $data;
		}
		return array();	
	}
		
	public function set_default($default=0){
		global $_wt_options;
		if($this->id) {
			if($default) {
				$_wt_options->update(array("default_artist"=>$this->id));	
			} else {
				$_wt_options->update(array("default_artist"=>0));
			}
			$this->retrieve($this->id);
			$this->db_result("success",$wpdb,array("result"=>$this->data,"html"=>$this->admin_html("get_artist_row_html")));
		}
	}
	
	public function insert($values) {
		global $wpdb;
		if($this->validate($values,self::NONCE_INSERT)) {
			$wpdb->insert(WORDTOUR_ARTISTS,$this->db_in($values));
			if($wpdb->result && $wpdb->insert_id) {
				$this->id = $wpdb->insert_id;	
				$insert_wpdb = clone $wpdb;
				if(!empty($values["artist_thumbnail_id"]) && is_string($values["artist_thumbnail_id"])) $this->update_thumbnail($values["artist_thumbnail_id"],"artist");
				if(!empty($values["artist_category"])) $this->update_category($this->dbprepere->json_in($values["artist_category"]),"artist");
				if(!empty($values["artist_videos"])) $this->update_videos($this->dbprepere->json_in($values["artist_videos"]),"artist");
				if(!empty($values["artist_gallery"])) $this->update_gallery($this->dbprepere->json_in($values["artist_gallery"]),"artist");
				if(!empty($values["artist_genre"]) && is_string($values["artist_genre"])) $this->update_genre($this->dbprepere->json_in($values["artist_genre"]),"artist");
							
				$this->retrieve();
				$this->db_result("success",$insert_wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_artist_row_html")));
				return true; 		
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error adding new Artist, please try again(<i>".$wpdb->last_error."</i>)"));
				return false;	
			}		
		} 
		return false; 
	}
	
	public function update($values) {
		global $wpdb;
		$artist_id = $this->id;
		if($this->validate($values,self::NONCE_UPDATE)) {
			if($artist_id) {
				$wpdb->update(WORDTOUR_ARTISTS,$this->db_in($values),array("artist_id"=>$artist_id));
				if($wpdb->result) {
					$update_wpdb = clone $wpdb ;
					if(!empty($values["artist_thumbnail_id"]) && is_string($values["artist_thumbnail_id"])) $this->update_thumbnail($values["artist_thumbnail_id"],"artist");
					if(!empty($values["artist_category"]) && is_string($values["artist_category"])) $this->update_category($this->dbprepere->json_in($values["artist_category"]),"artist");
					if(!empty($values["artist_videos"]) && is_string($values["artist_videos"])) $this->update_videos($this->dbprepere->json_in($values["artist_videos"]),"artist");
					if(!empty($values["artist_gallery"]) && is_string($values["artist_gallery"])) $this->update_gallery($this->dbprepere->json_in($values["artist_gallery"]),"artist");
					if(!empty($values["artist_genre"]) && is_string($values["artist_genre"])) $this->update_genre($this->dbprepere->json_in($values["artist_genre"]),"artist");
					$this->retrieve();
					$this->db_result("success",$update_wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_artist_row_html")));
					return true;
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error updating artist, please try again<br>".$wpdb->last_error));
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
				"artist_name"     => $post["artist_name"],
				"artist_order"    => $post["artist_order"],
				"artist_gallery_status"  => $post["artist_gallery_status"],
				"artist_video_status"    => $post["artist_video_status"],
				"artist_flickr_status"   => $post["artist_flickr_status"],
				"artist_post_status"     => $post["artist_post_status"],
				"artist_tour_status"     => $post["artist_tour_status"],
				"_nonce" 		  => $post["_nonce"]
			));
			
			unset($values["artist_id"]);
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
			$artist_id = $id ? $id : $this->id;
			// Check if artist assigned to event
			$wpdb->get_row("SELECT event_id FROM ".WORDTOUR_EVENTS." WHERE event_artist_id=$artist_id LIMIT 1","ARRAY_A");
			
			if(!$wpdb->num_rows) {
				$wpdb->query($wpdb->prepare("DELETE FROM ".WORDTOUR_ARTISTS." WHERE artist_id=$artist_id"));
				if($wpdb->result) {
					$attachments = new WT_Attachment();
					$attachments->delete("attachment_target_id=$artist_id&attachment_target=artist");
					$this->db_result("success",$wpdb);	
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error delete artist, please try again<br>".$wpdb->last_error));	
				}
				return $wpdb->result;
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Can't Delete artist because it's attached to event."));	
			}
		}
		return false;
	}
	
	public function delete_all($artist_id=array(),$nonce="") {
		global $wpdb;
		if($this->validate($nonce,self::NONCE_DELETE)) {
			$result = array();
			foreach($artist_id as $id) {
				$this->delete(null,$id,0);
				$result[$id] = $this->db_result; 
			}
			$this->db_result = $result;	
		}
		return false;
	}
	
	public function events($options=array()){
		$r = new WT_Renderer();
		return $r->events(array_merge(array(
			"date_range"    => "all",
			'artist'        => $this->id,
			'group_by_tour' => 0, 			
		),$options),null,"wordtour_events");	
	}
	
	
	public function flickr() {
		return parent::flickr("artist"); 	
	}
	
	public function is_artist() {
		return $this->data;
	}
	
	public function is_gallery() {
		return $this->data["artist_gallery_status"] ? true : false ;
	}
	
	public function is_video() {
		return $this->data["artist_video_status"] ? true : false ;
	}
	
	public function is_post() {
		return $this->data["artist_post_status"] ? true : false ;
	}
	
	public function is_flickr() {
		return $this->data["artist_flickr_status"] ? true : false ;
	}
	
	public function is_tour() {
		return $this->data["artist_tour_status"] ? true : false ;
	}
	
	
	
	//public function query(){
//		if(!empty($start_with)) {
//			$sql = WT_Artist::sql("*","WHERE UPPER(a.artist_name) LIKE UPPER('$attr[start_with]%') ORDER BY $order");
//			$artists = $wpdb->get_results("$sql ","ARRAY_A");
//		} else if($artist_to_show){
//			$cond_artists = $this->columns_sql("artist_id",$artist_to_show);
//			$sql = WT_Artist::sql("*","WHERE $cond_artists ORDER BY $order");
//			$artists = $wpdb->get_results("$sql ","ARRAY_A");	
//		} else {
//			$artists = WT_Artist::all();	
//		}
		
	//}
	
	public static function all($artists_id=array(),$columns = "*") {
		global $wpdb ;
		$sql = "";
		if(count($artists_id)) {
			$artists_sql = array();
			foreach($artists_id as $id) {
				$artists_sql[] = "artist_id=$id";		
			}
			$sql = "WHERE ".implode(" OR ",$artists_sql);
		}
		$result = $wpdb->get_results($wpdb->prepare("SELECT $columns FROM ".WORDTOUR_ARTISTS." $sql ORDER BY artist_name"),"ARRAY_A");
		if(!$result) $result = array(); 
		return $result; 
	}
	
	public static function sql($columns = "*",$sql = "") {
		return "SELECT $columns FROM ".WORDTOUR_ARTISTS." $sql";
	}
	
}

