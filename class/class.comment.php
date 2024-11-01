<?php
class WT_Comment extends WT_Component {
	const NONCE_UPDATE = 'event-comments-update';  
	const NONCE_INSERT = 'event-comments';
	const NONCE_DELETE = 'event-comments-delete';
	const NONCE_APPROVE = 'event-comments-approve';
	const NONCE_UNAPPROVE = 'event-comments-unapproved';

	private $event_id = 0 ;
	
	public function retrieve() {
		global $wpdb;
		$id = $this->id;
		$comment = null;
		if($id) {
			$comment = $wpdb->get_row($wpdb->prepare("SELECT *  FROM ".WORDTOUR_COMMENTS." WHERE comment_id = $id"),"ARRAY_A");
			if($comment) $this->event_id = $comment["comment_event_id"]; 
		}
		return parent::retrieve($comment); 		
	}
	
	public function template($data = null) {
		if(!$data) $data = $this->data;
		$data = $this->db_out($data);
		if($data) {
			$user_id = $data["comment_user_id"];
			$avatar = get_avatar($user_id,32);
			$db = $this->dbprepere;
			return array(
				"author" => $data["comment_author"],
				"email"  => $data["comment_email"],
				"avatar"   => $avatar, 
				"date"     => $db->date_display($data["comment_date"])." at ".$db->time_out($data["comment_date"]),
				"date_raw" => $data["comment_date"],
				"approved" => $data["comment_approved"] == 1 ? 1 : 0,
				"content"  => $data["comment_content"]
			); 
		}
	}
	
	public function get_by_event($event_id = 0,$query=""){
		if($event_id) {
			global $wpdb,$_wt_options;
			$current_user = get_current_user_info();
			$comments = $this->query("comment_event_id=$event_id&comment_approved=1");
			$is_login = is_user_logged_in();
			foreach((array) $comments as $i=>$comment) {
				$comments[$i] = $this->template($comment);	
			}
			
			$captcha="";
			if(!$_wt_options->options("comment_registration") && $_wt_options->options("comment_captcha") ==1 && !$is_login) {
				$public_key = (string) $_wt_options->options("captcha_public_key");
				$private_key = (string) $_wt_options->options("captcha_public_key");
				if(!empty($public_key) && !empty($private_key)) {
					require_once(WT_PLUGIN_PATH.'recaptcha/recaptchalib.php');
					$captcha = recaptcha_get_html($public_key);	
					 
				}
			}			
			return array(
				"is_login"      => $is_login,
				"captcha"       => $captcha,
				"allow_comment" => $_wt_options->options("comment_registration") == 1 ? 0 : 1,
				"nickname"      => $current_user->user_nicename,
				"total"         => count($comments),
				"nonce"         => wp_create_nonce(WT_Comment::NONCE_INSERT),
				"event_id"      => $event_id,
				"author"        => $current_user->user_nicename,
				"comments"      => $comments
			);
		}
	}
	
	public function html(){
		if($this->data) {
			$tpl = new WT_Theme();
			$tpl->comment(array("comments"=>array($this->template())));
		} 
		
		return "";
	}
	
	public function validate(&$data,$nonce_name) {
		global $_wt_options;
		$is_valid = parent::validate($data,$nonce_name);
		if($is_valid) {
			$is_valid = true;
			if(empty($data["comment_content"])) {
				$is_valid = false ;
				$this->add_db_result("comment_content","required","Content is missing");	
			}
				
			if(empty($data["comment_author"])) {
				$is_valid = false ;
				$this->add_db_result("comment_author","required","Author is missing");	
			}
			if(!empty($data["comment_event_id"]) && $data["comment_event_id"] == 0) {
				$is_valid = false ;
				$this->add_db_result("comment_event_id","required","Comment event id is missing");	
			}

			
			if(!empty($data["comment_author_email"]) && !is_valid_email($data["comment_author_email"])) {
				$is_valid = false ;
				$this->add_db_result("comment_author_email","field","Email in not valid");	
			}
			if(isset($data["recaptcha_challenge_field"]) && isset($data["recaptcha_response_field"])) {
				require_once(WT_PLUGIN_PATH.'recaptcha/recaptchalib.php');
				$private_key = (string) $_wt_options->options("captcha_private_key");
				if(!empty($private_key)) {
					$resp = recaptcha_check_answer($private_key,$_SERVER["REMOTE_ADDR"],$data["recaptcha_challenge_field"],$data["recaptcha_response_field"]);	
					$is_valid = $resp->is_valid ;
					$this->add_db_result("comment_captcha","field","The reCAPTCHA wasn't entered correctly. Go back and try it again.");
				}
			}
			if(!$is_valid) $this->db_result("error",null,array("data"=>$this->db_response_msg));
		}
		return $is_valid;
	}
	
