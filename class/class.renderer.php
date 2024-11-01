<?php
class WT_Renderer {
	private $is_filter_assigned = false;
	private static $events_default = array(
		# can display event page, tour page or artists page
		// shortcode or widget
		'render_type' => "shortcode",
		'country'     => '',
		'navigation'  => 0,
		#future,past,present
		'date_range'  => "0,13",
		# date yyyy-m-d
		'start_date' => '',
		'end_date'   => '',
		#number of results
		'limit'       => "",
		#DESC,ASC
		'order'       => "DESC",
		#"",order
		'order_by'    => "",
		# group_by:artist/date
		'group_by'    => "NO-GROUP",
		'group_by_tour' => 0,
		# show selected tour id's
		'tour'        => 0,
		'venue'       => 0,
		# show selected artists id's
		'artist'     => 0,
		'genre'         => "",
		#if group artist - show event if artist is opening
		'show_if_not_headline' => 0,
		# file path to template theme/$file
		'theme_file_name'=> "",
		'type'       => ""
	);
	
	private static $tour_default = array(
		'tour'            => "",
		'start_with'     => "",
		'exclude'       => "",
		'artist'          => "",
		'theme_file_name' => "",
		'order_by'        => "name",
		'genre'         => "",
		'limit'         => 0
	);
	
	private static $artist_default = array(
		'start_with'     => "",
		'artist'         => "",
		'direction'      => "DESC",
		'exclude'       => "",
		'theme_file_name'  => "",
		'order_by'    => "name",
		'genre'         => "",
		'limit'         => 0
	);
	
	private static $venue_default = array(
		'start_with'    => "",
		'venue'         => "",
		'exclude'       => "",
		'theme_file_name' => "",
		'direction'     => "DESC",
		'order_by'      => "name",
		"country"       => "",
		'genre'         => "",
		'limit'         => 0
	);
	
	private static $album_default = array(
		'start_with'    => "",
		'album'         => "",
		'exclude'       => "",
		'artist'        => "", 
		'theme_file_name' => "",
		'direction'     => "DESC",
		'order_by'      => "name",
		'type'          => "",
		'genre'         => "",
		'limit'         => 0
	);
	
	private function is_value(&$value) {
		if(!isset($value)) return 0;
		$value = trim($value);
		if(empty($value)) return 0;
		return 1;
	}
	
	private function bool_value(&$value) {
		if(!isset($value)) return 0 ;
		$value = trim($value);	
		if($value=="0") {
			$value = 0;
			return $value;
		} 
		$value = 1;
		return $value;
	}
	
	private function columns_sql($column_name = "",$values = array(),$cond="",$op ="=") {
		$sql = array();
		
		foreach($values as $id) {
			
			$sql[] = "$column_name$op".(is_int($id) ? $id : "'$id'");		
		}
		if(count($sql)) return "$cond (".implode(" OR ",$sql).")";
		return "";
	}
	
	private function result_unique($column_name = "",&$values) {
		$exist_columns = array();
		$new_values = array();
		foreach($values as $value) {
			if(!in_array($value[$column_name],$exist_columns)) {
				$exist_columns[] = $value[$column_name];
				$new_values[] =$value; 
			} 
		}
		
		$values = $new_values;
	}
	
	
	
	private function event_map(&$events = array()) {
		$e = new WT_Event();
		foreach($events as $key=>$value) {
			$events[$key] = $e->template($value,1,1);	
		}
	}
	
	private function parse_url($qs_addition = "") {
		$url = get_bloginfo("url");
		$get = count($_GET); 
		if($get) {
			$url.="?";
			$query_str = array();
			foreach($_GET as $key=>$value){
				if(strtoupper($key) !="WTTOUR" && strtoupper($key) !="WTARTIST") {
					$query_str[] = "$key=$value";			
				}	
			}
			$url.= implode("&",$query_str);
		}
		
		return $url;	
	}
	
