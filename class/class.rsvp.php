<?php
class WT_RSVP extends WT_Component {
	private $rsvp = false;
	private $users ;
	//private $event_id ;
	//private $rsvp_id ;
//	public function __construct($event_id = 0) {
//		if($event_id) $this->_select($event_id);
//		$this->event_id = $event_id ;
//	}

	public function retrieve() {
		global $wpdb,$_wt_options;
		$rsvp_id = $this->id;
		$rsvp = null;
		if($rsvp_id) {
			$rsvp = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_ATTENDING." WHERE rsvp_id = $rsvp_id"),"ARRAY_A");	 
		}
		return parent::retrieve($rsvp); 		
	}
	
	public function get_users_by_event($event_id = 0,$limit_start = 0,$limit_end=20) {
		global $wpdb;
		if(!$event_id) return 0;
		$current_user = get_current_user_info();
		$users = $wpdb->get_results($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS *  FROM ".WORDTOUR_ATTENDING." WHERE rsvp_event_id = $event_id LIMIT $limit_start,$limit_end"),"ARRAY_A");
		$total_users = $wpdb->get_var("SELECT FOUND_ROWS()"); 
		$user_attending = $wpdb->get_row($wpdb->prepare("SELECT *  FROM ".WORDTOUR_ATTENDING." WHERE rsvp_event_id = $event_id AND rsvp_user=".$current_user->ID),"ARRAY_A");
		return array(
			"users" => !is_array($users) ? array() : $users,
			"total" => $total_users,
			"attending" => $user_attending ? 1 : 0  
		); 
	}
	
	public function template($data = array()) {
		$result = array(
			"is_login" => is_user_logged_in(),
			"users"    => array(),
			"total"    => $data["total"],
			"attending"=> $data["attending"]
		);
		
		foreach($data["users"] as $user) {
			$user_data = get_userdata($user["rsvp_user"]);
			array_push($result["users"],array(
				"nickname" => $user_data->nickname,
				"avatar"   => get_avatar($user["rsvp_user"]),
				"id"       => $user["rsvp_id"],
				"user_id"  => $user["rsvp_user"],
				"firstname"=> $user_data->user_firstname,
				"lastname" => $user_data->user_lastname
			));
		};
		
		return $result;
	}
	
	public function attend_event($event_id=0){
		global $wpdb;
		if($event_id && !empty($event_id)) {
			$current_user = get_current_user_info();
			$values = array("rsvp_event_id"=>$event_id,"rsvp_user"=>$current_user->ID,"rsvp_date"=>current_time("mysql",0));
			$wpdb->insert(WORDTOUR_ATTENDING,$values,array('%d','%d','%s'));
			if($wpdb->insert_id) {
				$wpdb->query("UPDATE ".WORDTOUR_EVENTS." SET rsvp_count=rsvp_count+1 WHERE event_id=".$event_id);	
			} 
			return $wpdb->result; 
		}

		return 0;
	}
	
	public function notattend_event($event_id=0){
		global $wpdb;
		if($event_id && !empty($event_id)) {
			$current_user = get_current_user_info();
			$wpdb->query("DELETE FROM ".WORDTOUR_ATTENDING." WHERE rsvp_event_id=$event_id AND rsvp_user=".$current_user->ID);
			if($wpdb->result) {
				if($wpdb->result) {
					$wpdb->query("UPDATE ".WORDTOUR_EVENTS." SET rsvp_count=rsvp_count-1 WHERE event_id=".$event_id);
					return $wpdb->result;
				}
			} 
		}
		return 0;
	}
	
	public function remove(){
		global $wpdb;
		$this->retrieve();
		$event_id = $this->data["rsvp_event_id"];
		$rsvp_id = $this->data["rsvp_id"];
		if($event_id && $rsvp_id) {
			$wpdb->query("DELETE FROM ".WORDTOUR_ATTENDING." WHERE rsvp_id=$rsvp_id");
			if($wpdb->result) {
				$wpdb->query("UPDATE ".WORDTOUR_EVENTS." SET rsvp_count=rsvp_count-1 WHERE event_id=".$event_id);
				$this->db_result("success",$wpdb);
				return true;
			} 
		}
		$this->db_result("error",$wpdb);
		return false;
	}
	
	public function panel_markup($event_id=0){
		if($event_id) {
			$users = $this->get_users_by_event($event_id);
			$tpl = new WT_Theme();
			echo $tpl->rsvp($this->template($users));
		}
	}
}