	public function db_in($post) {
		if($post) {
			$db = $this->dbprepere;
			$data = array(
				"comment_content"      => $db->html_in($post["comment_content"]),
				"comment_author"       => $db->str_in($post["comment_author"]),
				"comment_author_email" => $db->str_in($post["comment_author_email"]), 
				"comment_user_id"      => $db->int_in($post["comment_user_id"]),
				"comment_event_id"     => $db->int_in($post["comment_event_id"]),
				"comment_approved"     => $db->int_in($post["comment_approved"]) 		
			);
		}
		
		return parent::db_in($data);
	}
	
	public function db_out($data = null,$is_htmlspecial=1) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $this->dbprepere;
			if($is_htmlspecial) $data["comment_content"] = convert_smilies($data["comment_content"]);
			$data = array_merge($data,array(
				"comment_content" => $db->html_out($data["comment_content"],$is_htmlspecial),
				"comment_date"    => $db->datetime_out($data["comment_date"]),
				"_nonce"          => empty($data["comment_id"]) ? wp_create_nonce(self::NONCE_INSERT) : wp_create_nonce(self::NONCE_UPDATE)
			));
			
			return $data;
		}
		return array();	
	}
	
	public function insert($values) {
		global $wpdb,$_wt_options;
		
		if(is_user_logged_in()){
			$user = get_user();
			$values["comment_author"]  = $user["user_nickname"];
			$values["comment_user_id"] = $user["user_id"];
		}
		if($this->validate($values,self::NONCE_INSERT) && $values["comment_event_id"]!=0) {
			if(is_user_logged_in() || $_wt_options->options("comment_registration") != 1){
				$values = $this->db_in($values);
				$values["comment_date"]    = current_time("mysql",0); 
				if(!current_user_can($_wt_options->options("user_role"))) {
					$values["comment_approved"] = $_wt_options->options("moderation_notify") ? 0 : 1;
				} else {
					$values["comment_approved"] = 1;
				};	
				$wpdb->insert(WORDTOUR_COMMENTS,$values);
				if($wpdb->result && $wpdb->insert_id) {
					$this->id = $wpdb->insert_id; 
					$this->retrieve(); 
					if($this->data["comment_approved"] == 1) $wpdb->query("UPDATE ".WORDTOUR_EVENTS." SET comment_count=comment_count+1 WHERE event_id=".$values["comment_event_id"]);
					return 1; 					
				}
				return 0; 
			} 			
			
		} 
		return 0; 
	}
	
		
	public function update($values) {
		global $wpdb ;
		$id = $this->id;
		if($this->validate($values,self::NONCE_UPDATE)) {
			if($id) {	
				$values = $this->db_in($values);
				$update = $wpdb->update(WORDTOUR_COMMENTS,$values,array("comment_id"=>$id));
				
				if($wpdb->result) {
					$this->retrieve();
					$this->db_result("success",$wpdb,array("result"=>$this->db_out(null,0),"html"=>$this->admin_html("get_comment_row_html")));
					return 1;
				} else {
					$this->db_result("error",$wpdb,array("msg"=>"Error updating comment, please try again<br>".$wpdb->last_error));
					return 0;
				}			
			}
		} 
		return 0;
		
	}

	public function approve($nonce = "",$event_id = 0) {
		global $wpdb;
		if(parent::validate($nonce,self::NONCE_APPROVE)) {
			$id = $this->id;
			if($id) {
				$wpdb->update(WORDTOUR_COMMENTS,array("comment_approved"=>1),array("comment_id"=>$id),array("%d"),array("%d"));
				$comment_wpdb = clone $wpdb; 
				if($wpdb->result) {
					$wpdb->query("UPDATE ".WORDTOUR_EVENTS." SET comment_count=comment_count+1 WHERE event_id=$event_id");
					$this->retrieve();
					$this->db_result("success",$comment_wpdb,array("result"=>$this->db_out(),"html"=>$this->admin_html("get_comment_row_html")));
					return 1;
				}
				$this->db_result("error",$wpdb,array("msg"=>"Error updating comment status, please try again<br>".$wpdb->last_error));
				return 0 ;		
			}
		}
		$this->db_result("error",null,array("msg"=>"Error updating comment status, please try again")); 
		return 0;
	}
	
	public function unapprove($nonce = "",$event_id = 0) {
		global $wpdb;
		if(parent::validate($nonce,self::NONCE_UNAPPROVE)) {
			$id = $this->id;
			if($id) {
				$wpdb->update(WORDTOUR_COMMENTS,array("comment_approved"=>0),array("comment_id"=>$id),array("%d"),array("%d"));
				$comment_wpdb = clone $wpdb; 
				if($wpdb->result) {
					if($event_id) $wpdb->query("UPDATE ".WORDTOUR_EVENTS." SET comment_count=comment_count-1 WHERE event_id=$event_id");
					$this->retrieve();
					$this->db_result("success",$comment_wpdb,array("result"=>$this->db_out(),"html"=>$this->admin_html("get_comment_row_html")));
					return 1;	
				}
				$this->db_result("error",$wpdb,array("msg"=>"Error updating comment status, please try again<br>".$wpdb->last_error));
				return 0;
			}
		}
		return 0;
	}
	
	public function delete($nonce = "",$event_id = 0,$validate=1) {
		global $wpdb;
		$is_valid = 1 ;
		if($validate) $is_valid = parent::validate($nonce,self::NONCE_DELETE);
		if($is_valid) {
			$id = $this->id;
			if($id) {
				$this->retrieve();
				$wpdb->query("DELETE FROM ".WORDTOUR_COMMENTS." WHERE comment_id=$id");
				
				$comment_wpdb = clone $wpdb;
				if($wpdb->result) {
					if($this->data["comment_approved"] == 1 && $event_id) $wpdb->query("UPDATE ".WORDTOUR_EVENTS." SET comment_count=comment_count-1 WHERE event_id=$event_id");
					$this->db_result("success",$comment_wpdb,array("msg"=>"Comment permanently deleted."));
					return 1;	
				} 
				$this->db_result("error",$comment_wpdb,array("msg"=>"Error delete comment, please try again<br>".$wpdb->last_error));
				return 0;
			}
		}
		return 0;
	}
	
	public function delete_all($comment_id=array(),$nonce="") {
		global $wpdb;
		if(parent::validate($nonce,self::NONCE_DELETE)) {
			$result = array();
			foreach($comment_id as $id) {
				$this->id = $id;
				$this->delete(null,$id,0);
				$result[$id] = $this->db_result; 
			}
			$this->db_result = $result;	
		}
		return false;
	}
	
	public function query($query = 0) {
		global $wpdb;
 		if($query) {
 			parse_str($query,$params);
 			$sql = array();
 			foreach($params as $column=>$value) {
 				$value = is_numeric($value) ? $value : "'$value'";
 				if($column!="order" && $column!="limit" && $column!="direction") $sql[] = "$column=$value";	
 			}
 			
 			$direction = isset($params["direction"]) ? $params["direction"] : "DESC";
 			$order = "ORDER BY ".(isset($params["order"]) ? $params["order"] : "comment_date"). " $direction";
 			$limit = (isset($params["limit"]) ? "LIMIT $params[limit]" : "");
 			
 			return $wpdb->get_results($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM ".WORDTOUR_COMMENTS." WHERE ".implode(" AND ",$sql)." $order $limit"),"ARRAY_A");
 		}

	}
	
}

