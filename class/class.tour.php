<?php
class WT_Tour extends WT_Object {
	const NONCE_UPDATE = 'wt-tour-update';  
	const NONCE_INSERT = 'wt-tour-insert';
	const NONCE_DELETE = 'wt-tour-delete';
	const NONCE_DELETE_ALL = 'wt-tour-delete-all';
	public function retrieve() {
		global $wpdb,$_wt_options;
		$tour_id = $this->id;
		$tour = null;
		if($tour_id) {
			$tour = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_TOUR." WHERE tour_id=$tour_id"),"ARRAY_A");
			if($tour) {
				$tour = array_merge($tour,array(
					"is_default" => $_wt_options->options("default_tour") == $tour_id ? 1 : 0,
					"tour_thumbnail" =>  get_attachment_data($this->get_thumbnail("tour")),
					"tour_videos"    => $this->get_videos("tour"),
					"tour_gallery"   => $this->get_gallery("tour"),
					"tour_category"  => $this->get_category("tour"),
					"tour_genre"     => $this->get_genre("tour")
				));
				
			}	 
		}
		return parent::retrieve($tour);		
	}
	
	public function defaults(){
		parent::defaults(WORDTOUR_TOUR);
		$this->data = array_merge($this->data,array(
			"is_default" =>  0,
			"tour_thumbnail" => "",
			"tour_videos"  => array(),
			"tour_gallery" => array(),
			"tour_category"=> array(),
			"tour_genre"   => array()
		));
		
		return $this->data;
	}
	
	protected function validate(&$data,$nonce_name) {
		$is_valid = parent::validate($data,$nonce_name);
		if($is_valid && is_array($data)) {
			$is_valid = true ;
			if(empty($data["tour_name"])) {
				$is_valid = false ;
				$this->add_db_result("tour_name","required","Name is missing");	
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
				"tour_name"=>$db->str_in($post["tour_name"]),
				"tour_description" => $db->html_in($post["tour_description"]),
				"tour_order"=> $db->int_in($post["tour_order"]),
				"tour_publish_date" => current_time("mysql",0),
				"tour_tour_status" => $db->int_in($post["tour_tour_status"]),
				"tour_gallery_status" => $db->int_in($post["tour_gallery_status"]),
				"tour_video_status" => $db->int_in($post["tour_video_status"]),
				"tour_flickr_status"=> $db->int_in($post["tour_flickr_status"]),
				"tour_post_status" => $db->int_in($post["tour_post_status"]),
				"tour_author"       => $current_user->ID
			);
		}
		return parent::db_in($data);
	}
	
	public function db_out($data = null,$is_htmlspecial=1) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $this->dbprepere;
			$data = array_merge($data,array(
				"tour_name"               => $db->str_out($data["tour_name"],$is_htmlspecial),
				"tour_description"        => $db->html_out($data["tour_description"]),
				"tour_order"              => $db->int_out($data["tour_order"]),
				"tour_publish_date"       => $db->datetime_short_out($data["tour_publish_date"]),
				"tour_publish_date_raw"   => $data["tour_publish_date"],
				"tour_thumbnail_id"       => $data["tour_thumbnail"]["id"],
				'tour_gallery_status'     => $db->int_out($data["tour_gallery_status"]),
				'tour_video_status'       => $db->int_out($data["tour_video_status"]),
				'tour_flickr_status'      => $db->int_out($data["tour_flickr_status"]),
				'tour_post_status'        => $db->int_out($data["tour_post_status"]),
				'tour_tour_status'        => $db->int_out($data["tour_tour_status"]),
				"permalink"               => $data["tour_id"]>0 ? $db->link_out(wt_get_permalink("tour",$data["tour_id"],array("%name%"=>$data["tour_name"]))) : "",			
				"_nonce"                  => empty($data["tour_id"]) ? wp_create_nonce(WT_Tour::NONCE_INSERT) : wp_create_nonce(WT_Tour::NONCE_UPDATE)
			));
			return $data;
		}
		return array();	
	}
	
	public function insert($values) {
		global $wpdb;
		if($this->validate($values,self::NONCE_INSERT)) {
			$wpdb->insert(WORDTOUR_TOUR,$this->db_in($values));
			if($wpdb->result && $wpdb->insert_id) {
				$this->id = $wpdb->insert_id;	
				$insert_wpdb = clone $wpdb ;
				if(!empty($values["tour_thumbnail_id"]) && is_string($values["tour_thumbnail_id"])) $this->update_thumbnail($values["tour_thumbnail_id"],"tour");
				if(!empty($values["tour_category"]) && is_string($values["tour_category"])) $this->update_category($this->dbprepere->json_in($values["tour_category"]),"tour");
				if(!empty($values["tour_videos"]) && is_string($values["tour_videos"])) $this->update_videos($this->dbprepere->json_in($values["tour_videos"]),"tour");
				if(!empty($values["tour_gallery"]) && is_string($values["tour_gallery"])) $this->update_gallery($this->dbprepere->json_in($values["tour_gallery"]),"tour");
				if(!empty($values["tour_genre"]) && is_string($values["tour_genre"])) $this->update_genre($this->dbprepere->json_in($values["tour_genre"]),"tour");
						
				$this->retrieve();
				$this->db_result("success",$insert_wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_tour_row_html")));
				return true; 		
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error adding new Tour, please try again(<i>".$wpdb->last_error."</i>)"));
				return false;	
			}		
		} 
		return false; 
	}
	
	public function update($values) {
		global $wpdb;
		$tour_id = $this->id;
		if($this->validate($values,self::NONCE_UPDATE)) {
			if($tour_id) {
				$wpdb->update(WORDTOUR_TOUR,$this->db_in($values),array("tour_id"=>$tour_id));
				if($wpdb->result) {
					$update_wpdb = clone $wpdb ;
					if(!empty($values["tour_thumbnail_id"]) && is_string($values["tour_thumbnail_id"])) $this->update_thumbnail($values["tour_thumbnail_id"],"tour");
					if(!empty($values["tour_category"]) && is_string($values["tour_category"])) $this->update_category($this->dbprepere->json_in($values["tour_category"]),"tour");
					if(!empty($values["tour_videos"]) && is_string($values["tour_videos"])) $this->update_videos($this->dbprepere->json_in($values["tour_videos"]),"tour");
					if(!empty($values["tour_gallery"]) && is_string($values["tour_gallery"])) $this->update_gallery($this->dbprepere->json_in($values["tour_gallery"]),"tour");
					if(!empty($values["tour_genre"]) && is_string($values["tour_genre"])) $this->update_genre($this->dbprepere->json_in($values["tour_genre"]),"tour");
					$this->retrieve();
					$this->db_result("success",$update_wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_tour_row_html")));
					return true;
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error updating tour, please try again<br>".$wpdb->last_error));
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
				"tour_name"     => $post["tour_name"],
				"tour_order"    => $post["tour_order"],
				"tour_gallery_status"  => $post["tour_gallery_status"],
				"tour_video_status"    => $post["tour_video_status"],
				"tour_flickr_status"   => $post["tour_flickr_status"],
				"tour_post_status"     => $post["tour_post_status"],
				"tour_tour_status"     => $post["tour_tour_status"],
				"_nonce" 		  => $post["_nonce"]
			));
			
			unset($values["tour_id"]);
			return $this->update($values);
		}
		$this->db_result("error",array(),array("msg"=>"Error updating Artist, please try again"));
	}
	
	public function set_default($default=0){
		global $_wt_options;
		if($this->id) {
			if($default) {
				$_wt_options->update(array("default_tour"=>$this->id));	
			} else {
				$_wt_options->update(array("default_tour"=>0));
			}
			$this->retrieve($this->id);
			$this->db_result("success",$wpdb,array("result"=>$this->data,"html"=>$this->admin_html("get_tour_row_html")));
		}
	}
	
	public function events($options=array()){
		$r = new WT_Renderer();
		return $r->events(array_merge(array(
			"date_range"    => "all",
			'tour'          => $this->id,
			'group_by_tour' => 0, 			
		),$options),null,"wordtour_events");	
	}
	
	public function flickr() {
		return parent::flickr("tour"); 	
	}
	
	public function is_tour() {
		return $this->data;
	}
	
	public function is_gallery() {
		return $this->data["tour_gallery_status"] ? true : false ;
	}
	
	public function is_flickr() {
		return $this->data["tour_flickr_status"] ? true : false ;
	}
	
	public function is_post() {
		return $this->data["tour_post_status"] ? true : false ;
	}
	
	public function is_video() {
		return $this->data["tour_video_status"] ? true : false ;
	}
	
	public function admin_html($data = null) {
		return parent::admin_html("get_tour_row_html",$data);	
	}
	
	
	public function delete($nonce="",$id = 0,$validate=1) {
		global $wpdb;
		$is_valid = 1 ;
		if($validate) $is_valid = $this->validate($nonce,self::NONCE_DELETE);	
		#
		if($is_valid) {
			$tour_id = $id ? $id : $this->id;
			$wpdb->query($wpdb->prepare("DELETE FROM ".WORDTOUR_TOUR." WHERE tour_id=$tour_id"));
			if($wpdb->result) {
				$attachments = new WT_Attachment();
				$attachments->delete("attachment_target_id=$tour_id&attachment_target=tour");
				$this->db_result("success",$wpdb);	
			} else {
				$this->db_result("error",$wpdb,array("msg"=>"Error delete comment, please try again<br>".$wpdb->last_error));	
			}
			return $wpdb->result;
		}
		return false;
	}
	
	public function delete_all($tour_id=array(),$nonce="") {
		global $wpdb;
		if($this->validate($nonce,self::NONCE_DELETE)) {
			$result = array();
			foreach($tour_id as $id) {
				$this->delete(null,$id,0);
				$result[$id] = $this->db_result; 
			}
			$this->db_result = $result;	
		}
		return false;
	}
	
	public function template($data = null) {
		global $wpdb;
		$db = $this->dbprepere;
		if(!$data) $data = $this->data;
		$tour_poster_id = $data["tour_thumbnail"] ? $data["tour_thumbnail"]["id"] : $this->get_thumbnail("tour");
		$tour_poster = $db->media_out($tour_poster_id);
		return array(
			"name"        => $db->str_out($data["tour_name"]),
			"id"          => $db->int_out($data["tour_id"]),
			"poster"      => $tour_poster,
			"description" => $db->html_out($data["tour_description"]),
			"short_description" => $db->html_teaser_out($data["tour_description"]),
			"url"         => $db->link_out(wt_get_permalink("tour",$data["tour_id"],array("%name%"=>$data["tour_name"])))
		);
	}
	public static function sql($column = '*',$sql){
		return "SELECT $column FROM ".WORDTOUR_TOUR." $sql";
	}
	# Get all tour listing 
	public static function all($order="tour_name") {
		global $wpdb ;
		$tour = $wpdb->get_results($wpdb->prepare("SELECT 
				* 
				FROM ".WORDTOUR_TOUR." 
				ORDER BY $order ASC"),"ARRAY_A");
		if(!$tour) $tour = array(); 
		return $tour;
	}
	# Get all tour listing by artist
	public static function all_by_artist($artist_id=array(),$order="tour_name") {
		global $wpdb ;
		$artist_sql = $artist_id ? " AND e.event_artist_id=$artist_id" : "";
		 
		$tour = $wpdb->get_results(
					$wpdb->prepare(WT_Event::sql_all_tables("tour_id,tour_name,tour_order,tour_thumbnail_id",
					array("meta"=>0,"venue"=>0,"artists"=>0,"tour"=>1))." 
					$artist_sql GROUP BY e.event_tour_id ORDER BY $order ASC"),"ARRAY_A");	
		return $tour ;
	}
	
}