	private function get_date_sql($range="0,13"){
		$range = strtoupper($range);
		if($range ==="TODAY") $range = "0,0";
		if($range ==="UPCOMING") $range = "0,13";
		if($range ==="ALL") $range = "-13,13"; 
		if($range ==="ARCHIVE") $range = "-13,0";
		$range = explode(",",$range);
		$start_range = $range[0];
		$end_range = $range[1];
		$time = time();
		// UPCOMING
		if($start_range == "0" && $end_range == "13") {
			return "AND e.event_start_date >= CURDATE()";
		}
		// TODAY
		if($start_range == "0" && $end_range == "0") {
			return "AND e.event_start_date = CURDATE()";
		}
		// ARCHIVE
		if($start_range == "-13" && $end_range == "0") {
			return  "AND e.event_start_date <= CURDATE()";
		}
		if($start_range == "-13" && $end_range == "13") {
			return  "";
		}
		if($start_range == "-13" && $end_range > "-13") {
			$date = date('Y-m-d',mktime(0,0,0,date('m')+$end_range, date('d'), date('Y')));
			return "AND e.event_start_date <= '$date'";
		}
		if($end_range == "13" && $start_range > "-13") {
			$date = date('Y-m-d',mktime(0,0,0,date('m')+$start_range, date('d'), date('Y')));
			return "AND e.event_start_date >= '$date'";
		}
		
		$date_start = date('Y-m-d',mktime(0,0,0,date('m')+$start_range, date('d'), date('Y')));
		$date_end = date('Y-m-d',mktime(0,0,0,date('m')+$end_range, date('d'), date('Y')));
		return "AND (e.event_start_date >='$date_start' AND e.event_end_date <='$date_end')";
	}
	
	public static function exclude_values($exclude = array(),&$exclude_from = array(),$db_name){
		if(!count($exclude)) return;
		for($i=count($exclude_from) ; $i >= 0 ; $i--) {
			$exclude_from_item = $exclude_from[$i];
			if(!in_array($exclude_from_item[$db_name],$exclude)) unset($exclude_from[$i]);	
		}
	}
	
	public function get_navigation($page = 1,$per_page = null,$total_items){
		global $wp_rewrite;
		$total_pages = ceil($total_items/$per_page);
		
		if($wp_rewrite->using_permalinks()) {
			$url = add_query_arg(array("wt_page="=>""),get_permalink(get_query_var("page_id")));
		} else {
			$url = wt_get_cleanurl(array("wt_page="));
		}
		
		$next = $page == $total_pages ? false : $page+1;
		$prev = ($page > 1) ? false : $page-1;
		return $navigation_attr = array(
			"pages" => $total_pages,
			"page"  => $page,
			"items" => $total_items,
			"url"   => $url,
			"next"  => $next ? $url.$next : false,
			"prev"  => $next ? $url.$prev : false,
		); 	
	}
		
