<?php
class WT_Social extends WT_Component{
	const NONCE_INSERT = 'wt-social-insert';
	public function retrieve($social_id = 0) {
		global $wpdb;
		if(!$social_id) $social_id = $this->id;
		$facebook = null;
		if($social_id) {
			$facebook = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_SOCIAL." WHERE social_id=$social_id"),"ARRAY_A");	 
		}
		return parent::retrieve($facebook); 		
	}
	
	public function get_by_event($event_id = 0,$type = "fbstatus") {
		global $wpdb;
		if($event_id) {
			return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_SOCIAL." WHERE social_parent_type='event' AND social_parent_id=$event_id AND social_type='$type' ORDER BY social_publish_time DESC LIMIT 1"),"ARRAY_A");
		}
	}
	
	public function get_by_venue($venue_id = 0,$type = "ebvenue") {
		global $wpdb;
		if($venue_id) return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_SOCIAL." WHERE social_parent_type='venue' AND social_parent_id=$venue_id AND social_type='$type' ORDER BY social_publish_time DESC LIMIT 1"),"ARRAY_A");
	}
	
	public function get_by_ref_id($ref_id = 0,$parent_type="event",$type = "fbstatus") {
		global $wpdb;
		if($ref_id) return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_SOCIAL." WHERE social_parent_type='$parent_type' AND social_ref_id=$ref_id AND social_type='$type' ORDER BY social_publish_time DESC LIMIT 1"),"ARRAY_A");
	}
	
	public function db_out($data = null,$is_htmlspecial=1) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $this->dbprepere;
			$data = array_merge($data,array(
				"social_id" => $db->int_out($data["social_id"]),
				"social_ref_id" => $db->int_out($data["social_ref_id"]),
				"social_parent_id" => $db->int_out($data["social_parent_id"]),
				"social_publish_time" => $db->datetime_out($data["social_publish_time"])
			));
			return $data;
		}
		return array();	
	}
	
	public function insert($nonce = 0,$type = "",$id = 0,$ref_id=0,$parent_type = "event") {
		global $wpdb;
		if(parent::validate($nonce,self::NONCE_INSERT)) {
			if($id) {
				$wpdb->insert(WORDTOUR_SOCIAL,array(
					'social_parent_id' => $id,
					'social_parent_type' => $parent_type,
					'social_type' =>$type,
					'social_publish_time'=>current_time("mysql",0),
					'social_ref_id'=>$ref_id), array('%d','%s','%s','%s','%d'));
				if($wpdb->result && $wpdb->insert_id) {
					$this->id = $wpdb->insert_id;	
					$insert_wpdb = clone $wpdb ;		
					$this->retrieve();
					$this->db_result("success",$insert_wpdb,array("result"=>$this->db_out($this->data)));
					return true; 		
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Update was succesfull, unfortontly database didnt updated with recent changes(<i>".$wpdb->last_error."</i>)"));
					return false;	
				}			
				return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_SOCIAL." WHERE social_parent_id=$id AND social_type='$type'"),"ARRAY_A");	 					
			}
		}
		
		return 0;
	}
	
	public function update($nonce = 0,$social_id = 0,$type = "",$id = 0,$ref_id=0,$parent_type = "event") {
		global $wpdb;
		if(parent::validate($nonce,self::NONCE_INSERT)) {
			if($id) {
				$wpdb->update(WORDTOUR_SOCIAL,array(
					'social_parent_id' => $id,
					'social_parent_type' => $parent_type,
					'social_type' =>$type,
					'social_publish_time'=>current_time("mysql",0),
					'social_ref_id'=>$ref_id),array("social_id"=>$social_id), array('%d','%s','%s','%s','%d'),array('%d'));
				if($wpdb->result) {
					$update_wpdb = clone $wpdb ;		
					$this->retrieve();
					$this->db_result("success",$update_wpdb,array("result"=>$this->db_out($this->data)));
					return true; 		
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Update was succesfull, unfortontly database didnt updated with recent changes(<i>".$wpdb->last_error."</i>)"));
					return false;	
				}			
				return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_SOCIAL." WHERE social_parent_id=$id AND social_type='$type'"),"ARRAY_A");	 					
			}
		}
		
		return 0;
	}
}