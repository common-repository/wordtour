<?php
class WT_Post extends WT_Component{
	const NONCE_INSERT = 'wt-post-insert';  
	protected function validate(&$data,$nonce_name) {
		# check in parent class if nonce is legal
		$is_valid = parent::validate($data["_post_nonce"],$nonce_name);
		if($is_valid) {
//			if(empty($data["post_title"])) {
//				$is_valid = false ;
//				$this->add_db_result("post_title","required","Title is missing");	
//			}
//			
//			if(empty($data["post_event_id"])) {
//				$is_valid = false ;
//				$this->add_db_result("post_event_id","required","Event Id is missing");	
//			}
		
	        if(!$is_valid) $this->db_result("error",null,array("data"=>$this->db_response_msg));	
		}
    
		return $is_valid;
	}
	
	public function update($values) {
		if($this->validate($values,self::NONCE_INSERT)) {
			$update = wp_insert_post(array(
				"post_title"=>$values["post_title"],
				"post_content" => "[wordtour_event id='$values[post_event_id]']",
				"post_status" => 'publish',
				'post_type' => "post"
			),0);
			if($update){
				$social = new WT_Social();
				$social->insert(wp_create_nonce(WT_SOCIAL::NONCE_INSERT),"post",$values["post_event_id"],$update);
				$result = $social->db_result["result"];
				$result["post_link"] = admin_url("post.php?action=edit&post=$update");
				
				$this->db_result("success",null,array("msg"=>"Post was added succefully","result"=>$result));
			} else {
				$this->db_result("error",null,array("msg"=>"Error adding Post, please try again"));
			}
		} 
		
		return false;
	}
}

