<?php
require_once("../../../../wp-load.php");
require_once("./handlers.php");

if(isset($_GET['term']) && $_GET['type'] == "yql") {
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;
	$result = array();
	if($_GET['method'] == "track") {	
		$yql_base_url = "http://query.yahooapis.com/v1/public/yql";
		$yql_query = "select * from music.track.search where keyword=\"".$_GET['term']."\"";
		if(!empty($_GET['artist'])) $yql_query.=" AND Artist.name LIKE \"%$_GET[artist]%\""; 
		$yql_query.=" LIMIT $max_result";
		$yql_query_url = $yql_base_url . "?q=".urlencode($yql_query);
		$yql_query_url .= "&format=json";
		$response = wt_file_get_contents($yql_query_url);
		$response = json_decode($response);
		if($response->query->results) {
			$tracks = $response->query->results->Track;
			$result = array();
			foreach($tracks as $track) {
				$result[] = array(
					"duration" => $track->duration,
					"release_year" => $track->releaseYear,
					"title" => $track->title,
					"artist" => $track->Artist->name,
					"album" => $track->Album->Release->title,
					"album_release_date" => WT_DBPrepere::admin_date_out($track->Album->Release->releaseDate),
					"label" => $track->Album->Release->label,
				);	
			}
			
		} 
	}
	
	echo json_encode($result);
	exit();
}




if(isset($_GET['term']) && $_GET['type'] == "country") {
	$countries = get_countries() ;
	$term = strtolower($_GET['term']) ; 
	$result = array();
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	$i = 0 ;
	
	foreach($countries as $code=>$country) {
		if(stripos(strtolower($country),$term) !== false && $i<$max_result) {
			array_push($result,array("term"=>$code,"value"=>$country)) ;
			$i++;
		}		
	}
	
	echo json_encode($result);
	exit();
}

if(isset($_GET['term']) && $_GET['type'] == "genre") {
	$genres = wordtour_get_all_genre() ;
	$term = strtolower($_GET['term']) ; 
	$result = array();
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	$i = 0 ;
	
	foreach($genres as $genre) {
		if(stripos(strtolower($genre),$term) !== false && $i<$max_result) {
			array_push($result,array("term"=>$genre,"value"=>ucwords($genre))) ;
			$i++;
		}		
	}
	
	echo json_encode($result);
	exit();
}

if(isset($_GET['term']) && $_GET['type'] == "event_type") {
	$type = get_all_event_type() ;
	$term = strtolower($_GET['term']) ; 
	$result = array();
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	$i = 0 ;
	
	foreach($type as $t) {
		if(stripos(strtolower($t),$term) !== false && $i<$max_result) {
			array_push($result,array("term"=>$t,"value"=>ucwords($t))) ;
			$i++;
		}		
	}
	
	echo json_encode($result);
	exit();
}

if(isset($_GET['term']) && $_GET['type'] == "state") {
	$states = get_states() ;
	$term = strtolower($_GET['term']) ; 
	$result = array();
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	$i = 0 ;
	
	foreach($states as $code=>$state) {
		if(stripos(strtolower($state),$term) !== false && $i<$max_result) {
			array_push($result,array("term"=>$code,"value"=>$state)) ;
			$i++;
		}		
	}
	
	echo json_encode($result); 
	
	exit();
}




if(isset($_GET['term']) && $_GET['type'] == "venues") {
	global $wpdb; 
	$term = $_GET['term'];
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	
    $venues = $wpdb->get_results("SELECT * FROM ".WORDTOUR_VENUES." AS v WHERE v.venue_name LIKE '%$term%' LIMIT $max_result","ARRAY_A"); 
	$response = array();
    if($venues) {
		foreach($venues as $venue) {
			$term = $venue["venue_id"];
			$value = $venue["venue_name"] ;
			$label = "$value<div style='font-size:80%;color:#AAAAAA'>".get_country_by_code($venue["venue_country"])."</div>";
			array_push($response,array("term"=>$term,"label"=>$label,"value"=>$value));					
		}
	}
	echo json_encode($response);
    
} 

if(isset($_GET['term']) && $_GET['type'] == "artists") {
	global $wpdb;
	$term = $_GET['term'];
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	
    $artists = $wpdb->get_results("SELECT * FROM ".WORDTOUR_ARTISTS." AS a WHERE a.artist_name LIKE '%$term%' LIMIT $max_result","ARRAY_A"); 
	$response = array();
    if($artists) {
		foreach($artists as $artist) {
			$term = $artist["artist_id"];
			$value = $artist["artist_name"] ;
			$label = $value;
			array_push($response,array("term"=>$term,"label"=>$label,"value"=>$value));					
		}
	}
	echo json_encode($response);
    
}

if(isset($_GET['term']) && $_GET['type'] == "tracks") {
	global $wpdb;
	$term = $_GET['term'];
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	$tracks = $wpdb->get_results("SELECT * FROM ".WORDTOUR_TRACKS." AS t LEFT JOIN ".WORDTOUR_ARTISTS." AS a ON t.track_artist_id = a.artist_id WHERE t.track_title LIKE '%$term%' ORDER BY t.track_title LIMIT $max_result","ARRAY_A"); 
	$response = array();
    if($tracks) {
		foreach($tracks as $track) {
			$term = $track["track_id"];
			$value = $track["track_title"] ;
			$label = $value."<br><small>$track[artist_name]</small>";
			array_push($response,array("term"=>$term,"label"=>$label,"value"=>$value));					
		}
	}
	echo json_encode($response);
}

if(isset($_GET['term']) && $_GET['type'] == "tour") {
	global $wpdb;
	$term = $_GET['term'];
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	
    $tours = $wpdb->get_results("SELECT * FROM ".WORDTOUR_TOUR." AS t WHERE t.tour_name LIKE '%$term%' LIMIT $max_result","ARRAY_A"); 
	$response = array();
    if($tours) {
		foreach($tours as $tour) {
			$term = $tour["tour_id"];
			$value = $tour["tour_name"] ;
			$label = $value;
			array_push($response,array("term"=>$term,"label"=>$label,"value"=>$value));					
		}
	}
	echo json_encode($response);
    
} 

if(isset($_GET['term']) && $_GET['type'] == "category") {
	global $wpdb;
	$term = $_GET['term'];
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	$categories = get_categories(array('search'=>$term)); 
	$response = array();
    if($categories) {
		foreach($categories as $category) {
			$term = $category->term_id;
			$value = $category->cat_name ;
			$label = $value;
			array_push($response,array("term"=>$term,"label"=>$label,"value"=>$value));					
		}
	}
	echo json_encode($response);
	
}
  
if(isset($_GET['term']) && $_GET['type'] == "thumbnail") {
	global $wpdb;
	$term = $_GET['term'];
	$max_result = isset($_GET["maxRows"]) ?(int) $_GET["maxRows"] : 10 ;	
	
    $thumbnails = $wpdb->get_results("SELECT ID,post_name,guid FROM $wpdb->posts WHERE post_name LIKE '%$term%' AND post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1 AND post_mime_type LIKE 'image%' LIMIT $max_result","ARRAY_A"); 
	$response = array();
    if($thumbnails) {
		foreach($thumbnails as $thumbnail) {
			$term = $thumbnail["ID"].":".$thumbnail["guid"];
			$value = $thumbnail["post_name"] ;
			$label = "<img src='".$thumbnail["guid"]."' width='20' height='20'> $value";
			array_push($response,array("term"=>$term,"label"=>$label,"value"=>$value));					
		}
	}
	echo json_encode($response);
    
} 

		


?>