	public function events($atts = null,$content=null, $code=""){
		global $wpdb,$_wt_options;
		require_once(WT_PLUGIN_PATH.'admin/template.php');
		require_once(WT_PLUGIN_PATH.'admin/handlers.php');
		require_once(WT_PLUGIN_PATH.'dwoo/dwooAutoload.php');
		$dwoo = new Dwoo();
		$defaults = self::$events_default; 
		$settings = shortcode_atts($defaults,$atts);
		# FILE
		$theme_file_name = empty($settings["theme_file_name"]) ? 0 : $settings["theme_file_name"];
		# LIMIT
		if($settings[limit]>0) {
			$limit = " LIMIT $settings[limit] ";
			$limit_cond = " AND e.event_is_headline=1 ";	
		} else {
			$limit = "";
			$limit_cond = "";
		}
		
		#NAVIGATION - only in no group presentation
		$navigation = ($settings["limit"]>0) ? $settings["navigation"] : 0;
		# ORDER by date direction
		$order = strtoupper($settings["order"]);
		# GENRE
		$genre = (!empty($attr["genre"])) ? $attr["genre"]: 0;
		if($order!="DESC" && $order!="ASC") $order = $defaults["order"];
		# ORDER by tour,artist - alphabatically or by order
		$order_by = strtoupper($settings["order_by"]);
		# DATE
		$date = $this->get_date_sql($settings["date_range"]);
		if(!empty($settings["start_date"]) || !empty($settings["end_date"])) {
			if(empty($settings["start_date"])) $settings["start_date"] = $settings["end_date"];
			if(empty($settings["end_date"])) $settings["end_date"] = $settings["start_date"];
			$date = "AND e.event_start_date  BETWEEN '".$settings["start_date"]."' AND '".$settings["end_date"]."'";  
		}
		# COUNTRY
		$country_sql = !empty($settings["country"]) ? "AND v.venue_country = '".$settings["country"]."'" : "";
		# EVENT TYPE
		$type_sql = !empty($settings["type"]) ? "AND e.event_type = '".$settings["type"]."'" : "";
		#ARTIST TO INCLUDE
		if($this->is_value($settings["artist"])) {
			$artist_to_show = explode(",",$settings["artist"]);
			$show_artists = true;
		} else {
			$artist_to_show = array();
			$show_artists = true;
		}
		#TOUR TO INCLUDE
		$force_tour = isset($_GET["wttour"]) ? $_GET["wttour"] : 0;
		if($this->is_value($settings["tour"]) || $force_tour) {
			if(strtoupper($settings["tour"])!="NO") {
				$tour_to_show = explode(",",$force_tour ? $force_tour : $settings["tour"]);
				$show_tour = true;			
			} else {
				$tour_to_show = array();
				$show_tour = false;		
			}	
		} else {
			$tour_to_show = array();
			$show_tour = true;	
		}
		#VENUE TO INCLUDE
		if($this->is_value($settings["venue"])) {
			$venue_to_show = explode(",",$settings["venue"]);
			$show_venues = true;
		} else {
			$venue_to_show = array();
			$show_venues = true;
		}
		#GROUP BY
		$group_by = strtoupper($settings["group_by"]);
		#GROUP BY TOUR
		$group_by_tour = $force_tour ? 0 : $this->bool_value($settings["group_by_tour"]);
		// SHOW ALL EVENTS
		if($group_by == "NO-GROUP" || empty($group_by)) {
			if($group_by_tour){
				$cond_artists = $this->columns_sql("artist_id",$artist_to_show,"AND");
				$cond_venues = $this->columns_sql("venue_id",$venue_to_show,"AND");
				$cond_tour = $this->columns_sql("tour_id",$tour_to_show,"AND");
				
				$order_tour_sql = ($order_by === "ORDER" ? "t.tour_order,":"")."t.tour_name";
				//AND event_tour_id!=0 
				$tours = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables("event_tour_id,tour_name",array("meta"=>0,"venue"=>1,"artists"=>1,"tour"=>1))." $cond_artists $cond_venues $cond_tour $type_sql $country_sql $date GROUP BY e.event_tour_id ORDER BY $order_tour_sql ASC"),"ARRAY_A");	
				$subgroup_tpl = array();
				
				if(count($tours)) {
					foreach($tours as $tour) {
						$events = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables()." 
							AND event_tour_id=".$tour["event_tour_id"]." $date $cond_artists $cond_venues $limit_cond     
							ORDER BY e.event_start_date $order $limit"),"ARRAY_A");
						if(!empty($limit)) {
						$result_event_id = array();
							foreach($events as $event) {
								$result_event_id[] = $event["event_id"];	
							}
							$result_event_id = implode(" OR event_id=",$result_event_id);
							$result_event_id = "AND (event_id=$result_event_id )";  
							$events = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables()." 
								AND event_tour_id=".$tour["event_tour_id"]." $date $cond_artists $cond_venues $result_event_id      
								ORDER BY e.event_start_date $order"),"ARRAY_A");
						}
						wt_set_event_more_artists($events);
						if(count($events)){
							$this->event_map($events);
							$subgroup_tpl[] = array(
								"name" => $tour["tour_name"],
								"data" => $events  
							);	
						}
					}		
				}
				
				$tpl["group"][] = array("name" => "","subgroup" => $subgroup_tpl);
				
			} else{
				$cond_artists = $this->columns_sql("artist_id",$artist_to_show,"AND");
				$cond_tour = $this->columns_sql("tour_id",$tour_to_show,"AND");
				$cond_venues = $this->columns_sql("venue_id",$venue_to_show,"AND");
				if($navigation) {
					$current_page = isset($_GET["wt_page"]) ? (int) $_GET["wt_page"] : 1;
					$per_page = (int) $settings["limit"];
					$start_index = ($current_page*$per_page)-$per_page;
					$limit = "LIMIT $start_index,$per_page";
				} 
				
				$events = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables("SQL_CALC_FOUND_ROWS *")." 
							$cond_artists $cond_tour $cond_venues $type_sql $country_sql $date $limit_cond 
							ORDER BY e.event_start_date $order $limit"),"ARRAY_A");
							
				if($navigation) {
					$navigation_attr = $this->get_navigation($current_page,$per_page,$wpdb->get_var("SELECT FOUND_ROWS()"));
				}
				
				if(!empty($limit)) {
					$result_event_id = array();
					foreach($events as $event) {
						$result_event_id[] = $event["event_id"];	
					}
					$result_event_id = implode(" OR event_id=",$result_event_id);
					$result_event_id = "AND (event_id=$result_event_id )";  
					
					$events = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables("SQL_CALC_FOUND_ROWS *")." 
							$cond_artists $cond_tour $cond_venues $type_sql $country_sql $date $result_event_id
							ORDER BY e.event_start_date $order"),"ARRAY_A");
				}
							
				wt_set_event_more_artists($events);		
				if(count($events)){
					$this->event_map($events);					
					$subgroup_tpl[] = array(
						"name" => "",
						"data" => $events  
					);
				}
				$tpl["group"][] = array(
					"name" => "",
					"subgroup" => $subgroup_tpl
				);
				
				
			}
			
		}
		// SHOW ALL EVENTS BY ARTIST - ALWAYS SHOW THE ARTIST!
		if($group_by =="ARTIST") {
			$cond_artists = $this->columns_sql("artist_id",$artist_to_show,"AND");
			$cond_tour = $this->columns_sql("event_tour_id",$tour_to_show,"AND");
			$cond_venues = $this->columns_sql("venue_id",$venue_to_show,"AND");
			$order_artist_sql = ($order_by === "ORDER" ? "a.artist_order,":"")."a.artist_name";
			#SHOW IF NOT HEADLINE
			$show_if_not_headline = $settings["show_if_not_headline"] == 1 ? "" : " AND event_is_headline=1 ";
		
			if($order_by === "ORDER") $order_artist_sql = "a.artist_order,$order_artist_sql";
			$artists = (array) $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables("event_artist_id,artist_name",array("meta"=>0,"venue"=>!empty($country_sql),"artists"=>1,"tour"=>0))." $cond_artists $country_sql $date $show_if_not_headline GROUP BY e.event_artist_id ORDER BY $order_artist_sql ASC"),"ARRAY_A");	
			$group_tpl = array("group" => array());
			$cond_tour = $this->columns_sql("tour_id",$tour_to_show,"AND");
			foreach($artists as $artist) {
				$subgroup_tpl = array();
				$artist_id = $artist["event_artist_id"];
				$artist_name = $artist["artist_name"];
				if($group_by_tour){
					$order_tour_sql = "t.tour_name";
					if($order_by === "ORDER") $order_tour_sql = "t.tour_order,$order_tour_sql";
					
					$tours = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables("event_tour_id,tour_name",array("meta"=>0,"venue"=>!empty($country_sql),"artists"=>0,"tour"=>1))." AND event_artist_id=$artist_id $cond_tour $type_sql $country_sql $date GROUP BY e.event_tour_id ORDER BY $order_tour_sql ASC"),"ARRAY_A");		
					if(count($tours)) {
						foreach($tours as $tour) {
							$events = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables()." 
								AND event_tour_id=".$tour["event_tour_id"]."
								AND event_artist_id=$artist_id 
								$date
								$cond_venues 
								$type_sql  
								$country_sql  
								$show_if_not_headline 
								ORDER BY e.event_start_date $order"),"ARRAY_A");
							if(count($events)){
								$this->event_map($events);
								$subgroup_tpl[] = array(
									"name" => $tour["tour_name"],
									"data" => $events  
								);	
							}
						}		
					} 
				} else {
					$events = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables()." AND event_artist_id=$artist_id 
								$cond_tour $cond_venues $type_sql $country_sql $show_if_not_headline $date 
								ORDER BY e.event_start_date $order $limit"),"ARRAY_A");
					if(count($events)){
						$this->event_map($events);					
						$subgroup_tpl[] = array(
							"name" => "",
							"data" => $events  
						);
					}	
				}
				
				$tpl["group"][] = array(
					"name"     => $artist_name,
					"filter"   => $filter,
					"subgroup" => $subgroup_tpl
				);	
			}
		}
		
		if($group_by == "DATE") {
			
			$dates = $wpdb->get_results($wpdb->prepare((!empty($country_sql) ? WT_Event::sql_all_tables("event_start_date") : WT_Event::sql("event_start_date"))." $date $type_sql $country_sql GROUP BY e.event_start_date $order"),"ARRAY_A");
			$group_tpl = array(
				"group" => array()
			);		
			foreach($dates as $date) {
				
				$events = $wpdb->get_results($wpdb->prepare(WT_Event::sql_all_tables()." AND e.event_start_date ='".$date["event_start_date"]."' $type_sql $country_sql ORDER BY e.event_artist_id $order $limit"),"ARRAY_A");
				wt_set_event_more_artists($events);
				if(count($events)){
					$this->event_map($events);	
				}
				$tpl["group"][] = array(
					// to do - change name to date admin format text
					"name" => $date["event_start_date"],
					"data" => $events
				);							
			}
		}

		if($settings["render_type"] == "widget") {
			$theme = new WT_Theme();
			$theme->events_widget(array("attr"=>$settings,"tpl"=>$tpl));	
		} else {
			
			require(WT_PLUGIN_PATH.'theme/layout.renderer.php');
			
			return $html;	
		}
				
		
	}
	
	public function single($page = 0,$id) {
		if($page && $id) {
			require_once(ABSPATH."wp-load.php");
			require_once(WT_PLUGIN_PATH."admin/template.php");
			require_once(WT_PLUGIN_PATH."admin/handlers.php");
			$dwoo = new Dwoo();  
				
			switch($page) {
				case "event":
					$event_id = $id;
					if($event_id) {
						$event = new WT_Event($event_id);
						$event->retrieve();
					}
				break;
				case "artist":
					$artist_id = $id;
					if($artist_id) {
						$artist = new WT_Artist($artist_id);
						$artist->retrieve();
					}
				break;
				case "tour":
					$tour_id = $id;
					if($tour_id) {
						$tour = new WT_Tour($tour_id);
						$tour->retrieve();
					}
				break;
				case "venue":
					$venue_id = $id;
					if($venue_id) {
						$venue = new WT_Venue($venue_id);
						$venue->retrieve();
					}
				break;
				case "album":
					$album_id = $id;
					if($album_id) {
						$album = new WT_Album($album_id);
						$album->retrieve();
					}
				break;
			}
			
			$tpl = new WT_Theme();
			include $tpl->single_path();
			exit;
		
		}	
	}
	
	public function artists($atts = null,$content=null, $code="") {
		global $wpdb,$_wt_options;
		require_once(WT_PLUGIN_PATH.'admin/template.php');
		require_once(WT_PLUGIN_PATH.'admin/handlers.php');
		require_once(WT_PLUGIN_PATH.'dwoo/dwooAutoload.php');
	
		
		
		$dwoo = new Dwoo();
		# CONFIG PARAMS
		$attr = shortcode_atts(self::$artist_default,$atts);
		# TEMPLATE
		$theme_file_name = empty($attr["theme_file_name"]) ? 0 : $attr["theme_file_name"];
		#ARTIST
		$artists_to_show = empty($attr["artist"]) ? 0 : explode(",",$attr["artist"]);
		#EXCLUDE ARTIST
		$artists_to_hide = empty($attr["exclude"]) ? 0 : explode(",",$attr["exclude"]);
		#GENRE
		$genre_to_show = empty($attr["genre"]) ? 0 : explode(",",$attr["genre"]);
		#ORDER
		$order = strtolower($attr["order_by"]) == "order" ? "artist_order" : "artist_name";
		#DIRECTION
		$direction = strtoupper($attr["direction"]) == "ASC" ? "ASC" : "DESC";
		#START WITH
		$start_with = $attr["start_with"];
		#TYPE
		$album_type = (!empty($attr["type"])) ? $attr["type"]: 0;
		#TYPE
		$album_genre = (!empty($attr["genre"])) ? $attr["genre"]: 0;
		#LIMIT
		$limit = ($attr["limit"] && is_numeric($attr["limit"])) ? " LIMIT $attr[limit] " : "";
		
		
		$artists = 0;
		# GET RESULTS
		$conds = array();
		$conds_str = "";
		if(!empty($start_with)) $conds[] = "UPPER(artist.artist_name) LIKE UPPER('$start_with%')";
		if($artists_to_show) $conds[] = $this->columns_sql("artist.artist_id",$artists_to_show);
		if($artists_to_hide) $conds[] = $this->columns_sql("artist.artist_id",$artists_to_hide,null,"!=");
		if($album_genre) {
			$conds_str .= 	" JOIN wp_wtr_attachment AS att ON artist.artist_id = att.attachment_target_id";
			$conds[] = "att.attachment_target='artist'";
			$conds[] = "att.attachment_type='genre'";
			$conds[] = $this->columns_sql("att.attachment_info",$genre_to_show);
		}
		if(count($conds) > 0) $conds_str.=" WHERE ".implode(" AND ",$conds);
		$artists = $wpdb->get_results("SELECT * FROM ".WORDTOUR_ARTISTS. " as artist $conds_str GROUP BY artist.artist_id ORDER BY $order $direction $limit","ARRAY_A");
	
		# SET TEMPLATE
		$tpl = array();
		if($artists) {
			$artistObj = new WT_Artist(); 
			foreach($artists as $artist) {
				$artistObj->id = $artist["artist_id"];
				$tpl["data"][] = $artistObj->template($artist);
			}
		} else {
			$tpl["data"] = array();	
		} 
		
		include WT_PLUGIN_PATH.'theme/layout.renderer.php';
		
		return $html;	
	}
	
	public function albums($atts = null,$content=null, $code="") {
		global $wpdb,$_wt_options;
		require_once(WT_PLUGIN_PATH.'admin/template.php');
		require_once(WT_PLUGIN_PATH.'admin/handlers.php');
		require_once(WT_PLUGIN_PATH.'dwoo/dwooAutoload.php');
	
		$dwoo = new Dwoo();
		# CONFIG PARAMS
		$attr = shortcode_atts(self::$album_default,$atts);
		# TEMPLATE
		$theme_file_name = empty($attr["theme_file_name"]) ? 0 : $attr["theme_file_name"];
		#ARTIST
		$artists_to_show = empty($attr["artist"]) ? 0 : explode(",",$attr["artist"]);
		#ALBUMS
		$albums_to_show = empty($attr["album"]) ? 0 : explode(",",$attr["album"]);
		#EXCLUDE ALBUMS
		$albums_to_hide = empty($attr["exclude"]) ? 0 : explode(",",$attr["exclude"]);
		#GENRE
		$genre_to_show = empty($attr["genre"]) ? 0 : explode(",",$attr["genre"]);
		#ORDER
		$order = strtolower($attr["order_by"]) == "order" ? "album_order" : "album_title";
		#DIRECTION
		$direction = strtoupper($attr["direction"]) == "ASC" ? "ASC" : "DESC";
		#START WITH
		$start_with = $attr["start_with"];
		#TYPE
		$album_type = (!empty($attr["type"])) ? $attr["type"]: 0;
		#TYPE
		$album_genre = (!empty($attr["genre"])) ? $attr["genre"]: 0;
		#LIMIT
		$limit = ($attr["limit"] && is_numeric($attr["limit"])) ? " LIMIT $attr[limit] " : "";
		
		$albums = 0;
		# GET RESULTS
		$conds = array();
		$conds_str = "";
		if(!empty($start_with)) $conds[] = "UPPER(album.album_title) LIKE UPPER('$start_with%')";
		if($albums_to_show) $conds[] = 	$this->columns_sql("album.album_id",$albums_to_show);
		if($albums_to_hide) $conds[] = 	$this->columns_sql("album.album_id",$albums_to_hide,null,"!=");
		if($artists_to_show) $conds[] = $this->columns_sql("album.album_artist_id",$artists_to_show);
		if($album_type) $conds[] = 	"album.album_type='$album_type'";
		if($album_genre) {
			$conds_str .= 	" JOIN wp_wtr_attachment AS att ON album.album_id = att.attachment_target_id";
			$conds[] = "att.attachment_target='album'";
			$conds[] = "att.attachment_type='genre'";
			$conds[] = $this->columns_sql("att.attachment_info",$genre_to_show);
		}
		if(count($conds) > 0) $conds_str.=" WHERE ".implode(" AND ",$conds);
		$albums = $wpdb->get_results("SELECT * FROM ".WORDTOUR_ALBUMS. " as album LEFT JOIN ".WORDTOUR_ARTISTS." as artist ON artist.artist_id=album.album_artist_id $conds_str GROUP BY album.album_id ORDER BY $order $direction $limit","ARRAY_A");
		//$this->result_unique("album_id",$albums);
		# SET TEMPLATE
		$tpl = array();
		if($albums) {
			$albumObj = new WT_Album(); 
			foreach($albums as $album) {
				$albumObj->id = $album["album_id"];
				$tpl["data"][] = $albumObj->template($album);
			}
		} else {
			$tpl["data"] = array();	
		} 
		
		include WT_PLUGIN_PATH.'theme/layout.renderer.php';
		
		return $html;	
	}
	
	public function tours($atts = null,$content=null, $code="") {
		global $wpdb,$_wt_options;
		require_once(WT_PLUGIN_PATH.'admin/template.php');
		require_once(WT_PLUGIN_PATH.'admin/handlers.php');
		require_once(WT_PLUGIN_PATH.'dwoo/dwooAutoload.php');
		
		$dwoo = new Dwoo();
		# CONFIG PARAMS
		$attr = shortcode_atts(self::$tour_default,$atts);
		# TEMPLATE
		#ARTIST
		$artist_to_show = empty($attr["artist"]) ? 0 : explode(",",$attr["artist"]);
		#TOUR
		$tour_to_show = empty($attr["tour"]) ? 0 : explode(",",$attr["tour"]);
		#ORDER
		$order = strtolower($attr["order_by"]) == "name" ? "tour_name" : "tour_order";
		#DIRECTION
		$direction = strtoupper($attr["direction"]) == "ASC" ? "ASC" : "DESC";
		#START WITH
		$start_with = $attr["start_with"];  
		
		
		$theme_file_name = empty($attr["theme_file_name"]) ? 0 : $attr["theme_file_name"];
		#TOUR
		$tours_to_show = empty($attr["tour"]) ? 0 : explode(",",$attr["tour"]);
		#EXCLUDE TOUR
		$tours_to_hide = empty($attr["exclude"]) ? 0 : explode(",",$attr["exclude"]);
		#ARTIST
		$artists_to_show = empty($attr["artist"]) ? 0 : explode(",",$attr["artist"]);
		#GENRE
		$genre_to_show = empty($attr["genre"]) ? 0 : explode(",",$attr["genre"]);
		#ORDER
		$order = strtolower($attr["order_by"]) == "name" ? "tour_name" : "tour_order";
		#DIRECTION
		$direction = strtoupper($attr["direction"]) == "ASC" ? "ASC" : "DESC";
		#START WITH
		$start_with = $attr["start_with"];
		#TYPE
		$tour_genre = (!empty($attr["genre"])) ? $attr["genre"]: 0;
		#LIMIT
		$limit = ($attr["limit"] && is_numeric($attr["limit"])) ? " LIMIT $attr[limit] " : "";
		
		
		$tours = 0;
		# GET RESULTS
		$conds = array();
		$conds_str = "";
		if(!empty($start_with)) $conds[] = "UPPER(tour.tour_name) LIKE UPPER('$start_with%')";
		if($tours_to_show) $conds[] = 	$this->columns_sql("tour.tour_id",$tours_to_show);
		if($tours_to_hide) $conds[] = 	$this->columns_sql("tour.tour_id",$tours_to_hide,null,"!=");
		if($tour_genre) {
			$conds_str .= 	" JOIN wp_wtr_attachment AS att ON tour.tour_id = att.attachment_target_id";
			$conds[] = "att.attachment_target='tour'";
			$conds[] = "att.attachment_type='genre'";
			$conds[] = $this->columns_sql("att.attachment_info",$genre_to_show);
		}
		if(count($conds) > 0) $conds_str.=" WHERE ".implode(" AND ",$conds);
		$tours = $wpdb->get_results("SELECT * FROM ".WORDTOUR_TOUR. " as tour $conds_str GROUP BY tour.tour_id ORDER BY $order $direction $limit","ARRAY_A");
		if($artists_to_show) {
			// get all ids of tour
			$tours_to_show = array();
			foreach($tours as $tour) {
				$tours_to_show[] = $tour["tour_id"];  	
			}
			$conds = array();
			$conds[] = $this->columns_sql("event.event_tour_id",$tours_to_show);
			$conds[] = $this->columns_sql("event.event_artist_id",$artists_to_show);
			if(count($conds) > 0) $conds_str.=" WHERE ".implode(" AND ",$conds);
			$artist_tours = $wpdb->get_results("SELECT event.event_tour_id FROM ".WORDTOUR_EVENTS. " as event $conds_str","ARRAY_A");
			$tours_to_show = array();
			foreach($artist_tours as $artist_tour) {
				$tours_to_show[] = $artist_tour["event_tour_id"];  	
			}
			$tours_to_show = array_unique($tours_to_show);
			$new_tours = array();
			foreach($tours as $tour) {
				if(in_array($tour["tour_id"],$tours_to_show)) $new_tours[] = $tour;
			}
			$tours = $new_tours; 
		}
		
		$tpl = array();
		if($tours) {
			foreach($tours as $tour) {
				$t = new WT_Tour($tour["tour_id"]);
				$tpl["data"][] = $t->template($tour);
			}
		} else {
			$tpl["data"] = 0;	
		} 
		
		include WT_PLUGIN_PATH.'theme/layout.renderer.php';
		
		return $html;	
	}
	
	public function venues($atts = null,$content=null, $code="") {
		global $wpdb,$_wt_options;
		require_once(WT_PLUGIN_PATH.'admin/template.php');
		require_once(WT_PLUGIN_PATH.'admin/handlers.php');
		require_once(WT_PLUGIN_PATH.'dwoo/dwooAutoload.php');
	
		$dwoo = new Dwoo();
		# CONFIG PARAMS
		$attr = shortcode_atts(self::$venue_default,$atts);
		
		
		$theme_file_name = empty($attr["theme_file_name"]) ? 0 : $attr["theme_file_name"];
		#VENUE
		$venues_to_show = empty($attr["venue"]) ? 0 : explode(",",$attr["venue"]);
		#EXCLUDE VENUES
		$venues_to_hide = empty($attr["exclude"]) ? 0 : explode(",",$attr["exclude"]);
		#GENRE
		$genre_to_show = empty($attr["genre"]) ? 0 : explode(",",$attr["genre"]);
		#ORDER
		$order = strtolower($attr["order_by"]) == "name" ? "venue_name" : "venue_order";
		#DIRECTION
		$direction = strtoupper($attr["direction"]) == "ASC" ? "ASC" : "DESC";
		#START WITH
		$start_with = $attr["start_with"];
		#TYPE
		$venue_genre = (!empty($attr["genre"])) ? $attr["genre"]: 0;
		#LIMIT
		$limit = ($attr["limit"] && is_numeric($attr["limit"])) ? " LIMIT $attr[limit] " : "";
		#COUNTRY
		$country = !empty($attr["country"]) ? $attr["country"] : 0;
		
		$venues = 0;
		# GET RESULTS
		$conds = array();
		$conds_str = "";
		if(!empty($start_with)) $conds[] = "UPPER(venue.venue_name) LIKE UPPER('$start_with%')";
		if($venues_to_show) $conds[] = 	$this->columns_sql("venue.venue_id",$venues_to_show);
		if($venues_to_hide) $conds[] = 	$this->columns_sql("venue.venue_id",$venues_to_hide,null,"!=");
		if($country) $conds[] = "UPPER(venue.venue_country) = '".strtoupper($country)."'";
		if($venue_genre) {
			$conds_str .= 	" JOIN wp_wtr_attachment AS att ON venue.venue_id = att.attachment_target_id";
			$conds[] = "att.attachment_target='venue'";
			$conds[] = "att.attachment_type='genre'";
			$conds[] = $this->columns_sql("att.attachment_info",$genre_to_show);
		}
		if(count($conds) > 0) $conds_str.=" WHERE ".implode(" AND ",$conds);
		$venues = $wpdb->get_results("SELECT * FROM ".WORDTOUR_VENUES. " as venue $conds_str GROUP BY venue.venue_id ORDER BY $order $direction $limit","ARRAY_A");
		$tpl = array();
		if($venues) {
			foreach($venues as $venue) {
				$v = new WT_Venue($venue["venue_id"]);
				$tpl["data"][] = $v->template($venue);
			}
		} else {
			$tpl["data"] = array();	
		} 
		
		include WT_PLUGIN_PATH.'theme/layout.renderer.php';
		
		return $html;	
	}
	
	
	
	public function event($atts) {
		require_once(WT_PLUGIN_PATH.'admin/template.php');
		require_once(WT_PLUGIN_PATH.'admin/handlers.php');
		require_once(WT_PLUGIN_PATH.'dwoo/dwooAutoload.php');
	
		$dwoo = new Dwoo();  
		
		if($atts["id"]) {
			$event = new WT_Event($atts["id"]);
			$event->retrieve();
			if($event->data) {
				$theme = new WT_Theme();
				return $theme->post_event($event->template(),false);
			}	
		} 
		return "";	
	}
	
	public function bio($atts = null,$content=null, $code="") {
		require_once(WT_PLUGIN_PATH.'admin/template.php');
		require_once(WT_PLUGIN_PATH.'admin/handlers.php');
		require_once(WT_PLUGIN_PATH.'dwoo/dwooAutoload.php');
	
		$dwoo = new Dwoo();  
		if($atts["artist"]) {
			$artist = new WT_Artist($atts["artist"]);
			$artist->retrieve();
			if($artist->data) {
				$tpl = $artist->template();
				include WT_PLUGIN_PATH.'theme/layout.renderer.php';
				return $html;
			}	
		} 
		return "";	
	}
	
	public function videos($atts = null,$content=null, $code="") {
		require_once(WT_PLUGIN_PATH.'admin/template.php');
		require_once(WT_PLUGIN_PATH.'admin/handlers.php');
		require_once(WT_PLUGIN_PATH.'dwoo/dwooAutoload.php');
	
		$dwoo = new Dwoo();  
		
		
		if(isset($atts["artist"])) {
			$object = new WT_Artist($atts["artist"]);
			$object_type = "artist";
		} else if(isset($atts["tour"])) {
			$object = new WT_Tour($atts["tour"]);
			$object_type = "tour";
		} else if(isset($atts["venue"])) {
			$object = new WT_Venue($atts["venue"]);
			$object_type = "venue";
		} else if (isset($atts["event"])) {
			$object = new WT_Event($atts["event"]);
			$object_type = "event";
		}
		
		if($object && $object_type) {
			$videos = $object->video($object->get_videos($object_type));
			$tpl= array("videos"=>$videos,"total"=>count($videos));
			include WT_PLUGIN_PATH.'theme/layout.renderer.php';
		}
		
		
		return $html;	
	}
}

?>