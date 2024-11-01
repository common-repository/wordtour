<?php
class WT_Track extends WT_Object {
	const NONCE_UPDATE = 'wt-track-update';  
	const NONCE_INSERT = 'wt-track-insert';
	const NONCE_DELETE = 'wt-track-delete';
	const NONCE_DELETE_ALL = 'wt-track-delete-all';
	
	public function retrieve() {
		global $wpdb,$_wt_options;
		$track_id = $this->id;
		$track = null;
		if($track_id) {
			$track = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_TRACKS." AS t LEFT JOIN ".WORDTOUR_ARTISTS." AS a ON t.track_artist_id = a.artist_id WHERE t.track_id=$track_id"),"ARRAY_A");
			if($track) {
				$track["track_thumbnail"] = get_attachment_data($this->get_thumbnail("track"));
				$track["track_genre"] = $this->get_genre("track");
			}	 
		}
		return parent::retrieve($track); 		
	}
	
	public function defaults(){
		global $_wt_options;
		parent::defaults(WORDTOUR_TRACKS);
		
		$default_artist =  $_wt_options->options("default_artist"); 
		
		if(!empty($default_artist) && empty($this->data["track_artist_id"])) {
			$id = $_wt_options->options("default_artist") ;
			$artist = new WT_Artist($id);
			$data = $artist->retrieve();
			if($data) {
				$this->data["track_artist_id"] = $id;
				$this->data["artist_name"] = $data["artist_name"];	
			};	
		}
		
		$this->data = array_merge($this->data,array(
			"track_id" => "",
			"track_title"  => "",
			"track_lyrics"  => "",
			"track_genre" => array()
		));
		
		return $this->data;
	}
	
	public function template($data=null) {
		global $wpdb;
		if(!$data) $data = $this->data;
		$db = $this->dbprepere;
		
		$track_poster_id = $data["track_thumbnail"] ? $data["track_thumbnail"]["id"] : $this->get_thumbnail("track");
		$track_poster = $db->media_out($track_poster_id);
		
		
		$genre  = $this->get_genre_tpl("track");
		
		return array(
			"id"          => $db->int_out($data["track_id"]),
			"title"       => $db->str_out($data["track_title"]),
			"artist"      => $db->str_out($data["artist_name"]),
			"artist_id"   => $db->str_out($data["track_artist_id"]),
			"credits"     => $db->str_out($data["track_credits"]),
			"release"      => $db->date_out($data["track_release_date"]),
			"release_raw"      => $data["track_release_date"],
			"about"       => $db->html_out($data["track_about"]),
			"short_about" => $db->html_teaser_out($data["track_about"]),
			"playcount"   => $db->int_out($data["track_play_count"]),
			"lyrics"       => $db->html_out($data["track_lyrics"]),
			"author"       => $db->str_out($data["track_author"]),
			"genre"       => implode(", ",$genre),
			"genre_array" => $genre,
			"label"      => $db->str_out($data["track_label"]),
			"poster"     => $track_poster
		);
	}
	
	protected function validate(&$data,$nonce_name) {
		global $wpdb;
		# check in parent class if nonce is legal
		$is_valid = parent::validate($data,$nonce_name);
		if($is_valid && is_array($data)) {
			$is_valid = true ;
			if(empty($data["track_title"])) {
				$is_valid = false ;
				$this->add_db_result("track_title","required","Title is missing");	
			}
			
			if(!isset($data["track_artist_id"])) {
				if(empty($data["artist_name"])) {
					$is_valid = false ;
					$this->add_db_result("artist_name","required","Artist is missing");
				} else if(!is_numeric($data["artist_name"])) {
					$is_artist = $wpdb->get_row("SELECT artist_id FROM ".WORDTOUR_ARTISTS." WHERE UPPER(artist_name)='".trim(strtoupper($data["artist_name"])."'"),"ARRAY_A");
					if($is_artist) {
						$data["track_artist_id"] = $is_artist["artist_id"];
					} else {
						$is_valid = false ;
						$this->add_db_result("artist_name","required","Artist '$data[artist_name]' doesn't exist");
					}
				}
			} else {
				if($data["track_artist_id"]==0 || empty($data["track_artist_id"])) {
					$is_valid = false ;
					$this->add_db_result("artist_name","required","Artist is missing");	
				}	
				
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
			
			$data = array(
				"track_title"        =>$db->str_in($post["track_title"]),
				"track_play_count"   => $db->int_in($post["track_play_count"]),
				"track_artist_id"    => $db->int_in($post["track_artist_id"]),
				"track_label"        =>$db->str_in($post["track_label"]),
				"track_credits"      =>$db->str_in($post["track_credits"]),
				"track_about"        => $db->html_in($post["track_about"]),
				"track_lyrics"       => $db->html_in($post["track_lyrics"]),
				"track_lyrics_author"=> $db->str_in($post["track_lyrics_author"]),
				'track_release_date' => $db->date_in($post["track_release_date"]),
				"track_publish_date" => current_time("mysql",0),
				"track_author"       => $current_user->ID
			);
		}
		return parent::db_in($data);
	}
	
	public function db_out($data = null,$is_htmlspecial=1) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $this->dbprepere; 
			$data = array_merge($data,array(
				"track_title"         => $db->str_out($data["track_title"],$is_htmlspecial),
				"track_credits"       => $db->str_out($data["track_credits"],$is_htmlspecial),
				"track_about"         => $db->str_out($data["track_about"],$is_htmlspecial),
				"track_label"         => $db->str_out($data["track_label"],$is_htmlspecial),
				"track_publish_date"  => $db->datetime_short_out($data["track_publish_date"]),
				"track_release_date"  => $db->admin_date_out($data["track_release_date"]),
				"track_thumbnail_id"  => $data["track_thumbnail"]["id"],
				"track_artist_id"     => $db->int_out($data["track_artist_id"]),
				"track_play_count"    => $db->int_out($data["track_play_count"]),
				"track_lyrics"        => $db->str_out($data["track_lyrics"],$is_htmlspecial),
				"track_lyrics_author" => $db->str_out($data["track_lyrics_author"],$is_htmlspecial),
				"_nonce"              => empty($data["track_id"]) ? wp_create_nonce(WT_Track::NONCE_INSERT) : wp_create_nonce(WT_Track::NONCE_UPDATE)
			));
			return $data;
		}
		return array();	
	}
	
	public function insert($values) {
		global $wpdb;
		if($this->validate($values,self::NONCE_INSERT)) {
			$wpdb->insert(WORDTOUR_TRACKS,$this->db_in($values));
			if($wpdb->result && $wpdb->insert_id) {
				$this->id = $wpdb->insert_id;	
				$insert_wpdb = clone $wpdb ;
				if(!empty($values["track_thumbnail_id"]) && is_string($values["track_thumbnail_id"])) $this->update_thumbnail($values["track_thumbnail_id"],"track");
				if(!empty($values["track_genre"]) && is_string($values["track_genre"])) $this->update_genre($this->dbprepere->json_in($values["track_genre"]),"track");
				$this->retrieve();
				$this->db_result("success",$insert_wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_track_row_html")));
				return true; 		
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error adding new Track, please try again(<i>".$wpdb->last_error."</i>)"));
				return false;	
			}		
		} 
		return false; 
	}
	
	public function update($values) {
		global $wpdb;
		$track_id = $this->id;
		if($this->validate($values,self::NONCE_UPDATE)) {
			if($track_id) {
				$wpdb->update(WORDTOUR_TRACKS,$this->db_in($values),array("track_id"=>$track_id));
				if($wpdb->result) {
					$update_wpdb = clone $wpdb ;
					if(!empty($values["track_thumbnail_id"]) && is_string($values["track_thumbnail_id"])) $this->update_thumbnail($values["track_thumbnail_id"],"track");
					if(!empty($values["track_genre"]) && is_string($values["track_genre"])) $this->update_genre($this->dbprepere->json_in($values["track_genre"]),"track");
					$this->retrieve();
					$this->db_result("success",$update_wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_track_row_html")));
					return true;
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error updating track, please try again<br>".$wpdb->last_error));
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
				"track_title"     => $post["track_title"],
				"track_label"     => $post["track_label"],
				"track_release_date"     => $post["track_release_date"],
				"track_about"     => $post["track_about"],
				"track_credits"     => $post["track_credits"],
				"artist_name"    => $post["artist_name"],
				"_nonce" 		  => $post["_nonce"]
			));
			
			unset($values["track_id"]);
			return $this->update($values);
		}
		$this->db_result("error",array(),array("msg"=>"Error updating Track, please try again"));
	}
	
	
	
	public function delete($nonce="",$id = 0,$validate=1) {
		global $wpdb;
		# When using delete_all no need to check every time nonce. nd add id
		$is_valid = 1 ;
		if($validate) $is_valid = $this->validate($nonce,self::NONCE_DELETE);	
		if($is_valid) {
			$track_id = $id ? $id : $this->id;
			// Check if artist assigned to event
			$wpdb->query($wpdb->prepare("DELETE FROM ".WORDTOUR_TRACKS." WHERE track_id=$track_id"));
			$delete_wpdb = clone $wpdb;
			if($wpdb->result) {
				parent::delete_attachments($track_id,"track");
				$this->db_result("success",$delete_wpdb);	
			} else {
				$this->db_result("error",$delete_wpdb,array("msg"=>"Error delete track, please try again<br>".$wpdb->last_error));	
			}
			return $wpdb->result;
		}
		return false;
	}
	
	public function delete_all($track_id=array(),$nonce="") {
		global $wpdb;
		if($this->validate($nonce,self::NONCE_DELETE)) {
			$result = array();
			foreach($track_id as $id) {
				$this->delete(null,$id,0);
				$result[$id] = $this->db_result; 
			}
			$this->db_result = $result;	
		}
		return false;
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

