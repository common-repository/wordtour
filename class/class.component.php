<?php
abstract class WT_Component {
	# PROPERTIES
	protected $db_response_msg = array();
	protected $db_result    = null ;
	protected $dbprepere;
	protected $data    =  null;
	protected $id = 0 ;
	# magic
	public function __call($method, $arguments) {
		echo "method {$method} doesn't exist";
	}
	public function __set($name, $value) {
		$this->$name = $value;
    }

    public function __get($name) {
    	if (property_exists($this,$name)) {
            return $this->$name;
        }
        
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
	
	#PRIVATE METHODS
	public function __construct($id = 0,$data=null) {
		$this->dbprepere = new WT_DBPrepere();
		if(is_numeric($id)) {
			$this->id = $id;
			return $this;		
		}
		
		if(is_array($data)) $this->data = $data;
		$this->id = 0;
	}
	
	public function retrieve($data=null) {
		$this->data = $data;
		return $this->data; 
	}
	
	public function defaults($tables = array()){
		global $wpdb;
		if(is_string($tables)) $tables = array($tables);
		$this->data = array();
		foreach($tables as $table) {
			$columns = $wpdb->get_results("SHOW COLUMNS FROM $table","ARRAY_A");
			foreach($columns as $column) {
				$this->data[$column["Field"]] = $column["Default"];
			}
		}
		return $this->data;	
	}
	#PROTECTED METHODS
	protected function validate(&$data=array(),$nonce_name) {
		if(is_array($data)) {
			$nonce = $data["_nonce"];
			unset($data["_nonce"]);	
		} else {
			$nonce = $data;
		}
		
		
		$is_nonce = wp_verify_nonce($nonce,$nonce_name);
		
		if(!$is_nonce) {
			$this->db_result("security");		
		}
		return $is_nonce;
	}
	
	protected function add_db_result($id='',$type='',$txt=''){
		$this->db_response_msg[$id] = array(
			"type"=>$type,
			"txt" => $txt
		);
	}
	
	protected function db_result($type,$wpdb = null,$data = array()) {
		$db = array();
		if($type == "security") { 
			$db = array(
				"type" => "error",
				"msg"  => "Security Error, You are doing something illegal"
			); 
		} else { 
			$db["type"] =$type ;
			
			if($wpdb) {
				$db["wpdb"] = array(
					"result"     	=> (String) $wpdb->result,
					"rows_affected" => $wpdb->rows_affected,
					"num_rows"      => $wpdb->num_rows,
					"last_error"    => $wpdb->last_error,
					"insert_id"     => $wpdb->insert_id
				);
			}	
		};
		
		$this->db_result = array_merge($db,$data); 
	}
	#PUBLIC METHODS
	public function db_response($response_type = "") {
		switch($response_type) {
			case "json":
				if($this->db_result) echo json_encode($this->db_result);
			break;
			case "html":
				echo $this->html();
			break;
			case "error":
				echo json_encode(array("type"=>"error","data"=>$this->db_response_msg));
			break;	
			default:
				return $this->db_result; 
			break;
		}
	}
	
	public function template() {}
	public function html(){}
	public function admin_html($callback = "") {
		if(!$this->data || empty($callback)) return ""; 
		$html = call_user_func($callback,$this->db_out());
		return $html;	
	}
	public function db_in($data,$schema = null) {
		if($schema) {
			$new_data = array();
			if(is_array($schema)) {
				foreach($data as $key=>$value){
					if(in_array($key,$schema)) $new_data[$key] = $value;		
				}
			}
			return $new_data;
		} else {
			//unset($data['action']);
			//unset($data['PHPSESSID']);
			return $data;
		}
		
		
	}
	public function db_out($data = 0) {
		//if(!$data) $data = $this->component();
		//return $data ;  
	}
	
	public function query($query = "",$table="") {
		global $wpdb; 
 		
 		parse_str($query,$params);
 		$sql = array();
 		foreach($params as $column=>$value) {
 			$value = is_numeric($value) ? $value : "'$value'";
 			if($column!="order" && $column!="limit" && $column!="direction") $sql[] = "$column=$value";	
 		}
 		
 		$direction = isset($params["direction"]) ? $params["direction"] : "DESC";
 		$order = isset($params["order"]) ? "ORDER BY $params[order] $direction" : "";
 		$limit = (isset($params["limit"]) ? "LIMIT $params[limit]" : "");
 		$where = (count($sql)) ? "WHERE ".implode(" AND ",$sql) : "";
 	 	//print_r("SELECT SQL_CALC_FOUND_ROWS * FROM $table $where $order $limit");
 		return $wpdb->get_results($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM $table $where $order $limit"),"ARRAY_A");
	}
}
