<?php
class WT_Attachment extends WT_Component{
	public function retrieve($attachment_id = 0) {
		global $wpdb;
		if(!$attachment_id) $attachment_id = $this->id;
		if($attachment_id) {
			$attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WORDTOUR_ATTACHMENT." WHERE attachment_id=$attachment_id"),"ARRAY_A");	 
		}
		return parent::retrieve($attachment); 		
	}
	
	public function query($query = 0) {
		global $wpdb;
 		if($query) {
 			parse_str($query,$params);
 			$sql = array();
 			foreach($params as $column=>$value) {
 				$value = is_numeric($value) ? $value : "'$value'";
 				$sql[] = "$column=$value";	
 			}
 			return $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WORDTOUR_ATTACHMENT." WHERE ".implode(" AND ",$sql))." ORDER BY attachment_id","ARRAY_A");
 		}
	}
	
	public function delete($query = 0) {
		global $wpdb;
 		if($query) {
 			parse_str($query,$params);
 			$sql = array();
 			foreach($params as $column=>$value) {
 				$value = is_numeric($value) ? $value : "'$value'";
 				$sql[] = "$column=$value";	
 			}
 			return $wpdb->get_results($wpdb->prepare("DELETE FROM ".WORDTOUR_ATTACHMENT." WHERE ".implode(" AND ",$sql)),"ARRAY_A");
 		}
	}
	
    public function insert($data) {
		global $wpdb;
		$insert_sql = array();
		foreach($data as $attach) {
			$target = $attach->attachment_target ? $attach->attachment_target : "";
			$target_id = $attach->attachment_target_id ? $attach->attachment_target_id : 0;
			$type = $attach->attachment_type ? $attach->attachment_type : "";
			$type_id = $attach->attachment_type_id ? $attach->attachment_type_id : 0;
			$info = $attach->attachment_info ? $attach->attachment_info : "";
			
			$insert_sql[] = "('$target',$target_id,'$type','$type_id','$info')";
		}
		
		if(count($insert_sql)) {
			$wpdb->query("INSERT INTO ".WORDTOUR_ATTACHMENT." 
				(attachment_target,attachment_target_id,attachment_type,attachment_type_id,attachment_info) 
				VALUES ".implode(",",$insert_sql)
        	);
		}
		
		
	}
	
	public function update($data) {
		global $wpdb;
		$insert_sql = array();
		$update_sql = array();
		$remove_sql = array();
		foreach($data as $attach) {
			switch($attach->action){
				case "insert":
					$insert_sql[] = "('$attach->attachment_target',$attach->attachment_target_id,'$attach->attachment_type','$attach->attachment_type_id','$attach->attachment_info')";
				break;
				case "remove":
					$remove_sql[] = "attachment_id=$attach->attachment_id";	
				break;
			}
		}
		
		if(count($insert_sql)) {
			$wpdb->query("INSERT INTO ".WORDTOUR_ATTACHMENT." 
				(attachment_target,attachment_target_id,attachment_type,attachment_type_id,attachment_info) 
				VALUES ".implode(",",$insert_sql)
        	);
		}
		
		if(count($remove_sql)) {
			$wpdb->query("DELETE FROM ".WORDTOUR_ATTACHMENT." WHERE ".implode(" OR ",$remove_sql));
		}
	}
}