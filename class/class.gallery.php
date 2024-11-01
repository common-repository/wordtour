<?php
class WT_Gallery extends WT_Component {
	# Define all nonce names
	const NONCE_UPDATE = 'wt-gallery-update';  
	const NONCE_INSERT = 'wt-gallery-insert';
	const NONCE_DELETE = 'wt-gallery-delete';
	const NONCE_DELETE_ALL = 'wt-gallery-delete-all';
	
	public function retrieve() {
		global $wpdb,$_wt_options;
		$gallery_id = $this->id;
		$gallery = null;
		if($gallery_id) $gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_GALLERY." WHERE gallery_id=$gallery_id"),"ARRAY_A");
		return parent::retrieve($gallery);		
	}
	
	protected function validate(&$data,$nonce_name) {
		# check in parent class if nonce is legal
		$is_valid = parent::validate($data,$nonce_name);
		if($is_valid && is_array($data)) {
			$is_valid = true ;
			if(empty($data["gallery_name"])) {
				$is_valid = false ;
				$this->add_db_result("gallery_name","required","Name is missing");	
			}
			if(!$is_valid) $this->db_result("error",null,array("data"=>$this->db_response_msg));		
		}
		return $is_valid;
	}
	
	public function db_in($data = null) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $db = $this->dbprepere;
			
			$post = array(
				"gallery_name" => $db->str_in($data["gallery_name"]),
				"gallery_publish_time" => current_time("mysql",0),
				"gallery_attachment" => empty($data["gallery_attachment"]) ? serialize(array()) :  serialize($db->json_in($data["gallery_attachment"]))
			);
		}
		return parent::db_in($post);
	}
	
	public function db_out($data = null,$is_htmlspecial=1) {
		if(!$data) $data = $this->data;
		if($data) {
			$db = $db = $this->dbprepere;
			$data = array_merge($data,array(
				"gallery_name" => $db->str_out($data["gallery_name"],$is_htmlspecial),
				"gallery_attachment" => $this->get_attachments(),
				"_nonce"=> empty($data["gallery_id"]) ? wp_create_nonce(self::NONCE_INSERT) : wp_create_nonce(self::NONCE_UPDATE)
			));
			
			return $data;
		}
		return array();	
	}
	
	public function defaults(){
		parent::defaults(WORDTOUR_GALLERY);
		$this->data = array_merge($this->data,array(
			"gallery_name" => ""
		));
		return $this->data;
	}
	
	public function admin_html($data = null) {
		return parent::admin_html("get_gallery_row_html",$data);	
	}
	
	public function insert($values) {
		global $wpdb;
		if($this->validate($values,self::NONCE_INSERT)) {
			$insert = $wpdb->insert(WORDTOUR_GALLERY,$this->db_in($values));
			$temp_wpdb = clone $wpdb;
			if($wpdb->result && $wpdb->insert_id) {
				$id = $wpdb->insert_id;
				$this->id = $id;
				$this->retrieve();
				$this->db_result("success",$temp_wpdb,array("result"=>$this->data,"html"=>$this->admin_html($this->data)));
				return 1;
			} 
			$this->db_result("error",$wpdb,array("msg"=>"Error adding new gallery, please try again(<i>".$wpdb->last_error."</i>)"));			
		} 
		return 0; 
	}
	
	public function update($values) {
		global $wpdb;
		$gallery_id = $this->id;
		if($this->validate($values,self::NONCE_UPDATE)) {
			if($gallery_id) {
				$wpdb->update(WORDTOUR_GALLERY,$this->db_in($values),array("gallery_id"=>$gallery_id));
				$update_wpdb = clone $wpdb; 
				if($wpdb->result) {
					$this->retrieve();
					//print_r($this->data);
					$this->db_result("success",$update_wpdb,array("result"=>$this->data,"html"=>$this->admin_html($this->data)));
					return true;
				} else {
					$this->db_result("error",$update_wpdb,array("msg"=>"Error updating gallery, please try again<br>".$wpdb->last_error));
					return false;
				}
			}
		}
		return false;
	}
	
	public function delete($nonce="",$id = 0,$validate=1) {
		global $wpdb;
		$is_valid = 1 ;
		if($validate) $is_valid = $this->validate($nonce,self::NONCE_DELETE);	
		if($is_valid) {
			$gallery_id = $id ? $id : $this->id;
			$wpdb->query($wpdb->prepare("DELETE FROM ".WORDTOUR_GALLERY." WHERE gallery_id=$gallery_id"));
			$delete_wpdb = clone $wpdb; 
			if($wpdb->result) $wpdb->query($wpdb->prepare("DELETE FROM ".WORDTOUR_ATTACHMENT." WHERE attachment_type_id=$gallery_id AND attachment_type='gallery'"));
			if($wpdb->result) {
				$this->db_result("success",$delete_wpdb);	
			} else {
				$this->db_result("error",$delete_wpdb,array("msg"=>"Error delete gallery, please try again<br>".$wpdb->last_error));	
			}
			return $wpdb->result;
		}
		return false;
	}
	
	public function delete_all($gallery_id=array(),$nonce="") {
		global $wpdb;
		if($this->validate($nonce,self::NONCE_DELETE)) {
			$result = array();
			foreach($gallery_id as $id) {
				$this->delete(null,$id,0);
				$result[$id] = $this->db_result; 
			}
			$this->db_result = $result;	
		}
		return false;
	}
	
	public function get_attachments($size = array()){
		$attachments = array();
		if($this->data) {
			if(!empty($this->data["gallery_attachment"])) {
				foreach(unserialize($this->data["gallery_attachment"]) as $attachment) {
					if(!count($size)) {
						$attachments[] = get_attachment_data($attachment,$size);	
					} else {
						$a = array();
						foreach($size as $s) {
						$a[$s] = get_attachment_data($attachment,$s);	
						}
						$attachments[] = $a ;
					}
				};
			}
		}
		return $attachments; 
	}
	
	public static function all($columns = "*",$sql = "") {
		global $wpdb ;
		$result = $wpdb->get_results($wpdb->prepare("SELECT $columns FROM ".WORDTOUR_GALLERY." $sql ORDER BY gallery_publish_time DESC"),"ARRAY_A");
		if(!$result) $result = array(); 
		return $result;
	}
	
}
