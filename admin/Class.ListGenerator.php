<?php
require_once("template.php");

Class WT_List {
	const total_per_page = 15 ;
	protected $page = 0;
	protected $page_links;
	  
	protected $total_found_rows = 0;
	protected $results = 0; 
	public $columns = array();
	public $columns_name ;
	public $id ;
	
	public function __construct() {}
	
	protected function found_rows() {
		return $this->total_found_rows;
	}
	
	protected function get_start_limit($current_page){
		$limit = self::total_per_page ;
		$this->page =	intval($current_page);
		if(!$this->page) $this->page = 1;
		$limit_start = ($this->page-1)*$limit;
		return $limit_start;
	}
	
	protected function paging(){
		$rows = $this->found_rows();
		$max_rows = self::total_per_page;
		if($rows > $max_rows){
			$total_pages = ceil($rows/$max_rows);
			
			$this->page_links = paginate_links( array(
				'base' => add_query_arg( 'paged', '%#%' ),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $total_pages,
				'current' => $this->page
			));
		}
	}
	
	public function is_paging(){
		return !empty($this->page_links) ; 	
	}
	
	public function render_paging(){
		$limit = self::total_per_page;
		$rows = $this->found_rows();
		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
			number_format_i18n( ($this->page - 1 ) * $limit + 1 ),
			number_format_i18n( min($this->page * $limit,$rows) ),
			number_format_i18n($rows),
			$this->page_links
		); 
		echo $page_links_text;	
	}
	
	public function get_results($sql,$current_page = 1) {
		global $wpdb;
		$limit = $this->get_start_limit($current_page);
		$results = $wpdb->get_results($sql." LIMIT ".$limit.",".self::total_per_page,"ARRAY_A");
		if($results) {
			$this->results = $results;
			$this->total_found_rows = $wpdb->get_var("SELECT FOUND_ROWS()"); 
			$this->paging();
		}
	}
	
	public function set_columns($name,$columns = array()) {
		register_column_headers($name,$columns);
		$this->columns_name = $name ;
		$this->columns = $columns;
	}
	
	protected function get_rows($tpl_handler) {
		# Call Function From "template.php"
		call_user_func($tpl_handler,$this->results);
	}	
	
	protected function get_cols() {
		print_column_headers($this->columns_name);	
	}
	
	public function render($id="",$tpl_handler="") {
		echo "<table id=\"".$id."\" class=\"widefat\" cellspacing=\"0\">" ;
		echo 	"<thead>" ;
		echo    	"<tr>";
						 $this->get_cols();
		echo		"</tr>";
		echo	"</thead>";
		
		echo	"<tbody>";
					$this->get_rows($tpl_handler); 	
		echo	"</tbody>";
		
		echo	"<tfoot>";
		echo		"<tr>";
						$this->get_cols();
		echo		"</tr>";
		echo	"</tfoot>";

		echo "</table>";	
	}	
}




Class ListGenerator {
	private $total_found_rows = 0; 
	public $columns = array();
	public $columns_name ;
	public $id ;

	public function __construct($id="") {
		$this->id = $id ;		
	}
	
	public function found_rows() {
		return $this->total_found_rows;
	}
	
	public function render($sql,$tpl_handler) {
		echo "<table id=\"".$this->id."\" class=\"widefat post fixed\" cellspacing=\"0\">" ;
		echo 	"<thead>" ;
		echo    	"<tr>";
						 $this->getCols();
		echo		"</tr>";
		echo	"</thead>";
		
		echo	"<tbody>";
					$this->getRows($sql,$tpl_handler); 	
		echo	"</tbody>";
		
		echo	"<tfoot>";
		echo		"<tr>";
						$this->getCols();
		echo		"</tr>";
		echo	"</tfoot>";

		echo "</table>";	
	}
	
	public function setColumns($name,$columns = array()) {
		register_column_headers($name,$columns);
		$this->columns_name = $name ;
		$this->columns = $columns;
	}
	
	public function getCols() {
		print_column_headers($this->columns_name);	
	}
	
	public function getRows($sql,$tpl_handler) {
		global $wpdb;
		$rows = $wpdb->get_results($sql,"ARRAY_A"); 
		$this->total_found_rows = $wpdb->get_var("SELECT FOUND_ROWS()"); 
//		if(!$rows) {
//			echo "No Results";
//			return ; 
//			
//		} 
		// call function from template.php
		call_user_func($tpl_handler,$rows);

	}	
	
}
