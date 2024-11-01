<?php
class WT_Album extends WT_Object {
	const NONCE_UPDATE = 'wt-album-update';  
	const NONCE_INSERT = 'wt-album-insert';
	const NONCE_DELETE = 'wt-album-delete';
	const NONCE_DELETE_ALL = 'wt-album-delete-all';
	
	public function retrieve() {
		global $wpdb,$_wt_options;
		$album_id = $this->id;
		$album = null;
		if($album_id) {
			$album = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_ALBUMS." AS t LEFT JOIN ".WORDTOUR_ARTISTS." AS a ON t.album_artist_id = a.artist_id WHERE t.album_id=$album_id"),"ARRAY_A");
			if($album) {
				$album["album_thumbnail"] = get_attachment_data($this->get_thumbnail("album"));
				$album["album_tracks"] = $this->get_tracks();
				$album["album_genre"] = $this->get_genre("album");
			}	 
		}
		return parent::retrieve($album); 		
	}
	
	public function defaults(){
		global $_wt_options;
		parent::defaults(WORDTOUR_ALBUMS);
		
		$default_artist =  $_wt_options->options("default_artist"); 
		
		if(!empty($default_artist) && empty($this->data["album_artist_id"])) {
			$id = $_wt_options->options("default_artist") ;
			$artist = new WT_Artist($id);
			$data = $artist->retrieve();
			if($data) {
				$this->data["album_artist_id"] = $id;
				$this->data["artist_name"] = $data["artist_name"];	
			};	
		}
		
		$this->data = array_merge($this->data,array(
			"album_id" => "",
			"album_title"  => "",
			"album_tracks" =>array(),
			"album_genre" => array()
		));
		return $this->data;
	}
	
	public function is_album() {
		return $this->data;
	}
	
	public function is_tracks() {
		return $this->data["album_tracks_status"] ? 1 : 0 ;
	}
	
	public function is_similar() {
		return $this->data["album_similar_status"] ? 1 : 0 ;
	}
	
	public function template($data=null) {
		global $wpdb;
		if(!$data) $data = $this->data;
		$db = $this->dbprepere;
		
		$album_poster_id = $data["album_thumbnail"] ? $data["album_thumbnail"]["id"] : $this->get_thumbnail("album");
		$album_poster = $db->media_out($album_poster_id);
		
		$social_links = unserialize($data["album_buy_links"]); 
		$tracks = isset($data["album_tracks"]) ?  $data["album_tracks"] : $this->get_tracks();
		$genre  = $this->get_genre_tpl("album");
		
		return array(
			"id"          => $db->int_out($data["album_id"]),
			"title"       => $db->str_out($data["album_title"]),
			"artist"      => $db->str_out($data["artist_name"]),
			"artist_id"   => $db->str_out($data["album_artist_id"]),
			"type"        => $db->str_out($data["album_type"]),
			"credits"     => $db->str_out($data["album_credits"]),
			"release"     => $db->date_out($data["album_release_date"]),
			"release_raw" => $data["album_release_date"],
			"about"       => $db->html_out($data["album_about"]),
			"short_about" => $db->html_teaser_out($data["album_about"]),
			"genre"       => implode(", ",$genre),
			"genre_array" => $genre,
			"label"      => $db->str_out($data["album_label"]),
			"url"        => $db->link_out(wt_get_permalink("album",$data["album_id"],array("%name%"=>$data["album_title"]))),
			"poster"     => $album_poster,
			"amazon"     => $db->link_out($social_links["album_buy_amazon"]),
			"amazonmp3"  => $db->link_out($social_links["album_buy_amazon_mp3"]),
			"itunes"     => $db->link_out($social_links["album_buy_itunes"]),
			"buylink1"   => $db->link_out($social_links["album_buy_link_1"]),
			"buylink2"   => $db->link_out($social_links["album_buy_link_2"]),
			"buylink3"   => $db->link_out($social_links["album_buy_link_3"]),
			"paypal"     => $db->html_out($social_links["album_buy_pay_pal"]),
			"tracks"     => $tracks,
			"total_tracks" => count($tracks)
		);
	}
	
	protected function validate(&$data,$nonce_name) {
		global $wpdb;
		# check in parent class if nonce is legal
		$is_valid = parent::validate($data,$nonce_name);
		if($is_valid && is_array($data)) {
			$is_valid = true ;
			if(empty($data["album_title"])) {
				$is_valid = false ;
				$this->add_db_result("album_title","required","Album is missing");	
			}
			
			if(!empty($data["album_buy_amazon"]) && !is_valid_url($data["album_buy_amazon"])) {
				$is_valid = false ;
				$this->add_db_result("album_buy_amazon","required","Amazon url in not valid, the required format is http://buy_link_url");	
			}
			
			if(!empty($data["album_buy_amazon_mp3"]) && !is_valid_url($data["album_buy_amazon_mp3"])) {
				$is_valid = false ;
				$this->add_db_result("album_buy_amazon_mp3","required","Amazon MP3 url in not valid, the required format is http://buy_link_url");	
			}
			
			if(!empty($data["album_buy_itunes"]) && !is_valid_url($data["album_buy_itunes"])) {
				$is_valid = false ;
				$this->add_db_result("album_buy_itunes","required","iTunes url in not valid, the required format is http://buy_link_url");	
			}
			
			if(!empty($data["album_buy_link_1"]) && !is_valid_url($data["album_buy_link_1"])) {
				$is_valid = false ;
				$this->add_db_result("album_buy_link_1","required","Buy Link 1 url in not valid, the required format is http://buy_link_url");	
			}
			
			if(!empty($data["album_buy_link_2"]) && !is_valid_url($data["album_buy_link_2"])) {
				$is_valid = false ;
				$this->add_db_result("album_buy_link_2","required","Buy Link 2 url in not valid, the required format is http://buy_link_url");	
			}
			
			if(!empty($data["album_buy_link_3"]) && !is_valid_url($data["album_buy_link_3"])) {
				$is_valid = false ;
				$this->add_db_result("album_buy_link_3","required","Buy Link 3 url in not valid, the required format is http://buy_link_url");	
			}
			
			if(empty($data["artist_name"])) {
				$is_valid = false ;
				$this->add_db_result("artist_name","required","Artist is missing");
			} else if(!is_numeric($data["track__name"])) {
				$is_artist = $wpdb->get_row("SELECT artist_id FROM ".WORDTOUR_ARTISTS." WHERE UPPER(artist_name)='".trim(strtoupper($data["artist_name"])."'"),"ARRAY_A");
				if($is_artist) {
					$data["album_artist_id"] = $is_artist["artist_id"];
				} else {
					$is_valid = false ;
					$this->add_db_result("artist_name","required","Artist '$data[artist_name]' doesn't exist");
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
				"album_title"        =>$db->str_in($post["album_title"]),
				"album_artist_id"    => $db->int_in($post["album_artist_id"]),
				"album_order"        => $db->int_in($post["album_order"]),
				"album_label"        =>$db->str_in($post["album_label"]),
				"album_type"        =>$db->str_in($post["album_type"]),
				"album_credits"      =>$db->str_in($post["album_credits"]),
				"album_about"        => $db->html_in($post["album_about"]),
				'album_release_date' => $db->date_in($post["album_release_date"]),
				"album_publish_date" => current_time("mysql",0),
				"album_tracks_status"=> $db->int_in($post["album_tracks_status"]),
				"album_similar_status"=> $db->int_in($post["album_similar_status"]),
				"album_buy_links" => serialize(array(
					"album_buy_amazon" => $db->link_in($post["album_buy_amazon"]),
			 		"album_buy_amazon_mp3" => $db->link_in($post["album_buy_amazon_mp3"]),
					"album_buy_itunes" => $db->link_in($post["album_buy_itunes"]),
					"album_buy_link_1" => $db->link_in($post["album_buy_link_1"]),
					"album_buy_link_2" => $db->link_in($post["album_buy_link_2"]),
					"album_buy_link_3" => $db->link_in($post["album_buy_link_3"]),
					"album_buy_pay_pal" => $db->html_form_in($post["album_buy_pay_pal"])					
				)),
				"album_author"       => $current_user->ID
			);
		}
		
		return parent::db_in($data);
	}
	
	public function db_out($data = null,$is_htmlspecial=1) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $this->dbprepere; 
			$buy_links = unserialize($data["album_buy_links"]);
			
			$data = array_merge($data,array(
				"album_title"         => $db->str_out($data["album_title"],$is_htmlspecial),
				"album_type"          => $db->str_out($data["album_type"],$is_htmlspecial),
				"album_order"         => $db->int_out($data["album_order"]),
				"album_buy_links"     => $db->str_out($data["album_buy_links"],$is_htmlspecial),
				"album_credits"       => $db->str_out($data["album_credits"],$is_htmlspecial),
				"album_about"         => $db->html_out($data["album_about"],$is_htmlspecial),
				"album_label"         => $db->str_out($data["album_label"],$is_htmlspecial),
				"album_publish_date"  => $db->datetime_short_out($data["album_publish_date"]),
				"album_release_date"  => $db->admin_date_out($data["album_release_date"]),
				"album_artist_id"     => $db->int_out($data["album_artist_id"]),
				"album_thumbnail_id"  => $data["album_thumbnail"]["id"],
				"album_buy_amazon"     => $db->link_out($buy_links["album_buy_amazon"]),
		 		"album_buy_amazon_mp3" => $db->link_out($buy_links["album_buy_amazon_mp3"]),
				"album_buy_itunes"     => $db->link_out($buy_links["album_buy_itunes"]),
				"album_buy_link_1"    => $db->link_out($buy_links["album_buy_link_1"]),
				"album_buy_link_2"     => $db->link_out($buy_links["album_buy_link_2"]),
				"album_buy_link_3"     => $db->link_out($buy_links["album_buy_link_3"]),
				"album_buy_pay_pal"    => $db->html_out($buy_links["album_buy_pay_pal"]),
				"permalink"            => $data["album_id"]>0 ? $db->link_out(wt_get_permalink("album",$data["album_id"],array("%name%"=>$data["album_title"]))) : "",		
				"_nonce"              => empty($data["album_id"]) ? wp_create_nonce(WT_Album::NONCE_INSERT) : wp_create_nonce(WT_Album::NONCE_UPDATE)
			));
			
			return $data;
		}
		return array();	
	}
	
	public function insert($values) {
		global $wpdb;
		if($this->validate($values,self::NONCE_INSERT)) {
			$wpdb->insert(WORDTOUR_ALBUMS,$this->db_in($values));
			if($wpdb->result && $wpdb->insert_id) {
				$this->id = $wpdb->insert_id;	
				$insert_wpdb = clone $wpdb ;
				if(!empty($values["album_thumbnail_id"]) && is_string($values["album_thumbnail_id"])) $this->update_thumbnail($values["album_thumbnail_id"],"album");
				if(!empty($values["album_tracks"]) && is_string($values["album_tracks"])) $this->update_tracks($this->dbprepere->json_in($values["album_tracks"]),$this->id,$values["album_artist_id"]);
				if(!empty($values["album_genre"]) && is_string($values["album_genre"])) $this->update_genre($this->dbprepere->json_in($values["album_genre"]),"album");
				$this->retrieve();		
				$this->db_result("success",$insert_wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_album_row_html")));
				return true; 		
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error adding new Album, please try again(<i>".$wpdb->last_error."</i>)"));
				return false;	
			}		
		} 
		return false; 
	}
	
	public function update($values) {
		global $wpdb;
		$album_id = $this->id;
		if($this->validate($values,self::NONCE_UPDATE)) {
			if($album_id) {
				$wpdb->update(WORDTOUR_ALBUMS,$this->db_in($values),array("album_id"=>$album_id));
				if($wpdb->result) {
					$update_wpdb = clone $wpdb ;
					if(!empty($values["album_thumbnail_id"]) && is_string($values["album_thumbnail_id"])) $this->update_thumbnail($values["album_thumbnail_id"],"album");
					if(!empty($values["album_tracks"]) && is_string($values["album_tracks"])) $this->update_tracks($this->dbprepere->json_in($values["album_tracks"]),$album_id,$values["album_artist_id"]);
					if(!empty($values["album_genre"]) && is_string($values["album_genre"])) $this->update_genre($this->dbprepere->json_in($values["album_genre"]),"album");
					$this->retrieve();
					$this->db_result("success",$update_wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_album_row_html")));
					return true;
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error updating album, please try again<br>".$wpdb->last_error));
					return false;
				}
			}
		}
		return false;
	}
	
	public function update_tracks($tracks = array(),$album_id,$artist_id) {
		global $wpdb;
		$wt_track = new WT_Track();
		$attachments = array();
		foreach($tracks as $track) {
			
			$is_track = $wpdb->get_row("SELECT * FROM ".WORDTOUR_TRACKS." WHERE track_title = '".mysql_real_escape_string($track)."' AND track_artist_id=$artist_id");
			if(count($is_track) > 0) {
				$attachments[] = (object) array("action"=>"insert","attachment_type_id"=>$is_track->track_id); 		
			} else {
				$insert = $wt_track->insert(array(
					"track_title" => $track,
					"track_artist_id" => $artist_id,
					"_nonce" => wp_create_nonce(WT_Track::NONCE_INSERT)
				));
				
				if($insert) {
					$attachments[] = (object) array("action"=>"insert","attachment_type_id"=>$wt_track->data["track_id"]);		
				}
			}
		}
		$delete = parent::delete_attachments($album_id,"album","track");
		return parent::update_attachments($attachments,$album_id,"album","track");	
	}
	
	public function get_tracks($album_id=0) {
		$wt_track = new WT_Track();
		$tracks = array();
		$attachments = parent::get_attachments($album_id,"album","track");
		
		foreach($attachments as $attachment) {
			$track = $wt_track->query("track_id=$attachment[attachment_type_id]",WORDTOUR_TRACKS);
			if(count($track) >0) $tracks[] = array("id"=>$track[0]["track_id"],"name"=>$track[0]["track_title"]);
		}
		
		return $tracks;
	}
	
	public function tracks($data = 0) {
		$tracks = array();
		try {
			if(!$data) $data = $this->data["album_tracks"];
			if($data) {
				$wt_track = new WT_Track();
				foreach($data as $track) {
					$wt_track->id = $track["id"];
					$wt_track->retrieve();
					$tracks[] = $wt_track->template();  			
				}
			}
		} catch (Exception $e) {
			
		}
		return $tracks;
	}
	
	public function similar($data = 0) {
		global $wpdb;
		$result = array();
		try {
			if(!$data) $data = $this->data["album_genre"];
			$artist_id = $this->data["album_artist_id"];
			$album_id = $this->data["album_id"];
			if($data) {
				$genres = array();
				foreach($data as $genre) {
					$genres[] = "att.attachment_info='$genre[0]'";
				}
				
				if(count($genres)) {
					$genre_sql = "(".implode(" OR ",$genres).")";
					$sql = "SELECT * FROM ".WORDTOUR_ALBUMS." as album 
							JOIN ".WORDTOUR_ATTACHMENT." AS att ON album.album_id = att.attachment_target_id 
							AND att.attachment_target='album' AND att.attachment_type='genre' AND album.album_id != $album_id 
							AND $genre_sql GROUP BY album.album_id ORDER BY album.album_title LIMIT 5";
					$albums = $wpdb->get_results($sql,"ARRAY_A");
					$albums_obj = new WT_Album();
					
					foreach($albums as $album) {
						$albums_obj->id = $album["album_id"];
						$albums_obj->retrieve();
						$result[] = $albums_obj->template();		
					}						
				}
		
			}
		} catch (Exception $e) {
			return array();	
		}
		return $result;
	}
	
	public function quick_update($post) {
		$this->retrieve();
		if($this->data) {
			$values = array_merge($this->db_out($this->data,0),array(
				"album_title"     => $post["album_title"],
				"album_order"     => $post["album_order"],
				"album_similar_status"     => $post["album_similar_status"],
				"album_tracks_status"     => $post["album_tracks_status"],
				"artist_name"    => $post["artist_name"],
				"_nonce" 		  => $post["_nonce"]
			));
			
			return $this->update($values);
		}
		$this->db_result("error",array(),array("msg"=>"Error updating Album, please try again"));
	}
	
	
	
	public function delete($nonce="",$id = 0,$validate=1) {
		global $wpdb;
		# When using delete_all no need to check every time nonce. nd add id
		$is_valid = 1 ;
		if($validate) $is_valid = $this->validate($nonce,self::NONCE_DELETE);	
		if($is_valid) {
			$album_id = $id ? $id : $this->id;
			// Check if artist assigned to event
			$wpdb->query($wpdb->prepare("DELETE FROM ".WORDTOUR_ALBUMS." WHERE album_id=$album_id"));
			$delete_wpdb = clone $wpdb;
			if($wpdb->result) {
				parent::delete_attachments($album_id,"album");
				$this->db_result("success",$delete_wpdb);	
			} else {
				$this->db_result("error",$delete_wpdb,array("msg"=>"Error delete album, please try again<br>".$wpdb->last_error));	
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
	
//	public static function all($album_id=array(),$columns = "*") {
//		global $wpdb ;
//		$sql = "";
//		if(count($album_id)) {
//			$albums_sql = array();
//			foreach($albums_id as $id) {
//				$artists_sql[] = "album_id=$id";		
//			}
//			$sql = "WHERE ".implode(" OR ",$artists_sql);
//		}
//		$result = $wpdb->get_results($wpdb->prepare("SELECT $columns FROM ".WORDTOUR_ARTISTS." $sql ORDER BY artist_name"),"ARRAY_A");
//		if(!$result) $result = array(); 
//		return $result; 
//	}
	
//	public static function sql($columns = "*",$sql = "") {
//		return "SELECT $columns FROM ".WORDTOUR_ALBUMS." $sql";
//	}
	
}

